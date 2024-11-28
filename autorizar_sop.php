<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $numero_caja = $_SESSION['numero_caja'];
}

$num = $_POST['num'];

$fecha   = date('Y-m-d');

include('conexion.php');

$qry = "UPDATE solicitud_orden_pago set estado = 'Autorizada'
        where numero_orden = '$num'";

$res = mysqli_query($connection, $qry);

echo 1;
?>