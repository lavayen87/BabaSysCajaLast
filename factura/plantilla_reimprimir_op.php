
<?php
date_default_timezone_set("America/Argentina/Salta");
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$nombre = $_SESSION['nombre'];
	$rol = $_SESSION['rol'];
	$numero_caja = $_SESSION['numero_caja'];
}
	$num_op = $_GET['num_op']; // Dato enviado por post

	include('../conexion.php');
	include('../funciones.php');
	
	$query = "SELECT * from orden_pago 
	          WHERE numero_orden = '$num_op'";   

	$result = mysqli_query($connection, $query);    
	$datos = mysqli_fetch_array($result);

	$numero_orden = $datos['numero_orden'];
	$fecha = $datos['fecha']; 
	$emisor = $datos['numero_caja'];
	$importe = $datos['importe'];
	$detalle = $datos['detalle'];
	$empresa= $datos['empresa'];
	$obra = $datos['obra'];
	$cuenta = $datos['cuenta'];
	$moneda = $datos['moneda'];
	$recibe = $datos['recibe']; 
	$hora = date('G').':'.date('i').':'.date('s');

	// Numero de solicitud
	$qry = "SELECT num_solicitud
	FROM cheques_cartera 
	WHERE num_orden_pago = '$num_op'";

	$res_solic = mysqli_query($connection, $qry);
	$datos_solic = mysqli_fetch_assoc($res_solic);
	$num_solicitud = $datos_solic['num_solicitud'];

	$qry = "SELECT solicitante,numero_caja,caja_pago
	FROM solicitud_orden_pago 
	WHERE numero_orden = '$num_solicitud'";

	$res_nom_solic = mysqli_query($connection, $qry);
	$datos_nom_solic = mysqli_fetch_assoc($res_nom_solic);
	$solicitante = $datos_nom_solic['solicitante'];
	$numero_caja_solicitante = $datos_nom_solic['numero_caja'];
	$caja_pago = $datos_nom_solic['caja_pago'];
	
	// Rol de caja de pago
	$qry = "SELECT rol
	FROM usuarios
	WHERE numero_caja = '$caja_pago'";

	$res_rol = mysqli_query($connection, $qry);
	$datos_rol = mysqli_fetch_assoc($res_rol);
	$rol_caja_pago = $datos_rol['rol'];

	$res_ch = mysqli_query($connection, $qry);
	// Datos de cheques
	$qry = "SELECT fecha_vto,banco,importe,num_cheque
	FROM cheques_cartera 
	WHERE num_orden_pago = '$num_op'
	ORDER BY fecha_vto";

	$res_ch = mysqli_query($connection, $qry);

	$p = ""; 
	$k = "";
	$band = 0; // existencia de cheques
	$c = 0; // cantidad de cheques
	$j = 0; // contador para los primeros cheques

	if($res_ch->num_rows > 0)
	{
		$c = $res_ch->num_rows; 
		$band = 1; 

		if($c <= 4)
		{	
					
			while($datos_cheq = mysqli_fetch_array($res_ch))
			{
				$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
				
			}
		}
		else{
			
			while($datos_cheq = mysqli_fetch_array($res_ch))
			{	
				
				if($j < 4 )
				{
					$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
					
					$j++;
				}
				else{
					$k.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
					
				}
				
			}
			
		}

	}

	$aux = 0;
	$texto1 = '';
	$texto2 = '';
	$findme   = "CERO";
	require "../conversor.php";

	//if($datos['importe'] > 0)
	//{
		$cantidad = '$'.number_format($datos['importe'],2,',','.');
		$aux = $datos['importe'];
		
		if( parte_entera(strval($aux)) <> 0)
		{	
			$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
			$pos = strpos($texto1, $findme)."</br>";		
			if ($pos > 0)
			{
				$texto1 = str_replace($findme, "", $texto1);
			}
			
		}
		if( parte_decimal(strval($aux)) <> 0) 
		{	
			//echo "parte decimal: ".parte_decimal(strval($aux))."</br>";

			$texto1.= " CON ";		
			$texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
			$pos = strpos($texto2, $findme);
			if ($pos === true){
				$texto2 = str_replace($findme, "", $texto2);
			}
		}
		/*if( parte_entera(strval($aux)) <> 0){	
			$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";
		}

		if( parte_decimal(strval($aux)) <> 0) 
		{	
			$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS CON ";		
			$texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
		}*/
		
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Comprobante</title>
    <link rel="stylesheet" href="style-pdf.css">
	<style>
	#page {
		width: 980px;
		
	}

	#left {
		width: 680px;
		float: left;
		background: #0099CC;
		min-height:150px;
	}
	#right {
		width: 300px;
		float: right;
		background: #00CC33;
		min-height:150px;
	}
	</style>
