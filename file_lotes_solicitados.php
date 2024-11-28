<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}
?>
 
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="img/logo-sistema.png">	
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
</head>
<body>
<div class="page-wrapper chiller-theme toggled">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  <?php include('menu_main.php');?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">
    <div class="container">
      <h2>Lotes y servicios</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
               
                <strong>Servicios</strong>
                <select name="servicio" id="servicio-lote">
                   <option value=""></option>
                   <option value="agua">Agua</option>
                   <option value="agrimensor">Agrimensor</option>
                   <option value="cloacas">Cloacas</option>
                   <option value="red_cloacas">Red Cloacas</option>
               </select> 

               <strong>Estado</strong>
                <select name="estado" id="estado-lote">
                   <option value=""></option>
                   <option value="Pendiente">Pendiente</option>
                   <option value="Solicitado">Solicitado</option>
                   <option value="Realizado">Realizado</option>
               </select>
               
               <button id="btn-lotes" name="btn-lotes" class="btn btn-success">listar</button>
                
                <?php

                    $serv="";
                    $est ="";
                    if(isset($_POST['servicio']))
                    {
                        $serv = $_POST['servicio'];
                    }
                    if(isset($_POST['estado']))
                    {
                        $est = $_POST['estado'];
                    }
                    
                    echo "<a href='factura/listado_lotes.php?serv=$serv&est=$est' type='submit' name='listado-lotes' id='listado-lotes'  target='_blank' style='float: right; display: none;'' class='btn btn-primary' title='Imprimir'><i class='fas fa-print'></i></a>";
                    echo"</br></br>";
                ?>

                <?php
                    $tabla = "<table class='table table-striped table-hover'>
                    <thead>  
                    <tr> 
                    <td><strong>Servicio</strong></td>
                    <td><strong>Lote</strong></td>
                    <td><strong>Fecha pago</strong></td>
                    <td><strong>Recibo</strong></td>
                    <td><strong>fecha solicitud</strong></td>
                    <td><strong>fecha Realizado</strong></td>
                    <td><strong>Estado</strong></td>
                    </tr>
                    </thead>
                    <tbody id='tbody-datos'>";

                    include('conexion.php');
                    include('funciones.php');

                    // Lotes solicitados

                    $qry = "SELECT * FROM agua as agu inner join agrimensor
                            as agr inner join cloacas as clo
                            on agu.lote = agr.lote = clo.lote
                            group by agu.lote
                            ";

                    $res = mysqli_query($connection, $qry);
                            
                    if($res->num_rows > 0)
                    {
                        while($datos = mysqli_fetch_array($res))
                        {
                            $tabla.="<tr>
                            <td>".'sevicio'."</td>
                            <td>".$datos['lote']."</td>
                            <td>".fecha_min($datos['fecha_pago'])."</td>
                            <td>".$datos['recibo']."</td>
                            <td>".fecha_min($datos['fecha_solicitud'])."</td>
                            <td>".fecha_min($datos['fecha_realizado'])."</td>";

                            if($datos['estado'] == 'Pendiente')
                            {
                              $tabla.="<td style='background: #F18C24;'>".$datos['estado']."</td>
                              </tr>";  
                            }
                            else
                            {
                              if($datos['estado'] == 'Solicitado')
                              {
                                $tabla.="<td style='background: #E3D822;'>".$datos['estado']."</td>
                                </tr>";  
                              }
                              else
                                  $tabla.="<td style='background: #91EC7F;'>".$datos['estado']."</td>
                                  </tr>";                                         
                            }
                        }

                        $tabla.="</tbody>";
                        echo $tabla;
                        echo"<script>
                            $('#listado-lotes').show();
                            </script>";
                    }
                    else
                    {
                        echo "<strong>No hay lotes solicitados.</strong>";
                    }

                    // Estados de lotes

                    if(isset($_POST['btn-lotes']))
                    {
                      if($_POST['servicio']!="" && $_POST['estado']!="")
                      {
                                             

                        $servicio = $_POST['servicio'];
                        $estado   = $_POST['estado'];

                        $qry = "SELECT * FROM $servicio
                                WHERE estado = '$estado' 
                                order by id";

                        $res = mysqli_query($connection, $qry);
                            
                        if($res->num_rows > 0)
                        {
                          while($datos = mysqli_fetch_array($res))
                          {
                            $tabla.="<tr>
                            <td>".$servicio."</td>
                            <td>".$datos['lote']."</td>
                            <td>".fecha_min($datos['fecha_pago'])."</td>
                            <td>".$datos['recibo']."</td>
                            <td>".fecha_min($datos['fecha_solicitud'])."</td>
                            <td>".fecha_min($datos['fecha_realizado'])."</td>";

                            if($datos['estado'] == 'Pendiente')
                            {
                              $tabla.="<td style='background: #F18C24;'>".$datos['estado']."</td>
                              </tr>";  
                            }
                            else
                            {
                              if($datos['estado'] == 'Solicitado')
                              {
                                $tabla.="<td style='background: #E3D822;'>".$datos['estado']."</td>
                                </tr>";  
                              }
                              else
                                  $tabla.="<td style='background: #91EC7F;'>".$datos['estado']."</td>
                                  </tr>";                                         
                            }
                          }
                          $tabla.="</tbody>";
                          echo $tabla;
                          echo"<script>
                              $('#listado-lotes').show();
                              </script>";
                        }
                        else
                        {
                          echo "<strong>No hay lotes con estado '$estado'.</strong>";
                        }
                        
                      }
                      else
                      {  
                        echo "<strong style='color: #AD0A35;'>
                              Debe seleccionar un servicio y estado
                              </strong>";
                      }
                    }
                ?>   
                  
                                           
            </form>
          </div>
        </div>
      </div>
    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>