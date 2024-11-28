<?php
// Datos de conexión a la base de datos
$host = 'localhost'; 
$usuario = 'root'; 
$contrasena = ''; 
$basedatos = 'bd_sistema';//'bd_sistema'; 

// Conexión a la base de datos
// $conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

// // Verificar la conexión
// if ($conexion->connect_error) {
//     die("Error en la conexión: " . $conexion->connect_error);
// }

$resp = 0;

// Nombre del archivo de respaldo
$nombreArchivo = 'backup_' . date('d-m-Y') . '.sql';

// Comando para realizar el respaldo
//$comando = "mysqldump --host=$host --user=$usuario --password=$contrasena $basedatos > $nombreArchivo";

$comando = "mysqldump -h$host -u$usuario -p$contrasena --opt $basedatos > $nombreArchivo";

// Ejecutar el comando para respaldar la base de datos
system($comando, $output);

$zip = New ZipArchive();

$nombrezip = 'backup_' . date('d-m-Y') . '.zip';

if($zip->open($nombrezip, ZipArchive::CREATE) === true ){
    $zip->addFile($nombreArchivo);   
    $zip->Close();
    unlink($nombreArchivo);
    header("Location: $nombreArchivo");

}


// Descargar el archivo de respaldo
// if (file_exists($nombreArchivo)) 
// {
//     // Encabezados para la descarga
//     header('Content-Description: File Transfer');
//     header('Content-Type: application/octet-stream');
//     header('Content-Disposition: attachment; filename=' . basename($nombreArchivo));
//     header('Expires: 0');
//     header('Cache-Control: must-revalidate');
//     header('Pragma: public');
//     header('Content-Length: ' . filesize($nombreArchivo));
//     ob_clean();
//     flush();
//     readfile($nombreArchivo);
//     $resp = 1;
//     // Eliminar el archivo después de la descarga (opcional)
//     //unlink($nombreArchivo);
//     exit;
// } 

//     echo $res;
?>
