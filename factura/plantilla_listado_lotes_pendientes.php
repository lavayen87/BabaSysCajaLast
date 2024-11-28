<?php 

date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

// $loteo    = $_GET['loteo'];
// $servicio = $_GET['serv'];
// $estado   = $_GET['est'];

?>

<!DOCTYPE html>
<html>
<head>
<!--link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous"-->

<link rel="stylesheet" href="../css/table-style.css">
<style>
    @page { margin: 4px; } 
    body { margin: 4px; } 
</style>
</head>
<body>

<div style="background-color: #d6e9c6; width: 100%; height: 4%; padding: 8px 8px;">
    <div style="float: left;">
        <img src="img/baba-img2.png" style="height: 40px; width: 40px; padding-top: 8px;">
        <span style="display: inline-block; padding-bottom: 8px;">Baba Urbanizaciones </span>
        <span style="display: inline-block; padding-bottom: 8px; padding-left: 18%;">
         <?php 
          echo "Listado de Lotes sin terminar";
         ?>
        </span>
    </div>
    
</div>
<br>
<?php

include('../conexion.php');
include('../funciones.php');

$p="<p>";
$p.="<label style='float: left;'>Fecha: ".fecha_min(date('Y-m-d'))." - Hora: ".date('G').':'.date('i').':'.date('s')."</label>";
$p.="<label style='float: right;'>Emitido por: ".$_SESSION['rol']." (caja ".$_SESSION['numero_caja'].")</label></p>"; 
echo $p."<br><br>";

$tabla_pendientes = "<table>
<thead>  
<tr> 
<td><strong>#</strong></td>
<td><strong>Loteo</strong></td>
<td><strong>Lote</strong></td>
<td><strong>Servicio</strong></td>
<td><strong>Estado</strong></td>
</tr>
</thead>
<tbody id='tbody-datos'>";

$consulta = "SELECT * FROM lotes_pendientes WHERE estado = 'P' ORDER BY 3 ";
$respuesta = mysqli_query($connection, $consulta);

$estado = "<strong style='color: red;'>Sin Terminar</strong>";

while($datos_consulta = mysqli_fetch_array($respuesta))
{
                              
  $tabla_pendientes.="<tr id='".$datos_consulta['id_lote']."'>
  <td style='text-align: center;'>".$datos_consulta['id_lote']."</td> 
  <td style='text-align: center;'>".$datos_consulta['loteo']."</td>                      
  <td style='text-align: center;'>".$datos_consulta['lote']."</td>
  <td style='text-align: center;'>".$datos_consulta['servicio']."</td>               
  <td style='text-align: center;'>".$estado."</td>                     
  </tr>";
                          
}

$tabla_pendientes.="</tbody></table>";

echo $tabla_pendientes;

                         
?>

</body>
</html>