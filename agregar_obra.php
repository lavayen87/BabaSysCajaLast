<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$nombre_obra = $_POST['nombre_obra'];

include('conexion.php');

$insert = "INSERT IGNORE INTO obras VALUES ('$nombre_obra','')";
$result = mysqli_query($connection, $insert);

echo "ok";

?>