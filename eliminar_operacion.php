
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
$saldo_anterior = 0.00;
$saldo_anterior_dolares = 0;
$saldo_anterior_euros = 0;
$saldo_anterior_cheques = 0;

$saldo_anterior2 = 0.00;
$saldo_anterior_dolares2 = 0;
$saldo_anterior_euros2 = 0;
$saldo_anterior_cheques2 = 0; 

$caja_origen = 0;
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

// Consigo datos de la fila a eliminar
$query = "SELECT * from caja_gral 
		WHERE numero = '$fila' 
		AND numero_caja = '$numero_caja'";    
$result = mysqli_query($connection, $query); 
$datos = mysqli_fetch_array($result);
$op = $datos['operacion']; // numero de operacion (pesos, dolar, euro)
$fecha = date('Y-m-d');

// Elimino la fila indicada en caja_gral.
$query = "DELETE  from caja_gral 
		WHERE numero = '$fila'
		and fecha = '$fecha'";    
$result = mysqli_query($connection, $query);  

// Si es orden de pago, la elimino.
$qry_get = "SELECT * from orden_pago 
			WHERE numero_orden = '$fila'
			AND numero_caja = '$numero_caja'";
$res_get = mysqli_query($connection, $qry_get);

if($res_get->num_rows > 0)
{
	$delete_op = "DELETE  from orden_pago 
				  WHERE numero_orden = '$fila'
				  AND numero_caja = '$numero_caja'";    
	$result_op = mysqli_query($connection, $delete_op);  
}

// Si es transferencia, la elimino.
$qry_get = "SELECT * from transferencias 
			WHERE numero_tr = '$fila'
			AND fecha = '$fecha'";
$res_get = mysqli_query($connection, $qry_get);

if($res_get->num_rows > 0)
{
	$datos = mysqli_fetch_assoc($res_get);
	$caja_origen  = $datos['numero_caja_origen'];
	$caja_destino = $datos['numero_caja_destino'];
	
	// elimino la operación de la tabla Transferencias
	$delete_tr = "DELETE from transferencias 
				  WHERE numero_tr = '$fila' 
				  AND fecha = '$fecha'";    
	$result_tr = mysqli_query($connection, $delete_tr);  

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

	$saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha);
    $saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha);
    $saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha);
    $saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha);

	//Consigo total del dia en pesos desde mi caja
		        
	$pesos_hoy = get_total(1,$numero_caja,$fecha);

	//Consigo total del dia en dolares desde mi caja
		          
	$dolares_hoy = get_total(2,$numero_caja,$fecha);

	//Consigo total del dia en euros desde mi caja
		          
	$euros_hoy = get_total(3,$numero_caja,$fecha);

	//Consigo total del dia en cheques desde mi caja
		          
	$cheques_hoy = get_total(4,$numero_caja,$fecha);

	// Consigo cobranza diaria
	$qry = "SELECT  importe from cobranza
			WHERE fecha = '$fecha' 
			AND numero_caja = '$numero_caja'
			order by numero limit 1";
	$res = mysqli_query($connection, $qry);
	$datos = mysqli_fetch_array($res);
	$ultimo_cobro = $datos['importe'];

	if($ultimo_cobro <> []){
		$monto = $ultimo_cobro;
	}

	//cargo  totales generales

	if( ($pesos_hoy<>[]) && ($monto>=0) )
	{
		$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
	}
	else
		if( ($pesos_hoy==[]) && ($monto>=0) )
		{
			$total_gral_pesos = ($saldo_anterior + $monto);
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

}

// Recalculamos las columnas de acuerdo a la moneda :

