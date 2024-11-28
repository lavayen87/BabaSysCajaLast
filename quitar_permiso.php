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

include("conexion.php");

//agregado

if($id_permiso == 46)
{

    $update = "UPDATE usuarios SET block = 0 WHERE numero_caja = '$num_caja'";
	$res = mysqli_query($connection,$update);

    $delete = "DELETE FROM det_permisos 
               WHERE numero_caja = '$num_caja'
               AND id_permiso = '$id_permiso'
               AND btn_accion = 1";
}
else{
    if($id_permiso == 47)
    {
        $del = "DELETE FROM asignaciones  WHERE numero_caja = '$num_caja' and block_se = 1";
	    $res = mysqli_query($connection,$del);

        $delete = "DELETE FROM det_permisos 
                   WHERE numero_caja = '$num_caja'
                   AND id_permiso = '$id_permiso'
                   AND btn_accion = 1";
    }
    else{
        if($id_permiso == 48)
        {
            $del = "DELETE FROM asignaciones  WHERE numero_caja = '$num_caja' and block_sf = 1";
	        $res = mysqli_query($connection,$del);

            $delete = "DELETE FROM det_permisos 
                       WHERE numero_caja = '$num_caja'
                       AND id_permiso = '$id_permiso'
                       AND btn_accion = 1";
        }
        else
        {
            $delete = "DELETE FROM det_permisos 
                       WHERE numero_caja = '$num_caja'
                       AND id_permiso = '$id_permiso'
                       AND btn_accion = 1";
        }
    }
}





mysqli_query($connection, $delete);

echo 1;

?>