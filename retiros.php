<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$numero_caja = $_SESSION['numero_caja'];
}

$persona = $_POST['persona'];
$concepto= $_POST['concepto'];
$cuenta  = $_POST['cuenta'];
$importe = $_POST['importe'];
$detalle = $_POST['detalle'];
$fecha   = date('Y-m-d');
$ing_servicio = 0.00;
$saldo_anterior = 0.00;
$saldo_anterior_dolares = 0.00;
$saldo_anterior_euros = 0.00;
$pesos_hoy = 0.00;
$dolares_hoy = 0;
$euros_hoy = 0;
$ultimo_cobro = 0.00;
$total_gral = 0.00;
$pesos_a_restar = 0.00;
$monto = 0;
$monto_serv = 0;

include('conexion.php');	
include('funciones.php');
////////////////////////////////////////////

// Consigo pesos desde mi caja
$query = "SELECT pesos from caja_gral
		  where numero_caja = '$numero_caja'
		  and operacion = 1
		  and fecha = '$fecha'
		  order by numero desc limit 1";    
$result = mysqli_query($connection, $query);
$datos = mysqli_fetch_array($result);
$datos_pesos = $datos['pesos'];

//Consigo dolares desde mi caja
$query = "SELECT dolares as total_dolares from caja_gral
		  where numero_caja = '$numero_caja'
		  and operacion = 2
		  and fecha = '$fecha'
		  order by numero desc limit 1";    
$result = mysqli_query($connection, $query);
$datos = mysqli_fetch_array($result);
$datos_dolares = $datos['total_dolares'];

//Consigo euros desde mi caja
$query = "SELECT euros as total_euros from caja_gral
		  where numero_caja = '$numero_caja'
		  and operacion = 3
		  and fecha = '$fecha'
		  order by numero desc limit 1";    
$result = mysqli_query($connection, $query);
$datos = mysqli_fetch_array($result);
$datos_euros = $datos['total_euros'];

// consigo cobranza diaria
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
// Realizo la operacion
/*if($ultimo_cobro > 0.00)
{
	if($datos_pesos == [])
	{
		$pesos_a_restar = ($ultimo_cobro - $importe);
	}
	else
	{
		$pesos_a_restar = ($datos_pesos - $importe);
	}
}
else
{
	if($datos_pesos == [])
	{
		$pesos_a_restar = (0 - $importe);
	}
	else
	{
		$pesos_a_restar = ($datos_pesos - $importe);
	}
}*/

if($ultimo_cobro > 0.00)
{
	if($datos_pesos == [])
	{
		$pesos_a_retiros = ($ultimo_cobro + $ing_servicio - $importe);
	}
	else
	{
		$pesos_a_retiros = ($datos_pesos - $importe);
	}
}
else
{
	if($ing_servicio > 0.00)
	{
		if($datos_pesos == [])
		{
			$pesos_a_restar = ($ing_servicio - $importe);
		}
		else
		{
			$pesos_a_restar = ($datos_pesos - $importe);
		}
	}
	else{
		if($datos_pesos == [])
		{
			$pesos_a_restar = (-1)*$importe;
		}
		else
		{
			$pesos_a_restar = ($datos_pesos - $importe);
		}
	}
}
//////////////////////////////////////////

// Cargo el retiro en mi caja
$insert1 = "INSERT IGNORE INTO caja_gral VALUES 
('','$numero_caja','$fecha','$fecha','$detalle',0,0,'$importe','$pesos_a_restar',0,0,0,1)";
$insert_result1 = mysqli_query($connection, $insert1);

// consigo numero de retiro
$qry = "SELECT numero from caja_gral
		  where numero_caja = '$numero_caja'
		  and operacion = 1
		  and fecha = '$fecha'
		  order by numero desc limit 1";
		  
$res_qry = mysqli_query($connection, $qry);
$get_datos = mysqli_fetch_array($res_qry);
$num = $get_datos['numero'];

// cargo el retiro en tabla retiro
$insert2 = "INSERT  INTO retiros 
VALUES ('$num','$fecha','$numero_caja','$persona','$concepto','$cuenta','$importe','$detalle')";
$result_insert2 = mysqli_query($connection, $insert2);


//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);
			
// consigo total pesos, dolares, euros y cheques del dia
$pesos_hoy = get_total(1,$numero_caja,$fecha);
$dolares_hoy = get_total(2,$numero_caja,$fecha);
$euros_hoy = get_total(3,$numero_caja,$fecha);
$cheques_hoy = get_total(4,$numero_caja,$fecha);

// ultima cobranza
$qry = "SELECT  importe from cobranza
		WHERE fecha = '$fecha' 
		AND numero_caja = '$numero_caja'
		order by numero limit 1";
$res = mysqli_query($connection, $qry);
$datos = mysqli_fetch_array($res);
$ultimo_cobro = $datos['importe'];

if( $ultimo_cobro<>[] )
{ 
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

if( $ultimo_ingreso<>[])
{ 
  $monto_serv = $ultimo_ingreso;
}

if( ($pesos_hoy<>[]) && ($monto>=0) && ($monto_serv>=0) )
{
  $total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto + $monto_serv);
}
else
  if( ($pesos_hoy==[]) && ($monto>=0) && ($monto_serv>=0))
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

echo 'ok';

?>