<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

$num_caja   = $_POST['num_caja'];
$id_permiso = $_POST['id_permiso'];
$valor_btn  = $_POST['valor'];

include("conexion.php");

// agregado

if($id_permiso == 46) 
{
	$update = "UPDATE usuarios SET block = 1 WHERE numero_caja = '$num_caja'";
	$res = mysqli_query($connection,$update);

	$insert = "INSERT IGNORE INTO det_permisos VALUES
	('',
	'$num_caja',
	'$id_permiso',
	'$valor_btn'
	)";
}
else
{
	if($id_permiso == 47) 
	{
		$update = "INSERT IGNORE INTO asignaciones VALUES ('','$num_caja',1,0)";
		$res = mysqli_query($connection,$update);

		$insert = "INSERT IGNORE INTO det_permisos VALUES
		('',
		'$num_caja',
		'$id_permiso',
		'$valor_btn'
		)";
	}
	else
	{
		if($id_permiso == 48) 
		{
			$update = "INSERT IGNORE INTO asignaciones VALUES ('','$num_caja',0,1)";
			$res = mysqli_query($connection,$update);

			$insert = "INSERT IGNORE INTO det_permisos VALUES
			('',
			'$num_caja',
			'$id_permiso',
			'$valor_btn'
			)";
		}
		else
		{
			$insert = "INSERT IGNORE INTO det_permisos VALUES
			('',
			'$num_caja',
			'$id_permiso',
			'$valor_btn'
			)";
		}
	}
}


mysqli_query($connection, $insert);

echo 1;

?>