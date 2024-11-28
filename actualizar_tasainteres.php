<?php

include('conexion.php');


//version original

$datos = json_decode($_POST['datos']);

foreach ($datos as $dato) {
    
    if(($dato -> indice) >= 0)
    {
       $id = $dato -> id;
       $indice = $dato -> indice;

       $update = "UPDATE indices_fn 
                  SET indice = '$indice'
                  WHERE id = '$id'";

       mysqli_query($connection,$update);    
    }
}

        
echo 1;

/**--------------------------------------------------**/



?>