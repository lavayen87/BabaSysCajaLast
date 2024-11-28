<?php

    
    date_default_timezone_set("America/Argentina/Salta");
    session_start();
    if($_SESSION['active'])
    {
        $micaja = $_SESSION['nombre_caja'];
        $nombre = $_SESSION['nombre'];
        $rol = $_SESSION['rol'];
        $numero_caja = $_SESSION['numero_caja'];
    }

 
    $lote = $_POST['lote'];
    $servicio = $_POST['servicio'];

    include('conexion.php');

    // verifico que exista el lote
    $qry = "SELECT COUNT(*) as cantidad FROM det_lotes WHERE lote = '$lote'";
    $res = mysqli_query($connection, $qry);
    $datos = mysqli_fetch_array($res);
    $cantidad = $datos['cantidad']; 

    if($cantidad > 0) 
    {
        // si existe el lote, cehqueo si esta en lotes_pendientes
        $qry2 = "SELECT COUNT(*) as cantidad FROM lotes_pendientes 
        WHERE lote = '$lote' AND servicio = '$servicio'"; //in ( 'Agua','Cloacas')";
        $res2 = mysqli_query($connection, $qry2);
        $datos2 = mysqli_fetch_array($res2);
        $cantidad2 = $datos2['cantidad'];        

        if($cantidad2 < 1 ) // == 0
        {
            // si existe en lotes pendientes, verifico si el servicio fue realizado o no.
            $qry2 = "SELECT COUNT(*) as cantidad FROM det_servicio 
            WHERE lote = '$lote' AND servicio = '$servicio'
            and estado in ('Realizado','REALIZADO')"; //in ( 'Agua','Cloacas')";
            $res2 = mysqli_query($connection, $qry2);
            $datos2 = mysqli_fetch_array($res2);

            if($datos2['cantidad'] > 0)
            {
                echo 3; // existe y ya fue realizado;
                //echo 1; // existe y no está en lotes pendientes.
            }
            else echo 1; // existe y no está en lotes pendientes.
        }
        else // el lote ya existe en lotes pendientes
        {
            // linea original :  echo 2; // existe y ya está en lotes pendientes.  
            $qry2 = "SELECT * FROM lotes_pendientes 
            WHERE lote = '$lote' AND servicio = '$servicio'"; //in ( 'Agua','Cloacas')";
            $res2 = mysqli_query($connection, $qry2);
            $datos2 = mysqli_fetch_array($res2);
            $estado = $datos2['estado'];   

            if($estado == 'P') 
                echo 2; // existe y ya está en lotes pendientes con estado 'P' (sin terminar).  
            else echo 3; // existe y ya fue realizado;
        }
    }
    else
        echo 0; // no existe.
?>