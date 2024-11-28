<?php
include('conexion.php');

$data = $_POST['value'];
$lote = $_POST['valuelote'];
$field = (string)strip_tags($_POST['field']);
$id_cliente = (string)strip_tags($_POST['id']);
$id = $_POST['id_cliente'];


//$update = 'UPDATE clientes SET '.$field.' = "'.$data.'" WHERE id_cliente=13';
/*$update1 = "UPDATE det_lotes2 SET $field = '$data' WHERE id = '$id_cliente'";
$connection->query($update1);

$update2 = "UPDATE det_servicio SET $field = '$data'
            WHERE id_cliente = '$id_cliente'
            AND id = '$id'";
$connection->query($update2);*/

$update1 = "UPDATE det_lotes SET $field = '$data' WHERE id = '$id_cliente' and lote = '$lote'";
$connection->query($update1);

$update2 = "UPDATE det_servicio SET $field = '$data'
            WHERE id_cliente = '$id'
            AND id = '$id_cliente'
            and lote = '$lote'";
$connection->query($update2);

echo $data;
?>