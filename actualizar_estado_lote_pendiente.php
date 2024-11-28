<?php
date_default_timezone_set('America/Argentina/Salta');
include('conexion.php');


$id_servicio = $_POST['id_servicio'];
$hoy = date('Y-m-d');

// obnenemos lote y servicio de la tabla lotes_pendientes
$qry = "SELECT * FROM lotes_pendientes 
        WHERE id_lote = '$id_servicio'";
$res = mysqli_query($connection,$qry);
$datos = mysqli_fetch_array($res);
$lote = $datos['lote'];
$servicio = $datos['servicio'];

// actualizamos las dos tablas
$update1 = "UPDATE det_servicio  
           SET estado = 'Realizado',
           fecha_realizado = '$hoy'           
           WHERE lote = '$lote'
           AND servicio = '$servicio'";

mysqli_query($connection,$update1);     

$update2 = "UPDATE lotes_pendientes  
           SET estado = 'F'           
           WHERE id_lote = '$id_servicio'";

mysqli_query($connection,$update2);        

echo "state ok";


?>