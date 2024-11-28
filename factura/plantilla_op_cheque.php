
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
	
	include('../conexion.php');
	include('../funciones.php');

	//datos de la orden de pago con cheque.
	$query = "SELECT * from orden_pago 
			  WHERE numero_caja = '$numero_caja'  
	          order by numero_orden desc limit 1";    
	$result = mysqli_query($connection, $query);    
	$datos = mysqli_fetch_array($result);

	$fecha_actual = date('Y-m-d'); // fecha actual

	$numero_orden = $datos['numero_orden'];
	$fecha = $datos['fecha']; 
	$recibe = $datos['recibe'];
	$empresa= $datos['empresa'];
	$obra = $datos['obra'];
	$cuenta = $datos['cuenta'];
	$detalle = $datos['detalle'];
	$moneda = $datos['moneda'];
	$importe = $datos['importe'];
	
	//datos de los cheques usados para la orden de pago
	/*$query = "SELECT * from cheques_cartera
			  WHERE num_caja_origen = '$numero_caja'  
			  and fecha_entrega = '$fecha_actual'
			  and num_orden_pago= '$numero_orden'";   
	$result = mysqli_query($connection, $query);*/  

	// Datos de cheques
	$qry = "SELECT fecha_vto,banco,importe,num_cheque
			FROM cheques_cartera 
			WHERE num_orden_pago = $numero_orden";

	// Datos de cheques
	$qry = "SELECT fecha_vto,banco,importe,num_cheque
	FROM cheques_cartera 
	WHERE num_orden_pago = '$numero_orden'
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
	
	$hora = date('G').':'.date('i').':'.date('s');

	$aux = 0;
	$texto1 = '';
	$texto2 = '';
	$findme = "CERO";
	require "../conversor.php";

	if($datos['importe'] > 0)
	{
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
		
	}	 
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Comprobante</title>
    <link rel="stylesheet" href="style-pdf.css">
	
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
					<span class="h2"><?php echo strtoupper('Órden de pago con Cheque'); ?></span>
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

						<p> <strong><?php echo 'Emitida por: '.$rol." (caja $numero_caja)"; ?></strong></p>
						<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
						<p>
							<strong><?php echo "Empresa: ".$empresa. " - "." Obra: ".$obra;?></strong>
						</p>
						<!--p> Empresa: <strong><?php echo "$empresa";?></strong></p>
						<p> Obra: <strong><?php echo "$obra";?></strong></p-->
						<p><strong><?php echo "Cuenta: ".$cuenta;?></strong></p>
						<!--p> Recibe : <strong><?php echo 'Juan Perez'; ?></strong></p-->
						<p> <strong><?php echo "Recibe: ".$recibe; ?></strong></p>
						<p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
						 
						<p> 
							<strong>
								<?php 
									echo "Son: ".$cantidad." "."($texto1 $texto2)";
								?>
							</strong>
						</p>

						<div style="width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;">
								 
								<div style="display: inline-block;">
									<?php
										if($band == 1)
										{					
											echo $p;											
										}	
									?>
									
								</div>

								<div style="display: inline-block; margin-left: 10px;">
									<?php
										if($band == 1)
										{
											if($c > 4)
											{
												echo $k;
											}
											
										}						
									?>
								
								</div>

						</div>
										
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

<?php 
	// $c --> indica si hay cheques o no
	// $c = 0 --> <br><br><br>
	// $c = 3 --> <br>
	// $c >= 4 --> sin <br>
	switch($c){
		case 0: echo "<br><br><br><br><br><br><br><br>"; // ok
		break;

		case 1: echo "<br><br><br>";
		break;

		case 2: echo "<br><br>";
		break;

		case 3: echo "<br>";
		break;
	}
	if($c >= 4){
		echo "<br>";
	}
	
?>
<!--................................espacio....................................................-->

<div class="space" style="margin-top: 5%;">
	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/logo-baba.png" style="width: 150px; height: 80px;">
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Órden de pago con cheque'); ?></span>
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
	<div id="page_pdf">
		
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

							<p> <strong><?php echo 'Emitida por: '.$rol." (caja $numero_caja)"; ?></strong></p>
							<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
							<p>
								<strong><?php echo "Empresa: ".$empresa. " - "." Obra: ".$obra;?></strong>
							</p>
							<!--p> Empresa: <strong><?php echo "$empresa";?></strong></p>
							<p> Obra: <strong><?php echo "$obra";?></strong></p-->
							<p><strong><?php echo "Cuenta: ".$cuenta;?></strong></p>
							<!--p> Recibe : <strong><?php echo 'Juan Perez'; ?></strong></p-->
							<p> <strong><?php echo "Recibe: ".$recibe; ?></strong></p>
							<p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
							
							<p> 
								<strong>
									<?php 
										echo "Son: ".$cantidad." "."($texto1 $texto2)";
									?>
								</strong>
							</p>

							<div style="width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;">
								 
								<div style="display: inline-block;">
									<?php
										if($band == 1){
											
											echo $p;
											
										}
											
									?>
									
								</div>

								<div style="display: inline-block; margin-left: 10px; ">
									<?php
										if($band == 1){
											if($c > 4){
												echo $k;
											}
											
										}
											
									?>
								
								</div>

							</div>
											
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
</div>
</body>
</html>

