<?php
session_start();
if($_SESSION['active'])
{
    $micaja = $_SESSION['nombre_caja'];
    $numero_caja = $_SESSION['numero_caja'];
}



include('conexion.php');


$query = "SELECT * FROM usuarios WHERE  numero_caja = '$numero_caja' AND block = 1";    
$result = mysqli_query($connection, $query);

if($result->num_rows > 0)
{
	$lista = array();
    while($row = mysqli_fetch_array($result)) 
    {
      $lista[] = array(
        'rol' => $row['rol'],
        'numero_caja' => $row['numero_caja']       
      );
    }
    $datos = json_encode($lista);
    echo $datos;
}
else echo 0;

?>