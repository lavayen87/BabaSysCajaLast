<?php
date_default_timezone_set('America/Argentina/Salta');
include('conexion.php');

$servicio = $_POST['servicio'];
$id_servicio = $_POST['id_servicio'];
$hoy = date('Y-m-d');
$id = 0;

//version original

$id_servicio = $_POST['id_servicio'];
$hoy = date('Y-m-d');

$update = "UPDATE det_servicio  
           SET estado = 'Realizado',
           fecha_realizado = '$hoy'
           WHERE id = '$id_servicio'";

mysqli_query($connection,$update);    

//conseguimos datos para actualizar en lotes_pendientes:
$qry = "SELECT * FROM  det_servicio
        WHERE id = '$id_servicio'";   
$res = mysqli_query($connection,$qry);
$datos = mysqli_fetch_array($res);

$lote = $datos['lote'];
$serv = $datos['servicio'];

$update_lp = "UPDATE lotes_pendientes 
              SET estado = 'F'
              WHERE lote = '$lote'
              AND servicio = '$serv'";
              
mysqli_query($connection,$update_lp);   

echo "state ok";

/**--------------------------------------------------**/

//version 2
/*switch($servicio){
    case 1: // servicio agrimensor
        $id = $id_servicio - 4;
        $update = "UPDATE det_lotes
                  SET estado_agr = 'Realizado',
                  realizado_agr = '$hoy'
                  WHERE id = '$id'";
        break;
    case 2: // servicio agua
        $id = $id_servicio - 8;
        $update = "UPDATE det_lotes
                    SET estado_agua = 'Realizado',
                    realizado_agua = '$hoy'
                    WHERE id = '$id'";
        break;
    case 3: // servicio cloacas
        $id = $id_servicio - 12;
        $update = "UPDATE det_lotes
                    SET estado_clo = 'Realizado',
                    realizado_clo = '$hoy'
                    WHERE id = '$id'";
        break;

mysqli_query($connection,$update);

echo 'state ok';*/

?>