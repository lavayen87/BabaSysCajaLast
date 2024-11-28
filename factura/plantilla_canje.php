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
	require "../conversor.php";
	$aux = 0;
	$texto1 = '';
	$texto2 = '';
	$texto3 = '';
	$texto4 = '';
	$findme = "CERO";
	
	$fecha = date('Y-m-d'); 

	// Consigo el ingreso
	$query = "SELECT * from caja_gral 
			where numero = (SELECT (Max(numero) - 1) FROM `caja_gral` 
							where numero_caja = '$numero_caja' 
							and fecha = '$fecha')";    
	$result = mysqli_query($connection, $query);    
	$ing = mysqli_fetch_assoc($result);

	if($ing['operacion'] == 1)
	{
		$ingreso = '$'.number_format($ing['ingreso'],2,',','.');
		$aux = $ing['ingreso'];
			
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
			if ($pos === true)
			{
				$texto2 = str_replace($findme, "", $texto2);
			}
		}
	}
	else
	{
		if($ing['operacion'] == 2)
		{
			$ingreso = 'US'.round($ing['ingreso']);
			$aux = $ing['ingreso'];	
			if( parte_entera(strval($aux)) <> 0)
			{	
				$texto1 = convertir(parte_entera(strval($aux))).' '."DOLARES";
				$pos = strpos($texto1, $findme)."</br>";
				if($pos > 0)
				{
					$texto1 = str_replace($findme, "", $texto1);
				}
			}
		}
		else
		{
			$ingreso = '€'.round($ing['ingreso']);
			$aux = $ing['ingreso'];	
			$texto1 = convertir(parte_entera(strval($aux))).' '." EUROS";
			$pos = strpos($texto1, $findme)."</br>";
			if($pos > 0)
			{
				$texto1 = str_replace($findme, "", $texto1);
			}

		} 
	}
	/*------------------------------------------------------*/

	// Consigo el egreso
	$query = "SELECT * from caja_gral 
			where numero = (SELECT Max(numero) FROM `caja_gral` 
							where numero_caja = '$numero_caja' 
							and fecha = '$fecha')";    
	$result = mysqli_query($connection, $query);    
	$eg = mysqli_fetch_assoc($result);

	if($eg['operacion'] == 1)
	{
		$egreso = '$'.number_format($eg['egreso'],2,',','.');
			
		$aux = $eg['egreso'];
			
		if( parte_entera(strval($aux)) <> 0)
		{	
			$texto3 = convertir(parte_entera(strval($aux))).' '."PESOS";				
			$pos = strpos($texto3, $findme)."</br>";		
			if ($pos > 0)
			{
				$texto3 = str_replace($findme, "", $texto3);
			}
				
		}
		if( parte_decimal(strval($aux)) <> 0) 
		{	

			$texto3.= " CON ";		
			$texto4 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
			$pos = strpos($texto4, $findme);
			if ($pos === true)
			{
				$texto4 = str_replace($findme, "", $texto4);
			}
		}
	}
	else
	{
		if($eg['operacion'] == 2)
		{
			$egreso = 'US'.round($eg['egreso']);
			$aux = $eg['egreso'];	
			if( parte_entera(strval($aux)) <> 0)
			{	
				$texto3 = convertir(parte_entera(strval($aux))).' '."DOLARES";
				$pos = strpos($texto3, $findme)."</br>";
				if($pos > 0)
				{
					$texto3 = str_replace($findme, "", $texto3);
				}
			}
		}
		else
		{
			$egreso = '€'.round($eg['egreso']); 
			$aux = $eg['egreso'];	
			$texto3 = convertir(parte_entera(strval($aux))).' '." EUROS";
			$pos = strpos($texto3, $findme)."</br>";
			if($pos > 0)
			{
				$texto3 = str_replace($findme, "", $texto3);
			}

		}
	}

	$hoy = date('Y-m-d');
	$fecha = $eg['fecha']; 
	$caja = $eg['numero_caja'];
	$detalle = $eg['detalle'];
	$hora = date('G').':'.date('i').':'.date('s');
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
					<span class="h2"><?php echo strtoupper('Canje'); ?></span>
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
							<p style="float: left;">Fecha: 
								<strong><?php echo fecha_min($hoy); ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								
							</p>

							<p style="display: inline-block;  float:right;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>

						<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
						<!--p> Cange Nº: <strong><?php echo 1;?></strong></p-->
						<p> Ingreso: 
							<strong>
								<?php echo "$ingreso"." "."($texto1 $texto2)";?>
							</strong>
						</p>
						
						<p> Egreso: 
							<strong>
								<?php echo "$egreso"." "."($texto3 $texto4)";?>
							</strong>
						</p>
											
						<p> Detalle : <strong><?php echo ' '.$detalle; ?></strong></p>
						 
								
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

	<br><br><br><br><br><br><br><br><br><br>
	<!---------------------------------------------------->

	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/baba-img.png" >
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Canje'); ?></span>
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
							<p style="float: left;">Fecha: 
								<strong><?php echo fecha_min($hoy); ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								
							</p>

							<p style="display: inline-block;  float:right;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>

						<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
						<!--p> Cange Nº: <strong><?php echo 1;?></strong></p-->
						<p> Ingreso: 
							<strong>
								<?php echo "$ingreso"." "."($texto1 $texto2)";?>
							</strong>
						</p>
						
						<p> Egreso: 
							<strong>
								<?php echo "$egreso"." "."($texto3 $texto4)";?>
							</strong>
						</p>
											
						<p> Detalle : <strong><?php echo ' '.$detalle; ?></strong></p>
						 
								
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


