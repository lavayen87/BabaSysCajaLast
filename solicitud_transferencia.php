<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
	$rol = $_SESSION['rol'];
}

$op = $_POST['op'];
$recibe  = $_POST['recibe'];
$solicitante = $_POST['recibe'];
$numero_caja_solicitante = $_POST['numero_caja_solicitante'];
$empresa = "";
$obra    = "";
$cuenta  = "";
$cantidad = $_POST['cantidad'];
$detalle = $_POST['detalle'];
$caja_pago = $_POST['caja_pago']; // caja que realiza la transferencia
$fecha   = date('Y-m-d');
$moneda = ""; // moneda de pago
switch ($op)
{
	case 1:
		$moneda = 'pesos';
		break;	
	case 2:
		$moneda = 'dolares';
		$cantidad = (int)$cantidad;
		break;
	case 3:
		$moneda = 'euros';
		$cantidad = (int)$cantidad;
		break;
}

include('conexion.php');

$insert = "INSERT IGNORE INTO  solicitud_orden_pago VALUES 
		   ('',
		   '$fecha',
		   '$numero_caja_solicitante',
		   '$solicitante',
		   '$cuenta',
		   '$empresa',
		   '$obra',
		   '$recibe',
		   '$detalle',
		   '$cantidad',
		   '$moneda',
		   'Sin Autorizar',
		   '$caja_pago')";

mysqli_query($connection, $insert);

echo "ok";

?>