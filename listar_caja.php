<?php
session_start();
if($_SESSION['active'])
{
    $micaja = $_SESSION['nombre_caja'];
    $numero_caja = $_SESSION['numero_caja'];
}
include('conexion.php');


$query = "SELECT * from $micaja";    
$result = mysqli_query($connection, $query);

if($result)
{
	$lista = array();
    while($row = mysqli_fetch_array($result)) 
    {
      $lista[] = array(
        'numero' => $row['numero'],
        'mumero_caja' => $row['numero_caja'],
        'fecha' => $row['fecha'],
        'detalle'  => $row['detalle'],  
        'ingreso' => $row['ingreso'],
        'egreso' => $row['egreso'],
        'pesos' => $row['pesos'],
        'dolares' => $row['dolares'],
        'euros' => $row['euros'],
      );
    }
    $datos = json_encode($lista);
    echo $datos;
}
else echo "Error";

?>