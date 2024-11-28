

<?php

/* todos los archivos*/
$files = glob('carpeta_conenedora/*'); //obtenemos todos los nombres de los ficheros
foreach($files as $file){
    if(is_file($file))
    unlink($file); 
}

/*un tipo específico de manera recursiva*/

$files = glob('carpeta_conenedora/*.jpg'); //obtenemos todos los nombres de los ficheros
foreach($files as $file){
    if(is_file($file))
    unlink($file); 
}
/*ficheros antiguos del servidor
ficheros que se han modificado antes de una fecha específica*/

$files = glob('carpeta_conenedora/*'); //obtenemos el nombre de todos los ficheros
foreach($files as $file){
    $lastModifiedTime = filemtime($file);
    $currentTime = time();
    $timeDiff = abs($currentTime - $lastModifiedTime)/(60*60); //en horas
    if(is_file($file) && $timeDiff > 10)
    unlink($file);
}

/* ----------------------------------------------------------- */
$respuesta = "";

if(isset($_POST['btn-drop']))
{
    if($_POST['nom-tabla'] != "")
    {
        $nom_tabla = $_POST['nom-tabla'];
        include('conexion.php');

        $delete = "DROP TABLE $nom_tabla";
        $result = mysqli_query($connection,$delete);
        if($result==0)
        {
            $respuesta = "<strong>No se ha podido eliminar o no existe la tabla...</strong>";
        }
        else
        {
            $respuesta = "<strong>La tabla '$nom_tabla' se ha eliminado correctamente.</strong>";
        }
        mysqli_close($connection);
    }
    else{
        $respuesta = "<strong>Debe ingresar en nombre de la tabla.</strong>";
    }
}

?>

<!DOCTYPE html>
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
        <strong>Ingrese La tabla</strong>
        <input type="text" name="nom-tabla" style="width: 20%; height: 30px; border-radius: 5px 5px 5px 5px;">
        <button class="btn btn-danger" name="btn-drop">Eliminar</button>
        <br>
        <br>
        <div><?php echo $respuesta;?></div>
    </form>
</body>
</html>

