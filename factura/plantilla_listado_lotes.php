<?php 

date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

$loteo    = $_GET['loteo'];
$servicio = $_GET['serv'];
$estado   = $_GET['est'];

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
          if($estado == 'Pendiente'){
            echo "Listado de Lotes Pendientes";
          }
          else{
            if($estado == 'Solicitado'){
              echo "Listado de Lotes Solicitados";
            }
            else{
              if($estado == 'Realizado')
                echo "Listado de Lotes Realizados";
              else{
                echo "Listado de Lotes";
              }
            }
          }
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

$tabla = "<table>
          <thead>  
          <tr> 
          <td><strong>Servicio</strong></td>
          <td><strong>Lote</strong></td>
          <td><strong>Loteo</strong></td>
          <td><strong>Solicitado</strong></td>
          <td><strong>Realizado</strong></td>
          <td><strong>Estado</strong></td>
          </tr>
          </thead>
          <tbody id='tbody-datos'>";

// caso 1
if($loteo =="" && $servicio =="" && $estado =="")
{
  $qry = "SELECT * FROM det_servicio
          WHERE servicio <> 'Red Cloacas'";
}
else
{
  // caso 2
  if($loteo!="" && $servicio!="" && $estado!="")
  {

    $qry = "SELECT * FROM det_servicio
            WHERE loteo = '$loteo'
            AND servicio = '$servicio'
            AND estado = '$estado'
            AND servicio <> 'Red Cloacas'
            ORDER BY fecha_solicitud";
  }
  else
  {
    // caso 3
    if($loteo !="" && $servicio =="" && $estado =="")
    {

      $qry = "SELECT * FROM det_servicio
              WHERE loteo = '$loteo'
              AND servicio <> 'Red Cloacas'
              ORDER BY fecha_solicitud";
    }
    else
    {
      // caso 4
      if($loteo =="" && $servicio !="" && $estado =="")
      {

        $qry = "SELECT * FROM det_servicio
                WHERE servicio = '$servicio'
                AND servicio <> 'Red Cloacas'
                ORDER BY fecha_solicitud";
      }
      else
      {
        // caso 5
        if($loteo =="" && $servicio =="" && $estado !="")
        {
  
          $qry = "SELECT * FROM det_servicio
                  WHERE estado = '$estado'
                  AND servicio <> 'Red Cloacas'
                  ORDER BY fecha_solicitud";
        }
        else
        {
          // caso 6
          if($loteo =="" && $servicio !="" && $estado !="")
          {

            $qry = "SELECT * FROM det_servicio
                    WHERE servicio = '$servicio'
                    AND estado = '$estado'
                    AND servicio <> 'Red Cloacas'
                    ORDER BY fecha_solicitud";
          }
          else
          {
            // caso 7
            if($loteo !="" && $servicio !="" && $estado =="")
            {

              $qry = "SELECT * FROM det_servicio
                      WHERE loteo = '$loteo'
                      AND servicio = '$servicio'
                      AND servicio <> 'Red Cloacas'
                      ORDER BY fecha_solicitud";
            }
            else
            {
              //caso 8
              if($loteo !="" && $servicio =="" && $estado !="")
              {

                $qry = "SELECT * FROM det_servicio
                        WHERE loteo = '$loteo'
                        AND estado = '$estado'
                        AND servicio <> 'Red Cloacas'
                        ORDER BY fecha_solicitud";
              }
            }
          }
        }
      }
    }
  }
}


$res = mysqli_query($connection, $qry);                         

while($datos = mysqli_fetch_array($res))
{
  $tabla.="<tr>
  <td style='text-align: left;'>".$datos['servicio']."</td>
  <td style='text-align: center;'>".$datos['lote']."</td>
  <td style='text-align: center;'>".$datos['loteo']."</td>
  <td style='text-align: center;'>".fecha_min($datos['fecha_solicitud'])."</td>
  <td style='text-align: center;'>".fecha_min($datos['fecha_realizado'])."</td>
  <td style='text-align: center;'>".$datos['estado']."</td>
  </tr>";

}

$tabla.="</tbody>";
                                
echo $tabla;
                         
?>

</body>
</html>