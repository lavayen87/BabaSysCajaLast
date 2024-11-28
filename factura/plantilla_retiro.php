
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
	$num_re = "";
	include('../conexion.php');
	//include('../funciones.php');
	if($_GET['num_re'])
	{
		$num_re = $_GET['num_re'];
		$query = "SELECT * from retiros
			  WHERE numero_retiro = '$num_re'  
	          order by numero_retiro desc limit 1";    
		$result = mysqli_query($connection, $query);
	}
	else
	{
		$query = "SELECT * from retiros
			  WHERE numero_caja = '$numero_caja'  
	          order by numero_retiro desc limit 1";    
		$result = mysqli_query($connection, $query);    
	}
	
	$datos = mysqli_fetch_array($result);

	$numero_retiro = $datos['numero_retiro'];
	$fecha = $datos['fecha_retiro']; 
	$numero_caja = $datos['numero_caja'];
	$persona = $datos['personal_habilitado'];
	$concepto = $datos['concepto'];
	$cuenta = $datos['cuenta'];
	$importe = $datos['importe'];
	$detalle = $datos['observaciones'];
	
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
					<img src="img/baba-img.png" >
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Retiro'); ?></span>
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
							<p style="display: inline-block;">N°. de retiro: 
								<strong><?php echo $numero_retiro; ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								Fecha: <strong><?php require '../funciones.php';echo fecha_min($fecha); ?></strong>
							</p>

							<p style="display: inline-block;  margin-left:210px;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>

						<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
						<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
						<p> Retira: <strong><?php echo "$persona";?></strong></p>
						<p> Concepto: <strong><?php echo "$concepto";?></strong></p>
						<p> Cuenta: <strong><?php echo "$cuenta";?></strong></p>						
						<p> Observaciones : <strong><?php echo ' '.$detalle; ?></strong></p>
						
						<p>Recibi(mos) la suma de pesos: 
							<strong>
								<?php 
									echo $cantidad." "."($texto1 $texto2)";
								?>
							</strong>
						</p>
										
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
				<strong style="margin: 0px auto; text-align: center; ">Emitió</strong>
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


<!--................................espacio....................................................-->

<div class="space" style="margin-top: 10%;">
	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/baba-img.png" >
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Retiro'); ?></span>
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
								<p style="display: inline-block;">N°. de retiro: 
									<strong><?php echo $numero_retiro; ?></strong> 
								</p>

								<p style="display: inline-block; margin-left:140px;">
									Fecha: <strong><?php echo fecha_min($fecha); ?></strong>
								</p>

								<p style="display: inline-block;  margin-left:210px;">
								Hora: <strong><?php echo $hora; ?></strong>
								</p>
							</div>
							
							<hr>

							<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
							<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
							<p> Retira: <strong><?php echo "$persona";?></strong></p>
							<p> Concepto: <strong><?php echo "$concepto";?></strong></p>
							<p> Cuenta: <strong><?php echo "$cuenta";?></strong></p>						
							<p> Observaciones : <strong><?php echo ' '.$detalle; ?></strong></p>
							
							<p>Recibi(mos) la suma de pesos: 
								<strong>
									<?php 
										echo $cantidad." "."($texto1 $texto2)";
									?>
								</strong>
							</p>
											
					</div>
				</td>

			</tr>
		</table>

		<br><br>
		<div class="nota-op">

				
				<div style="display: inline-block; width: 30%; height: 3%; ">	
					<!--hr style="width:100%; color: black; margin: 0px auto;"-->
					<strong style="margin: 0px auto; text-align: center; ">Emitió</strong>
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

