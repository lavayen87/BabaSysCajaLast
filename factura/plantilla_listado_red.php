<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $nombre = $_SESSION['nombre']; 
  $rol = $_SESSION['rol'];
}

$op = $_GET['op'];

if(isset($_GET['loteo']) && isset($_GET['red']))
{
    $loteo = $_GET['loteo'];
    $red   = $_GET['red'];
}
if(isset($_GET['loteo']) && isset($_GET['f1']) && isset($_GET['f2']) && isset($_GET['red']))
{
    $loteo = $_GET['loteo'];
    $fecha_ini   = $_GET['f1'];
    $fecha_fin   = $_GET['f2'];
    $red   = $_GET['red'];
}

if(isset($_GET['loteo']) && isset($_GET['f1']) && isset($_GET['f2']))
{
    $loteo = $_GET['loteo'];
    $fecha_ini   = $_GET['f1'];
    $fecha_fin   = $_GET['f2'];
}

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
                //echo $rol." (caja "."<label style='font-size: 13px;'>".$numero_caja."</label>".")"; 
                echo "Listado Red de Cloacas";
            ?>
        </span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            include('../funciones.php');
            echo "Fecha: ".fecha_min(date('Y-m-d'))." - Hora: ".date('G').':'.date('i').':'.date('s');
            
            //echo "Listado de caja (".fecha_min($fecha1)." - ".fecha_min($fecha2).")";
        ?>
    </div>
</div>
 
<div><?php echo "Emitido por: $rol (caja $numero_caja)";?></div>
 
<main class="">
<div class="form-group col-md-12">
	<div class="alert alert-success" role="alert">
    	<div class="container">
                 
            <?php
               include('../conexion.php');
               
               $tabla = "<table class='table table-striped table-hover'>        
               <thead>  
               <tr> 
               <td><strong>Titluar</strong></td>
               <td><strong>Loteo</strong></td>
               <td><strong>Lote</strong></td>
               <td><strong>Fecha</strong></td>
               <td><strong>Forma pago</strong></td>
               </tr>
               </thead>
               <tbody id='tbody-datos'>";
               
               switch($op)
               {    
                case 1:
                     
                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago 
                        FROM det_lotes as t1 inner join det_servicio as t2
                        on t1.lote = t2.lote
                        WHERE servicio = 'Red de Cloacas'
                        AND fecha_pago BETWEEN '$fecha_ini' AND '$fecha_fin'
                        AND t2.forma_pago <> ''
                        AND t1.loteo = '$loteo'
                        group by t1.lote";
                    
                    break;
                case 2:
                   
                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                        FROM det_lotes as t1 inner join det_servicio as t2
                        on t1.lote = t2.lote
                        WHERE servicio = 'Red de Cloacas'
                        AND fecha_pago BETWEEN '$fecha_ini' AND '$fecha_fin' 
                        AND t1.titular <>''
                        AND t2.forma_pago = ''
                        AND t1.loteo = '$loteo'
                        group by t1.lote"; 
                    
                    break;
                case 3:

                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                            FROM det_lotes as t1 inner join det_servicio as t2
                            on t1.lote = t2.lote
                            WHERE (servicio = 'Red de Cloacas')
                            AND (t2.fecha_pago BETWEEN '$fecha_ini' AND '$fecha_fin')
                            AND (t1.loteo = '$loteo')
                            AND t1.titular <> ''
                            group by t1.lote";

                    break;
                
                case 4:

                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                              FROM det_lotes as t1 inner join det_servicio as t2
                              on t1.lote = t2.lote
                              WHERE (servicio = 'Red de Cloacas') 
                              AND t1.loteo = '$loteo'
                              AND t2.forma_pago <> ''
                              group by t1.lote
                              order by t1.lote";
                    break;
                case 5:

                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                              FROM det_lotes as t1 inner join det_servicio as t2
                              on t1.lote = t2.lote
                              WHERE (servicio = 'Red de Cloacas') 
                              AND t1.loteo = '$loteo'
                              AND t1.titular <> ''
                              AND t2.forma_pago = ''
                              group by t1.lote
                              order by t1.lote";
                    
               }


               $res = mysqli_query($connection,$qry);
               while($datos = mysqli_fetch_array($res))
                {
                    $tabla.="<tr>
                            <td>".$datos['titular']."</td>
                            <td style='text-align: center;'>".$datos['loteo']."</td>
                            <td style='text-align: center;'>".$datos['lote']."</td>
                            <td style='text-align: center;'>".fecha_min($datos['fecha_pago'])."</td>
                            <td style='text-align: center;'>".$datos['forma_pago']."</td>
                            </tr>";

                }
                $tabla.="</tbody></table>";
                echo $tabla;
    		?>  			
             
    	</div>
    </div>
</div>
</div>
</body>
</html>


