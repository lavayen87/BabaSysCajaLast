<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}
include('conexion.php');
include('funciones.php');

$cantidad = $_POST['cantidad'];

$detalle = $_POST['detalle'];

$fecha = date('Y-m-d');

$monto = 0;	

// consigo pesos
$qry = "SELECT pesos from caja_gral
		where numero_caja = '$numero_caja'
		AND operacion = 1
		AND fecha = '$fecha'
		order by numero desc limit 1";    
$res = mysqli_query($connection, $qry);
$dato = mysqli_fetch_array($res); 
$pesos = $dato['pesos'];

// consigo cobranza
$qry = "SELECT  importe from cobranza
		WHERE fecha = '$fecha' 
		AND numero_caja = '$numero_caja'
		order by numero limit 1";
$res = mysqli_query($connection, $qry);
$datos = mysqli_fetch_array($res);
$ultimo_cobro = $datos['importe']; 

$op = 0;		 

if($ultimo_cobro > 0.00)
{
	if($pesos == [] || $pesos == 0)
	{
		if($ultimo_cobro >= $cantidad){
			$pesos_a_restar = $ultimo_cobro - $cantidad;
			$op = 1;
		}
		else{
			// consigo saldo anterior:
			$sa = saldo_ant('pesos',$numero_caja,$fecha);

			if($sa >= $cantidad)
			{
				$pesos_a_restar = ($sa - $cantidad);
				$op = 2;	
			}
		}
	}
	else
	{
		if($pesos >= $cantidad){
			$pesos_a_restar = $pesos - $cantidad;
			$op = 3;
		}
		else{
			if($ultimo_cobro >= $cantidad){
				$pesos_a_restar = $ultimo_cobro - $cantidad;
				$op = 4;
			}
			else{
				// consigo saldo anterior:
				$sa = saldo_ant('pesos',$numero_caja,$fecha);

				if($sa >= $cantidad)
				{
					$pesos_a_restar = ($sa - $cantidad);
					$op = 5;	
				}
			}
		}
	}
}
else
{
	if($pesos == [])
	{
		// consigo saldo anterior:
		$sa = saldo_ant('pesos',$numero_caja,$fecha);

		if($sa >= $cantidad)
		{
			$pesos_a_restar = ($sa - $cantidad);
			$op = 6;	
		}
					
	}
	else
	{
		if($pesos >= $cantidad)
		{
			$pesos_a_restar = $pesos - $cantidad;
			$op = 7;
		}
		else{

			// consigo saldo anterior:
			$sa = saldo_ant('pesos',$numero_caja,$fecha);
			if($sa >= $cantidad)
			{
				$pesos_a_restar = ($sa - $cantidad);
							$op = 8;	
			}
					
		}
	}
}
			
if($op > 0)
{
	// en 3 o 5 setear saldo anterior reordenar las variables !!

	$det1 = limitar_cadena("I/ch ".$detalle,27); 

	$det2 = limitar_cadena("E/$ ".$detalle,27);

	// consigo cheques
	$qry = "SELECT cheques from caja_gral
			where numero_caja = '$numero_caja'
			AND operacion = 4
			AND fecha = '$fecha'
			order by numero desc limit 1";    
	$res = mysqli_query($connection, $qry);
	$dato = mysqli_fetch_array($res); 
	$cheques = $dato['cheques'];

	$cheque_a_sumar = ($cheques + $cantidad);
	           
	// cargo el canje en mi caja:

	$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
			('','$numero_caja','$fecha','$fecha','$det1','$cantidad',0,0,0,0,'$cheque_a_sumar',4)";
	$result_inert_mc = mysqli_query($connection, $insert_mc);

	$insert_mc = "INSERT IGNORE INTO caja_gral VALUES 
			('','$numero_caja','$fecha','$fecha','$det2',0,'$cantidad','$pesos_a_restar',0,0,0,1)";
	$result_inert_mc = mysqli_query($connection, $insert_mc);
																		   							

	//Buscamos Saldo anterior en pesos, dolares, euros y cheques: 

	$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
	$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
	$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
	$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);

	//Consigo total del dia en pesos  
				        
	$pesos_hoy = get_total(1,$numero_caja,$fecha);

	//Consigo total del dia en dolares  

	$dolares_hoy = get_total(2,$numero_caja,$fecha);

	//Consigo total del dia en euros  

	$euros_hoy = get_total(3,$numero_caja,$fecha);

	//Consigo total del dia en cheques  

	$cheques_hoy = get_total(4,$numero_caja,$fecha);

	//cargo  totales generales

	if($ultimo_cobro > 0){
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

	echo 1;
}	
else{
	echo 2;
}			


?>