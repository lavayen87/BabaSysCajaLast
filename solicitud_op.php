<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}


$empresa = $_POST['empresa'];
$obra    = $_POST['obra'];
$cuenta  = $_POST['cuenta'];
$importe = $_POST['importe'];
$detalle = $_POST['detalle'];
$fecha   = date('Y-m-d');

include('conexion.php');

$query = "SELECT pesos
		from caja_gral
		where pesos <> 0 and numero_caja = '$numero_caja'
		order by numero desc limit 1";    
$result = mysqli_query($connection, $query);

if($result)
{
	$datos = mysqli_fetch_array($result); 
	
	$insert2 = "INSERT  IGNORE INTO orden_pago 
				VALUES ('','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra',1)";
	$result_insert2 = mysqli_query($connection, $insert2);

	echo "ok";
	
}
else    
    echo "Error de conexión";

?>