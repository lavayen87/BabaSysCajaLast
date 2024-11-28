
<?php
	date_default_timezone_set("America/Argentina/Salta");
	session_start();
	include('../conexion.php');
	include('../funciones.php');
	if($_SESSION['active'])
	{
	    $micaja = $_SESSION['nombre_caja'];
	    $numero_caja = $_SESSION['numero_caja'];
        $rol = $_SESSION['rol'];
	}

	$num_solic = $_GET['id']; // Dato enviado por post
    $caja_pago = $_GET['caja_pago'];

	if(!is_numeric($num_solic) || !is_numeric($caja_pago)){
		header("Location: ../file_emitir_orden.php");
	}
	else{
		if(is_numeric($num_solic) && $num_solic > 0)	
		{
			$c = 0;
			$monto = 0;
			$monto2 = 0;
			$fecha = date('Y-m-d');
			$hora = date('G').':'.date('i').':'.date('s');
			
			$query = "SELECT * from solicitud_orden_pago
					WHERE numero_orden = '$num_solic'";    
			$result = mysqli_query($connection, $query); 
			$datos=mysqli_fetch_array($result);

			if($datos['estado'] == 'Realizada') // estado realizada !
			{
				header("Location: ../file_emitir_orden.php"); 
			}
			else
			{ 
				// Datos de la solicitud  
				$numero_orden = $datos['numero_orden'];
				$caja_solicitante = $datos['numero_caja'];
				$solicitante = $datos['solicitante'];
				$empresa= $datos['empresa'];
				$obra = $datos['obra'];
				$cuenta = $datos['cuenta'];
				$detalle = $datos['detalle'];
				$moneda = $datos['moneda'];
				$importe = $datos['importe'];// consigo el importe de la solicitud
				$recibe = $datos['recibe']; 
				$cuenta = $datos['caja_pago'];
				$op = 0;

				switch ($moneda) {
					case 'pesos':
						$op = 1;
						break;
					
					case 'dolares':
						$op = 2;
						break;

					case 'euros':
						$op = 3;
						break;
				}

				// **** PASOS GENERALIZADOS ****
				$query = "SELECT $moneda 
					from caja_gral
					where numero_caja = '$caja_pago'
					AND operacion = $op
					and anulado = 0
					AND fecha = '$fecha'
					order by numero desc limit 1";
				
				$result = mysqli_query($connection, $query);	
				$datos = mysqli_fetch_array($result); // obtengo pesos,dolares o euros a transferir
				$moneda_a_restar = $datos[$moneda];

				// datos del usuario que emite la orden de pago (caja_pago)
				// $query = "SELECT pesos 
				// 	from caja_gral
				// 	where numero_caja = '$caja_pago'
				// 	AND operacion = 1
				// 	AND fecha = '$fecha'
				// 	order by numero desc limit 1";
				
				// $result = mysqli_query($connection, $query);	
				// $datos = mysqli_fetch_array($result); // obtengo pesos a transferir
				// $pesos = $datos['pesos'];

				////////////////////////////////////////////
				$qry = "SELECT  importe from cobranza
							WHERE fecha = '$fecha' 
							AND numero_caja = '$caja_pago'
							order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro = $datos['importe']; // consigo el ultimo cobro en caja cobranza
				
				// consigo ingreso por servicios
				$qry_serv = "SELECT  importe from ingresos_servicios
							WHERE fecha = '$fecha' 
							AND numero_caja = '$caja_pago'
							order by id limit 1";
				$res_serv = mysqli_query($connection, $qry_serv);
				$datos_serv = mysqli_fetch_array($res_serv);
				
				if($datos_serv<>[])
				{
					$ing_servicio = $datos_serv['importe'];
				}
				else $ing_servicio = 0;

				/*if($ultimo_cobro > 0.00)
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
						$pesos_a_restar =  (-1)*$importe;
					}
					else
					{
						$pesos_a_restar = $pesos - $importe;
					}
				}*/
				// codigo original
				// if($ultimo_cobro > 0.00)
				// {
				// 	if($pesos == [])
				// 	{
				// 		$pesos_a_restar = ($ultimo_cobro + $ing_servicio - $importe);
				// 	}
				// 	else
				// 	{
				// 		$pesos_a_restar = ($pesos - $importe);
				// 	}
				// }
				// else
				// {
				// 	if($ing_servicio > 0.00)
				// 	{
				// 		if($pesos == [])
				// 		{
				// 			$pesos_a_restar = ($ing_servicio - $importe);
				// 		}
				// 		else
				// 		{
				// 			$pesos_a_restar = ($pesos - $importe);
				// 		}
				// 	}
				// 	else{
				// 		if($pesos == [])
				// 		{
				// 			$pesos_a_restar = (-1)*$importe;
				// 		}
				// 		else
				// 		{
				// 			$pesos_a_restar = ($pesos - $importe);
				// 		}
				// 	}
				// }
				//**//

				//////////////////////////////////////////////

				/* codigo para que impacte la transferencia en caja_pago */
				$pesos_a_trasferir = $importe;
						
				$det = limitar_cadena("TR ".$detalle,27); 
				
				// 'Transferencia en $moneda'	

				switch ($op) {
					case 1: // pesos
						if($ultimo_cobro > 0.00)
						{
							if($moneda_a_restar == [])
							{
								$pesos_a_restar = ($ultimo_cobro + $ing_servicio - $importe);
							}
							else
							{
								$pesos_a_restar = ($moneda_a_restar - $importe);
							}
						}
						else
						{
							if($ing_servicio > 0.00)
							{
								if($moneda_a_restar == [])
								{
									$pesos_a_restar = ($ing_servicio - $importe);
								}
								else
								{
									$pesos_a_restar = ($moneda_a_restar - $importe);
								}
							}
							else{
								if($moneda_a_restar == [])
								{
									$pesos_a_restar = (-1)*$importe;
								}
								else
								{
									$pesos_a_restar = ($moneda_a_restar - $importe);
								}
							}
						}

						// agregado : max numero de operacion para asingar en caja_gral
						$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
						$qry_res = mysqli_query($connection,$qry_num);
						$qry_datos = mysqli_fetch_array($qry_res);
						$numero = $qry_datos['numero'];
						/**/

						$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
						('$numero','$caja_pago','$fecha','$fecha','$det',0,0,'$importe','$pesos_a_restar',0,0,0,1,0)";
						$result_inert_mc = mysqli_query($connection, $insert_mc);
						break;
					
					case 2:

						
						if($moneda_a_restar == [])
						{
							$dolares_a_restar =  (-1)*$importe;
						}
						else
						{
							$dolares_a_restar = ($moneda_a_restar - $importe);
						}
						
						// agregado : max numero de operacion para asingar en caja_gral
						$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
						$qry_res = mysqli_query($connection,$qry_num);
						$qry_datos = mysqli_fetch_array($qry_res);
						$numero = $qry_datos['numero'];
						/**/

						$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
						('$numero','$caja_pago','$fecha','$fecha','$det',0,0,'$importe',0,'$dolares_a_restar',0,0,2,0)";
						$result_inert_mc = mysqli_query($connection, $insert_mc);
						break;

					case 3:

						if($moneda_a_restar == [])
						{
							$euros_a_restar =  (-1)*$importe;
						}
						else
						{
							$euros_a_restar = ($moneda_a_restar - $importe);
						}

						// agregado : max numero de operacion para asingar en caja_gral
						$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
						$qry_res = mysqli_query($connection,$qry_num);
						$qry_datos = mysqli_fetch_array($qry_res);
						$numero = $qry_datos['numero'];
						/**/

						$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
						('$numero','$caja_pago','$fecha','$fecha','$det',0,0,'$importe',0,0,'$euros_a_restar',0,3,0)";
						$result_inert_mc = mysqli_query($connection, $insert_mc);
						break;
				}

				// codigo original
				// $insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
				// ('','$caja_pago','$fecha','$fecha','$det',0,0,'$importe','$pesos_a_restar',0,0,0,1)";
				// $result_inert_mc = mysqli_query($connection, $insert_mc);
				//**//

				/* consigo numero de operacion para agregar a la transferencia */
				$get_num = "SELECT numero FROM caja_gral 
						WHERE numero_caja = '$caja_pago'
						AND operacion = '$op'
						and anulado = 0
						AND fecha = '$fecha' 
						ORDER BY numero DESC LIMIT 1";
				$res_get_num = mysqli_query($connection, $get_num);
				$array = mysqli_fetch_array($res_get_num);
				$num = $array['numero'];

				//** fin operaciones caja_pago **//

				/*--- ENVIO DE TRANFERENCIA A CAJA DESTINO ---*/

				$qry = "SELECT $moneda from caja_gral	
						where numero_caja = '$caja_solicitante'
						AND operacion = '$op'
						AND fecha = '$fecha'
						order by numero desc limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$moneda_a_sumar = $datos[$moneda];

				// consigo cobranaza en caja destino
				$qry = "SELECT  importe from cobranza
							WHERE fecha = '$fecha' 
							AND numero_caja = '$caja_solicitante'
							order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro2 = $datos['importe'];
				
				// consigo ingreso por servicios en caja destino
				$qry_serv2 = "SELECT  importe from ingresos_servicios
							WHERE fecha = '$fecha' 
							AND numero_caja = '$caja_solicitante'
							order by id limit 1";
				$res_serv2 = mysqli_query($connection, $qry_serv2);
				$datos_serv2 = mysqli_fetch_array($res_serv2);
				
				if($datos_serv2<>[])
				{
					$ing_servicio2 = $datos_serv2['importe'];
				}
				else $ing_servicio2 = 0;


				/*if($ultimo_cobro2 > 0.00)
				{
					if($pesos2 == [])
					{
						$pesos_a_sumar = ($ultimo_cobro2 + $importe);
					}
					else
					{
						$pesos_a_sumar = ($pesos2 + $importe);
					}
				}
				else
				{
					if($pesos2 == [])
					{
						$pesos_a_sumar = (0 + $importe);
					}
					else
					{
						$pesos_a_sumar = ($pesos2 + $importe);
					}
				}*/
				

				switch($op){
					case 1:
						if($ultimo_cobro2 > 0.00)
						{
							if($moneda_a_sumar == [])
							{
								$pesos_a_sumar = ($ultimo_cobro2 + $ing_servicio2 + $importe);
							}
							else
							{
								$pesos_a_sumar = ($moneda_a_sumar + $importe);
							}
						}
						else
						{
							if($ing_servicio2 > 0.00)
							{
								if($moneda_a_sumar == [])
								{
									$pesos_a_sumar = ($ing_servicio2 + $importe);
								}
								else
								{
									$pesos_a_sumar = ($moneda_a_sumar + $importe);
								}
							}
							else{
								if($moneda_a_sumar == [])
								{
									$pesos_a_sumar = $importe;
								}
								else
								{
									$pesos_a_sumar = ($moneda_a_sumar + $importe);
								}
							}
						}

						// Cargo la transferencia en caja destino	

						$insert_caja_destino = "INSERT IGNORE INTO  caja_gral VALUES 
						('$num','$caja_solicitante','$fecha','$fecha','$det',0,'$importe',0,'$pesos_a_sumar',0,0,0,1,0)";
						//$result_insert_caja_destino = mysqli_query($connection, $insert_caja_destino);
						mysqli_query($connection, $insert_caja_destino);

						// Cargo la transferencia en tabla "transferencias"
						$transfer = "INSERT IGNORE INTO transferencias VALUES 
						('$num','$fecha','$fecha','$rol','$caja_pago','$recibe','$caja_solicitante','$moneda','$importe',0,0,0,'Recibido','$detalle')"; 
						//$result_transfer =  mysqli_query($connection, $transfer);
						mysqli_query($connection, $transfer);

						break;

					case 2:
						if($moneda_a_sumar == [])
						{
							$dolares_a_sumar =  $importe;
						}
						else
						{
							$dolares_a_sumar = ($moneda_a_sumar + $importe);
						}
					
						$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
						('$num','$caja_solicitante','$fecha','$fecha','$det',0,'$importe',0,0,'$dolares_a_sumar',0,0,2,0)";
						$result_inert_mc = mysqli_query($connection, $insert_mc);

						// Cargo la transferencia en tabla "transferencias"
						$transfer = "INSERT IGNORE INTO transferencias VALUES 
						('$num','$fecha','$fecha','$rol','$caja_pago','$recibe','$caja_solicitante','$moneda',0,'$importe',0,0,'Recibido','$detalle')"; 
						//$result_transfer =  mysqli_query($connection, $transfer);
						mysqli_query($connection, $transfer);

						break;

					case 3:
						if($moneda_a_sumar == [])
						{
							$euros_a_sumar =  $importe;
						}
						else
						{
							$euros_a_sumar = ($moneda_a_sumar + $importe);
						}
					
						$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
						('$num','$caja_solicitante','$fecha','$fecha','$det',0,'$importe',0,0,0,'$euros_a_sumar',0,3,0)";
						$result_inert_mc = mysqli_query($connection, $insert_mc);

						// Cargo la transferencia en tabla "transferencias"
						$transfer = "INSERT IGNORE INTO transferencias VALUES 
						('$num','$fecha','$fecha','$rol','$caja_pago','$recibe','$caja_solicitante','$moneda',0,0,'$importe',0,'Recibido','$detalle')"; 
						//$result_transfer =  mysqli_query($connection, $transfer);
						mysqli_query($connection, $transfer);

						break;
				}

				

				// cargamos totales generales en caja destino

				//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales

				$saldo_anterior=saldo_ant('pesos',$caja_solicitante,$fecha);
				$saldo_anterior_dolares=saldo_ant('dolares',$caja_solicitante,$fecha);
				$saldo_anterior_euros=saldo_ant('euros',$caja_solicitante,$fecha);
				$saldo_anterior_cheques=saldo_ant('cheques',$caja_solicitante,$fecha);

				//Consigo total del dia en pesos desde mi caja
						
				$pesos_hoy = get_total(1,$caja_solicitante,$fecha);

				//Consigo total del dia en dolares desde mi caja
						
				$dolares_hoy = get_total(2,$caja_solicitante,$fecha);

				//Consigo total del dia en euros desde mi caja
						
				$euros_hoy = get_total(3,$caja_solicitante,$fecha);

				//Consigo total del dia en cheques desde mi caja
						
				$cheques_hoy = get_total(4,$caja_solicitante,$fecha);

				//Consigo cobranza

				$qry = "SELECT  importe from cobranza
							WHERE fecha = '$fecha' 
							AND numero_caja = '$caja_solicitante'
							order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro = $datos['importe']; 
					
				if($ultimo_cobro<>[])
				{
					$monto = $ultimo_cobro;
				}
					
				// ultimo ingreso por servicios
				$ultimo_ingreso = 0;
				$monto_serv = 0;
				$qry2 = "SELECT  importe from ingresos_servicios
						 WHERE fecha = '$fecha' 
						 AND numero_caja = '$caja_solicitante'
						 order by id DESC limit 1";
				$res2 = mysqli_query($connection, $qry2);
				$datos_ingresos = mysqli_fetch_array($res2);
				$ultimo_ingreso = $datos_ingresos['importe'];

				if( $ultimo_ingreso<>[])
				{ 
					$monto_serv = $ultimo_ingreso;
				}

				if( ($pesos_hoy<>[]) && ($monto>=0) && ($monto_serv)>=0)
				{
					$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto + $monto_serv);
				}
				else
					if( ($pesos_hoy==[]) && ($monto>=0) && ($monto_serv)>=0)
					{
						$total_gral_pesos = ($saldo_anterior + $monto + $monto_serv);
					}
					else $total_gral_pesos = $saldo_anterior;
					
				$total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
				$total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
				$total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);

				// cargo los totales generales en caja destino
				$qry = "SELECT * from caja_gral_temp
									where operacion = 1 
									and numero_caja = '$caja_solicitante'
									and fecha = '$fecha'	
									order by numero desc limit 1";    
				$res = mysqli_query($connection, $qry);

				if($res->num_rows>0)
				{
					$set = "UPDATE caja_gral_temp
									SET pesos = '$total_gral_pesos',
									dolares = '$total_gral_dolares',
									euros = '$total_gral_euros',
									cheques = '$total_gral_cheques'
									WHERE numero_caja = '$caja_solicitante'
									and fecha = '$fecha'
									and operacion = 1";
					$res = mysqli_query($connection, $set);
				}
				else{
					$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
					('','$caja_solicitante','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

					$result_insert = mysqli_query($connection, $insert);
				}

				// cargo totales generales en mi caja

				//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales

				$saldo_anterior2=saldo_ant('pesos',$caja_pago,$fecha);
				$saldo_anterior_dolares2=saldo_ant('dolares',$caja_pago,$fecha);
				$saldo_anterior_euros2=saldo_ant('euros',$caja_pago,$fecha);
				$saldo_anterior_cheques2=saldo_ant('cheques',$caja_pago,$fecha);
				
				
				// consigo total pesos, dolares, euros y cheques del dia
				$pesos_hoy2 = get_total(1,$caja_pago,$fecha);
				$dolares_hoy2 = get_total(2,$caja_pago,$fecha);
				$euros_hoy2 = get_total(3,$caja_pago,$fecha);
				$cheques_hoy2 = get_total(4,$caja_pago,$fecha);
				
				// consigo cobranza
				$qry = "SELECT  importe from cobranza
						WHERE fecha = '$fecha' 
						AND numero_caja = '$caja_pago'
						order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro = $datos['importe']; 
				
				if($ultimo_cobro<>[]){
					$monto2 = $ultimo_cobro;
				}

				// ultimo ingreso por servicios
				$ultimo_ingreso2 = 0;
				$monto_serv2 = 0;
				$qry2 = "SELECT  importe from ingresos_servicios
						 WHERE fecha = '$fecha' 
						 AND numero_caja = '$caja_pago'
						 order by id DESC limit 1";
				$res2 = mysqli_query($connection, $qry2);
				$datos_ingresos2 = mysqli_fetch_array($res2);
				$ultimo_ingreso2 = $datos_ingresos2['importe'];

				if( $ultimo_ingreso2<>[])
				{ 
					$monto_serv2 = $ultimo_ingreso2;
				}

				if( ($pesos_hoy2<>[]) && ($monto2>=0) && ($monto_serv2)>=0)
				{
					$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2 + $monto_serv2);
				}
				else
					if( ($pesos_hoy2==[]) && ($monto2>=0) && ($monto_serv2)>=0)
					{
						$total_gral_pesos2 = ($saldo_anterior2 + $monto2 + $monto_serv2);
					}
					else $total_gral_pesos2 = $saldo_anterior2;

				$total_gral_dolares2 = ($saldo_anterior_dolares2 + $dolares_hoy2);
				$total_gral_euros2 = ($saldo_anterior_euros2 + $euros_hoy2);
				$total_gral_cheques2 = ($saldo_anterior_cheques2 + $cheques_hoy2);
				
				$qry = "SELECT * from caja_gral_temp
						where operacion = 1 
						and numero_caja = '$caja_pago'
						and fecha = '$fecha'	
						order by numero desc limit 1";    
				$res = mysqli_query($connection, $qry);

				if($res->num_rows>0)
				{
					$set = "UPDATE caja_gral_temp
							SET pesos = '$total_gral_pesos2',
							dolares = '$total_gral_dolares2',
							euros = '$total_gral_euros2',
							cheques = '$total_gral_cheques2'
							WHERE numero_caja = '$caja_pago'
							and fecha = '$fecha'
							and operacion = 1";
					$res = mysqli_query($connection, $set);
				}
				else{
					$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
					('','$caja_pago','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

					$result_insert = mysqli_query($connection, $insert);
				}


				$update = "UPDATE solicitud_orden_pago SET estado = 'Realizada'
						WHERE numero_orden = '$num_solic'";
				mysqli_query($connection, $update);

				// texto del importe
				$aux = 0;
				$texto1 = '';
				$texto2 = '';
				$findme = "CERO";
				$simb = '';

				switch ($moneda) {
					case 'pesos':
						$simb = '$';
						break;
					
					case 'dolares':
						$simb = '$US';
						break;

					case 'euros':
						$simb = '€';
						break;
				}
				require "../conversor.php";

				//$textoprecio = TextoPrecio(number_format($importe,2,',','.'));

				if($importe > 0)
				{
					$cantidad = $importe;
					$aux = $importe;//number_format($importe,2,',','.');
					
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
					/*if( parte_entera(strval($aux)) <> 0){	
						$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";
					}

					if( parte_decimal(strval($aux)) <> 0) 
					{	
						$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS CON ";		
						$texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
					}*/
					
				}		 	
				// else
				// {
				// 	if($datos['dolares'] > 0)
				// 	{
				// 		$cantidad = '$US '.$datos['dolares'];
				// 		$aux = $datos['dolares'];	
				// 		if( parte_entera(strval($aux)) <> 0)
				// 		{	
				// 			$texto1 = convertir(parte_entera(strval($aux))).' '."DOLARES";
				// 		}
				// 		//$texto1 = convertir(parte_entera(strval($aux))).' '." DOLARES";	
				// 	}	       
				// 	else 
				// 		if($datos['euros'] > 0)
				// 		{
				// 			$cantidad = '€'.$datos['euros'];
				// 			$aux = $datos['euros'];	
				// 			$texto1 = convertir(parte_entera(strval($aux))).' '." EUROS";
				// 		}
				// 		else
				// 		{
				// 			$cantidad = '$'.number_format($datos['cheques'],2,',','.');
				// 			//$cantidad = '$'.$datos['pesos'];
				// 			$aux = $datos['cheques'];
						
				// 			if( parte_entera(strval($aux)) <> 0){	
				// 				$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
				// 				$pos = strpos($texto1, $findme)."</br>";		
				// 				if ($pos > 0)
				// 				{
				// 					$texto1 = str_replace($findme, "", $texto1);
				// 				}
				// 			}

				// 			if( parte_decimal(strval($aux)) <> 0) 
				// 			{	
				// 				$texto1.= " CON ";		
				// 				$texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
				// 				$pos = strpos($texto2, $findme);
				// 				if ($pos === true){
				// 					$texto2 = str_replace($findme, "", $texto2);
				// 				}
				// 			}
				// 		}
				// }	
				mysqli_close($connection);
			}
		}
		else{
			header("Location: ../file_emitir_orden.php");
		}
	} 
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Comprobante</title>
<style>
    
    @page { 
        margin-left: 4px;
        margin-right: 4px; 
        margin-top: 2px; 
        box-sizing: border-box;
    } 
    p, label{
        font-family: 'BrixSansRegular';
        font-size: 11pt;
        line-height:6px;
    }
    #page {
        width: 100%; 
    }
    #header {
        height: 85px;
        background:white;
        
    }
    #left {
        width: 44%;
        float: left;
         
    }

    #right {
        width: 55%;
        float: right;
        
    }
    #content-datos {
        padding: 4px;
        clear: both;
        border-radius: 10px;
        border: 1px solid #049776;
    }
    #titulo{
        display: inline-block; 
        height: 45px; 
        width: 35%;
        padding-top: 35px;
        margin-left: 30%;
        text-align: center;
        
    }
    #content-cheques{
        width: 100%; 
        /* height: 90px; */
        overflow: hidden; 
        border: 1px solid blue;
        align-items: flex-start;
    }
    #content-firma{
        width: 100%; 
        height: 30px;
        padding-top: 50px;
        padding-left: 4px;
        overflow: hidden; 
        
    }
    
