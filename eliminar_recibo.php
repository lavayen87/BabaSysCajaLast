<?php

date_default_timezone_set('America/Argentina/Salta');

$num_recibo = $_POST['num_recibo'];

include('conexion.php');

$fecha = date('Y-m-d');

$delete = "DELETE FROM recibo WHERE numero = '$num_recibo'";

mysqli_query($connection, $delete);
mysqli_close($connection);

echo 1;
?>