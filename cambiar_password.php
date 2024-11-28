<?php  
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
}

$pass = $_POST['password'];
include('conexion.php');

$qry = "UPDATE usuarios SET pass = '$pass' WHERE numero_caja = '$numero_caja'";
$res = mysqli_query($connection, $qry);

echo "ok";

?>