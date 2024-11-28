<?php
date_default_timezone_set('America/Argentina/Salta');

// dato enviado por post (id de cheque a eliminar)
$id_cheque = $_POST['id_cheque'];

//variables para actualizar saldos
$saldo_anterior = 0.00;
$saldo_anterior_dolares = 0;
$saldo_anterior_euros = 0;
$saldo_anterior_cheques = 0;
$total_gral = 0.00;
$monto = 0;
$fecha = date('Y-m-d');

include('conexion.php');

//consigo numero de caja e importe del cheque a eliminar
$qry = "SELECT num_caja_origen, importe, fecha_carga FROM cheques_cartera 
        WHERE id_cheque = '$id_cheque'";
$res = mysqli_query($connection, $qry);

if($res->num_rows > 0)
{
    $datos = mysqli_fetch_array($res);
    $num_caja_origen = $datos['num_caja_origen'];
    $importe_cheq = $datos['importe'];
    $fecha_carga = $datos['fecha_carga'];
}

//elimino el cheque seleccionado
$delete = "DELETE FROM cheques_cartera WHERE id_cheque = '$id_cheque'";
mysqli_query($connection, $delete);
mysqli_close($connection);

echo 1;
?>