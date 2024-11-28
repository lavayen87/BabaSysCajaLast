<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$moneda  = $_POST['moneda'];
$empresa = $_POST['empresa'];
$obra    = $_POST['obra'];
$cuenta  = $_POST['cuenta'];
$importe = $_POST['importe'];
$detalle = $_POST['detalle'];
$recibe = $_POST['recibe'];
$fecha   = date('Y-m-d');

$saldo_anterior = 0.00;
$saldo_anterior_dolares = 0;
$saldo_anterior_euros = 0;
$saldo_anterior_cheques = 0;
$monto = 0;
$ing_servicio = 0;
$monto_serv = 0;
$total_gral = 0.00;

include('conexion.php');
include('funciones.php');

switch ($moneda) {
	case 1:
		// pesos...
		// Consigo pesos desde mi caja
		if($numero_caja == 3)
		{
			$query = "SELECT pesos
					from caja_gral
					where numero_caja = '$numero_caja' 
					AND operacion = 1
					and anulado = 0
					order by numero desc limit 1"; 
		}
		else
		{
			$query = "SELECT pesos
					from caja_gral
					where numero_caja = '$numero_caja' 
					AND anulado = 0
					AND operacion = 1
					AND fecha = '$fecha'
					order by numero desc limit 1"; 
		}
		
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$pesos = $datos['pesos'];

		//Consigo dolares desde mi caja
		$query = "SELECT sum(dolares) as total_dolares from caja_gral
					where numero_caja = '$numero_caja'
					and operacion = 2
					and anulado = 0
					and fecha = '$fecha'
					order by numero desc limit 1";    
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$dolares_hoy = $datos['total_dolares'];

		//Consigo euros desde mi caja
		$query = "SELECT sum(euros) as total_euros from caja_gral
					where numero_caja = '$numero_caja'
					and operacion = 3
					and anulado = 0
					and fecha = '$fecha'
					order by numero desc limit 1";    
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$euros_hoy = $datos['total_euros'];

		//Consigo cheques desde mi caja
		$query = "SELECT sum(cheques) as total_cheques from caja_gral
				where numero_caja = '$numero_caja'
				and operacion = 4
				and anulado = 0
				and fecha = '$fecha'
				order by numero desc limit 1";   
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$cheques_hoy = $datos['total_cheques'];

		////////////////////////////////////////////

		// consigo el ultimo cobro en caja cobranza
		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = 0;
		//$ultimo_cobro = $datos['importe']; 

		if($res->num_rows > 0)
			$ultimo_cobro = $datos['importe'] > 0.00 ? $datos['importe'] : 0.00;

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

		// Realizo la operacion  

		/*if($ultimo_cobro > 0.00)
		{
			if($pesos == [])
			{
				$pesos_a_restar = $ultimo_cobro + $ing_servicio - $importe;
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
				$pesos_a_restar = $ing_servicio + ((-1)*$importe);
			}
			else
			{
				$pesos_a_restar = $pesos - $importe;
			}
		}*/
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
					$pesos_a_restar = ($ing_servicio + (-1)*$importe);
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

		$sql = "SELECT * FROM orden_pago
			WHERE fecha = '$fecha'
			AND empresa = '$empresa'
			AND obra = '$obra'
			AND cuenta = '$cuenta'
			AND detalle = '$detalle'
			AND importe = '$importe'
			AND moneda = 'pesos'
			AND recibe = '$recibe'
			AND numero_caja = '$numero_caja'";
		$res_sql = mysqli_query($connection,$sql);
		if($res_sql->num_rows == 0)
		/*  */
		{
			// cargo la orden en mi caja
			$insert1 = "INSERT IGNORE INTO caja_gral
			VALUES ('','$numero_caja','$fecha','$fecha','$detalle',0,0,'$importe','$pesos_a_restar',0,0,0,1,0)";
			mysqli_query($connection, $insert1);

			// consigo numero de movimiento 
			$qry = "SELECT numero FROM caja_gral
						WHERE numero_caja = '$numero_caja'
						AND operacion = 1
						AND fecha = '$fecha'
						order by numero desc limit 1";
			$res_qry = mysqli_query($connection, $qry);
			$get_datos = mysqli_fetch_array($res_qry);
			$num = $get_datos['numero'];

			// cargo la orden en tabla orden_pago
			$insert2 = "INSERT IGNORE INTO orden_pago VALUES ('$num','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra','pesos','$recibe')";
			//$result_insert2 = mysqli_query($connection, $insert2);
			mysqli_query($connection, $insert2);

			//$new_id = mysqli_insert_id( $connection );

			//Buscamos Saldo anterior 
			$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
			$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
			$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
			$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);


			//Consigo total del dia en pesos desde mi caja

			$pesos_hoy = get_total(1,$numero_caja,$fecha);

			//Consigo total del dia en dolares desde mi caja

			$dolares_hoy = get_total(2,$numero_caja,$fecha);

			//Consigo total del dia en euros desde mi caja

			$euros_hoy = get_total(3,$numero_caja,$fecha);

			//Consigo total del dia en cheques desde mi caja

			$cheques_hoy = get_total(4,$numero_caja,$fecha);

			// consigo cobranza
			$cob = "SELECT importe from cobranza
					WHERE fecha = '$fecha'
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
			$res_cob = mysqli_query($connection, $cob);
			$datos_cob = mysqli_fetch_array($res_cob);

			// cargo la tabla de totales generales

			if($datos_cob['importe']<>[])
			{
				$monto = $datos_cob['importe'];
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

			$qry = "SELECT * from caja_gral_temp
					where operacion = 1
					and numero_caja = '$numero_caja'
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
						WHERE numero_caja = '$numero_caja'
						and fecha = '$fecha'
						and operacion = 1";
				$res = mysqli_query($connection, $set);
			}
			else{
				$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

				$result_insert = mysqli_query($connection, $insert);
			}


			echo 1;
		}
		else{
			echo 400;
		}
		
		break;
	
	case 2:
		// dolares...
		$importe = (int)$importe;

		$query_pesos = "SELECT pesos from caja_gral
				where numero_caja = '$numero_caja' 
				AND operacion = 1
				AND anulado = 0
				AND fecha = '$fecha'
				order by numero desc limit 1"; 
		$result = mysqli_query($connection, $query_pesos);
		$datos = mysqli_fetch_array($result);
		$pesos = $datos['pesos'];
			
		$query_dolares = "SELECT dolares
				from caja_gral
				where numero_caja = '$numero_caja' 
				AND operacion = 2
				AND anulado = 0
				AND fecha = '$fecha'
				order by numero desc limit 1"; 
	
		
		$result = mysqli_query($connection, $query_dolares);
		$datos = mysqli_fetch_array($result);
		$dolares = $datos['dolares'];

		//Consigo dolares desde mi caja
		$query = "SELECT sum(dolares) as total_dolares from caja_gral
					where numero_caja = '$numero_caja'
					and operacion = 2
					AND anulado = 0
					and fecha = '$fecha'
					order by numero desc limit 1";    
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$dolares_hoy = $datos['total_dolares'];

		//Consigo euros desde mi caja
		$query = "SELECT sum(euros) as total_euros from caja_gral
					where numero_caja = '$numero_caja'
					and operacion = 3
					AND anulado = 0
					and fecha = '$fecha'
					order by numero desc limit 1";    
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$euros_hoy = $datos['total_euros'];

		//Consigo cheques desde mi caja
		$query = "SELECT sum(cheques) as total_cheques from caja_gral
				where numero_caja = '$numero_caja'
				and operacion = 4
				AND anulado = 0
				and fecha = '$fecha'
				order by numero desc limit 1";   
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$cheques_hoy = $datos['total_cheques'];

		////////////////////////////////////////////

		// consigo el ultimo cobro en caja cobranza
		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe']; 

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

		// Realizo la operacion  

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
					$pesos_a_restar = ($ing_servicio + (-1)*$importe);
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

		$dolares_a_restar = 0;

		$sql = "SELECT * FROM orden_pago
			WHERE fecha = '$fecha'
			AND empresa = '$empresa'
			AND obra = '$obra'
			AND cuenta = '$cuenta'
			AND detalle = '$detalle'
			AND importe = '$importe'
			AND moneda = 'dolares'
			AND recibe = '$recibe'
			AND numero_caja = '$numero_caja'";
		$res_sql = mysqli_query($connection,$sql);
		if($res_sql->num_rows == 0)
		/*  */
		{
			if($dolares == [])
				$dolares_a_restar = (-1)*$importe;
			else
				$dolares_a_restar = $dolares - $importe;

			// cargo la orden en mi caja
			$insert1 = "INSERT IGNORE INTO caja_gral
			VALUES ('','$numero_caja','$fecha','$fecha','$detalle',0,0,'$importe',0,'$dolares_a_restar',0,0,2,0)";
			mysqli_query($connection, $insert1);

			// consigo numero de movimiento 
			$qry = "SELECT numero FROM caja_gral
						WHERE numero_caja = '$numero_caja'
						AND operacion = 2
						AND fecha = '$fecha'
						order by numero desc limit 1";
			$res_qry = mysqli_query($connection, $qry);
			$get_datos = mysqli_fetch_array($res_qry);
			$num = $get_datos['numero'];

			// cargo la orden en tabla orden_pago
			$insert2 = "INSERT IGNORE INTO orden_pago VALUES ('$num','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra','dolares','$recibe')";
			//$result_insert2 = mysqli_query($connection, $insert2);
			mysqli_query($connection, $insert2);

			//$new_id = mysqli_insert_id( $connection );

			//Buscamos Saldo anterior 
			$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
			$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
			$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
			$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);


			//Consigo total del dia en pesos desde mi caja

			$pesos_hoy = get_total(1,$numero_caja,$fecha);

			//Consigo total del dia en dolares desde mi caja

			$dolares_hoy = get_total(2,$numero_caja,$fecha);

			//Consigo total del dia en euros desde mi caja

			$euros_hoy = get_total(3,$numero_caja,$fecha);

			//Consigo total del dia en cheques desde mi caja

			$cheques_hoy = get_total(4,$numero_caja,$fecha);

			// consigo cobranza
			$cob = "SELECT importe from cobranza
					WHERE fecha = '$fecha'
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
			$res_cob = mysqli_query($connection, $cob);
			$datos_cob = mysqli_fetch_array($res_cob);

			// cargo la tabla de totales generales

			if($datos_cob['importe']<>[])
			{
				$monto = $datos_cob['importe'];
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

			$qry = "SELECT * from caja_gral_temp
					where operacion = 1
					and numero_caja = '$numero_caja'
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
						WHERE numero_caja = '$numero_caja'
						and fecha = '$fecha'
						and operacion = 1";
				$res = mysqli_query($connection, $set);
			}
			else{
				$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

				$result_insert = mysqli_query($connection, $insert);
			}


			echo 1;
		}
		else{
			echo 400;
		}
		break;

	case 3:
		// euros..
		$importe = (int)$importe;

		$query_pesos = "SELECT pesos from caja_gral
					where numero_caja = '$numero_caja' 
					AND anulado = 0
					AND operacion = 1
					AND fecha = '$fecha'
					order by numero desc limit 1"; 
		$result = mysqli_query($connection, $query_pesos);
		$datos = mysqli_fetch_array($result);
		$pesos = $datos['pesos'];
			
		$query_dolares = "SELECT dolares
				from caja_gral
				where numero_caja = '$numero_caja' 
				AND anulado = 0
				AND operacion = 2
				AND fecha = '$fecha'
				order by numero desc limit 1"; 		
		$result = mysqli_query($connection, $query_dolares);
		$datos = mysqli_fetch_array($result);
		$dolares = $datos['dolares'];

		$query_euros = "SELECT euros
				from caja_gral
				where numero_caja = '$numero_caja' 
				AND anulado = 0
				AND operacion = 3
				AND fecha = '$fecha'
				order by numero desc limit 1"; 		
		$result = mysqli_query($connection, $query_euros);
		$datos = mysqli_fetch_array($result);
		$euros = $datos['euros'];

		//Consigo dolares desde mi caja
		$query = "SELECT sum(dolares) as total_dolares from caja_gral
					where numero_caja = '$numero_caja'
					AND anulado = 0
					and operacion = 2
					and fecha = '$fecha'
					order by numero desc limit 1";    
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$dolares_hoy = $datos['total_dolares'];

		//Consigo euros desde mi caja
		$query = "SELECT sum(euros) as total_euros from caja_gral
					where numero_caja = '$numero_caja'
					AND anulado = 0
					and operacion = 3
					and fecha = '$fecha'
					order by numero desc limit 1";    
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$euros_hoy = $datos['total_euros'];

		//Consigo cheques desde mi caja
		$query = "SELECT sum(cheques) as total_cheques from caja_gral
				where numero_caja = '$numero_caja'
				AND anulado = 0
				and operacion = 4
				and fecha = '$fecha'
				order by numero desc limit 1";   
		$result = mysqli_query($connection, $query);
		$datos = mysqli_fetch_array($result);
		$cheques_hoy = $datos['total_cheques'];

		////////////////////////////////////////////

		// consigo el ultimo cobro en caja cobranza
		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe']; 

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

		// Realizo la operacion  

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
					$pesos_a_restar = ($ing_servicio + (-1)*$importe);
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

		$euros_a_restar = 0;

		$sql = "SELECT * FROM orden_pago
			WHERE fecha = '$fecha'
			AND empresa = '$empresa'
			AND obra = '$obra'
			AND cuenta = '$cuenta'
			AND detalle = '$detalle'
			AND importe = '$importe'
			AND moneda = 'euros'
			AND recibe = '$recibe'
			AND numero_caja = '$numero_caja'";
		$res_sql = mysqli_query($connection,$sql);
		if($res_sql->num_rows == 0)
		/*  */
		{
			if($euros == [])
				$euros_a_restar = (-1)*$importe;
			else
				$euros_a_restar = $euros - $importe;

			// cargo la orden en mi caja
			$insert1 = "INSERT IGNORE INTO caja_gral
			VALUES ('','$numero_caja','$fecha','$fecha','$detalle',0,0,'$importe',0,0,'$euros_a_restar',0,3,0)";
			mysqli_query($connection, $insert1);

			// consigo numero de movimiento 
			$qry = "SELECT numero FROM caja_gral
						WHERE numero_caja = '$numero_caja'
						AND operacion = 3
						AND fecha = '$fecha'
						order by numero desc limit 1";
			$res_qry = mysqli_query($connection, $qry);
			$get_datos = mysqli_fetch_array($res_qry);
			$num = $get_datos['numero'];

			// cargo la orden en tabla orden_pago
			$insert2 = "INSERT IGNORE INTO orden_pago VALUES ('$num','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra','euros','$recibe')";
			//$result_insert2 = mysqli_query($connection, $insert2);
			mysqli_query($connection, $insert2);

			//$new_id = mysqli_insert_id( $connection );

			//Buscamos Saldo anterior 
			$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
			$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
			$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
			$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);


			//Consigo total del dia en pesos desde mi caja

			$pesos_hoy = get_total(1,$numero_caja,$fecha);

			//Consigo total del dia en dolares desde mi caja

			$dolares_hoy = get_total(2,$numero_caja,$fecha);

			//Consigo total del dia en euros desde mi caja

			$euros_hoy = get_total(3,$numero_caja,$fecha);

			//Consigo total del dia en cheques desde mi caja

			$cheques_hoy = get_total(4,$numero_caja,$fecha);

			// consigo cobranza
			$cob = "SELECT importe from cobranza
					WHERE fecha = '$fecha'
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
			$res_cob = mysqli_query($connection, $cob);
			$datos_cob = mysqli_fetch_array($res_cob);

			// cargo la tabla de totales generales

			if($datos_cob['importe']<>[])
			{
				$monto = $datos_cob['importe'];
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

			$qry = "SELECT * from caja_gral_temp
					where operacion = 1
					and numero_caja = '$numero_caja'
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
						WHERE numero_caja = '$numero_caja'
						and fecha = '$fecha'
						and operacion = 1";
				$res = mysqli_query($connection, $set);
			}
			else{
				$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

				$result_insert = mysqli_query($connection, $insert);
			}


			echo 1;
		}
		else{
			echo 400;
		}
		break;
}

		 


?>