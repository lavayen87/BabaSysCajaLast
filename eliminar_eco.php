<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$nombre_eco = $_POST['nombre_eco'];

include('conexion.php');

$qry = "SELECT * from empresas where nombre_empresa = '$nombre_eco'";
$res = mysqli_query($connection, $qry);

if($res->num_rows > 0){
    $delete = "DELETE from empresas where nombre_empresa = '$nombre_eco'";
    $res_delete = mysqli_query($connection, $delete);
    echo 1;
    
}
else{
    $qry = "SELECT * from cuentas where descripcion = '$nombre_eco'";
    $res = mysqli_query($connection, $qry); 

    if($res->num_rows > 0){
        $delete = "DELETE from cuentas where descripcion = '$nombre_eco'";
        $res_delete = mysqli_query($connection, $delete);
        echo 1;
    
    }
    else{
        $qry = "SELECT * from obras where nombre_obra = '$nombre_eco'";
        $res = mysqli_query($connection, $qry); 
        echo 1;
    }
}


?>