if($op == 1) // caso pesos
{
	 
	/*---- funcion update ----*/

	if($caja_origen <> 0)
	{
		Update($caja_origen, $op, $fecha);
		Update($caja_destino, $op, $fecha);
	}
	else 
	{
		$caja_origen = $numero_caja;
		Update($caja_origen, $op, $fecha);
	}
	// totales generales para caja origen

	// consigo  pesos // antes era $numero_caja
	$qry = "SELECT pesos FROM caja_gral 
			WHERE fecha = '$fecha'
			and numero_caja = '$caja_origen'
			and operacion = 1
			order by numero desc limit 1";
	$res = mysqli_query($connection, $qry);

	$datos_pesos = mysqli_fetch_array($res);
	$pesos_hoy = $datos_pesos['pesos'];

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
	
	if($datos_cob['importe'] <>[]){
		$monto = $datos_cob['importe'];
	}


	if( ($pesos_hoy<>[]) && ($monto>=0) )
	{
		$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
	}
	else
		if( ($pesos_hoy==[]) && ($monto>=0) )
		{
			$total_gral_pesos = ($saldo_anterior + $monto);
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

	// totales generales para caja destino

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

		if($datos_cob['importe']  > 0){
			$monto2 = $datos_cob['importe'];
		}

		// cargo los totales generales

		if( ($pesos_hoy2<>[]) && ($monto2>=0) )
		{
			$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2);
		}
		else
			if( ($pesos_hoy2==[]) && ($monto2>=0) )
			{
				$total_gral_pesos2 = ($saldo_anterior2 + $monto2);
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

	// fin caso pesos //
}
else
{
	if($op == 2) // caso dolares
	{ 
		if($caja_origen <> 0)
		{
			Update($caja_origen, $op, $fecha);
			Update($caja_destino, $op, $fecha);
		}
		else 
		{
			$caja_origen = $numero_caja;
			Update($caja_origen, $op, $fecha);
		}

		// totales generales para caja origen

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
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
		$monto = 0;
		$cob = "SELECT importe from cobranza
					WHERE fecha = '$fecha'
					AND numero_caja = '$caja_origen'
					order by numero limit 1";
		$res_cob = mysqli_query($connection, $cob);
		$datos_cob = mysqli_fetch_array($res_cob);
		if($datos_cob['importe']<>[]){
			$monto = $datos_cob['importe'];
		}

		if( ($pesos_hoy<>[]) && ($monto>=0) )
		{
			$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
		}
		else
			if( ($pesos_hoy==[]) && ($monto>=0) )
			{
				$total_gral_pesos = ($saldo_anterior + $monto);
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

		// totales generales para caja destino
		if($caja_destino <> 0)
		{
			//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
			$saldo_anterior2=saldo_ant('pesos',$caja_destino,$fecha);
		    $saldo_anterior_dolares2=saldo_ant('dolares',$caja_destino,$fecha);
		    $saldo_anterior_euros2=saldo_ant('euros',$caja_destino,$fecha);
		    $saldo_anterior_cheques2=saldo_ant('cheques',$caja_destino,$fecha);

			//Consigo total del dia en pesos caja destino
				        
			$pesos_hoy2 = get_total(1,$caja_destino,$fecha);

			//Consigo total del dia en dolares caja destino
				          
			$dolares_hoy2 = get_total(2,$caja_destino,$fecha);

			//Consigo total del dia en euros caja destino
				          
			$euros_hoy2 = get_total(3,$caja_destino,$fecha);

			//Consigo total del dia en cheques caja destino
				          
			$cheques_hoy2 = get_total(4,$caja_destino,$fecha);

			// Obtengo datos de cobranza
			$monto2 = 0;
			$cob = "SELECT importe from cobranza
						WHERE fecha = '$fecha'
						AND numero_caja = '$caja_destino'
						order by numero limit 1";
			$res_cob = mysqli_query($connection, $cob);
			$datos_cob = mysqli_fetch_array($res_cob);

			if($datos_cob['importe'] > 0){
				$monto2 = $datos_cob['importe'];
			}

			if( ($pesos_hoy2<>[]) && ($monto2>=0) )
			{
				$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2);
			}
			else
				if( ($pesos_hoy2==[]) && ($monto2>=0) )
				{
					$total_gral_pesos2 = ($saldo_anterior2 + $monto2);
				}
				else $total_gral_pesos2 = $saldo_anterior2;

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
			// fin caso dolares //

	}
	else // caso euros
	
		if($op == 3)
		{
			if($caja_origen <> 0)
			{
				Update($caja_origen, $op, $fecha);
				Update($caja_destino, $op, $fecha);
			}
			else 
			{
				$caja_origen = $numero_caja;
				Update($caja_origen, $op, $fecha);
			}

			// totales generales para caja origen

			//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
			$saldo_anterior=saldo_ant('pesos',$caja_origen,$fecha);
		    $saldo_anterior_dolares=saldo_ant('dolares',$caja_origen,$fecha);
		    $saldo_anterior_euros=saldo_ant('euros',$caja_origen,$fecha);
		    $saldo_anterior_cheques=saldo_ant('cheques',$caja_origen,$fecha);

			//Consigo total del dia en pesos caja destino
				        
			$pesos_hoy = get_total(1,$caja_origen,$fecha);

			//Consigo total del dia en dolares caja destino
				          
			$dolares_hoy = get_total(2,$caja_origen,$fecha);

			//Consigo total del dia en euros caja destino
				          
			$euros_hoy = get_total(3,$caja_origen,$fecha);

			//Consigo total del dia en cheques caja destino
				          
			$cheques_hoy = get_total(4,$caja_origen,$fecha);

			// Obtengo datos de cobranza
			$cob = "SELECT importe from cobranza
					WHERE fecha = '$fecha'
					AND numero_caja = '$caja_origen'
					order by numero limit 1";
			$res_cob = mysqli_query($connection, $cob);
			$datos_cob = mysqli_fetch_array($res_cob);
			$monto = $datos_cob['importe'];

			if( ($pesos_hoy<>[]) && ($monto>=0) )
			{
				$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
			}
			else{
				if( ($pesos_hoy==[]) && ($monto>=0) )
				{
					$total_gral_pesos = ($saldo_anterior + $monto);
				}
				else $total_gral_pesos = $saldo_anterior;
			}
				
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
			else
			{
				$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				('','$caja_origen','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

				$result_insert = mysqli_query($connection, $insert);
			}

			// totales generales para caja destino

			if($caja_destino <> 0)
			{
				
				// obtenemos Saldo anterior en pesos, dolares y euros de caja de totales generales
				$saldo_anterior2 = saldo_ant('pesos',$caja_destino,$fecha);
				$saldo_anterior_dolares2 = saldo_ant('dolares',$caja_destino,$fecha);
				$saldo_anterior_euros2 = saldo_ant('euros', $caja_destino, $fecha);
				$saldo_anterior_cheques2 = saldo_ant('cheques', $caja_destino, $fecha);

				//Consigo total del dia en pesos caja destino
					        
				$pesos_hoy2 = get_total(1,$caja_destino,$fecha);

				//Consigo total del dia en dolares caja destino
					          
				$dolares_hoy2 = get_total(2,$caja_destino,$fecha);

				//Consigo total del dia en euros caja destino
					          
				$euros_hoy2 = get_total(3,$caja_destino,$fecha);

				//Consigo total del dia en cheques caja destino
					          
				$cheques_hoy2 = get_total(4,$caja_destino,$fecha);

				// Obtengo datos de cobranza
				$monto2 = 0;
				$cob = "SELECT importe from cobranza
						WHERE fecha = '$fecha'
						AND numero_caja = '$caja_destino'
						order by numero limit 1";
				$res_cob = mysqli_query($connection, $cob);
				$datos_cob = mysqli_fetch_array($res_cob);

				if($datos_cob['importe'] > 0)
				{
					$monto2 = $datos_cob['importe'];
				}


				if( ($pesos_hoy2<>[]) && ($monto2>=0) )
				{
					$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2);
				}
				else
					if( ($pesos_hoy2==[]) && ($monto2>=0) )
					{
						$total_gral_pesos2 = ($saldo_anterior2 + $monto2);
					}
					else $total_gral_pesos2 = $saldo_anterior2;
			
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
				else
				{
					$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
					('','$caja_destino','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

					$result_insert = mysqli_query($connection, $insert);
				}
			}
		}
		else
			// caso cheques
			if($caja_origen <> 0)
			{
				Update($caja_origen, $op, $fecha);
				Update($caja_destino, $op, $fecha);
			}
			else 
			{
				$caja_origen = $numero_caja;
				Update($caja_origen, $op, $fecha);
			}

			// obtenemos Saldo anterior en pesos, dolares y euros de caja de totales generales
			$saldo_anterior = saldo_ant('pesos',$caja_origen,$fecha);
			$saldo_anterior_dolares = saldo_ant('dolares',$caja_origen,$fecha);
			$saldo_anterior_euros = saldo_ant('euros', $caja_origen, $fecha);
			$saldo_anterior_cheques = saldo_ant('cheques', $caja_origen, $fecha);

			//Consigo total del dia en pesos caja destino
					        
			$pesos_hoy = get_total(1,$caja_origen,$fecha);

			//Consigo total del dia en dolares caja destino
					          
			$dolares_hoy = get_total(2,$caja_origen,$fecha);

			//Consigo total del dia en euros caja destino
					          
			$euros_hoy = get_total(3,$caja_origen,$fecha);

			//Consigo total del dia en cheques caja destino
					          
			$cheques_hoy = get_total(4,$caja_origen,$fecha);

			// Obtengo datos de cobranza
			$cob = "SELECT importe from cobranza
					WHERE fecha = '$fecha'
					AND numero_caja = '$caja_origen'
					order by numero limit 1";
			$res_cob = mysqli_query($connection, $cob);
			$datos_cob = mysqli_fetch_array($res_cob);
			$monto = $datos_cob['importe'];

			// cargo los totales generales
			/*if($pesos_hoy == 0.00)
				$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
			else $total_gral_pesos = ($saldo_anterior + $pesos_hoy);*/

			if( ($pesos_hoy<>[]) && ($monto>=0) )
			{
				$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
			}
			else{
				if( ($pesos_hoy==[]) && ($monto>=0) )
				{
					$total_gral_pesos = ($saldo_anterior + $monto);
				}
				else $total_gral_pesos = $saldo_anterior;
			}
				
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
			else
			{
				$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				('','$caja_origen','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

				$result_insert = mysqli_query($connection, $insert);
			}

			// totales generales para caja destino

			if($caja_destino <> 0)
			{
				// consigo total de pesos
				$pesos_hoy2 = get_tota(1, $caja_destino, $fecha);
				// consigo total dolares
				$dolares_hoy2 = get_total(2, $caja_destino, $fecha);
				// consigo total euros
				$euros_hoy2 = get_tota(3, $caja_destino, $fecha);
				// consigo total cheques
				$cheques_hoy2 = get_total(4, $caja_destino, $fecha);

				// obtenemos Saldo anterior en pesos, dolares y euros de caja de totales generales
				$saldo_anterior2 = saldo_ant('pesos',$caja_destino,$fecha);
				$saldo_anterior_dolares2 = saldo_ant('dolares',$caja_destino,$fecha);
				$saldo_anterior_euros2 = saldo_ant('euros', $caja_destino, $fecha);
				$saldo_anterior_cheques2 = saldo_ant('cheques', $caja_destino, $fecha);

				// Obtengo datos de cobranza
				$monto2 = 0;
				$cob = "SELECT importe from cobranza
						WHERE fecha = '$fecha'
						AND numero_caja = '$caja_destino'
						order by numero limit 1";
				$res_cob = mysqli_query($connection, $cob);
				$datos_cob = mysqli_fetch_array($res_cob);

				if($datos_cob['importe'] > 0)
				{
					$monto2 = $datos_cob['importe'];
				}


				if( ($pesos_hoy2<>[]) && ($monto2>=0) )
				{
					$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2);
				}
				else
					if( ($pesos_hoy2==[]) && ($monto2>=0) )
					{
						$total_gral_pesos2 = ($saldo_anterior2 + $monto2);
					}
					else $total_gral_pesos2 = $saldo_anterior2;
			
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
				else
				{
					$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
					('','$caja_destino','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

					$result_insert = mysqli_query($connection, $insert);
				}
			}		
		
	 
}

function Update(int $numero_caja, int $op, $fecha)
{
	$monto = 0;
	include('conexion.php');

	if($op == 1)
	{
		$moneda = 'pesos';
		//$set_moneda = 'pesos';
		
		// Consigo datos de cobranza diaria
		$cob = "SELECT importe from cobranza
				WHERE fecha = '$fecha'
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res_cob = mysqli_query($connection, $cob);
		$datos_cob = mysqli_fetch_array($res_cob);

		if($datos_cob['importe'] <> [])
			$monto = $datos_cob['importe'];
	}
	else
	{ 
		if($op == 2){
			$moneda = 'dolares';
			//$set_moneda = 'dolares';
		}
		else{
			if($op == 3)
				$moneda = 'euros';
			else $moneda = 'cheques';
			//$set_moneda = 'euros';
		} 
	}
	// vacio la columna con la moneda indicada
	$query_empty = "UPDATE caja_gral SET $moneda = 0 
				   where numero_caja = '$numero_caja' 
				   AND operacion = '$op'
				   AND fecha = '$fecha'";
	$result_empty = mysqli_query($connection, $query_empty);

	$qr = "SELECT numero FROM caja_gral 
		  where numero_caja = '$numero_caja' 
		  AND operacion = '$op'
		  AND fecha = '$fecha'";
	$res = mysqli_query($connection, $qr); // busqueda de numeros
	$cantidad = $res->num_rows; // cantidad de numeros obtenidos

	$k = 0;
	$lista = array();
	while ($r = mysqli_fetch_array($res))
	{
		
		$lista[$k] = $r['numero']; // obtengo una lista con los numeros
		$k++;	
	}

	$inicial = $lista[0];

	$query_get_data = "SELECT * FROM caja_gral 
					   where numero_caja = '$numero_caja'
					   and operacion = '$op' 
					   AND fecha = '$fecha'
					   ORDER BY numero LIMIT 1"; // datos para actualizar columna con la moneda indicada (primer fila)
	$result_get_data = mysqli_query($connection, $query_get_data);
	$data = mysqli_fetch_array($result_get_data);

	// cactualizamos los campos con cobranza diaria
	if($monto > 0.00)
	{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE caja_gral SET $moneda = '$monto' + '$pde'  
					WHERE numero = '$inicial'
					AND numero_caja = '$numero_caja' ";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE caja_gral SET $moneda = '$monto' - '$pde'  WHERE numero = '$inicial'
				 	AND numero_caja = '$numero_caja'";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
			}
	}
	else{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE caja_gral SET $moneda = 0 + '$pde' 
				WHERE numero = '$inicial'
				AND numero_caja = '$numero_caja' ";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE caja_gral SET $moneda = (-1) * '$pde'  
					WHERE numero = '$inicial'
					AND numero_caja = '$numero_caja' ";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
			}
	}

	for($i=0; $i <= $cantidad; $i++)
	{
		if(($i+1) <= $cantidad)
		{
			$n = $lista[$i+1]; // fila inferior
			$m = $lista[$i]; // fila suoerior
			$qry = "SELECT * FROM caja_gral
					WHERE numero = '$n'
					and numero_caja = '$numero_caja'";
			$res = mysqli_query($connection,$qry);
			$dta = mysqli_fetch_array($res);
			$ingreso = $dta['ingreso'];
			$egreso = $dta['egreso'];
				
			if($ingreso > 0)
			{
				$qry = "SELECT * FROM caja_gral
						WHERE numero = '$m'
						and numero_caja = '$numero_caja'";
				$res = mysqli_query($connection,$qry);
				$dta = mysqli_fetch_array($res);
				$pde = $dta[$moneda];
					
				$update = "UPDATE caja_gral SET $moneda = '$pde' + '$ingreso' 
							WHERE numero = '$n' 
							AND operacion = '$op'
							AND fecha = '$fecha'";
				$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
			}
			else
				if($egreso > 0)
				{
					$qry = "SELECT * FROM caja_gral 
					WHERE numero = '$m'
					and numero_caja = '$numero_caja'";
					$res = mysqli_query($connection,$qry);
					$dta = mysqli_fetch_array($res);
					$pde = $dta[$moneda];
						
					$update = "UPDATE caja_gral SET $moneda = '$pde' - '$egreso'
								WHERE numero = '$n'
								AND operacion = '$op'
								AND fecha = '$fecha'";
					$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
				}
		}		
			
	}
}


?>