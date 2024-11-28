<?php 

date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

include('conexion.php');

$num_orden = $_POST['num_orden'];

// Chequeamos si es solicitud en cheques
$qry = "SELECT * FROM ids_check_list WHERE num_orden = '$num_orden'";
$res = mysqli_query($connection, $qry);

if($res->num_rows > 0)
{
    $update = "UPDATE cheques_cartera
               SET activo = 1 
               WHERE num_solicitud = '$num_orden'";
               // activo = 1 (valor predeterminado)
    $res = mysqli_query($connection, $update);
    
    $delete = "DELETE FROM ids_check_list WHERE num_orden = '$num_orden'";
    $res = mysqli_query($connection, $delete);
}

$delete = "DELETE FROM solicitud_orden_pago 
           WHERE numero_orden = '$num_orden'
           AND estado = 'Sin Autorizar'";  

$res_delete = mysqli_query($connection, $delete);

echo 1;

?>