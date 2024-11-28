
<?php  

session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $mi_numero_caja = $_SESSION['numero_caja'];
}

$id = $_POST['id']; // numero de orden desde la tabla "orden_pago"
include('conexion.php');

$qry = "SELECT * from orden_pago WHERE numero_orden = '$id'";
$res = mysqli_query($connection, $qry);

if($res)
{
	$datos = mysqli_fetch_array($res);

	$numero_orden = $datos['numero_orden'];
	$fecha = $datos['fecha'];
	$numero_caja = $datos['numero_caja'];
	$empresa = $datos['empresa'];
	$obra = $datos['obra'];
	$cuenta = $datos['cuenta'];
	$detalle = $datos['detalle'];
	$importe = $datos['importe'];

	$qry = "DELETE from orden_pago_temp WHERE numero_caja = '$mi_numero_caja'";
	$res = mysqli_query($connection, $qry);

	$qry = "INSERT INTO orden_pago_temp 
			VALUES ('$numero_orden','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra')";
	$res = mysqli_query($connection, $qry);
	echo 'ok';
}
else echo 'Error';

?>