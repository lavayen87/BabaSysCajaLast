<?php 

include('conexion.php');


$id_empresa = $_POST['id_empresa'];

$desc = $_POST['desc'];


$update = "UPDATE empresas SET nombre_empresa = '$desc' 
		where id_empresa = '$id_empresa'";

mysqli_query($connection, $update);

echo 'ok';
?>