</head>
<body>

<div id="page_pdf">
	
	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/logo-baba.png" style="width: 150px; height: 80px;">
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Órden de pago'); ?></span>
					<!--p><?php echo $configuracion['razon_social']; ?></p>
					<p><?php echo $configuracion['direccion']; ?></p>
					<p>NIT: <?php echo $configuracion['nit']; ?></p>
					<p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
					<p>Email: <?php echo $configuracion['email']; ?></p-->
				</div>
				
			</td>
			<td class="info_factura">
				<!--div class="round">
					<span class="h3">Datos de la empresa</span>
					<p>C.U.I.T.: <strong>30-70737896-0 </strong></p>
					<p>D.G.R.: <strong>30-70737896-0 </strong></p>
					
				</div-->
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<!--span class="h3">Detalle</span-->
						<div>
							<p style="display: inline-block;">N°. de órden: 
								<strong><?php echo $numero_orden; ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								Fecha: <strong><?php echo fecha_min($fecha); ?></strong>
							</p>

							<p style="display: inline-block;  margin-left:190px;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>

						<p><strong><?php echo 'Solicitante: '.$solicitante.' (caja '.$numero_caja_solicitante.')'; ?></strong></p>
						<p><strong><?php echo 'Emitida por: '.$rol_caja_pago." (Caja $caja_pago)"; ?></strong></p>
						<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
						<?php 
								echo "<p><strong>Empresa: ".$empresa."</strong> / ".
										"<strong>Obra: ".$obra."</p>";
							
						?>
						
						<p><strong><?php echo "Cuenta: $cuenta";?></strong></p>
						<p><strong><?php echo "Recibe: $recibe";?></strong></p>
						<p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
						<p><strong><?php echo "Son: ".$cantidad." "."(".strtolower($texto1)." ".strtolower($texto2).")";?></strong></p>
						
						<?php
							if($band == 1)
							{
								$div = "";
								$div = "<div style='width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;'>
								
											<div style='display: inline-block;''>". $p . "</div>";
																				
											if($c > 4)
											{
												$div.= "<div style='display: inline-block; margin-left: 10px; height: 10.2%;'>". $k. "</div></div>";
											}												
											else $div.= "</div>";
								echo $div;
								
							}
						
						?>			
				</div>
			</td>

		</tr>
	</table>

	<br><br>
	<div class="nota-op">
			
			<div style="display: inline-block; width: 30%; height: 3%; ">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong style="margin: 0px auto; text-align: center; ">Confeccionó</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; text-align: center;">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong>Recibió (Firma y Aclaración)</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; margin-left: 10%;">
				<!--hr style="width: 100%; color: black; margin: 0px auto;"-->
				<strong style="float: right;">Autorizó</strong>
			</div>
	</div>
  
</div>

