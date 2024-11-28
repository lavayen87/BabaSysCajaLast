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

    $vlote = $_POST['lote'];
    $lote = strtoupper( substr($vlote,0,2) ).substr($vlote,2,4);

    $servicio = "";
    $op = 0;

    if($_POST['check1'] > 0)
     {
         $servicio = "Agua";
         //$op = 2;
     }            
     else
         if($_POST['check2'] > 0) 
         {

             $servicio = "Cloacas";
            // $op = 2;
         }

    $let = strtoupper(substr($lote,0,2)); 

    $loteo = "";
    
    switch ($let) {
    	case 'TE':
    		$loteo = "Terranova";
    		break;
    	case 'BC':
    		$loteo = "Buen Clima";
    		break;
    	case 'AI':
    		$loteo = "Airampo";
    		break;
    	case 'LI':
    		$loteo = "Libertad";
    		break;
    	case 'SC':
    		$loteo = "San Carlos";
    		break;
    	case 'PM':
    		$loteo = "Palo Marcado";
    		break;
    }  

    include('conexion.php');
  
    $qry = "INSERT INTO lotes_pendientes values ('','$loteo','$lote','$servicio','P')";
    $res = mysqli_query($connection, $qry);
 
    echo 1;

?>