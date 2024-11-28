
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
	
	$query = "SELECT * from orden_pago 
			  WHERE numero_caja = '$numero_caja'  
	          order by numero_orden desc limit 1";    
	$result = mysqli_query($connection, $query);    
	$datos = mysqli_fetch_array($result);

	$numero_orden = $datos['numero_orden'];
	$fecha = $datos['fecha']; 
	$emisor = $datos['numero_caja'];
	$empresa= $datos['empresa'];
	$obra = $datos['obra'];
	$cuenta = $datos['cuenta'];
	$detalle = $datos['detalle'];
	$importe = $datos['importe'];
	$moneda = $datos['moneda'];
	$recibe = $datos['recibe'];
	$hora = date('G').':'.date('i').':'.date('s');
	$sim = '';

	switch ($moneda) {
		case 'pesos':
			$sim = '$';
			break;		
		case 'dolares':
			$sim = '$US';
			break;
		case 'euros':
			$sim = '€';
			break;
	}

	$aux = 0;
	$texto1 = '';
	$texto2 = '';
	$findme = "CERO";
	require "../conversor.php";

	if($importe > 0)
	{
		$cantidad = $importe;
		$aux = $importe; //number_format($importe,2,',','.');
		
		if( parte_entera(strval($aux)) <> 0)
		{	
			$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
			$pos = strpos($texto1, $findme);		
			if ($pos > 0)
			{
				$texto1 = str_replace($findme, "", $texto1);
			}
			
		}
		
		$texto1.= " CON ";		
		$texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
		$pos = strpos($texto2, $findme);
		if ($pos === true)
		{
			$texto2 = str_replace($findme, "", $texto2);
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

						<p> Emitida por: <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
							<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
						<p>
							<label align="left">
								Empresa: 
								<strong><?php echo "$empresa";?></strong>
							</label>
							<label style="margin-left: 10px; margin-right: 10px;"> / </label>
							<label>
								Obra: 
								<strong><?php echo "$obra";?></strong>
							</label>
						</p>
						<!--p> Empresa: <strong><?php echo "$empresa";?></strong></p>
						<p> Obra: <strong><?php echo "$obra";?></strong></p-->
						<p> Cuenta: <strong><?php echo "$cuenta";?></strong></p>
						<!--p> Recibe : <strong><?php echo 'Juan Perez'; ?></strong></p-->
					
						<p> Detalle: <strong><?php echo ' '.$detalle; ?></strong></p>
						<p> Recibe: <strong><?php echo ' '.$recibe; ?></strong></p>
						
						<p>Son: 
							<strong>
								<?php 

								if( strlen($texto1." ".$texto2) > 100)
								{            
									echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$sim.number_format($aux,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong>";
								}
								else	
									echo "<strong style='word-wrap: break-word; line-height: 11.5px'>".$sim.number_format($aux,2,',','.')." "."($texto1 $texto2)"."</strong>";
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

<br><br><br>
<!--................................espacio....................................................-->

<div class="space" style="margin-top: 5%;">
	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/logo-baba.png" style="width: 150px; height: 80px; margin-left: 20px;">
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

							<p> Emitida por: <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
							<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
							<p>
								<label align="left">
									Empresa: 
									<strong><?php echo "$empresa";?></strong>
								</label>
								<label style="margin-left: 10px; margin-right: 10px;"> / </label>
								<label>
									Obra: 
									<strong><?php echo "$obra";?></strong>
								</label>
							</p>
							<!--p> Empresa: <strong><?php echo "$empresa";?></strong></p>
							<p> Obra: <strong><?php echo "$obra";?></strong></p-->
							<p> Cuenta: <strong><?php echo "$cuenta";?></strong></p>
							<p> Recibe: <strong><?php echo ' '.$recibe; ?></strong></p>
						
							<p> Detalle: <strong><?php echo ' '.$detalle; ?></strong></p>
							
							<p>Son: 
								<strong>
								<?php 

									if( strlen($texto1." ".$texto2) > 100)
									{            
										echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$sim.number_format($aux,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong>";
									}
									else	
										echo "<strong style='word-wrap: break-word; line-height: 11.5px'>".$sim.number_format($aux,2,',','.')." "."($texto1 $texto2)"."</strong>";
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

