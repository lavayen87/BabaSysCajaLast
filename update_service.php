<?php
include('conexion.php');

$data = $_POST['value'];
$field = (string)strip_tags($_POST['field']);
$id_cliente = (string)strip_tags($_POST['id_cliente']);
$id = $_POST['id'];

//$update = 'UPDATE clientes SET '.$field.' = "'.$data.'" WHERE id_cliente=13';
$update1 = "UPDATE clientes SET $field = '$data' WHERE id_cliente = '$id_cliente'";
$connection->query($update1);

$update2 = "UPDATE det_servicio SET $field = '$data'
            WHERE id_cliente = '$id_cliente'
            AND id = '$id'";
$connection->query($update2);

echo $data;
?>