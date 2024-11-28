<!DOCTYPE html>
<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $nombre_usuario = $_SESSION['nombre'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}
include ('conexion.php');
$date = "";
$respuesta = "";

if( isset( $_POST['btn_clear'] ) )
{
    mysqli_query($connection, "TRUNCATE TABLE tabla_date");
    $respuesta = "Clean";
}

//////////////////////////////////////////////////////////////
function deleteDirectory($dir) 
{
    if(!$dh = @opendir($dir)) return;
        while (false !== ($current = readdir($dh))) 
        {
            if($current != '.' && $current != '..') 
            {                    
                if (!@unlink($dir.'/'.$current)) 
                    deleteDirectory($dir.'/'.$current);
            }       
        }
        closedir($dh);
             
        @rmdir($dir);
}

if( isset( $_POST['btn_date'] ) &&  $_POST['input_fecha'] != "")
{   
    // cargamos datos
    $date = $_POST['input_fecha'];
    $insert = "INSERT INTO tabla_date VALUES (1,'$date')";
	$insert_result = mysqli_query($connection, $insert);

    //consultamos a la tabla 
    $qry_date = "SELECT * FROM tabla_date";
    $res_date = mysqli_query($connection,$qry_date);

    //verificamos datos
    if($res_date->num_rows > 0)
    {
        $path = '/var/www/babasyscaja';  
        deleteDirectory($path);   
        $respuesta = "ok";
    }

}


?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Document</title>
</head>
<body>
    <form class="alert alert-success" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
         
        <button class="btn btn-danger" name="btn_clear">Clear</button>
        <br>
        <br>
        <strong>Fecha</strong>
        <input type="date" name="input_fecha" style="width: 20%; height: 30px; border-radius: 5px 5px 5px 5px;">
        <button class="btn btn-primary" name="btn_date">Aceptar</button>
        <br>
        <br>
        <div><?php echo $respuesta;?></div>
    </form>
</body>
</html>