</style>
<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>

<body>
    <div id="page">
        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong>TRANSFERENCIA</strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;"><?php echo "N° de transferencia: ".$num; ?></strong>
                    <strong style="display: inline-block; margin-left: 35%;"><?php echo "Fecha: ".fecha_min($fecha)?></strong>
                    <strong style="float: right;"><?php echo "Hora: ".$hora; ?></strong>
                    </p>
                </div>
                <hr>
                <p><strong><?php echo "Caja orígen: ".$rol." (caja $caja_pago)";?></p>
				<p><strong><?php echo "Caja destino: ".$recibe." (caja $caja_solicitante)"; ?></strong></p> 
                <p><strong><?php echo "Transferencia en $moneda"; ?></strong></p>
                <p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
                

                <?php 
                    $space = 0; // variable para texto largo
                    //if( strlen($texto1." ".$texto2) > 100 )
                    if( strlen($texto1." ".$texto2) > 100)
                    {            
                        echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                        // echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.$importe." "."(".strtolower($textoprecio).")"."</strong></p>";
                        $space = 1;
                    }
                    else
                    echo "<p style='word-wrap: break-word; line-height: 11.5px><strong>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong></p>";
                    	// echo "<p><strong>Son: ".$sim.$importe." "."(".strtoupper($textoprecio).")"."</strong></p>";
                ?>
                
                
                
            </div>
            
            <div id="content-firma">
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <!--strong style="float: right;">Autorizó</strong-->
                </p>
            </div>
        </div>
        <!-------------------------- Espacio ------------------------------>
        <!--div style="height: 38px;"></div-->
        <!--div style="height: 35px; border:1px solid red;"> <br><br> </div-->
        
        <?php
            if($c >=1 && $c <4)
            {
                switch($c)
                {
                    case 1: echo "<br><br><br><br><br><br><br><br><br><br> ";
                    break;
                    case 2: echo "<br><br><br><br><br><br>";
                    break;
                    case 3: echo "<br><br><br>";
                    break;
                }
                
            }
            else{
				if($c >= 4)
                {
                    if($space == 1)
                    {
                        echo "<br><br><br><br>";
                    }
                    else
                        echo "<br><br><br><br><br>";
                }
                	
				else echo "<br><br><br><br><br><br><br><br><br><br><br><br>";
            }
        ?>

        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong>TRANSFERENCIA</strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;"><?php echo "N° de transferencia: ".$num; ?></strong>
                    <strong style="display: inline-block; margin-left: 35%;"><?php echo "Fecha: ".fecha_min($fecha)?></strong>
                    <strong style="float: right;"><?php echo "Hora: ".$hora; ?></strong>
                    </p>
                </div>
                <hr>
                <p><strong><?php echo "Caja orígen: ".$rol." (caja $caja_pago)";?></p>
				<p><strong><?php echo "Caja destino: ".$recibe." (caja $caja_solicitante)"; ?></strong></p> 
                <p><strong><?php echo "Transferencia en $moneda"; ?></strong></p>
                <p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
                <?php 
                    $space = 0; // variable para texto largo
                    //if( strlen($texto1." ".$texto2) > 100 )
                    if( strlen($texto1." ".$texto2) > 100)
                    {            
                        echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                        // echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.$importe." "."(".strtolower($textoprecio).")"."</strong></p>";
                        $space = 1;
                    }
                    else
                    echo "<p style='word-wrap: break-word; line-height: 11.5px><strong>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong></p>";
                    	// echo "<p><strong>Son: ".$sim.$importe." "."(".strtoupper($textoprecio).")"."</strong></p>";
                ?>
                
                
                
            </div>
            
            <div id="content-firma">
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <!--strong style="float: right;">Autorizó</strong-->
                </p>
            </div>
        </div>
        
    </div>

    
</body>
</html>
