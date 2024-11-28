<?php 

include('conexion.php');


$codigo = $_POST['codigo'];

$desc = $_POST['desc'];


$update = "UPDATE cuentas SET descripcion = '$desc' 
		where codigo = '$codigo'";

mysqli_query($connection, $update);

echo 'ok';
?>