<?php 

include('conexion.php');


$id_obra = $_POST['id_obra'];

$desc = $_POST['desc'];


$update = "UPDATE obras SET nombre_obra = '$desc' 
		where id_obra = '$id_obra'";

mysqli_query($connection, $update);

echo 'ok';
?>