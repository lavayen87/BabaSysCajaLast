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
  <?php include('menu_lateral.php');?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">
    <div class="container">
      <h2>Reimprimir Órden de pago</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="">
              <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div style="margin-bottom: 10px;">
                  <strong>Buscar por Nº de órden.</strong>
                </div>
                
                <input type="text" name="filtro-op" id="filtro-op">
                
                <button id="buscar-tr" name ="buscar-op" class="btn btn-success">Buscar</button>
                <button id="cancelar-busqueda-op" class="btn btn-secondary" style="display: inline-block;">Cancelar
                </button>
              </form>
              <hr>
                <?php  
                  $alerta = "";
                  $filtro = "";
                 if(isset($_POST['buscar-op']))
                 {
                  $filtro = $_POST['filtro-op'];
                  if($filtro != "")
                  {
                    include('conexion.php');
                    include('funciones.php');
                    $tabla = "<table class='table table-striped'>
                              <thead>
                                <tr>
                                  <th><strong>#</strong></th>
                                  <th><strong>Fecha</strong></th>
                                  <th><strong>Empresa</strong></th>
                                  <th><strong>Obra</strong></th>
                                  <th><strong>Cuenta</strong></th>                              
                                  <th><strong>Importe</strong></th>
                                  <th><strong>Acción</strong></th>
                                </tr>
                              </thead>
                              <tbody id='res-tr'>
                              </tbody>
                            ";
                    $qry = "SELECT * FROM orden_pago
                            WHERE numero_orden = '$filtro'
                            order by numero_orden";
                    $res = mysqli_query($connection, $qry);

                    if($res->num_rows > 0)
                    {
                      while($datos = mysqli_fetch_array($res))
                      {
                        $num_op = $datos['numero_orden'];
                        $tabla.="<tr>
                        <td>".$datos['numero_orden']."</td>
                        <td>".fecha_min($datos['fecha'])."</td>
                        <td>".$datos['empresa']."</td>
                        <td>".$datos['obra']."</td>
                        <td>".$datos['cuenta']."</td>
                        <td>"."$".number_format($datos['importe'],2,',','.')."</td>";
                        if($datos['moneda'] == 'pesos')
                        {
                          $tabla.="<td>"."<a href='factura/Reimprimir_op.php?num_op=$num_op' target='_blank' class='btn  btn-primary btn-solicitud-sop' id='".$datos['numero_orden']."'>
                                  <i class='fas fa-print'></i>
                                  </a>"."</td>
                                  </tr>";
                        }
                        else
                        $tabla.="<td>"."<a href='factura/Reimprimir_op_cheque.php?num_op=$num_op' target='_blank' class='btn  btn-primary btn-solicitud-sop' id='".$datos['numero_orden']."'>
                                  <i class='fas fa-print'></i>
                                  </a>"."</td>
                                  </tr>";      

                      }
                      $tabla.="</table>";
                      echo $tabla;
                    }
                    else
                    {
                      $alerta = "<strong>No se encontraron ordenes de pago.</strong>";
                    }
                  }
                  else
                  {
                    $alerta = "<strong><i class='fas fa-exclamation-triangle'></i>Debe ingresar un filtro de busqueda.</strong>";
                  }
                 }
                ?>
                <div style="display: none;" id="resultado-busqueda"></div>
                
                <div style="" id="alerta-resultado"><?php echo $alerta; ?></div>
                 
                <br><br>

                <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                  <strong>Desde:</strong> 
                  <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>">

                  <strong>Hasta:</strong> 
                  <input type="date" name="fecha_final" id="fecha_final" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>">

                  <!--strong>Nº de caja:</strong> <input type="number" name="num_caja" style="width: 60px;"-->
                  <input type="submit" name="listar-ordenes" value="Listar" id="btn-ordenes" class="btn btn-success" title='Listar caja'>
                </form>

                <hr>

                <?php
                  $tabla = "<table class='table table-striped'>
                            <thead>
                              <tr>
                                <th><strong>#</strong></th>
                                <th><strong>Fecha</strong></th>
                                <th><strong>Empresa</strong></th>
                                <th><strong>Obra</strong></th>
                                <th><strong>Cuenta</strong></th>                              
                                <th><strong>Importe</strong></th>
                                <th><strong>Acción</strong></th>
                              </tr>
                            </thead>
                            <tbody id='res-tr'>
                            </tbody>
                            ";
                  $f1 = "";
                  $f2 = "";
                  $alerta = "";
                  if(isset($_POST['listar-ordenes']))
                  {
                    if(isset($_POST['fecha_inicial']))
                      {
                        $f1 = $_POST['fecha_inicial'];
                      }
                    
                    if(isset($_POST['fecha_final']))
                    {
                      $f2 = $_POST['fecha_final'];
                    }

                    include('conexion.php');
                    include('funciones.php');

                    $qry = "SELECT * FROM orden_pago
                            WHERE fecha BETWEEN '$f1' AND '$f2'
                            order by numero_orden";
                    $res = mysqli_query($connection, $qry);

                    if($res->num_rows > 0)
                    {
                      while($datos = mysqli_fetch_array($res))
                      {
                        $num_op = $datos['numero_orden'];
                        $tabla.="<tr>
                        <td>".$datos['numero_orden']."</td>
                        <td>".fecha_min($datos['fecha'])."</td>
                        <td>".$datos['empresa']."</td>
                        <td>".$datos['obra']."</td>
                        <td>".$datos['cuenta']."</td>
                        <td>"."$".number_format($datos['importe'],2,',','.')."</td>";
                        if($datos['moneda'] == 'pesos')
                        {
                          $tabla.="<td>"."<a href='factura/Reimprimir_op.php?num_op=$num_op' target='_blank' class='btn  btn-primary btn-solicitud-sop' id='".$datos['numero_orden']."'>
                                  <i class='fas fa-print'></i>
                                  </a>"."</td>
                                  </tr>";
                        }
                        else
                        $tabla.="<td>"."<a href='factura/Reimprimir_op_cheque.php?num_op=$num_op' target='_blank' class='btn  btn-primary btn-solicitud-sop' id='".$datos['numero_orden']."'>
                                  <i class='fas fa-print'></i>
                                  </a>"."</td>
                                  </tr>";      

                      }
                      $tabla.="</table>";
                      echo $tabla;
                    }
                    else
                    {
                      $alerta = "<strong>No se encontraron ordenes de pago.</strong>";
                    }
                      
                  }
                  
                ?>

                <div style="" id="alerta-resultado"><?php echo $alerta; ?></div>
            
            </div>
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