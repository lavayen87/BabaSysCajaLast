

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


	$numero_orden = $_GET["id"]; // id es el numero de solicitud 

	if(isset($_GET["caja_pago"]))
	{
		if($_GET["caja_pago"] == 3)
		{
			$rol = "Banco";
			$numero_caja = $_GET["caja_pago"]; 
		}
		
	}


	$saldo_anterior = 0.00;
	$pesos_hoy = 0.00;
	$dolares_hoy = 0;
	$euros_hoy = 0;
	$ultimo_cobro = 0.00;
	$total_gral = 0.00;
	$monto = 0.00;

	include('../conexion.php');
	include('../funciones.php');
	
	$query = "SELECT * from solicitud_orden_pago 
			  WHERE numero_orden = '$numero_orden' 
	          order by numero_orden desc limit 1";

	$qry = "SELECT t1.id_cheque, t1.banco, t1.num_cheque, t1.importe, t1.fecha_vto
			FROM cheques_cartera as t1 INNER JOIN ids_check_list as t2
			ON t1.id_cheque = t2.id_cheque";
	$res_ch = mysqli_query($connection, $qry);
	


	$result = mysqli_query($connection, $query);    
	$datos = mysqli_fetch_array($result);

	if($datos['estado'] == 'Realizada99') // estado realizada !
	{
		header("Location: ../file_listado_solicitudes.php"); 
	}
	else{
		
		$numero_orden = $datos['numero_orden'];
		$fecha = date('Y-m-d');
		$caja_solicitante = $datos['numero_caja'];
		$solicitante = $datos['solicitante'];
		$empresa= $datos['empresa'];
		$obra = $datos['obra'];
		$cuenta = $datos['cuenta'];
		$detalle = $datos['detalle'];
		$moneda = $datos['moneda'];
		$importe = $datos['importe'];// consigo el importe de la solicitud
		$recibe = $datos['recibe']; 
		$op = 0;
		

		// Datos de cheques
		$qry = "SELECT fecha_vto,banco,importe,num_cheque
				FROM cheques_cartera 
				WHERE num_solicitud = '$numero_orden'
				ORDER BY fecha_vto";

		$res_ch = mysqli_query($connection, $qry);

		$p = ""; 
		$k = "";
		$band = 0; // existencia de cheques
		$c = 0; // cantidad de cheques
		$j = 0; // contador para los primeros cheques
		$titulo = "";

		if($res_ch->num_rows > 0)
		{
			$c = $res_ch->num_rows; 
			$band = 1; 
			$titulo = "Órden de pago con cheque";
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
		else{
			$titulo = "Órden de pago";
		}
		/*-------*/
		
		if($moneda == 'pesos')
		{
			if($numero_caja == 3)
			{
				$qry = "SELECT pesos FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 1
					order by numero desc limit 1";
			}
			else
			{
				$qry = "SELECT pesos FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 1
					and fecha = '$fecha'
					order by numero desc limit 1";
			
			}

			$res = mysqli_query($connection, $qry);
			$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
			$pesos = $get_datos['pesos'];

			$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);

			if($datos['importe']<>[]){
				$ultimo_cobro = $datos['importe']; // consigo el ultimo cobro en caja cobranza
			}
					

			if($ultimo_cobro > 0.00)
			{
				if($pesos == [])
				{
					$pesos_a_restar = $ultimo_cobro - $importe;
				}
				else
				{
					$pesos_a_restar = $pesos - $importe;
				}
			}
			else
			{
				if($pesos == [])
				{
					$pesos_a_restar = 0 - $importe;
				}
				else
				{
					$pesos_a_restar = $pesos - $importe;
				}
			}

			$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,'$importe','$pesos_a_restar',0,0,0,1)";
			$insert_result = mysqli_query($connection, $insert);

			$op = 1;

		}
		else{
			
			if($numero_caja == 3)
			{
				$qry = "SELECT cheques FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 4
					order by numero desc limit 1";
			}
			else
			{
				$qry = "SELECT cheques FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 4
					and fecha = '$fecha'
					order by numero desc limit 1";
			}
			
			$res = mysqli_query($connection, $qry);
			$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
			$cheques = $get_datos['cheques'];

			if($cheques == [])
			{
				$cheques_a_restar = (-1)*$importe;
			}
			else
			{
				$cheques_a_restar = ($cheques - $importe);
			}

			$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,'$importe',0,0,0,'$cheques_a_restar',4)";
			$insert_result = mysqli_query($connection, $insert);

			$op = 4;

			$set_cheques = "UPDATE cheques_cartera 
					SET fecha_entrega = '$fecha',
						persona_pago = '$recibe',
						estado = 'Entregado',
						activo = 4
					WHERE num_solicitud = '$numero_orden'";
			$res_cheque = mysqli_query($connection, $set_cheques);
		}
		//////////////////////////////////////////

		/*$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,'$importe','$pesos_a_restar',0,0,0,1)";
		$insert_result = mysqli_query($connection, $insert);*/

		$qry = "SELECT numero FROM caja_gral
				WHERE numero_caja = '$numero_caja'
				AND operacion = '$op'
				AND fecha = '$fecha'
				order by numero desc limit 1";
		$res_qry = mysqli_query($connection, $qry);
		$get_datos = mysqli_fetch_array($res_qry);
		$num = $get_datos['numero'];

		$insert2 = "INSERT  INTO orden_pago VALUES ('$num','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra','$moneda','$recibe')";
		$result_insert2 = mysqli_query($connection, $insert2);

		$qry = "UPDATE solicitud_orden_pago  
				SET estado = 'Realizada' 
				WHERE numero_orden = '$numero_orden'";
		$res = mysqli_query($connection, $qry);

		$set_cheques = "UPDATE cheques_cartera 
					SET  num_orden_pago = '$num'
					WHERE num_solicitud = '$numero_orden'";
		$res_cheque = mysqli_query($connection, $set_cheques);

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
		$saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha);
		$saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha);
		$saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha);
		$saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha);
			
		// consigo total pesos, dolares, euros y cheques del dia
		$pesos_hoy = get_total(1,$numero_caja,$fecha);
		$dolares_hoy = get_total(2,$numero_caja,$fecha);
		$euros_hoy = get_total(3,$numero_caja,$fecha);
		$cheques_hoy = get_total(4,$numero_caja,$fecha);

		//cargo  totales generales

		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe'];

		if($ultimo_cobro<>[]){ 
			$monto = $ultimo_cobro;
		}

		if( ($pesos_hoy<>[]) && ($ultimo_cobro>=0) )
		{
			$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
		}
		else
			if( ($pesos_hoy==[]) && ($ultimo_cobro>=0) )
			{
				$total_gral_pesos = ($saldo_anterior + $ultimo_cobro);
			}
			else $total_gral_pesos = $saldo_anterior;

		$total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
		$total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
		$total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);
		// cargo los totales generales
		$qry = "SELECT * from caja_gral_temp
				where fecha = '$fecha'
				and numero_caja = '$numero_caja'
				and operacion = 1	
				order by numero desc limit 1";    
		$res = mysqli_query($connection, $qry);

		if($res->num_rows>0)
		{
			$set = "UPDATE caja_gral_temp
					SET pesos = '$total_gral_pesos',
					dolares = '$total_gral_dolares',
					euros = '$total_gral_euros',
					cheques = '$total_gral_cheques'
					WHERE numero_caja = '$numero_caja'
					and fecha = '$fecha'
					and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else
		{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

			$result_insert = mysqli_query($connection, $insert);
		}

		
		/*-------*/
		// Plantilla de vista

		$hora = date('G').':'.date('i').':'.date('s');

		$aux = 0;
		$texto1 = '';
		$texto2 = '';
		$findme = "CERO";
		require "../conversor.php";

		if($importe > 0)
		{
			$cantidad = $importe;
			$aux = $importe;
			
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
					<span class="h2"><?php echo strtoupper($titulo); ?></span>
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
								<strong><?php echo $num; ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								Fecha: <strong><?php echo fecha_min($fecha); ?></strong>
							</p>

							<p style="display: inline-block;  margin-left:190px;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>
	
							
							<p><strong><?php echo 'Solicitante: '.$solicitante." (caja $caja_solicitante)";; ?></strong></p>
							<p><strong><?php echo 'Emitida por: '.$rol." (caja $numero_caja)"; ?></strong></p>
							
							<?php 
								echo "<p><strong>Empresa: ".$empresa."</strong> / ".
										"<strong>Obra: ".$obra."</p>";
							
							?>	
							
							<p><strong><?php echo "Cuenta: $cuenta";?></strong></p>
							<p><strong><?php echo "Recibe: $recibe"; ?></strong></p-->
							<p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
						
							<p> 
								<strong>
									<?php 
										echo "Son: $".number_format($cantidad,2,',','.')." "."($texto1 $texto2)";
									?>
								</strong>
							</p>

							<?php
								if($band == 1)
								{
									
									$div = "";
									$div.= "<div style='width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;'>
									
												<div style='display: inline-block;'>".$p."</div>";
																					
												if($c > 4)
												{
													$div.= "<div style='display: inline-block; margin-left: 10px; height: 9.7%;'>".$k."</div></div>";
												}												
												else $div.= "</div>";
									echo $div;
									
								}
								
							
							?>
							<!--div style="width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;">
									
									<div style="display: inline-block;">
										<?php	
											echo $p;		
										?>
										
									</div>

									<div style="display: inline-block; margin-left: 10px; height: 10.2%; ">
										<?php											
											if($c > 4){
												echo $k;
											}												
										?>
									
									</div>

							</div-->		
										
				</div>
			</td>

		</tr>
	</table>

	<?php if($c >= 4) echo "<br>"; else echo "<br><br>";?>
	
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

<?php 
	// $c --> indica si hay cheques o no
	// $c = 0 --> <br><br><br>
	// $c = 3 --> <br>
	// $c >= 4 --> sin <br>

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
					<span class="h2"><?php echo strtoupper($titulo); ?></span>
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
								<strong><?php echo $num; ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								Fecha: <strong><?php echo fecha_min($fecha); ?></strong>
							</p>

							<p style="display: inline-block;  margin-left:190px;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>
	
							
							<p><strong><?php echo 'Solicitante: '.$solicitante." (caja $caja_solicitante)";; ?></strong></p>
							<p><strong><?php echo 'Emitida por: '.$rol." (caja $numero_caja)"; ?></strong></p>
							
							<?php 
								echo "<p><strong>Empresa: ".$empresa."</strong> / ".
										"<strong>Obra: ".$obra."</p>";
							
							?>	
							
							<p><strong><?php echo "Cuenta: $cuenta";?></strong></p>
							<p><strong><?php echo "Recibe: $recibe"; ?></strong></p-->
							<p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
						
							<p> 
								<strong>
									<?php 
										echo "Son: $".number_format($cantidad,2,',','.')." "."($texto1 $texto2)";
									?>
								</strong>
							</p>
							<?php
								if($band == 1)
								{
									$div = "";
									$div = "<div style='width:100%; padding-top: 10px; margin-top: 7px; margin-bottom: -15px;'>
									
												<div style='display: inline-block;''>".$p."</div>";
																					
												if($c > 4)
												{
													$div.= "<div style='display: inline-block; margin-left: 10px; height: 25%;'>".$k."</div></div>";
												}												
												else $div.= "</div>";
									echo $div;
									
								}
							
							?>		
										
				</div>
			</td>

		</tr>
	</table>


	<?php if($c >= 4) echo "<br>"; else echo "<br><br>";?>
	
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

</body>
</html>



