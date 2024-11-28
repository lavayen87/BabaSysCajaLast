<?php

	date_default_timezone_set('America/Argentina/Salta');

	$num_caja = $_POST['num_caja'];

	include('conexion.php');
	/**/
	// version actualizada
		// $qry = "SELECT MAX(numero_caja) as numero_caja FROM usuarios";// order by numero_caja desc LIMIT 1";
		// $res = mysqli_query($connection, $qry);
		// $datos = mysqli_fetch_array($res);

		// if($datos['numero_caja'] == $num_caja)
		// {
		// 	$qry_op = "DELETE FROM usuarios WHERE numero_caja = '$num_caja'";
		// }
		// else
		// {
		// 	$qry_op = "UPDATE usuarios 
  //                         SET nombre  = '',
  //                             usuario = '',
  //                             pass    = '',
  //                             rol     = '',
  //                         nombre_caja = '',
  //                             block   = 0,
  //                          block_caja = 1,
		// 					  					    deleted = 1
  //                         WHERE numero_caja = $num_caja";
		// }

		// mysqli_query($connection, $qry_op);
		// mysqli_close($connection);
		// echo 1;
		
	/**/

	//Verison original
	
	$delete = "DELETE FROM usuarios WHERE numero_caja = '$num_caja'";

	mysqli_query($connection, $delete);
	mysqli_close($connection);

	echo 1;
	
?>