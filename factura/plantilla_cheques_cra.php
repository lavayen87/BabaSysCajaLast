
<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $rol = $_SESSION['rol'];
  $numero_caja = $_SESSION['numero_caja'];
}

$numero_caja = $_GET['num_caja'];
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
                if($numero_caja == 12){
                    echo "Listado de cheques";  
                }
                else    
                    echo "Cheques en cartera - ".$rol; 
                ?>
        </span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            include('../conexion.php');
            include('../funciones.php');
            
            $fecha = date('Y-m-d');
            $hora = date('G').':'.date('i').':'.date('s');
            echo fecha_min($fecha)." - "." Hora: ".$hora;
        ?>
    </div>
</div>

<br>

<main class="">
<div class="form-group col-md-12">
	<div class="alert alert-success" role="alert">
    	<div class="container">
                 
            <?php
                $cabecera = "<table>        
                <thead>  
                <tr> 
                <td><strong>Vence</strong></td>
                <td><strong>Número</strong></td>
                <td><strong>Banco</strong></td>
                <td><strong>Entregó</strong></td>
                <td><strong>Recibió</strong></td>
                <td><strong>Entregado</strong></td>
                <td><strong>Importe</strong></td>
                <td><strong>Estado</strong></td>
                </tr>
                </thead>
                <tbody>";
                $tabla = "";
                $destino = "";
                
                if($numero_caja == 4 || $numero_caja == 9 || $numero_caja == 12)
                {
                $qry = "SELECT * FROM cheques_cartera 
                        WHERE estado = 'En cartera'
                        ORDER BY fecha_vto";
                }
                else
                {
                $qry = "SELECT * FROM cheques_cartera 
                        WHERE (num_caja_origen = '$numero_caja'
                        or num_caja_destino = '$numero_caja')
                        and (activo < 3)
                        ORDER BY fecha_vto";
                }
                            
                $res = mysqli_query($connection, $qry);
                
                $tabla.=$cabecera;
                

                while($datos = mysqli_fetch_array($res))
                {
                    if($datos['estado']=="En cartera")
                    {
                        if($numero_caja == 4 || $numero_caja == 9 || $numero_caja == 12)
                        {
                            if($datos['caja_destino'] != "" ) 
                            {
                                $destino = $datos['caja_destino'];
                            }
                            else{
                                 $destino = $datos['caja_origen'];
                            }
                            $campo=$datos['estado']." - ".$destino;
                            
                        }
                        else 
                            $campo="<strong style='color:green;'>".$datos['estado']."</strong>";                         
                           
                    }
                   
                    else
                    {
                        if($datos['estado']=="Transferido")
                        {
                            if($numero_caja == 4 || $numero_caja == 9 || $numero_caja == 12)
                            {
                                if($datos['caja_destino'] != "" )
                                {
                                    $destino = $datos['caja_destino'];
                                }
                                else{ 
                                    $destino = $datos['caja_origen'];
                                }
                                $campo=$datos['estado']." (caja ".$destino.")";                         
                                
                                
                            }
                            else 
                                $campo="<strong style='color:blue;'>".$datos['estado']."</strong>";                         
                                
                        }
                        else{          
                            $campo="<strong>".$datos['estado']."</strong>";                         
                                                           
                        }
                    } 

                    $tabla.="<tr>
                    <td style='width: 7%;'>".fecha_min($datos['fecha_vto'])."</td>
                    <td style='text-align: center ; width: 8%;'>".$datos['num_cheque']."</td>
                    <td style='width: 10%; text-align: center ;'>".$datos['banco']."</td>
                    <td style='text-align: center ;'>".limitar_cadena($datos['entrego'],18)."</td>
                    <td style='text-align: center ;'>".limitar_cadena($datos['persona_pago'],18)."</td>
                    <td style='width: 7%; text-align: center;'>".fecha_min($datos['fecha_entrega'])."</td>
                    <td style='width: 13%; text-align: right;'>"."$".number_format($datos['importe'],2,',','.')."</td>
                    <td style='text-align: center ;'>".$campo."</td>";
                          
                   
                            
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

