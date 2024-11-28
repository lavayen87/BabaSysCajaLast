<?php
	date_default_timezone_set("America/Argentina/Salta");
	session_start();
	include('../conexion.php');
	include('../funciones.php');
	if($_SESSION['active'])
	{
	    $micaja = $_SESSION['nombre_caja'];
	    $numero_caja = $_SESSION['numero_caja'];
	}
	//$num_tr = $_GET['num_tr']; // Dato enviado por post
	$hoy = date('Y-m-d');
	$query = "SELECT * from transferencias
			  WHERE numero_caja_origen = '$numero_caja' 
			  AND fecha = '$hoy'    
	          order by numero_tr desc limit 1";    
	$result = mysqli_query($connection, $query); 
	if($result->num_rows == 0)
	{
		header("Location: ../file_nueva_transferencia.php"); 
	}
	else
	{ 
		// Datos de la Transferencia
		$datos = mysqli_fetch_array($result);
		$numero_tr = $datos['numero_tr'];
		$fecha = $datos['fecha']; 
		$hora = date('G').':'.date('i').':'.date('s');
		$numero_caja_origen = $datos['numero_caja_origen'];
		$nombre_caja_origen = $datos['nombre_caja_origen'];
		$nombre_caja_destino = $datos['nombre_caja_destino'];
		$numero_caja_destino = $datos['numero_caja_destino'];
		$moneda = $datos['moneda'];
		$detalle = $datos['observaciones'];
		
		// Datos de cheques
		$qry = "SELECT fecha_vto,banco,importe,num_cheque
				FROM cheques_cartera 
				WHERE num_tr = $numero_tr
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
		$findme = "CERO";
		require "../conversor.php";

		if($datos['pesos'] > 0 )
		{
			$cantidad = '$'.number_format($datos['pesos'],2,',','.');
			//$cantidad = '$'.$datos['pesos'];
			$aux = $datos['pesos'];
			
			if( parte_entera(strval($aux)) <> 0){	
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
		else
		{
		    if($datos['dolares'] > 0)
		    {
		    	$cantidad = '$US '.$datos['dolares'];
		    	$aux = $datos['dolares'];	
		    	if( parte_entera(strval($aux)) <> 0)
		    	{	
					$texto1 = convertir(parte_entera(strval($aux))).' '."DOLARES";
				}
				//$texto1 = convertir(parte_entera(strval($aux))).' '." DOLARES";	
		    }	       
		    else 
		    	if($datos['euros'] > 0)
				{
		    		$cantidad = '€'.$datos['euros'];
					$aux = $datos['euros'];	
					$texto1 = convertir(parte_entera(strval($aux))).' '." EUROS";
		    	}
				else
				{
					$cantidad = '$'.number_format($datos['cheques'],2,',','.');
					//$cantidad = '$'.$datos['pesos'];
					$aux = $datos['cheques'];
				
					if( parte_entera(strval($aux)) <> 0){	
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
		}	
		mysqli_close($connection);
	}
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
					<span class="h2"><?php echo strtoupper('Transferencia'); ?></span>
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
						<div style="text-align: center;">
							<p>
								<label style="display: inline-block">
									N°. de transferencia: 
									<strong><?php echo $numero_tr; ?></strong>
							 	</label>
							 
							 
							 	<label style="display: inline-block; margin-left:140px;">
									Fecha: 
									<strong><?php echo fecha_min($fecha); ?></strong>
								</label>
							 
								<label style="display: inline-block; margin-left:210px;">
									Hora: 
									<strong><?php echo $hora; ?></strong>
								</label>
							</p>
						</div>
						
						<hr>

						<!--p>N°. de transferencia: <strong><?php echo ' 0000'.$numero_tr; ?></strong></p>
						<p>Fecha: <strong><?php echo $fecha; ?></strong></p>
						<p>Hora: <strong><?php echo $hora; ?></strong></p-->
						<!--p align="right">Hora: <strong><?php echo $hora; ?></strong></p-->
						 
						<p>Caja origen: <strong><?php echo $nombre_caja_origen." (caja $numero_caja_origen)";?></p>
						<p>Caja destino: <strong><?php echo $nombre_caja_destino." (caja $numero_caja_destino)"; ?></strong></p>
						<p><strong><?php echo ' Transferencia en '.$moneda; ?></strong></p> 
						<p>Detalle :<strong><?php echo ' '.$detalle; ?></strong></p>
						<p>Son :<strong><?php echo ' '.$cantidad." "."($texto1 $texto2)"; ?></strong></p>
						<div style="width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;">
								 
								<div style="display: inline-block;">
									<?php
										if($band == 1){
											
											echo $p;
											
										}
											
									?>
									
								</div>

								<div style="display: inline-block; margin-left: 10px; height: 9.6%;">
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
	<div class="nota-tr">	
			<!--hr style="width: 40%; color: black; margin: 0px auto;">
			<strong>Emitió</strong-->
			<div style="display: inline-block; width: 30%; height: 3%; ">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong style="margin: 0px auto; text-align: center; ">Confeccionó</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; text-align: center;">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong>Recibió (Firma y Aclaración)</strong>
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

		case 1: echo "<br><br>";
		break;

		case 2: echo "<br><br><br><br>";
		break;

		case 3: echo "<br>";
		break;
	}
	if($c >= 4){
		echo "<br><br><br><br>";
	}
	
?>

<!--br><br><br><br><br><br><br><br-->
<!--.................Espacio..............................-->

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
					<span class="h2"><?php echo strtoupper('Transferencia'); ?></span>
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

						<div style="text-align: center;">
							<p>
								<label style="display: inline-block">
									N°. de transferencia: 
									<strong><?php echo $numero_tr; ?></strong>
							 	</label>
							 
							 
							 	<label style="display: inline-block; margin-left:140px;">
									Fecha: 
									<strong><?php echo fecha_min($fecha); ?></strong>
								</label>
							 
								<label style="display: inline-block; margin-left:210px;">
									Hora: 
									<strong><?php echo $hora; ?></strong>
								</label>
							</p>
						</div>
						
						<hr>

						<!--p>N°. de transferencia: <strong><?php echo ' 0000'.$numero_tr; ?></strong></p>
						<p>Fecha: <strong><?php echo $fecha; ?></strong></p>
						<p>Hora: <strong><?php echo $hora; ?></strong></p-->
						<p>Caja origen: <strong><?php echo $nombre_caja_origen." (caja $numero_caja_origen)";?></p>
						<p>Caja destino: <strong><?php echo $nombre_caja_destino." (caja $numero_caja_destino)"; ?></strong></p>
						<p><strong><?php echo ' Transferencia en '.$moneda; ?></strong></p> 
						<p >Detalle :<strong><?php echo ' '.$detalle; ?></strong></p>
						<p>Son :<strong><?php echo ' '.$cantidad." "."($texto1 $texto2)"; ?></strong></p>
						<div style="width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;">
								 
						<div style="display: inline-block;">
									<?php
										if($band == 1){
											 
											echo $p;
											
										}
											
									?>
									
								</div>

								<div style="display: inline-block; margin-left: 10px; height: 25.4%;">
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
	<div class="nota-tr">	
			<div style="display: inline-block; width: 30%; height: 3%; ">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong style="margin: 0px auto; text-align: center; ">Confeccionó</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; text-align: center;">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong>Recibió (Firma y Aclaración)</strong>
			</div>
	</div>


</div>

</body>
</html>