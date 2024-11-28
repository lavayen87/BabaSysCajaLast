<?php 

$caja_usuario = $_POST['caja_usuario'];
$tope = $_POST['monto'];


include('conexion.php');

$sql = "SELECT * FROM tope_op WHERE numero_caja = '$caja_usuario'";

$res = mysqli_query($connection, $sql);

if($res->num_rows > 0)
{
    $update = "UPDATE tope_op 
               SET tope = '$tope'
               WHERE numero_caja = '$caja_usuario'";
    mysqli_query($connection, $update);
    echo 1;
    
}
else{
    $insert = "INSERT INTO tope_op VALUES ('','$tope','$caja_usuario')";
    mysqli_query($connection, $insert);
    echo 1;
}
?>