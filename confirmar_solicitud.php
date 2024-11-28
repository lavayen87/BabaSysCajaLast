<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$fecha = date('Y-m-d');
$numero_orden = $_POST['numero_orden'];

include('conexion.php');

$qry = "SELECT * FROM solicitud_orden_pago WHERE numero_orden = '$numero_orden'";
$res = mysqli_query($connection, $qry);
if($res)
{
	$datos = mysqli_fetch_array($res);
	$importe = $datos['importe']; // consigo el importe de la solicitud

	$qry = "SELECT pesos FROM caja_gral 
					WHERE pesos <> 0 
					AND numero_caja = '$numero_caja'";
	$res = mysqli_query($connection, $qry);
	$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja

	$pesos = $get_datos['pesos'];
	$pesos_a_restar = $pesos - $importe;
	$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Pago Solicitud',0,'$importe','$pesos_a_restar',0,0,1)";
	$insert_result = mysqli_query($connection, $insert);

	$qry = "UPDATE solicitud_orden_pago  SET estado = 'Realizado' WHERE numero_orden = '$numero_orden'";
	$res = mysqli_query($connection, $qry);

	echo 'ok';
}


?>