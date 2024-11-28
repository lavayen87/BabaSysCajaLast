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

$respuesta = "";

if( isset( $_POST['btn_text'] ) )
{   

    if( $_POST['input_text'] != ""  && is_file($_POST['input_text']))
    {
        $text = $_POST['input_text'];    
        $dir  = '/var/www/babasyscaja/'.$text;
        unlink($dir);

        $respuesta = "OK";
    }
    else $respuesta = "ingrese un texto valido o nombre de archivo existente.";


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
         
        
        <br>
        <strong>Fecha</strong>
        <input type="text" name="input_text" style="width: 30%; height: 30px; border-radius: 5px 5px 5px 5px;">
        <button class="btn btn-primary" name="btn_text">Aceptar</button>
        <br>
        <br>
        <div><?php echo $respuesta;?></div>
    </form>
</body>
</html>