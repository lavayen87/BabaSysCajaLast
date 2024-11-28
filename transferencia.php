<?php
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$rol = $_SESSION['rol'];
	$numero_caja_origen = $_SESSION['numero_caja'];
}

include('conexion.php');

include('funciones.php');

if(isset($_POST['lista_ids']))
{
	$lista_ids = $_POST['lista_ids']; //lista de id de cheques 
}
$moneda = $_POST['moneda'];
$detalle = $_POST['detalle'];
$numero_caja_destino = $_POST['numero_caja_destino'];
$nombre_caja_destino = $_POST['caja_destino'];
$cantidad = $_POST['cantidad'];

$fecha = date('Y-m-d');
$saldo_anterior = 0.00;
$saldo_anterior_dolares = 0.00;
$saldo_anterior_euros = 0.00;
$saldo_anterior_cheques = 0;
$monto_ser = 0;
$monto_serv2 = 0;
$ing_servicio = 0;
$ing_servicio2 = 0;
$monto = 0.00;
$monto2 = 0.00;
$saldo_anterior2 = 0.00; 
$saldo_anterior_dolares2 =0;
$saldo_anterior_euros2 = 0;
$saldo_anterior_cheques2 = 0;
$monto2 = 0.00;
$ultimo_cobro =0;
$ultimo_cobro2=0;
$total_gral = 0.00;
$pesos_a_sumar = 0;
$pesos_a_restar = 0;
$cheques_a_sumar = 0;
$cheques_a_restar = 0;

