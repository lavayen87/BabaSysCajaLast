
<?php 
	date_default_timezone_set('America/Argentina/Salta');
	session_start();
	if($_SESSION['active'])
	{
		$micaja = $_SESSION['nombre_caja'];
		$numero_caja = $_SESSION['numero_caja'];
	}

	$fila = $_POST['fila'];

	$monto = 0.00;
	$monto2 = 0.00;
	$ing_servicio = 0.00;
	$ing_servicio2 = 0.00;
	$saldo_anterior = 0.00;
	$saldo_anterior_dolares = 0;
	$saldo_anterior_euros = 0;
	$saldo_anterior_cheques = 0;

	$saldo_anterior2 = 0.00;
	$saldo_anterior_dolares2 = 0;
	$saldo_anterior_euros2 = 0;
	$saldo_anterior_cheques2 = 0; 

	$caja_origen = -1;
	$caja_destino = 0;

	$pesos_hoy = 0.00;
	$dolares_hoy = 0;
	$euros_hoy = 0;
	$cheques_hoy = 0;

	$pesos_hoy2 =0.00;
	$dolares_hoy2 = 0;
	$euros_hoy2 = 0;
	$cheques_hoy2 = 0;	


	include('conexion.php');
	include('funciones.php');

	// Consigo datos del movimiento que vamos anular
	$query = "SELECT * from caja_gral 
			  WHERE numero = '$fila' 
			  AND numero_caja = '$numero_caja'";    
	$result = mysqli_query($connection, $query); 
	$datos = mysqli_fetch_array($result);
	$op = $datos['operacion']; // numero de operacion (pesos, dolar, euro, cheques)

	$fecha = date('Y-m-d');

	// Elimino la fila indicada en caja_gral. ************ 
	// $query = "DELETE  from caja_gral 
	// 		WHERE numero = '$fila'
	// 		and fecha = '$fecha'";    
	// $result = mysqli_query($connection, $query);  

	// Si es orden de pago, la elimino.
	// $qry_get = "SELECT * from orden_pago 
	// 			WHERE numero_orden = '$fila'
	// 			AND numero_caja = '$numero_caja'";
	// $res_get = mysqli_query($connection, $qry_get);

	// if($res_get->num_rows > 0)
	// {
	// 	$delete_op = "DELETE  from orden_pago 
	// 				  WHERE numero_orden = '$fila'
	// 				  AND numero_caja = '$numero_caja'";    
	// 	$result_op = mysqli_query($connection, $delete_op);  
	// }

	// Si es transferencia, la elimino solo para la caja destino
	$qry_get = "SELECT * from transferencias 
				WHERE numero_tr = '$fila'
				AND fecha = '$fecha'";
	$res_get = mysqli_query($connection, $qry_get);

	if($res_get->num_rows > 0)
	{
		$datos = mysqli_fetch_array($res_get);
		$caja_origen  = $datos['numero_caja_origen'];
		$caja_destino = $datos['numero_caja_destino'];
		$nombre_caja_origen = $datos['nombre_caja_origen'];
		$nombre_caja_destino = $datos['nombre_caja_destino'];
		
		// elimino la operación de la tabla Transferencias
		// $delete_tr = "DELETE from transferencias 
		// 			  WHERE numero_tr = '$fila' 
		// 			  AND fecha = '$fecha'";    
		// $result_tr = mysqli_query($connection, $delete_tr);  

		// ******** AGREGADO *********
		$delete_tr = "DELETE from caja_gral  
		 			  WHERE numero = '$fila' 
		 			  AND fecha = '$fecha'
		 			  AND numero_caja = '$caja_destino'";    
		mysqli_query($connection, $delete_tr);  

		// ***************************
	}

	// elimino el retiro indicado si está vinculado 
	$qry_get = "SELECT * from retiros
				WHERE numero_retiro = '$fila'
				AND numero_caja = '$numero_caja'";
	$res_get = mysqli_query($connection, $qry_get);	

	if($res_get->num_rows > 0)
	{
		$delete_retiro = "DELETE  from retiros 
					  WHERE numero_retiro = '$fila'
					  AND numero_caja = '$numero_caja'";    
		$result_op = mysqli_query($connection, $delete_retiro);  
	}


	// Recalculamos las columnas de acuerdo a la moneda :
	
	if($caja_origen <> -1)
	{
		Update($caja_origen, $op, $fecha,$fila);
		Update($caja_destino, $op, $fecha,$fila);
	}
	else 
	{
		$caja_origen = $numero_caja;		
		Update($caja_origen, $op, $fecha,$fila);
	}

	// Totales generales para caja origen

	// consigo  pesos // antes era $numero_caja
	$qry = "SELECT pesos FROM caja_gral 
			WHERE fecha = '$fecha'
			and numero_caja = '$caja_origen'
			and operacion = 1 and pesos <> 0
			order by numero desc limit 1";
	$res = mysqli_query($connection, $qry);

	$datos_pesos = mysqli_fetch_array($res);

	$pesos_hoy = $datos_pesos['pesos'];

	//echo $pesos_hoy;exit;

	$saldo_anterior=saldo_ant('pesos',$caja_origen,$fecha);
    $saldo_anterior_dolares=saldo_ant('dolares',$caja_origen,$fecha);
    $saldo_anterior_euros=saldo_ant('euros',$caja_origen,$fecha);
    $saldo_anterior_cheques=saldo_ant('cheques',$caja_origen,$fecha);

	//Consigo total del dia en pesos desde mi caja
		        
	$pesos_hoy = get_total(1,$caja_origen,$fecha);

	//Consigo total del dia en dolares desde mi caja
		          
	$dolares_hoy = get_total(2,$caja_origen,$fecha);

	//Consigo total del dia en euros desde mi caja
		          
	$euros_hoy = get_total(3,$caja_origen,$fecha);

	//Consigo total del dia en cheques desde mi caja
		          
	$cheques_hoy = get_total(4,$caja_origen,$fecha);

	// Obtengo datos de cobranza
	$cob = "SELECT importe from cobranza
				WHERE fecha = '$fecha'
				AND numero_caja = '$caja_origen'
				order by numero limit 1";
	$res_cob = mysqli_query($connection, $cob);
	$datos_cob = mysqli_fetch_array($res_cob);
	
	if($res_cob->num_rows > 0)
		$monto = $datos_cob['importe'] > 0.00 ? $datos_cob['importe'] : 0.00;
	

	// consigo ingreso por servicios
	$qry_serv = "SELECT  importe from ingresos_servicios
				WHERE fecha = '$fecha' 
				AND numero_caja = '$caja_origen'
				order by id limit 1";
	$res_serv = mysqli_query($connection, $qry_serv);
	$datos_serv = mysqli_fetch_array($res_serv);
	
	if($datos_serv<>[])
	{
	  $ing_servicio = $datos_serv['importe'];
	}

	if( ($pesos_hoy<>[]) && ($monto>=0) && ($ing_servicio>=0))
	{
		$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto + $ing_servicio);
	}
	else
		if( ($pesos_hoy==[]) && ($monto>=0) && ($ing_servicio>=0))
		{
			$total_gral_pesos = ($saldo_anterior + $monto + $ing_servicio);
		}
		else $total_gral_pesos = $saldo_anterior;

	$total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
	$total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
	$total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);

	$qry = "SELECT * from caja_gral_temp
					where operacion = 1 
					and numero_caja = '$caja_origen'
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
					WHERE numero_caja = '$caja_origen'
					and fecha = '$fecha'
					and operacion = 1";
		$res = mysqli_query($connection, $set);
	}
	else{
		$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
		('','$caja_origen','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

		$result_insert = mysqli_query($connection, $insert);
	}

	// Totales generales para caja destino (en caso de que el movimiento anulado sea una transferencia)

	if($caja_destino <> 0)
	{	
		$saldo_anterior2=saldo_ant('pesos',$caja_destino,$fecha);
	    $saldo_anterior_dolares2=saldo_ant('dolares',$caja_destino,$fecha);
	    $saldo_anterior_euros2=saldo_ant('euros',$caja_destino,$fecha);
	    $saldo_anterior_cheques2=saldo_ant('cheques',$caja_destino,$fecha);

		//Consigo total del dia en pesos desde mi caja
			        
		$pesos_hoy2 = get_total(1,$caja_destino,$fecha);

		//Consigo total del dia en dolares desde mi caja
			          
		$dolares_hoy2 = get_total(2,$caja_destino,$fecha);

		//Consigo total del dia en euros desde mi caja
			          
		$euros_hoy2 = get_total(3,$caja_destino,$fecha);

		//Consigo total del dia en cheques desde mi caja
			          
		$cheques_hoy2 = get_total(4,$caja_destino,$fecha);

		// Obtengo datos de cobranza
		$monto2 = 0;
		$cob = "SELECT importe from cobranza
					WHERE fecha = '$fecha'
					AND numero_caja = '$caja_destino'
					order by numero limit 1";
		$res_cob = mysqli_query($connection, $cob);
		$datos_cob = mysqli_fetch_array($res_cob);

		if($res_cob->num_rows > 0)
			$monto2 = $datos_cob['importe'] > 0.00 ? $datos_cob['importe'] : 0.00;		

		// consigo ingreso por servicios
		$qry_serv2 = "SELECT  importe from ingresos_servicios
					WHERE fecha = '$fecha' 
					AND numero_caja = '$caja_destino'
					order by id limit 1";
		$res_serv2 = mysqli_query($connection, $qry_serv2);
		$datos_serv2 = mysqli_fetch_array($res_serv2);

		if($datos_serv2<>[])
		{
			$ing_servicio2 = $datos_serv2['importe'];
		} 

		if( ($pesos_hoy2<>[]) && ($monto2>=0) && ($ing_servicio2>=0))
		{
			$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2 + $ing_servicio2);
		}
		else
			if( ($pesos_hoy2==[]) && ($monto2>=0) && ($ing_servicio2>=0))
			{
				$total_gral_pesos2 = ($saldo_anterior2 + $monto2 + $ing_servicio2);
			}
			else $total_gral_pesos2 = ($saldo_anterior2);

		$total_gral_dolares2 = ($saldo_anterior_dolares2 + $dolares_hoy2);
		$total_gral_euros2 = ($saldo_anterior_euros2 + $euros_hoy2);
		$total_gral_cheques2 = ($saldo_anterior_cheques2 + $cheques_hoy2);

		$qry = "SELECT * from caja_gral_temp
						where operacion = 1 
						and numero_caja = '$caja_destino'
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
						WHERE numero_caja = '$caja_destino'
						and fecha = '$fecha'
						and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$caja_destino','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

			$result_insert = mysqli_query($connection, $insert);
		}
	}


?>