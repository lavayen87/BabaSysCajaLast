<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $numero_caja = $_SESSION['numero_caja'];
}

$lote = $_POST['lote'];
$let = strtoupper(substr($lote,0,2)); // los dos primeros caracteres
$lote = strtoupper(substr($lote,0,2)).substr($lote,2,(strlen($lote)-2)); // codigo completo

include('conexion.php');

$qry = "SELECT * FROM clientes
        where lote = '$lote'";

$res = mysqli_query($connection, $qry);

if($res->num_rows > 0)
{
    echo false;
}
else echo true;

?>