<?php
date_default_timezone_set('America/Argentina/Salta');
include('conexion.php');


//version original

$datos = json_decode($_POST['datos']);

foreach ($datos as $dato) {
    
    if(($dato -> precio) > 0)
    {
       $id = $dato -> id;
       $precio = $dato -> precio;

       $update = "UPDATE precios_servicios  
               SET precio = '$precio'
               WHERE id = '$id'";

       mysqli_query($connection,$update);    
    }
}

        
echo 1;

/**--------------------------------------------------**/



?>