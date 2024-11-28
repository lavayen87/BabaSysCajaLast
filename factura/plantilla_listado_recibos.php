
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

if($_GET['caja'])
{   
    $numero_caja = $_GET['caja'];
    include('../conexion.php');
    $qry = "SELECT nombre FROM usuarios WHERE numero_caja = '$numero_caja'";
    $res = mysqli_query($connection, $qry);
    $datos = mysqli_fetch_array($res);
    $nombre_usuario = $datos['nombre'];
    mysqli_close($connection);
}
else 
    $nombre_usuario = $nombre;

if($_GET['f1'] && $_GET['f2']){
    $fecha1 = $_GET['f1'];
    $fecha2 = $_GET['f2'];
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
            <?php echo $rol." (caja "."<label style='font-size: 13px;'>".$numero_caja."</label>".")"; ?>
        </span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            include('../conexion.php');
            include('../funciones.php');
            
            echo "Listado de recibos (".fecha_min($fecha1)." - ".fecha_min($fecha2).")";
        ?>
    </div>
</div>

<main class="">
<div class="form-group col-md-12">
	<div class="alert alert-success" role="alert">
    	<div class="container">
                 
            <?php
                
                
                
                include('../conexion.php');
                $total = 0;
                $tabla ="<table>
                         <thead>
                            <tr>
                            <th>#</th>
                            <th>Fecha</th>                  
                            <th>Titular</th>
                            <th>Loteo</th>
                            <th>Lote</th>
                            <th>Concepto</th>
                            <th>Importe</th>
                            </tr>
                         </thead>
                         <tbody>";           
               

                
                // Consigo datos de cobranza diaria
                $sql = "SELECT * FROM recibo
                        WHERE fecha BETWEEN '$fecha1' AND '$fecha2'
                        order by numero";
                $res = mysqli_query($connection, $sql);
                $t = "";
                
                while($datos=mysqli_fetch_array($res))
                {
                    $lote = $datos['lote'];
                    
                    $numero = $datos['numero'];
                    $t=get_code_recibo($lote, $numero, $fecha1, $fecha2);
                    
                    $tabla.= "<tr> 
                            <td>".$datos['numero']."</td>   
                            <td>".fecha_min($datos['fecha'])."</td>
                            <td>".$datos['titular']."</td> 
                            <td style='text-align: center;'>".$datos['loteo']."</td>
                            <td style='text-align: center;'>".$datos['lote']."</td>   
                            <td style='text-align: center;'>".$t."</td>";
                            if($datos['estado'] == 1)
                            {
                                $total+= $datos['importe'];
                                $tabla.="<td style='text-align: right;'>"."$".number_format($datos['importe'],2,',','.')."</td>";
                            } 
                            else
                            {
                                $tabla.="<td style='text-align: right;'><s>"."$".number_format($datos['importe'],2,',','.')."</s></td>"; 
                            }                                
                            
                            $tabla.="</tr>";
                }
                $tabla.="<tr>
                        <td colspan='5'></td>
                        <td style='text-align: right;'><strong>Total:</strong></td>
                        <td style='text-align: right;'>".'$'.number_format($total,2,',','.')."</td>
                        </tr>
                        </tbody>
                        <table>";
                
                
                echo $tabla;
                      
                            
    		?>  			
             
    	</div>
    </div>
</div>
</div>
</body>
</html>

