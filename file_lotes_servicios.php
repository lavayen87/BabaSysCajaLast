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

             
                <strong>Lote</strong>
                <input type="text" class="form-control" name="codigo_lote" style="width: 30%;">
                <br>
                <button id="find-lote" name="find-lote" class="btn btn-success">Buscar</button>
                <button id="no-find" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                <br><br>
                
                <div class="row">
                  <div id="content-nueva-empresa" class="col-lg-12">
                    <?php
                      if(isset($_POST['find-lote']))
                      {
                        if($_POST['codigo_lote'] == "")
                        {
                          echo "Debe ingresar el codigo del lote.";
                        }
                        else
                        {
                          $op = 0;  
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

                          
                          $l = $_POST['codigo_lote'];

                          $lote = strtoupper(substr($l,0,2)).substr($l,2,4);

                          $ini = strtoupper(substr($lote,0,2));

                          if($ini == 'BC' || $ini == 'AI' || $ini == 'TE')
                          {
                            $qry1 = "SELECT * from agua 
                                    where lote = '$lote'
                                    and estado = 'Realizado'";
                            $res1 = mysqli_query($connection, $qry1);
                         
                            /////////////////////////////////////////
                            $qry2 = "SELECT * from agrimensor 
                                    where lote = '$lote'
                                    and estado = 'Realizado'";
                            $res2 = mysqli_query($connection, $qry2);
                            /////////////////////////////////////////
                            $qry3 = "SELECT * from cloacas
                                    where lote = '$lote'
                                    and estado = 'Realizado'";
                            $res3 = mysqli_query($connection, $qry3);

                            if($res1->num_rows>0 && $res2->num_rows>0 && $res3->num_rows>0)
                            {
                              $op = 1;
                            }
                            else
                            {
                              $qry1 = "SELECT * from agua 
                                    where lote = '$lote'
                                    and estado <> 'Realizado'";
                              $res1 = mysqli_query($connection, $qry1);
                              /////////////////////////////////////////
                              $qry2 = "SELECT * from agrimensor 
                                      where lote = '$lote'
                                      and estado <> 'Realizado'";
                              $res2 = mysqli_query($connection, $qry2);
                              /////////////////////////////////////////
                              $qry3 = "SELECT * from cloacas
                                      where lote = '$lote'
                                      and estado <> 'Realizado'";
                              $res3 = mysqli_query($connection, $qry3);

                              $op = 2;
                            }

                            if($op == 1)
                            {

                              Echo "El lote "."<strong>".$lote."</strong>"." Tiene Posesion.</br>";

                              /*$qry = "SELECT t1.lote, t1.fecha_pago, t1.recibo, t1.estado, t1.fecha_solicitud, t1.fecha_realizado 
                                      FROM agua as t1 INNER JOIN agrimensor as t2 INNER JOIN cloacas as t3 
                                      ON t1.lote = t2.lote WHERE t2.lote LIKE '$lote' and t3.lote LIKE '$lote' 
                                      group by lote";
                              $res = mysqli_query($connection, $qry);

                              if($res->num_rows > 0)
                              {*/
                              while($datos=mysqli_fetch_array($res1))
                              {
                                  $tabla.="<tr>
                                  <td>".'Agua'."</td>
                                  <td>".$datos['lote']."</td>
                                  <td>".fecha_min($datos['fecha_pago'])."</td>
                                  <td>".$datos['recibo']."</td>
                                  <td>".fecha_min($datos['fecha_solicitud'])."</td>
                                  <td>".fecha_min($datos['fecha_realizado'])."</td>
                                  <td style='background: #91EC7F;'>".$datos['estado']."</td>
                                  </tr>";
                              }
                              while($datos=mysqli_fetch_array($res2))
                              {
                                $tabla.="<tr>
                                <td>".'Agrimensor'."</td>
                                <td>".$datos['lote']."</td>
                                <td>".fecha_min($datos['fecha_pago'])."</td>
                                <td>".$datos['recibo']."</td>
                                <td>".fecha_min($datos['fecha_solicitud'])."</td>
                                <td>".fecha_min($datos['fecha_realizado'])."</td>
                                <td style='background: #91EC7F;'>".$datos['estado']."</td>
                                </tr>";
                              }
                              while($datos=mysqli_fetch_array($res3))
                              {
                                $tabla.="<tr>
                                <td>".'Cloacas'."</td>
                                <td>".$datos['lote']."</td>
                                <td>".fecha_min($datos['fecha_pago'])."</td>
                                <td>".$datos['recibo']."</td>
                                <td>".fecha_min($datos['fecha_solicitud'])."</td>
                                <td>".fecha_min($datos['fecha_realizado'])."</td>
                                <td style='background: #91EC7F;'>".$datos['estado']."</td>
                                </tr>";
                              }
                              $tabla.="</tbody>";
                              
                              echo $tabla;
                            }
                            else{
                              Echo "El lote "."<strong>".$lote."</strong>"." no tiene Posesion.</br>";
                              while($datos=mysqli_fetch_array($res1))
                              {
                                  $tabla.="<tr>
                                  <td>".'Agua'."</td>
                                  <td>".$datos['lote']."</td>
                                  <td>".fecha_min($datos['fecha_pago'])."</td>
                                  <td>".$datos['recibo']."</td>
                                  <td>".fecha_min($datos['fecha_solicitud'])."</td>
                                  <td>".fecha_min($datos['fecha_realizado'])."</td>
                                  <td style='background: #F1A013;'>".$datos['estado']."</td>
                                  </tr>";
                              }
                              while($datos=mysqli_fetch_array($res2))
                              {
                                $tabla.="<tr>
                                <td>".'Agrimensor'."</td>
                                <td>".$datos['lote']."</td>
                                <td>".fecha_min($datos['fecha_pago'])."</td>
                                <td>".$datos['recibo']."</td>
                                <td>".fecha_min($datos['fecha_solicitud'])."</td>
                                <td>".fecha_min($datos['fecha_realizado'])."</td>
                                <td style='background: #F1A013;'>".$datos['estado']."</td>
                                </tr>";
                              }
                              while($datos=mysqli_fetch_array($res3))
                              {
                                $tabla.="<tr>
                                <td>".'Cloacas'."</td>
                                <td>".$datos['lote']."</td>
                                <td>".fecha_min($datos['fecha_pago'])."</td>
                                <td>".$datos['recibo']."</td>
                                <td>".fecha_min($datos['fecha_solicitud'])."</td>
                                <td>".fecha_min($datos['fecha_realizado'])."</td>
                                <td style='background: #F1A013;'>".$datos['estado']."</td>
                                </tr>";
                              }
                              $tabla.="</tbody>";
                              
                              echo $tabla;
                            }
                   
                          }
                          else{
                            echo "No se encotro el lote $lote";
                          }
                          
                          /*{
                            case '1':
           
                              Echo "Tiene Posesion.</br>";
                              Echo $op."</br>";
                              while($datos = mysqli_fetch_array($res1))
                              {
                                  $tabla.="<tr>
                                  <td style='width: 7%;'>".$datos['lote']."</td>
                                  <td style='width: 15%;'>".fecha_min($datos['fecha_pago'])."</td>
                                  <td style='width: 10%;'>".$datos['recibo']."</td>
                                  <td style='width: 20%;'>".fecha_min($datos['fecha_solicitud'])."</td>
                                  <td style='width: 20%;'>".fecha_min($datos['fecha_realizado'])."</td>
                                  <td style='color: blue; width: 20%;'>".$datos['estado']."</td>";
                              }
                              $tabla.="</tbody></table>";
                              echo $tabla;
                              
                              break;

                            case '2':

                              
                              Echo "No Tiene Posesion.</br>";
                              Echo $op."</br>";
                              while($datos = mysqli_fetch_array($res1))
                              {
                                  $tabla.="<tr>
                                  <td style='width: 7%;'>".$datos['lote']."</td>
                                  <td style='width: 15%;'>".fecha_min($datos['fecha_pago'])."</td>
                                  <td style='width: 10%;'>".$datos['recibo']."</td>
                                  <td style='width: 20%;'>".fecha_min($datos['fecha_solicitud'])."</td>
                                  <td style='width: 20%;'>".fecha_min($datos['fecha_realizado'])."</td>
                                  <td style='color: blue; width: 20%;'>".$datos['estado']."</td>";
                              }
                              $tabla.="</tbody></table>";
                              echo $tabla;
                          }
                          */

                        }
                      }
                    ?>   
                  </div>
                </div>                           
                
              
             
              <br>
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