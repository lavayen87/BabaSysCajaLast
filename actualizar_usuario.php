<?php

$datos = json_decode($_POST['datos_usuario']);

//echo "Datos del usuario: ".print_r($datos);

$n_caja = $datos[0]->numero_caja;
$nombre = $datos[1]->nombre;
$usuario = $datos[2]->usuario;
$rol = $datos[3]->rol;
$nombre_caja = $datos[4]->nombre_caja;
$pass = $datos[5]->pass;

include('conexion.php');

if($pass!="")
{
    $update = "UPDATE usuarios 
                SET nombre = '$nombre',
                    usuario = '$usuario',
                    rol = '$rol',
                    pass = '$pass',
                    nombre_caja = '$nombre_caja'
            
                WHERE numero_caja = '$n_caja'";
}
else{
    $update = "UPDATE usuarios 
                SET nombre = '$nombre',
                    usuario = '$usuario',
                    rol = '$rol',
                    nombre_caja = '$nombre_caja'
                 
                WHERE numero_caja = '$n_caja'";
}


mysqli_query($connection, $update);

echo 1;
?>