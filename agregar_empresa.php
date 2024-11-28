<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$nombre_empresa = $_POST['nombre_empresa'];

include('conexion.php');

$insert = "INSERT IGNORE INTO empresas VALUES ('$nombre_empresa','')";
$result = mysqli_query($connection, $insert);

echo "ok";

?>