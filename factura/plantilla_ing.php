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

if($_GET['num_in'])
{
	$numero = $_GET['num_in'];
}

	include('../conexion.php');
	include('../funciones.php');

	$query = "SELECT * from caja_gral 
			  WHERE numero = '$numero'  
	          and ingreso > 0.00
	          order by numero desc limit 1";    
	$result = mysqli_query($connection, $query);    
	$datos = mysqli_fetch_array($result);

	//$numero = $datos['numero'];
	$hoy = date('Y-m-d');
	$fecha = $datos['fecha']; 
	$caja = $datos['numero_caja'];
	$detalle = $datos['detalle'];
	
	
	$hora = date('G').':'.date('i').':'.date('s');

	$aux = 0;
	$texto1 = '';
	$texto2 = '';
	$findme = "CERO";
	require "../conversor.php";

	if($datos['operacion'] == 1 || $datos['operacion'] == 4)
	{
		$cantidad = '$'.number_format($datos['ingreso'],2,',','.');
		$aux = $datos['ingreso'];
		
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
			
	}
	else{
		if($datos['operacion'] == 2)
		{
		    $cantidad = '$US '.$datos['ingreso'];
		    $aux = $datos['ingreso'];	
		    if( parte_entera(strval($aux)) <> 0)
		    {	
				$texto1 = convertir(parte_entera(strval($aux))).' '."DOLARES";
				$pos = strpos($texto1, $findme)."</br>";
				if($pos > 0){
					$texto1 = str_replace($findme, "", $texto1);
				}
			}
				
		}	       
		else 
		{
		    $cantidad = '€'.$datos['ingreso'];
			$aux = $datos['ingreso'];	
			$texto1 = convertir(parte_entera(strval($aux))).' '." EUROS";
			$pos = strpos($texto1, $findme)."</br>";
			if($pos > 0){
				$texto1 = str_replace($findme, "", $texto1);
			}
		}
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
					<span class="h2"><?php echo strtoupper('Ingreso'); ?></span>
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
							<p>
							<label style="display: inline-block">
								Nº de ingreso: 
								<strong><?php echo "$numero";?></strong> 
							 </label>

							<label style="display: inline-block; margin-left:210px;"> 
								Fecha:
								<strong><?php echo fecha_min($hoy); ?></strong> 
							</label>


							<label style="display: inline-block; margin-left:230px;">
									Hora: 
									<strong><?php echo $hora; ?></strong>
							</label>
							</p>
						</div>
						
						<hr>

						<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
						<p> fecha de ingreso: <strong><?php echo fecha_min($fecha);?></strong></p>
						<p> Ingreso en Nº de caja: <strong><?php echo "$caja";?></strong></p>
						<p> Detalle : <strong><?php echo ' '.$detalle; ?></strong></p>
						 
						<p>Cantidad: 
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

	<!------------------------------------------------------------------>
	<br><br><br><br><br><br><br><br><br> 

	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/baba-img.png" >
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Ingreso'); ?></span>
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
							<p>
							<label style="display: inline-block">
								Nº de ingreso: 
								<strong><?php echo "$numero";?></strong> 
							 </label>

							<label style="display: inline-block; margin-left:210px;"> 
								Fecha:
								<strong><?php echo fecha_min($hoy); ?></strong> 
							</label>


							<label style="display: inline-block; margin-left:230px;">
									Hora: 
									<strong><?php echo $hora; ?></strong>
							</label>
							</p>
						</div>
						
						<hr>

						<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
						<p> fecha de ingreso: <strong><?php echo fecha_min($fecha);?></strong></p>
						<p> Ingreso en Nº de caja: <strong><?php echo "$caja";?></strong></p>
											
						<p> Detalle : <strong><?php echo ' '.$detalle; ?></strong></p>
						 
						<p>Cantidad: 
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


