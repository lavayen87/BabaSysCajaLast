<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

include('conexion.php');

$filtro = $_POST['filtro'];

$qry = "SELECT * FROM transferencias
		WHERE numero_tr = '$filtro'
		or numero_caja_origen = '$filtro'
		order by numero_tr";
$res = mysqli_query($connection, $qry);

if($res->num_rows > 0)
{
	$lista[] = array();
	while($datos = mysqli_fetch_array($res))
	{
		$lista[] = array(
	        'numero_tr' => $datos['numero_tr'],
	        'fecha' => $datos['fecha'],
	        'nombre_caja_origen' => $datos['nombre_caja_origen'],
	        'numero_caja_origen'  => $datos['numero_caja_origen'], 
	        'nombre_caja_destino' => $datos['nombre_caja_destino'],
	        'numero_caja_destino'  => $datos['numero_caja_destino'],  
	        'moneda' => $datos['moneda'],
	        'pesos' => $datos['pesos'],
	        'dolares' => $datos['dolares'],
	        'euros' => $datos['euros'],
	        'estado' => $datos['estado'],
	        'observaciones' => $datos['observaciones']
      	);
	}
	$datos_tr = json_encode($lista);
    echo $datos_tr;
}
else echo "Error";
?>