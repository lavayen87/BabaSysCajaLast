<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$nombre_cuenta = $_POST['nombre_cuenta'];

include('conexion.php');

$insert = "INSERT IGNORE INTO cuentas VALUES ('','$nombre_cuenta')";
$result = mysqli_query($connection, $insert);

echo "ok";

?>