<?php 
	// $c --> indica si hay cheques o no
	// $c = 0 --> <br><br><br>
	// $c = 3 --> <br>
	// $c >= 4 --> sin <br>
	/*switch($c){
		case 0: echo "<br><br><br><br><br><br><br><br>"; // ok
		break;

		case 1: echo "<br><br><br>";
		break;

		case 2: echo "<br><br>";
		break;

		case 3: echo "<br>";
		break;
	}*/
	if($c == 0){
		echo "<br><br><br><br><br><br><br><br>";
	}
	else{
		if($c == 1){
			echo "<br><br><br><br><br><br>";
		}
		else{
			if($c == 2){
				echo "<br><br><br><br><br>";
			}
			else{
				if($c == 3){
					echo "<br><br><br><br>";
				}
				else{
					if($c >= 4){
						echo "<br><br><br>";
					}
				}
			}
		}
	}
	
?>

<!--................................espacio....................................................-->

<div id="page_pdf">
	
	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/logo-baba.png" style="width: 150px; height: 80px;">
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Órden de pago'); ?></span>
					<!--p><?php echo $configuracion['razon_social']; ?></p>
					<p><?php echo $configuracion['direccion']; ?></p>
					<p>NIT: <?php echo $configuracion['nit']; ?></p>
					<p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
					<p>Email: <?php echo $configuracion['email']; ?></p-->
				</div>
				
			</td>
			<td class="info_factura">
				<!--div class="round">
					<span class="h3">Datos de la empresa</span>
					<p>C.U.I.T.: <strong>30-70737896-0 </strong></p>
					<p>D.G.R.: <strong>30-70737896-0 </strong></p>
					
				</div-->
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<!--span class="h3">Detalle</span-->
						<div>
							<p style="display: inline-block;">N°. de órden: 
								<strong><?php echo $numero_orden; ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								Fecha: <strong><?php echo fecha_min($fecha); ?></strong>
							</p>

							<p style="display: inline-block;  margin-left:190px;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>

						<p><strong><?php echo 'Solicitante: '.$solicitante.' (caja '.$numero_caja_solicitante.')'; ?></strong></p>
						<p><strong><?php echo 'Emitida por: '.$rol_caja_pago." (Caja $caja_pago)"; ?></strong></p>
						<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
						<?php 
								echo "<p><strong>Empresa: ".$empresa."</strong> / ".
										"<strong>Obra: ".$obra."</p>";
							
						?>
						
						<p><strong><?php echo "Cuenta: $cuenta";?></strong></p>					
						<p><strong><?php echo "Recibe: $recibe";?></strong></p>
						<p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
						<p><strong><?php echo "Son: ".$cantidad." "."(".strtolower($texto1)." ".strtolower($texto2).")";?></strong></p>
						
						<?php
							if($band == 1)
							{
								$div = "";
								$div = "<div style='width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;'>
								
											<div style='display: inline-block;''>".$p."</div>";
																				
											if($c > 4)
											{
												$div.= "<div style='display: inline-block; margin-left: 10px; height: 10.2%;'>".$k."</div></div>";
											}												
											else $div.= "</div>";
								echo $div;
								
							}
							
						?>
						<!--div style="width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;">
								 
								<div style="display: inline-block;">
									<?php
										if($band == 1){
											
											echo $p;
											
										}
											
									?>
									
								</div>

								<div style="display: inline-block; margin-left: 10px; height: 10.2%; ">
									<?php
										if($band == 1){
											if($c > 4){
												echo $k;
											}
											
										}
											
									?>
								
								</div>

						</div-->				
				</div>
			</td>

		</tr>
	</table>

	<table id="factura_detalle">
			<thead>
				
			</thead>
			<tbody id="detalle_productos">

			</tbody>
			<tfoot id="detalle_totales">
		
			</tfoot>
	</table>
	<br><br>
	<div class="nota-op">
			
			<div style="display: inline-block; width: 30%; height: 3%; ">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong style="margin: 0px auto; text-align: center; ">Confeccionó</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; text-align: center;">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong>Recibió (Firma y Aclaración)</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; margin-left: 10%;">
				<!--hr style="width: 100%; color: black; margin: 0px auto;"-->
				<strong style="float: right;">Autorizó</strong>
			</div>
	</div>
  
</div>
</body>
</html>