switch ($moneda)
{	   		     
	//Transferir Pesos
	case 'pesos':

		if($numero_caja_origen == 3) //caja banco
		{
			$query = "SELECT pesos 
			  from caja_gral
			  where numero_caja = '$numero_caja_origen'
			  AND operacion = 1
			  and anulado = 0
			  order by numero desc limit 1"; 
		}
		else
		{
			$query = "SELECT pesos 
			  from caja_gral
			  where numero_caja = '$numero_caja_origen'
			  AND operacion = 1
			  and anulado = 0
			  AND fecha = '$fecha'
			  order by numero desc limit 1"; 
		}
		
		$result = mysqli_query($connection, $query);	
		$datos = mysqli_fetch_array($result); // obtengo pesos a transferir
		$pesos = $datos['pesos'];

		////////////////////////////////////////////
		//consigo cobraza
		$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_origen'
					order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe'];  
		 
		// consigo ingreso por servicios
		$qry_serv = "SELECT  importe from ingresos_servicios
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_origen'
				order by id limit 1";
		$res_serv = mysqli_query($connection, $qry_serv);
		$datos_serv = mysqli_fetch_array($res_serv);

		if($datos_serv<>[])
		{
			$ing_servicio = $datos_serv['importe'];
		}


		/*if($ultimo_cobro > 0.00)
		{
			if($pesos == [])
			{
				$pesos_a_restar = $ultimo_cobro + $ing_servicio - $cantidad;
			}
			else
			{
				$pesos_a_restar = $pesos - $cantidad;
			}
		}
		else
		{
			if($pesos == [])
			{
				$pesos_a_restar =  $ing_servicio + ((-1)*$cantidad);
			}
			else
			{
				$pesos_a_restar = $pesos - $cantidad;
			}
		}*/

		if($ultimo_cobro > 0.00)
		{
			if($pesos == [])
			{
				$pesos_a_restar = ($ultimo_cobro + $ing_servicio - $cantidad);
			}
			else
			{
				$pesos_a_restar = ($pesos - $cantidad);
			}
		}
		else
		{
			if($ing_servicio > 0.00)
			{
				if($pesos == [])
				{
					$pesos_a_restar = ($ing_servicio - $cantidad);
				}
				else
				{
					$pesos_a_restar = ($pesos - $cantidad);
				}
			}
			else{
				if($pesos == [])
				{
					$pesos_a_restar = (-1)*$cantidad;
				}
				else
				{
					$pesos_a_restar = ($pesos - $cantidad); 
				}
			}
		}
		//////////////////////////////////////////////

		/* codigo para que impacte la transferencia en mi caja */
		$pesos_a_trasferir = $cantidad;
                  
        $det = limitar_cadena("TR ".$detalle,27); 
           
		// 'Transferencia en $moneda'	

		$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
		('','$numero_caja_origen','$fecha','$fecha','$det',0,0,'$cantidad','$pesos_a_restar',0,0,0,1,0)";
		$result_inert_mc = mysqli_query($connection, $insert_mc);

		/* consigo numero de operacion para agregar a la transferencia */
	    $get_num = "SELECT numero FROM caja_gral 
	    		WHERE numero_caja = '$numero_caja_origen'
	    		AND operacion = 1
	    		AND fecha = '$fecha' 
	        	ORDER BY numero DESC LIMIT 1";
	    $res_get_num = mysqli_query($connection, $get_num);
	    $array = mysqli_fetch_array($res_get_num);
	    $num = $array['numero'];

		/*--- ENVIO DE TRANFERENCIA A CAJA DESTINO ---*/

		$qry = "SELECT pesos 
				from caja_gral	
				where numero_caja = '$numero_caja_destino'
				AND operacion = 1
				AND anulado = 0
				AND fecha = '$fecha'
				order by numero desc limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$pesos2 = $datos['pesos'];

		// consigo cobranaza en caja destino
		$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_destino'
					order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro2 = $datos['importe'];
		
		// consigo ingreso por servicios
		$qry_serv = "SELECT  importe from ingresos_servicios
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_destino'
				order by id limit 1";
		$res_serv = mysqli_query($connection, $qry_serv);
		$datos_serv = mysqli_fetch_array($res_serv);

		if($datos_serv<>[])
		{
			$ing_servicio2 = $datos_serv['importe'];
		}

		/*if($ultimo_cobro2 > 0.00)
		{
			if($pesos2 == [])
			{
				$pesos_a_sumar = ($ultimo_cobro2 + $ing_servicio2 + $cantidad);
			}
			else
			{
				$pesos_a_sumar = ($pesos2 + $cantidad);
			}
		}
		else
		{
			if($pesos2 == [])
			{
				$pesos_a_sumar = ($ing_servicio2 + $cantidad);
			}
			else
			{
				$pesos_a_sumar = ($pesos2 + $cantidad);
			}
		}*/
		if($ultimo_cobro2 > 0.00)
		{
			if($pesos2 == [])
			{
				$pesos_a_sumar = ($ultimo_cobro2 + $ing_servicio2 + $cantidad);
			}
			else
			{
				$pesos_a_sumar = ($pesos2 + $cantidad);
			}
		}
		else
		{
			if($ing_servicio2 > 0.00)
			{
				if($pesos2 == [])
				{
					$pesos_a_sumar = ($ing_servicio2 + $cantidad);
				}
				else
				{
					$pesos_a_sumar = ($pesos2 + $cantidad);
				}
			}
			else{
				if($pesos2 == [])
				{
					$pesos_a_sumar = (1)*$cantidad;
				}
				else
				{
					$pesos_a_sumar = ($pesos2 + $cantidad);
				}
			}
		}
		
		// Cargo la transferencia en caja destino	

		$insert_caja_destino = "INSERT IGNORE INTO  caja_gral VALUES 
		('$num','$numero_caja_destino','$fecha','$fecha','$det',0,'$cantidad',0,'$pesos_a_sumar',0,0,0,1,0)";
	    //$result_insert_caja_destino = mysqli_query($connection, $insert_caja_destino);
		mysqli_query($connection, $insert_caja_destino);

	    // Cargo la transferencia en tabla "transferencias"
	     $transfer = "INSERT IGNORE INTO transferencias VALUES 
		('$num','$fecha','$fecha','$rol','$numero_caja_origen','$nombre_caja_destino','$numero_caja_destino','$moneda','$pesos_a_trasferir',0,0,0,'Recibido','$detalle')"; 
		//$result_transfer =  mysqli_query($connection, $transfer);
		mysqli_query($connection, $transfer);


		// cargamos totales generales en caja destino

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales

		$saldo_anterior = saldo_ant('pesos',$numero_caja_destino,$fecha);
	    $saldo_anterior_dolares = saldo_ant('dolares',$numero_caja_destino,$fecha);
	    $saldo_anterior_euros = saldo_ant('euros',$numero_caja_destino,$fecha);
	    $saldo_anterior_cheques = saldo_ant('cheques',$numero_caja_destino,$fecha);

		//Consigo total del dia en pesos desde mi caja
		        
		$pesos_hoy = get_total(1,$numero_caja_destino,$fecha);

		//Consigo total del dia en dolares desde mi caja
		          
		$dolares_hoy = get_total(2,$numero_caja_destino,$fecha);

		//Consigo total del dia en euros desde mi caja
		          
		$euros_hoy = get_total(3,$numero_caja_destino,$fecha);

		//Consigo total del dia en cheques desde mi caja
		          
		$cheques_hoy = get_total(4,$numero_caja_destino,$fecha);

		//Consigo cobranza

		$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_destino'
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
		$qry2 = "SELECT  importe from ingresos_servicios
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_destino'
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
							and numero_caja = '$numero_caja_destino'
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
							WHERE numero_caja = '$numero_caja_destino'
							and fecha = '$fecha'
							and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja_destino','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

			$result_insert = mysqli_query($connection, $insert);
		}

		// cargo totales generales en mi caja

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales

		$saldo_anterior2 = saldo_ant('pesos',$numero_caja_origen,$fecha);
	    $saldo_anterior_dolares2 = saldo_ant('dolares',$numero_caja_origen,$fecha);
	    $saldo_anterior_euros2 = saldo_ant('euros',$numero_caja_origen,$fecha);
	    $saldo_anterior_cheques2 = saldo_ant('cheques',$numero_caja_origen,$fecha);
		
		
		// consigo total pesos, dolares, euros y cheques del dia
		$pesos_hoy2 = get_total(1,$numero_caja_origen,$fecha);
		$dolares_hoy2 = get_total(2,$numero_caja_origen,$fecha);
		$euros_hoy2 = get_total(3,$numero_caja_origen,$fecha);
		$cheques_hoy2 = get_total(4,$numero_caja_origen,$fecha);
		
		// consigo cobranza
		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_origen'
				order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe']; 
		
		if($ultimo_cobro<>[]){
			$monto2 = $ultimo_cobro;
		}

		// ultimo ingreso por servicios
		$ultimo_ingreso2 = 0;
		$qry2 = "SELECT  importe from ingresos_servicios
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_origen'
				order by id DESC limit 1";
		$res2 = mysqli_query($connection, $qry2);
		$datos_ingresos = mysqli_fetch_array($res2);
		$ultimo_ingreso2 = $datos_ingresos['importe'];

		if($ultimo_ingreso2<>[])
		{
			$monto_serv2 = $ultimo_ingreso2;
		}

		if( ($pesos_hoy2<>[]) && ($monto2>=0) && ($monto_serv2>=0))
		{
			$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2 + $monto_serv2);
		}
		else
			if( ($pesos_hoy2==[]) && ($monto2>=0) && ($monto_serv2>=0) )
			{
				$total_gral_pesos2 = ($saldo_anterior2 + $monto2 + $monto_serv2);
			}
			else $total_gral_pesos2 = $saldo_anterior2;


		$total_gral_dolares2 = ($saldo_anterior_dolares2 + $dolares_hoy2);
		$total_gral_euros2 = ($saldo_anterior_euros2 + $euros_hoy2);
		$total_gral_cheques2 = ($saldo_anterior_cheques2 + $cheques_hoy2);
		
		$qry = "SELECT * from caja_gral_temp
				where operacion = 1 
				and numero_caja = '$numero_caja_origen'
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
					WHERE numero_caja = '$numero_caja_origen'
					and fecha = '$fecha'
					and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja_origen','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

			$result_insert = mysqli_query($connection, $insert);
		}

		$saldo_anterior = 0.00;
		$saldo_anterior_dolares = 0.00;
		$saldo_anterior_euros = 0.00;
		$saldo_anterior_cheques = 0;

		$monto = 0.00;
		$monto2 = 0.00;
		$saldo_anterior2 = 0.00; 
		$saldo_anterior_dolares2 =0;
		$saldo_anterior_euros2 = 0;
		$saldo_anterior_cheques2 = 0;

		$monto2 = 0.00;
		$ultimo_cobro =0;
		$ultimo_cobro2=0;
		$total_gral = 0.00;
		$pesos_a_sumar = 0;
		$pesos_a_restar = 0;

		echo 'Transferencia realizada';
				
		break;

		/////////////////////////////////////////////////////////////////////

	// Transferir Dolares
	case 'dolares':

		$query = "SELECT dolares from caja_gral
					WHERE numero_caja = '$numero_caja_origen'
					AND operacion = 2
					and anulado = 0
					AND fecha = '$fecha' 
					order by numero desc limit 1";    
		$result = mysqli_query($connection, $query);	
		$datos = mysqli_fetch_array($result); // consigo dolares a transferir

		if($datos['dolares'] == [])
		{
			$dolares_a_restar = (-1)*$cantidad;
		}
		else
		{
			$dolares_a_restar = ($datos['dolares'] - $cantidad);
		}

		$dolares_a_trasferir = $cantidad;

		$det = limitar_cadena("TR ".$detalle,27); 

		/* codigo para que impacte la transferencia en mi caja*/
		$insert_mc = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja_origen','$fecha','$fecha','$det',0,0,'$cantidad',0,'$dolares_a_restar',0,0,2,0)";
		$result_inert_mc = mysqli_query($connection, $insert_mc);

		/* consigo numero de operacion para agregar a la transferencia */
		$get_num = "SELECT numero FROM caja_gral
			WHERE numero_caja = '$numero_caja_origen' 
			ORDER BY numero DESC LIMIT 1";
		$res_get_num = mysqli_query($connection, $get_num);
		$array = mysqli_fetch_array($res_get_num);
		$num = $array['numero'];
			
		/* ENVIO DE TRANFERENCIA A CAJA DESTINO / TEMPORAL !!! */

		$qry = "SELECT dolares from caja_gral	
			WHERE numero_caja = '$numero_caja_destino'
			AND operacion = 2
			AND anulado = 0
			AND fecha = '$fecha'
			order by numero desc limit 1";
		$res = mysqli_query($connection, $qry);
		$dolares = mysqli_fetch_array($res);

		if($dolares['dolares'] == [])
		{
			$dolares_a_sumar = (0 + $cantidad);
		}
		else
		{
			$dolares_a_sumar = ($dolares['dolares'] + $cantidad);
		}
			

		$insert_caja_destino = "INSERT IGNORE INTO  caja_gral VALUES 
		('$num','$numero_caja_destino','$fecha','$fecha','$det',0,'$dolares_a_trasferir',0,0,'$dolares_a_sumar',0,0,2,0)";
		$result_insert_caja_destino = mysqli_query($connection, $insert_caja_destino);

		//cargo la transferencia en tabla "transferencias"
		$transfer = "INSERT IGNORE INTO transferencias VALUES 
			('$num','$fecha','$fecha','$rol','$numero_caja_origen','$nombre_caja_destino','$numero_caja_destino','$moneda',0,'$dolares_a_trasferir',0,0,'Recibido','$detalle')"; 
		$result_transfer =  mysqli_query($connection, $transfer);


		// cargamos totales generales en caja destino

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
		$saldo_anterior=saldo_ant('pesos',$numero_caja_destino,$fecha);
	    $saldo_anterior_dolares=saldo_ant('dolares',$numero_caja_destino,$fecha);
	    $saldo_anterior_euros=saldo_ant('euros',$numero_caja_destino,$fecha);
	    $saldo_anterior_cheques=saldo_ant('cheques',$numero_caja_destino,$fecha);
		
		
		// consigo total pesos, dolares, euros y cheques del dia
		$pesos_hoy = get_total(1,$numero_caja_destino,$fecha);
		$dolares_hoy = get_total(2,$numero_caja_destino,$fecha);
		$euros_hoy = get_total(3,$numero_caja_destino,$fecha);
		$cheques_hoy = get_total(4,$numero_caja_destino,$fecha);

		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_destino'
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
				AND numero_caja = '$numero_caja_destino'
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

		// cargo los totales generales
		$qry = "SELECT * from caja_gral_temp
					where operacion = 1 
					and numero_caja = '$numero_caja_destino'
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
					WHERE numero_caja = '$numero_caja_destino'
					and fecha = '$fecha'
					and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else
		{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja_destino','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

			$result_insert = mysqli_query($connection, $insert);
		}
		
		
		// cargo totales generales en mi caja

		$saldo_anterior2=saldo_ant('pesos',$numero_caja_origen,$fecha);
	    $saldo_anterior_dolares2=saldo_ant('dolares',$numero_caja_origen,$fecha);
	    $saldo_anterior_euros2=saldo_ant('euros',$numero_caja_origen,$fecha);
	    $saldo_anterior_cheques2=saldo_ant('cheques',$numero_caja_origen,$fecha);
		
		// consigo total pesos, dolares, euros y cheques del dia
		$pesos_hoy2 = get_total(1,$numero_caja_origen,$fecha);
		$dolares_hoy2 = get_total(2,$numero_caja_origen,$fecha);
		$euros_hoy2 = get_total(3,$numero_caja_origen,$fecha);
		$cheques_hoy2 = get_total(4,$numero_caja_origen,$fecha);

		// consigo cobranza en mi caja
		$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_origen'
					order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe']; 

		if($ultimo_cobro<>[]){
			$monto2 = $ultimo_cobro;
		}

		// ultimo ingreso por servicios
		$ultimo_ingreso2 = 0;
		$qry2 = "SELECT  importe from ingresos_servicios
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_origen'
				order by id DESC limit 1";
		$res2 = mysqli_query($connection, $qry2);
		$datos_ingresos2 = mysqli_fetch_array($res2);
		$ultimo_ingreso2 = $datos_ingresos2['importe'];

		if( $ultimo_ingreso2<>[])
		{ 
			$monto_serv2 = $ultimo_ingreso2;
		}

		if( ($pesos_hoy2<>[]) && ($monto2>=0) && ($monto_serv2>=0) )
		{
			$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2 + $monto_serv2);
		}
		else
			if( ($pesos_hoy2==[]) && ($monto2>=0) && ($monto_serv2>=0) )
			{
				$total_gral_pesos2 = ($saldo_anterior2 + $monto2 + $monto_serv2);
			}
			else $total_gral_pesos2 = $saldo_anterior2;

		$total_gral_dolares2 = ($saldo_anterior_dolares2 + $dolares_hoy2);
		$total_gral_euros2 = ($saldo_anterior_euros2 + $euros_hoy2);
		$total_gral_cheques2 = ($saldo_anterior_cheques2 + $cheques_hoy2);

		// cargo los totales generales
		$qry = "SELECT * from caja_gral_temp
				where operacion = 1 
				and numero_caja = '$numero_caja_origen'
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
							WHERE numero_caja = '$numero_caja_origen'
							and fecha = '$fecha'
							and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja_origen','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

			$result_insert = mysqli_query($connection, $insert);
		}

		$saldo_anterior = 0.00;
		$saldo_anterior_dolares = 0.00;
		$saldo_anterior_euros = 0.00;
		$saldo_anterior_cheques = 0;
		$monto_serv2 = 0.00;
		$monto = 0.00;
		$monto2 = 0.00;
		$saldo_anterior2 = 0.00; 
		$saldo_anterior_dolares2 =0;
		$saldo_anterior_euros2 = 0;
		$saldo_anterior_cheques2 = 0;

		$monto2 = 0.00;
		$ultimo_cobro =0;
		$ultimo_cobro2=0;
		$total_gral = 0.00;
		$pesos_a_sumar = 0;
		$pesos_a_restar = 0;

		echo 'Transferencia realizada';
			
		break;

		/////////////////////////////////////////////////////////////////////////

		// Transferir Euros
	case 'euros':

			$query = "SELECT euros from caja_gral
				WHERE numero_caja = '$numero_caja_origen'
				AND operacion = 3
				and anulado = 0
				AND fecha = '$fecha' 
				order by numero desc limit 1";    
			$result = mysqli_query($connection, $query);	
			$datos = mysqli_fetch_array($result); // consigo euros a transferir

			if($datos['euros'] == [])
			{
				$euros_a_restar = (-1)*$cantidad;
			}
			else{
				$euros_a_restar = ($datos['euros'] - $cantidad);
			}

			$euros_a_trasferir = $cantidad;

			$det = limitar_cadena("TR ".$detalle,27); 

			/* codigo para que impacte la transferencia en mi caja*/
			$insert_mc = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja_origen','$fecha','$fecha','$det',0,0,'$cantidad',0,0,'$euros_a_restar',0,3,0)";
			$result_inert_mc = mysqli_query($connection, $insert_mc);

			/* consigo numero de operacion para agregar a la transferencia */
			$get_num = "SELECT numero FROM caja_gral
				WHERE numero_caja = '$numero_caja_origen' 
				ORDER BY numero DESC LIMIT 1";
			$res_get_num = mysqli_query($connection, $get_num);
			$array = mysqli_fetch_array($res_get_num);
			$num = $array['numero'];
				
			/* ENVIO DE TRANFERENCIA A CAJA DESTINO / TEMPORAL !!! */

			$qry = "SELECT euros from caja_gral	
				WHERE numero_caja = '$numero_caja_destino'
				AND operacion = 3
				AND anulado = 0
				AND fecha = '$fecha'
				order by numero desc limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);

			if($datos['euros'] == [])
			{
				$euros_a_sumar = (0 + $cantidad);
			}
			else{
				$euros_a_sumar = ($datos['euros'] + $cantidad);
			}
			
			// transfiero a caja destino
			$insert_caja_destino = "INSERT IGNORE INTO  caja_gral VALUES 
			('$num','$numero_caja_destino','$fecha','$fecha','$det',0,'$euros_a_trasferir',0,0,0,'$euros_a_sumar',0,3,0)";
			$result_insert_caja_destino = mysqli_query($connection, $insert_caja_destino);

			// cargamos totales generales en caja destino

			//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
			$saldo_anterior=saldo_ant('pesos',$numero_caja_destino,$fecha);
		    $saldo_anterior_dolares=saldo_ant('dolares',$numero_caja_destino,$fecha);
		    $saldo_anterior_euros=saldo_ant('euros',$numero_caja_destino,$fecha);
		    $saldo_anterior_cheques=saldo_ant('cheques',$numero_caja_destino,$fecha);
			
			// consigo total pesos, dolares, euros y cheques del dia
			$pesos_hoy = get_total(1,$numero_caja_destino,$fecha);
			$dolares_hoy = get_total(2,$numero_caja_destino,$fecha);
			$euros_hoy = get_total(3,$numero_caja_destino,$fecha);
			$cheques_hoy = get_total(4,$numero_caja_destino,$fecha);

			$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_destino'
					order by numero limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);
			$ultimo_cobro = $datos['importe'];

			if($ultimo_cobro<>[]){ 
				$monto = $ultimo_cobro;
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

			// cargo los totales generales
			$qry = "SELECT * from caja_gral_temp
						where operacion = 1 
						and numero_caja = '$numero_caja_destino'
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
						WHERE numero_caja = '$numero_caja_destino'
						and fecha = '$fecha'
						and operacion = 1";
				$res = mysqli_query($connection, $set);
			}
			else{
				$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				('','$numero_caja_destino','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

				$result_insert = mysqli_query($connection, $insert);
			}
			/*------------*/
			//cargo la transferencia en tabla "transferencias"
			$transfer = "INSERT IGNORE INTO transferencias VALUES 
				('$num','$fecha','$fecha','$rol','$numero_caja_origen','$nombre_caja_destino','$numero_caja_destino','$moneda',0,0,'$euros_a_trasferir',0,'Recibido','$detalle')"; // Cambiar estado y fecha
			$result_transfer =  mysqli_query($connection, $transfer);

			/*------------------------------*/

			// cargo totales generales en mi caja

			$saldo_anterior2=saldo_ant('pesos',$numero_caja_origen,$fecha);
		    $saldo_anterior_dolares2=saldo_ant('dolares',$numero_caja_origen,$fecha);
		    $saldo_anterior_euros2=saldo_ant('euros',$numero_caja_origen,$fecha);
		    $saldo_anterior_cheques2=saldo_ant('cheques',$numero_caja_origen,$fecha);
			
			// consigo total pesos, dolares, euros y cheques del dia
			$pesos_hoy2 = get_total(1,$numero_caja_origen,$fecha);
			$dolares_hoy2 = get_total(2,$numero_caja_origen,$fecha);
			$euros_hoy2 = get_total(3,$numero_caja_origen,$fecha);
			$cheques_hoy2 = get_total(4,$numero_caja_origen,$fecha);

			$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_origen'
					order by numero limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);
			$ultimo_cobro = $datos['importe'];

			if($ultimo_cobro<>[]){ 
				$monto2 = $ultimo_cobro;
			}

			

			// ultimo ingreso por servicios
			$ultimo_ingreso2 = 0;
			$qry2 = "SELECT  importe from ingresos_servicios
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_origen'
					order by id DESC limit 1";
			$res2 = mysqli_query($connection, $qry2);
			$datos_ingresos2 = mysqli_fetch_array($res2);
			$ultimo_ingreso2 = $datos_ingresos2['importe'];

			if( $ultimo_ingreso2<>[])
			{ 
				$monto_serv2 = $ultimo_ingreso2;
			}

			if( ($pesos_hoy2<>[]) && ($monto2>=0) && ($monto_serv2>=0) )
			{
				$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2 + $monto_serv2);
			}
			else
				if( ($pesos_hoy2==[]) && ($monto2>=0) && ($monto_serv2>=0) )
				{
					$total_gral_pesos2 = ($saldo_anterior2 + $monto2 + $monto_serv2);
				}
				else $total_gral_pesos2 = $saldo_anterior2;
			
			$total_gral_dolares2 = ($saldo_anterior_dolares2 + $dolares_hoy2);
			$total_gral_euros2 = ($saldo_anterior_euros2 + $euros_hoy2);
			$total_gral_cheques2 = ($saldo_anterior_cheques2 + $cheques_hoy2);


			// cargo los totales generales
			$qry = "SELECT * from caja_gral_temp
					where operacion = 1 
					and numero_caja = '$numero_caja_origen'
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
						WHERE numero_caja = '$numero_caja_origen'
						and fecha = '$fecha'
						and operacion = 1";
				$res = mysqli_query($connection, $set);
			}
			else{
				$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				('','$numero_caja_origen','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

				$result_insert = mysqli_query($connection, $insert);
			}

			$saldo_anterior = 0.00;
			$saldo_anterior_dolares = 0.00;
			$saldo_anterior_euros = 0.00;
			$saldo_anterior_cheques = 0;

			$monto = 0.00;
			$monto2 = 0.00;
			$saldo_anterior2 = 0.00; 
			$saldo_anterior_dolares2 =0;
			$saldo_anterior_euros2 = 0;
			$saldo_anterior_cheques2 = 0;

			$monto2 = 0.00;
			$ultimo_cobro =0;
			$ultimo_cobro2=0;
			$total_gral = 0.00;
			$pesos_a_sumar = 0;
			$pesos_a_restar = 0;

			echo 'Transferencia realizada';	
			break;

	case 'cheques':
			
			// obtengo cheques a transferir
			$query = "SELECT cheques 
					 from caja_gral
					 where numero_caja = '$numero_caja_origen'
					 AND operacion = 4
					 and anulado = 0
					 AND fecha = '$fecha'
					 order by numero desc limit 1"; 

			$result = mysqli_query($connection, $query);	
			$datos = mysqli_fetch_array($result); 
			$cheques = $datos['cheques'];
	
			if($cheques == [])
			{
				$cheques_a_restar =  (-1)*$cantidad;
			}
			else
			{
				$cheques_a_restar = ($cheques - $cantidad);
			}
		

		//////////////////////////////////////////////

		/* codigo para que impacte la transferencia en mi caja */
		$cheques_a_trasferir = $cantidad;
					
		$det = limitar_cadena("TR ".$detalle,27); 	

		$insert_mc = "INSERT INTO caja_gral VALUES 
		('','$numero_caja_origen','$fecha','$fecha','$det',0,0,'$cantidad',0,0,0,'$cheques_a_restar',4,0)";
		$result_inert_mc = mysqli_query($connection, $insert_mc);

		/* consigo numero de operacion para agregar a la transferencia */
		$get_num = "SELECT numero FROM caja_gral 
					WHERE numero_caja = '$numero_caja_origen'
					AND operacion = 4
					AND fecha = '$fecha' 
					ORDER BY numero DESC LIMIT 1";
		$res_get_num = mysqli_query($connection, $get_num);
		$array = mysqli_fetch_array($res_get_num);
		$num = $array['numero'];

		/* ENVIO DE TRANFERENCIA A CAJA DESTINO */
		
		if($numero_caja_destino == 3)
		{
			//conversion a pesos si la caja destino es el banco
			$qry = "SELECT pesos from caja_gral	
				where numero_caja = '$numero_caja_destino'
				AND operacion = 1
				and anulado = 0
				order by numero desc limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);
			$pesos_banco = $datos['pesos'];

			if($pesos_banco == [])
			{
				$pesos_a_sumar = (0 + $cantidad);
			}
			else
			{
				$pesos_a_sumar = ($pesos_banco + $cantidad);
			}

			// Consigo numero de cheque transferido
			$id_cheque = $lista_ids[0];
			$sql = "SELECT num_cheque FROM cheques_cartera 
					WHERE id_cheque = '$id_cheque'";
			$res_sql = mysqli_query($connection,$sql);
			$dato_num_cheque = mysqli_fetch_array($res_sql);
			$num_cheque = $dato_num_cheque['num_cheque'];

			// Cargo la transferencia en caja destino	
			$insert_caja_destino = "INSERT IGNORE INTO  caja_gral VALUES 
			('$num','$numero_caja_destino','$fecha','$fecha','$det','$num_cheque','$cantidad',0,'$pesos_a_sumar',0,0,0,1,0)";
			$result_insert_caja_destino = mysqli_query($connection, $insert_caja_destino);

			// Cargo la transferencia en tabla "transferencias"
			$transfer = "INSERT IGNORE INTO transferencias VALUES 
			('$num','$fecha','$fecha','$rol','$numero_caja_origen','$nombre_caja_destino','$numero_caja_destino','$moneda','$cantidad',0,0,0,'Recibido','$detalle')"; 
			$result_transfer =  mysqli_query($connection, $transfer);


		}
		else // caja distinta de banco
		{
			$qry = "SELECT cheques from caja_gral	
					where numero_caja = '$numero_caja_destino'
					AND operacion = 4
					anulado = 0
					AND fecha = '$fecha'
					order by numero desc limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);
			$cheques2 = $datos['cheques'];


		
			if($cheques2 == [])
			{
				$cheques_a_sumar = (0 + $cantidad);
			}
			else
			{
				$cheques_a_sumar = ($cheques2 + $cantidad);
			}
		

			// Cargo la transferencia en caja destino	

			$insert_caja_destino = "INSERT IGNORE INTO  caja_gral VALUES 
			('$num','$numero_caja_destino','$fecha','$fecha','$det','$cantidad',0,0,0,0,'$cheques_a_sumar',4,0)";
			$result_insert_caja_destino = mysqli_query($connection, $insert_caja_destino);

			// Cargo la transferencia en tabla "transferencias"
			$transfer = "INSERT IGNORE INTO transferencias VALUES 
			('$num','$fecha','$fecha','$rol','$numero_caja_origen','$nombre_caja_destino','$numero_caja_destino','$moneda',0,0,0,'$cantidad','Recibido','$detalle')"; 
			$result_transfer =  mysqli_query($connection, $transfer);
		}
		// cargamos totales generales en caja destino

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales

		$saldo_anterior=saldo_ant('pesos',$numero_caja_destino,$fecha);
		$saldo_anterior_dolares=saldo_ant('dolares',$numero_caja_destino,$fecha);
		$saldo_anterior_euros=saldo_ant('euros',$numero_caja_destino,$fecha);
		$saldo_anterior_cheques=saldo_ant('cheques',$numero_caja_destino,$fecha);

		//Consigo total del dia en pesos desde mi caja
				
		$pesos_hoy = get_total(1,$numero_caja_destino,$fecha);

		//Consigo total del dia en dolares desde mi caja
					
		$dolares_hoy = get_total(2,$numero_caja_destino,$fecha);

		//Consigo total del dia en euros desde mi caja
					
		$euros_hoy = get_total(3,$numero_caja_destino,$fecha);

		//Consigo total del dia en cheques desde mi caja
					
		$cheques_hoy = get_total(4,$numero_caja_destino,$fecha);

		//Consigo cobranza

		$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja_destino'
					order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe']; 
			
		if($ultimo_cobro<>[])
		{
			$monto = $ultimo_cobro;
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

		// cargo los totales generales en caja destino
		$qry = "SELECT * from caja_gral_temp
							where operacion = 1 
							and numero_caja = '$numero_caja_destino'
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
							WHERE numero_caja = '$numero_caja_destino'
							and fecha = '$fecha'
							and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja_destino','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

			$result_insert = mysqli_query($connection, $insert);
		}

		// cargo totales generales en mi caja

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales

		$saldo_anterior2=saldo_ant('pesos',$numero_caja_origen,$fecha);
		$saldo_anterior_dolares2=saldo_ant('dolares',$numero_caja_origen,$fecha);
		$saldo_anterior_euros2=saldo_ant('euros',$numero_caja_origen,$fecha);
		$saldo_anterior_cheques2=saldo_ant('cheques',$numero_caja_origen,$fecha);
		
		
		// consigo total pesos, dolares, euros y cheques del dia
		$pesos_hoy2 = get_total(1,$numero_caja_origen,$fecha);
		$dolares_hoy2 = get_total(2,$numero_caja_origen,$fecha);
		$euros_hoy2 = get_total(3,$numero_caja_origen,$fecha);
		$cheques_hoy2 = get_total(4,$numero_caja_origen,$fecha);
		
		// consigo cobranza
		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_origen'
				order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe']; 
		
		if($ultimo_cobro<>[]){
			$monto2 = $ultimo_cobro;
		}

		// ultimo ingreso por servicios
		$ultimo_ingreso2 = 0;
		$qry2 = "SELECT  importe from ingresos_servicios
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja_origen'
				order by id DESC limit 1";
		$res2 = mysqli_query($connection, $qry2);
		$datos_ingresos2 = mysqli_fetch_array($res2);
		$ultimo_ingreso2 = $datos_ingresos2['importe'];

		if( $ultimo_ingreso2<>[])
		{ 
			$monto_serv2 = $ultimo_ingreso2;
		}

		if( ($pesos_hoy2<>[]) && ($monto2>=0) && ($monto_serv2>=0) )
		{
			$total_gral_pesos2 = ($saldo_anterior2 + $pesos_hoy2 + $monto2 + $monto_serv2);
		}
		else
			if( ($pesos_hoy2==[]) && ($monto2>=0) && ($monto_serv2>=0) )
			{
				$total_gral_pesos2 = ($saldo_anterior2 + $monto2 + $monto_serv2);
			}
			else $total_gral_pesos2 = $saldo_anterior2;

		$total_gral_dolares2 = ($saldo_anterior_dolares2 + $dolares_hoy2);
		$total_gral_euros2 = ($saldo_anterior_euros2 + $euros_hoy2);
		$total_gral_cheques2 = ($saldo_anterior_cheques2 + $cheques_hoy2);
		
		$qry = "SELECT * from caja_gral_temp
				where operacion = 1 
				and numero_caja = '$numero_caja_origen'
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
					WHERE numero_caja = '$numero_caja_origen'
					and fecha = '$fecha'
					and operacion = 1";
			$res = mysqli_query($connection, $set);
		}
		else{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja_origen','$fecha','$fecha','Total gral.','$total_gral_pesos2','$total_gral_dolares2','$total_gral_euros2','$total_gral_cheques2',1)";

			$result_insert = mysqli_query($connection, $insert);
		}

		// seteo los datos de cheques transferidos:

		if($numero_caja_destino == 3)
		{
			$activo = 5;
		}
		else	
			$activo = 2;

		for($i = 0; $i<count($lista_ids); $i++)
		{
			$id_cheque = $lista_ids[$i]; 
			$set_cheques = "UPDATE cheques_cartera 
							SET num_caja_origen = 0,
								caja_destino = '$nombre_caja_destino',
								num_caja_destino = '$numero_caja_destino',
								num_tr = '$num',
								activo = '$activo'
							WHERE id_cheque = '$id_cheque'";
			$res_cheque = mysqli_query($connection, $set_cheques);
			
		}

		$saldo_anterior = 0.00;
		$saldo_anterior_dolares = 0.00;
		$saldo_anterior_euros = 0.00;
		$saldo_anterior_cheques = 0;

		$monto = 0.00;
		$monto2 = 0.00;
		$saldo_anterior2 = 0.00; 
		$saldo_anterior_dolares2 =0;
		$saldo_anterior_euros2 = 0;
		$saldo_anterior_cheques2 = 0;
		$monto_serv2 = 0;
		$monto_serv = 0;
		$ultimo_ingreso = 0;
		$ultimo_ingreso2 = 0;
		$monto2 = 0.00;
		$ultimo_cobro =0;
		$ultimo_cobro2=0;
		$total_gral = 0.00;
		$pesos_a_sumar = 0;
		$pesos_a_restar = 0;
		$cheques_a_sumar = 0;
		$cheques_a_restar = 0;

		echo 'Transferencia realizada';
				
		break;
		/////////////////////////////////////////

}

?>