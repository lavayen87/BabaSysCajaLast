
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
		if($_GET["caja_pago"] == 3) // en caso de q la caja sea el banco (11)
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
	$ing_servicio = 0;
	$total_gral = 0.00;
	$total_gral_pesos = 0.00;
	$total_gral_dolares = 0.00;
	$total_gral_euros = 0.00;
	$total_gral_cheques = 0.00;
	$monto = 0.00;

	$saldo_anterior_dolares = 0;
	$saldo_anterior_euros = 0;
	$saldo_anterior_cheques = 0;
	$monto_serv = 0;

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

	if($datos['estado'] == 'Realizada') // estado realizada !
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
		$confirm_datos_solic = "";
		$op = 0;
		
		//datos de la orden realizada por solicitud (solicitante,caja del solicitante y caja de pago)
		$qry_datos ="SELECT t1.solicitante, t1.numero_caja, t1.caja_pago ,t2.num_orden
		FROM solicitud_orden_pago as t1 inner join ids_check_list as t2
		on t1.numero_orden = t2.num_orden
		WHERE t2.num_orden = '$numero_orden'";
		$res_datos = mysqli_query($connection,$qry_datos);

		if($res_datos->num_rows > 0)
		{
			$datos_solicitud = mysqli_fetch_array($res_datos);

			$solicitante = $datos_solicitud['solicitante'];
			$numero_caja_solicitante = $datos_solicitud['numero_caja'];
			$caja_pago = $datos_solicitud['caja_pago'];
			$num_solicitud = $datos_solicitud['caja_pago'];
			
			// Rol de caja de pago
			$qry = "SELECT rol
			FROM usuarios
			WHERE numero_caja = '$caja_pago'";

			$res_rol = mysqli_query($connection, $qry);
			$datos_rol = mysqli_fetch_assoc($res_rol);
			$rol_caja_pago = $datos_rol['rol'];

			$confirm_datos_solic = true;
		}

		// Datos de cheques
		$qry = "SELECT fecha_vto,banco,importe,num_cheque
				FROM cheques_cartera 
				WHERE num_solicitud = '$numero_orden'
				ORDER BY fecha_vto";

		$res_ch = mysqli_query($connection, $qry);

		$p = "<table>"; 
		$k = "<table>";
		$band = 0; // existencia de cheques
		$c = 0; // cantidad de cheques
		$j = 0; // contador para los primeros cheques
		$titulo = "";

		if($res_ch->num_rows > 0)
		{
			$c = $res_ch->num_rows; 
			$band = 1; 
			$titulo = "ÓRDEN DE PAGO CON CHEQUES";
			if($c <= 4)
            {	
                        
                while($datos_cheq = mysqli_fetch_array($res_ch))
                {
                    $p.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($datos_cheq['fecha_vto'])."</strong></td>
                            <td style='; text-align: left;'><strong>".$datos_cheq['banco']." "."</strong></td>
                            <td style='text-align: right;'><strong>"."$".number_format($datos_cheq['importe'],2,',','.')."</strong></td>
                            <td style='width: 70px; text-align: right;'><strong>".$datos_cheq['num_cheque']."</strong></td>
                        </tr>";
                    //$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
                    
                }
                $p.="</table>";
            }
            else{
                
                while($datos_cheq = mysqli_fetch_array($res_ch))
                {	
                    
                    if($j < 4 )
                    {
                        //$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
                        $p.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($datos_cheq['fecha_vto'])."</strong></td>
                            <td style='; text-align: left;'><strong>".$datos_cheq['banco']." "."</strong></td>
                            <td style='text-align: right;'><strong>"."$".number_format($datos_cheq['importe'],2,',','.')."</strong></td>
                            <td style='width: 70px; text-align: right;'><strong>".$datos_cheq['num_cheque']."</strong></td>
                        </tr>";
                        $j++;
                    }
                    else{
                        //$k.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
                        $k.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($datos_cheq['fecha_vto'])."</strong></td>
                            <td style='; text-align: left;'><strong>".$datos_cheq['banco']." "."</strong></td>
                            <td style='text-align: right;'><strong>"."$".number_format($datos_cheq['importe'],2,',','.')."</strong></td>
                            <td style='width: 70px; text-align: right;'><strong>".$datos_cheq['num_cheque']."</strong></td>
                        </tr>";
                    }
                    
                }
                $p.="</table>";
                $k.="</table>";
            }
			
		}
		else{
			$titulo = "ÓRDEN DE PAGO";
		}
		/*-------*/

		switch ($moneda) {
			case 'pesos':

				if($numero_caja == 3)
				{
					$qry = "SELECT pesos FROM caja_gral 
						WHERE numero_caja = '$numero_caja'
						and operacion = 1
						and anulado = 0
						order by numero desc limit 1";

					$op = 1;
				}
				else
				{
					$qry = "SELECT pesos FROM caja_gral 
						WHERE numero_caja = '$numero_caja'
						and operacion = 1
						and anulado = 0
						and fecha = '$fecha'
						order by numero desc limit 1";	

					$op = 1;			
				}

				$res = mysqli_query($connection, $qry);
				$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
				$pesos = $get_datos['pesos'];

				// consigo ultima cobranza
				$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);

				if($datos['importe']<>[]){
					$ultimo_cobro = $datos['importe']; // consigo el ultimo cobro en caja cobranza
				}
				
				// consigo ingreso por servicios
				$qry_serv = "SELECT  importe from ingresos_servicios
							WHERE fecha = '$fecha' 
							AND numero_caja = '$numero_caja'
							order by id limit 1";
				$res_serv = mysqli_query($connection, $qry_serv);
				$datos_serv = mysqli_fetch_array($res_serv);

				if($datos_serv<>[])
				{
					$ing_servicio = $datos_serv['importe'];
				}

				if($ultimo_cobro > 0.00)
				{
					if($pesos == [])
					{
						$pesos_a_restar = ($ultimo_cobro + $ing_servicio - $importe);
					}
					else
					{
						$pesos_a_restar = ($pesos - $importe);
					}
				}
				else
				{
					if($ing_servicio > 0.00)
					{
						if($pesos == [])
						{
							$pesos_a_restar = ($ing_servicio - $importe);
						}
						else
						{
							$pesos_a_restar = ($pesos - $importe);
						}
					}
					else{
						if($pesos == [])
						{
							$pesos_a_restar = (-1)*$importe;
						}
						else
						{
							$pesos_a_restar = ($pesos - $importe);
						}
					}
				}
				
				// agregado : max numero de operacion para asingar en caja_gral
				$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
				$qry_res = mysqli_query($connection,$qry_num);
				$qry_datos = mysqli_fetch_array($qry_res);
				$numero = $qry_datos['numero'];
				/**/
				$insert = "INSERT INTO caja_gral VALUES ('$numero','$numero_caja','$fecha','$fecha','SO $detalle',0,0,'$importe','$pesos_a_restar',0,0,0,1,0)";
				$insert_result = mysqli_query($connection, $insert);
				break;
			
			case 'dolares':
				
				$qry = "SELECT dolares FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 2
					and anulado = 0
					and fecha = '$fecha'
					order by numero desc limit 1";

				$res = mysqli_query($connection, $qry);
				$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
				$dolares = $get_datos['dolares'];

				$op = 2;	

				$dolares_a_restar = 0;

				if($datos == []) 
	   			{ 
		   			$dolares_a_restar = (-1)*$ingreso;
				}
				else $dolares_a_restar = ($dolares - $importe);

				// agregado : max numero de operacion para asingar en caja_gral
				$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
				$qry_res = mysqli_query($connection,$qry_num);
				$qry_datos = mysqli_fetch_array($qry_res);
				$numero = $qry_datos['numero'];
				/**/

				$insert = "INSERT IGNORE INTO caja_gral VALUES 
				('$numero','$numero_caja','$fecha','$fecha','$detalle',0,0,'$importe',0,'$dolares_a_restar',0,0,2,0)";
				$result_insert = mysqli_query($connection, $insert);

				break;

			case 'euros':
				
				$qry = "SELECT euros FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 3
					and anulado = 0
					and fecha = '$fecha'
					order by numero desc limit 1";

				$res = mysqli_query($connection, $qry);
				$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
				$euros = $get_datos['euros'];

				$op = 3;

				$euros_a_restar = 0;

				if($euros == []) 
	   			{ 
		   			$euros_a_restar = (-1)*$importe;
				}
				else $euros_a_restar = ($euros - $importe);

				// agregado : max numero de operacion para asingar en caja_gral
				$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
				$qry_res = mysqli_query($connection,$qry_num);
				$qry_datos = mysqli_fetch_array($qry_res);
				$numero = $qry_datos['numero'];
				/**/

				$insert = "INSERT IGNORE INTO caja_gral VALUES 
				('$numero','$numero_caja','$fecha','$fecha','$detalle',0,0,'$importe',0,0,'$euros_a_restar',0,3,0)";
				$result_insert = mysqli_query($connection, $insert);
				break;

			case 'cheques':

				// case cheques
			
				if($numero_caja == 3)
				{
					$qry = "SELECT pesos FROM caja_gral 
						WHERE numero_caja = '$numero_caja'
						and operacion = 1
						and anulado = 0
						order by numero desc limit 1";

					$res = mysqli_query($connection, $qry);
					$get_datos = mysqli_fetch_array($res);  
					$pesos_banco = $get_datos['pesos'];

					if($pesos_banco == [])
					{
						$pesos_a_restar = (-1)*$importe;
					}
					else
					{
						$pesos_a_restar = ($pesos_banco - $importe);
					}

					// agregado : max numero de operacion para asingar en caja_gral
					$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
					$qry_res = mysqli_query($connection,$qry_num);
					$qry_datos = mysqli_fetch_array($qry_res);
					$numero = $qry_datos['numero'];
					/**/
					
					$insert = "INSERT INTO caja_gral VALUES 
					('$numero','$numero_caja','$fecha','$fecha','SO $detalle',0,0,'$importe','$pesos_a_restar',0,0,0,1,0)";
					$insert_result = mysqli_query($connection, $insert);

					$op = 1;

					$set_cheques = "UPDATE cheques_cartera 
							SET fecha_entrega = '$fecha',
								persona_pago = '$recibe',
								estado = 'Depositado',
								activo = 4
							WHERE num_solicitud = '$numero_orden'";
					$res_cheque = mysqli_query($connection, $set_cheques);
				}
				else
				{
					$qry = "SELECT cheques FROM caja_gral 
						WHERE numero_caja = '$numero_caja'
						and operacion = 4
						and anulado = 0
						and fecha = '$fecha'
						order by numero desc limit 1";
				
				
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

					// agregado : max numero de operacion para asingar en caja_gral
					$qry_num = "SELECT max(numero)+1 as numero FROM caja_gral";
					$qry_res = mysqli_query($connection,$qry_num);
					$qry_datos = mysqli_fetch_array($qry_res);
					$numero = $qry_datos['numero'];
					/**/

					$insert = "INSERT INTO caja_gral VALUES 
					('$numero','$numero_caja','$fecha','$fecha','SO $detalle',0,0,'$importe',0,0,0,'$cheques_a_restar',4,0)";
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
		}
		
		/*--------------------------------------------------------------*/
		// if($moneda == 'pesos')
		// {
		// 	if($numero_caja == 3)
		// 	{
		// 		$qry = "SELECT pesos FROM caja_gral 
		// 			WHERE numero_caja = '$numero_caja'
		// 			and operacion = 1
		// 			order by numero desc limit 1";
		// 	}
		// 	else
		// 	{
		// 		$qry = "SELECT pesos FROM caja_gral 
		// 			WHERE numero_caja = '$numero_caja'
		// 			and operacion = 1
		// 			and fecha = '$fecha'
		// 			order by numero desc limit 1";
			
		// 	}

		// 	$res = mysqli_query($connection, $qry);
		// 	$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
		// 	$pesos = $get_datos['pesos'];

		// 	// consigo ultima cobranza
		// 	$qry = "SELECT  importe from cobranza
		// 		WHERE fecha = '$fecha' 
		// 		AND numero_caja = '$numero_caja'
		// 		order by numero limit 1";
		// 	$res = mysqli_query($connection, $qry);
		// 	$datos = mysqli_fetch_array($res);

		// 	if($datos['importe']<>[]){
		// 		$ultimo_cobro = $datos['importe']; // consigo el ultimo cobro en caja cobranza
		// 	}
			
		// 	// consigo ingreso por servicios
		// 	$qry_serv = "SELECT  importe from ingresos_servicios
		// 				WHERE fecha = '$fecha' 
		// 				AND numero_caja = '$numero_caja'
		// 				order by id limit 1";
		// 	$res_serv = mysqli_query($connection, $qry_serv);
		// 	$datos_serv = mysqli_fetch_array($res_serv);

		// 	if($datos_serv<>[])
		// 	{
		// 		$ing_servicio = $datos_serv['importe'];
		// 	}

		// 	if($ultimo_cobro > 0.00)
		// 	{
		// 		if($pesos == [])
		// 		{
		// 			$pesos_a_restar = ($ultimo_cobro + $ing_servicio - $importe);
		// 		}
		// 		else
		// 		{
		// 			$pesos_a_restar = ($pesos - $importe);
		// 		}
		// 	}
		// 	else
		// 	{
		// 		if($ing_servicio > 0.00)
		// 		{
		// 			if($pesos == [])
		// 			{
		// 				$pesos_a_restar = ($ing_servicio - $importe);
		// 			}
		// 			else
		// 			{
		// 				$pesos_a_restar = ($pesos - $importe);
		// 			}
		// 		}
		// 		else{
		// 			if($pesos == [])
		// 			{
		// 				$pesos_a_restar = (-1)*$importe;
		// 			}
		// 			else
		// 			{
		// 				$pesos_a_restar = ($pesos - $importe);
		// 			}
		// 		}
		// 	}

		// 	$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,0,'$importe','$pesos_a_restar',0,0,0,1)";
		// 	$insert_result = mysqli_query($connection, $insert);

		// 	$op = 1;

		// }
		// else{ // Caso cheques
			
		// 	// if($numero_caja == 3)
		// 	// {
		// 	// 	$qry = "SELECT pesos FROM caja_gral 
		// 	// 		WHERE numero_caja = '$numero_caja'
		// 	// 		and operacion = 1
		// 	// 		order by numero desc limit 1";

		// 	// 	$res = mysqli_query($connection, $qry);
		// 	// 	$get_datos = mysqli_fetch_array($res);  
		// 	// 	$pesos_banco = $get_datos['pesos'];

		// 	// 	if($pesos_banco == [])
		// 	// 	{
		// 	// 		$pesos_a_restar = (-1)*$importe;
		// 	// 	}
		// 	// 	else
		// 	// 	{
		// 	// 		$pesos_a_restar = ($pesos_banco - $importe);
		// 	// 	}

		// 	// 	$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,0,'$importe','$pesos_a_restar',0,0,0,1)";
		// 	// 	$insert_result = mysqli_query($connection, $insert);

		// 	// 	$op = 1;

		// 	// 	$set_cheques = "UPDATE cheques_cartera 
		// 	// 			SET fecha_entrega = '$fecha',
		// 	// 				persona_pago = '$recibe',
		// 	// 				estado = 'Depositado',
		// 	// 				activo = 4
		// 	// 			WHERE num_solicitud = '$numero_orden'";
		// 	// 	$res_cheque = mysqli_query($connection, $set_cheques);
		// 	// }
		// 	// else
		// 	// {
		// 		$qry = "SELECT cheques FROM caja_gral 
		// 			WHERE numero_caja = '$numero_caja'
		// 			and operacion = 4
		// 			and fecha = '$fecha'
		// 			order by numero desc limit 1";
			
			
		// 		$res = mysqli_query($connection, $qry);
		// 		$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
		// 		$cheques = $get_datos['cheques'];

		// 		if($cheques == [])
		// 		{
		// 			$cheques_a_restar = (-1)*$importe;
		// 		}
		// 		else
		// 		{
		// 			$cheques_a_restar = ($cheques - $importe);
		// 		}

		// 		$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $       detalle',0,0,'$importe',0,0,0,'$cheques_a_restar',4)";
		// 		$insert_result = mysqli_query($connection, $insert);

		// 		$op = 4;

		// 		$set_cheques = "UPDATE cheques_cartera 
		// 				SET fecha_entrega = '$fecha',
		// 					persona_pago = '$recibe',
		// 					estado = 'Entregado',
		// 					activo = 4
		// 				WHERE num_solicitud = '$numero_orden'";
		// 		$res_cheque = mysqli_query($connection, $set_cheques);
		// 	//}
		// }
		//////////////////////////////////////////

		/*$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,'$importe','$pesos_a_restar',0,0,0,1)";
		$insert_result = mysqli_query($connection, $insert);*/

		/*--------------------------------------------------------------*/

		$qry = "SELECT numero FROM caja_gral
				WHERE numero_caja = '$numero_caja'
				AND operacion = '$op'
				and anulado = 0
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

		// orden de pago ligada a una solicitud
		$set_orden = "UPDATE ids_check_list SET orden = '$num' 
					  WHERE num_orden = '$numero_orden'"; // $numero_orden es el Nº de solicitud
		$res_set_orden = mysqli_query($connection, $set_orden);

		// seteo campo num_orden_pago
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

		// ultimo ingreso por servicios
		$ultimo_ingreso = 0;
		$qry2 = "SELECT  importe from ingresos_servicios
				 WHERE fecha = '$fecha' 
				 AND numero_caja = '$numero_caja'
				 order by id DESC limit 1";
		$res2 = mysqli_query($connection, $qry2);
		$datos_ingresos = mysqli_fetch_array($res2);
		$ultimo_ingreso = $datos_ingresos['importe'];

		$monto_serv = 0;
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
        $sim = "";
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
		require "../conversor.php";

		 //$textoprecio   = TextoPrecio(number_format($importe,2,',','.'));

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
		mysqli_close($connection);
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="js/jquery-3.5.1.min.js"></script> 
<title>Comprobante</title>
<style>
    
    @page { 
        margin-left: 4px;
        margin-right: 4px; 
        margin-top: 2px; 
        box-sizing: border-box;
    } 
    p, label, span{
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
                    <strong><?php echo $titulo;?></strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;"><?php echo "N° de órden: ".$numero_orden; ?></strong>
                    <strong style="display: inline-block; margin-left: 35%;"><?php echo "Fecha: ".fecha_min($fecha)?></strong>
                    <strong style="float: right;"><?php echo "Hora: ".$hora; ?></strong>
                    </p>
                </div>
                <hr>
                <?php 

                   // if($num_solicitud != 0){
                    if($confirm_datos_solic)
                    {
                        echo "<p> <strong>Solicitante: ".$solicitante." (caja ".$numero_caja_solicitante.")</strong></p>";
                        echo "<p> <strong>Emitida por: ".$rol_caja_pago." (caja ". $caja_pago.")</strong></p>";
                    }
                    else
                        echo "<p> <strong>Emitida por: ".$rol_caja_pago." (caja ". $caja_pago.")</strong></p>";
                ?>
                <p><strong><?php echo "Recibe: ".$recibe; ?></strong></p>
                <p><strong><?php echo "Empresa: ".$empresa. " - "." Obra: ".$obra;?></strong></p>
                <p><strong><?php echo "Cuenta: ".$cuenta;?></strong></p>
                <p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
                <?php 
                    // $space = 0; // variable para texto largo
                    // if( strlen($texto1." ".$texto2) > 100)
                    // {            
                    //     echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                    //     // echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.$importe." "."(".strtolower($textoprecio).")"."</strong></p>";
                    //     $space = 1;
                    // }
                    // else
                    // 	echo "<p><strong style='word-wrap: break-word; line-height: 11.5px>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong></p>";
                    // 	// echo "<p><strong>Son: ".$sim.$importe." "."(".strtoupper($textoprecio).")"."</strong></p>";
                ?>
				<p>Son: 
                <?php 
                    $space = 0; // variable para texto largo
                    //if( strlen($texto1." ".$texto2) > 100 )
                    if( strlen($texto1." ".$texto2) > 100)
                    {            
                        echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$sim.number_format($importe,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong>";
                        // echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.$importe." "."(".strtolower($textoprecio).")"."</strong></p>";
                        $space = 1;
                    }
                    else
                    	echo "<strong style='word-wrap: break-word; line-height: 11.5px;'>".$sim.number_format($importe,2,',','.')." "."(".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong>";
                    	// echo "<p><strong>Son: ".$sim.$importe." "."(".strtoupper($textoprecio).")"."</strong></p>";
                ?>
                </p>

                <?php
					$div="";
					if($band == 1){
						$div="<div><div id='left'>".$p."</div>";
						if($c > 4)
						{
							$div.= "<div id='right'>".$k."</div></div>";
						}
						else $div.="</div>";
						echo $div;
						if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
					}
										
				?>
                <!--div id="content-cheques33" >
                    <div id="left">
                        <?php if($band == 1) echo $p;?>
                    </div>
                    <div id="right">
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
                    <?php 
                        if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
                    ?>
                     
                </div-->
				
            </div>
            
            <div id="content-firma">
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <strong style="float: right;">Autorizó</strong>
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
                    case 1: echo "<br><br><br><br><br><br><br><br><br>";
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
                	
				else echo "<br><br><br><br><br><br><br><br><br>";
            }
        ?>
        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong><?php echo $titulo;?></strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;"><?php echo "N° de órden: ".$numero_orden; ?></strong>
                    <strong style="display: inline-block; margin-left: 35%;"><?php echo "Fecha: ".fecha_min($fecha)?></strong>
                    <strong style="float: right;"><?php echo "Hora: ".$hora; ?></strong>
                    </p>
                </div>
                <hr>
                <?php 

                   // if($num_solicitud != 0){
                    if($confirm_datos_solic)
                    {
                        echo "<p> <strong>Solicitante: ".$solicitante." (caja ".$numero_caja_solicitante.")</strong></p>";
                        echo "<p> <strong>Emitida por: ".$rol_caja_pago." (caja ". $caja_pago.")</strong></p>";
                    }
                    else
                        echo "<p> <strong>Emitida por: ".$rol_caja_pago." (caja ". $caja_pago.")</strong></p>";
                ?>
                <p><strong><?php echo "Recibe: ".$recibe; ?></strong></p>
                <p><strong><?php echo "Empresa: ".$empresa. " - "." Obra: ".$obra;?></strong></p>
                <p><strong><?php echo "Cuenta: ".$cuenta;?></strong></p>
                <p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
                <?php 
                    // $space = 0; // variable para texto largo
                    // //if( strlen($texto1." ".$texto2) > 100 )
                    // if( strlen($texto1." ".$texto2) > 100)
                    // {            
                    //     echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                    //     // echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.$importe." "."(".strtolower($textoprecio).")"."</strong></p>";
                    //     $space = 1;
                    // }
                    // else
                    // 	echo "<p><strong style='word-wrap: break-word; line-height: 11.5px>Son: ".$sim.number_format($importe,2,',','.')." "."(".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong></p>";
                    // 	// echo "<p><strong>Son: ".$sim.$importe." "."(".strtoupper($textoprecio).")"."</strong></p>";
                ?>
				<p>Son: 
                <?php 
                    $space = 0; // variable para texto largo
                    //if( strlen($texto1." ".$texto2) > 100 )
                    if( strlen($texto1." ".$texto2) > 100)
                    {            
                        echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$sim.number_format($importe,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong>";
                        // echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.$importe." "."(".strtolower($textoprecio).")"."</strong></p>";
                        $space = 1;
                    }
                    else
                    	echo "<strong style='word-wrap: break-word; line-height: 11.5px;'>".$sim.number_format($importe,2,',','.')." "."(".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong>";
                    	// echo "<p><strong>Son: ".$sim.$importe." "."(".strtoupper($textoprecio).")"."</strong></p>";
                ?>
                </p>
                <?php
					$div="";
					if($band == 1){
						 $div="<div>
								<div id='left'>".$p."</div>";
						if($c > 4){
							$div.= "<div id='right'>".$k."</div></div>";
						}
						else $div.="</div>";
						echo $div;
						if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
					}
										
				?>
                <!--div id="content-cheques33" >
                    <div id="left">
                        <?php if($band == 1) echo $p;?>
                    </div>
                    <div id="right">
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
                    <?php 
                        if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
                    ?>
                     
                </div-->
            </div>
            
            <div id="content-firma">
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <strong style="float: right;">Autorizó</strong>
                </p>
            </div>
        </div>
        
    </div>

    
</body>
</html>
