<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

 
if(isset($_POST['caja_pago'])){
	$caja_pago = $_POST['caja_pago']; // caja q emite la orden
}
else $caja_pago = $numero_caja;

if(isset($_POST['lista_ids'])){
	$lista_ids = $_POST['lista_ids']; //ids de cheques 
}
else $lista_ids = [];

if(isset($_POST['recibe'])){
	$recibe  = $_POST['recibe'];
}
else $recibe = ""; // QUIEN RECIBE EL CHEQUE.

$moneda = $_POST['moneda']; // moneda de pago

$solicitante = $_POST['solicitante'];
$empresa = $_POST['empresa'];
$obra    = $_POST['obra'];
$cuenta  = $_POST['cuenta'];
$importe = $_POST['importe'];
$detalle = $_POST['detalle'];
$fecha   = date('Y-m-d');

include('conexion.php');

/*if(count($lista_ids)>0)
{
	for($i=0; $i<count($lista_ids); $i++)
	{
		$j = $lista_ids[$i];
		$update = "UPDATE cheques_cartera 
				   SET activo = 3
				   WHERE id_cheque = '$j'";
		mysqli_query($connection, $update);	
		
		
		
	}
}*/

$insert = "INSERT IGNORE INTO  solicitud_orden_pago VALUES 
		   ('',
		   '$fecha',
		   '$numero_caja',
		   '$solicitante',
		   '$cuenta',
		   '$empresa',
		   '$obra',
		   '$recibe',
		   '$detalle',
		   '$importe',
		   '$moneda',
		   'Sin Autorizar',
		   '$caja_pago')";

$result_insert = mysqli_query($connection, $insert);

// caso solicitud en cheques
if(count($lista_ids)>0)
{
	$get_num_orden = "SELECT numero_orden FROM solicitud_orden_pago
				      ORDER BY numero_orden DESC LIMIT 1";
	$res = mysqli_query($connection, $get_num_orden);
	$datos_num = mysqli_fetch_array($res);
	$num_solicitud = $datos_num['numero_orden'];

	for($i=0; $i<count($lista_ids); $i++)
	{
		$j = $lista_ids[$i];
		$insert = "INSERT IGNORE INTO ids_check_list VALUES ('','$num_solicitud','$j',0)";
		$res = mysqli_query($connection, $insert);	

		$update = "UPDATE cheques_cartera 
			   SET num_solicitud = '$num_solicitud', 
			       activo = 3
			   WHERE id_cheque = '$j'";
		$res = mysqli_query($connection, $update);
	}
}
else
{
	// switch ($moneda) {
	// 	case 'value':
	// 		// code...
	// 		break;
		
	// 	default:
	// 		// code...
	// 		break;
	// }

	// caso solicitud en pesos
	$get_num_orden = "SELECT numero_orden FROM solicitud_orden_pago
				      ORDER BY numero_orden DESC LIMIT 1";
	$res = mysqli_query($connection, $get_num_orden);
	$datos_num = mysqli_fetch_array($res);
	$num_solicitud = $datos_num['numero_orden'];

	$insert = "INSERT IGNORE INTO ids_check_list VALUES ('','$num_solicitud',0,0)";
	$res = mysqli_query($connection, $insert);
}
echo "ok";

?>