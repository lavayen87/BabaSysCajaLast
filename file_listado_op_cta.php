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
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single4').select2();
  });
</script>
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
      <h2>Listado órdenes de pago</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            
              <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> <!--$_SERVER['PHP_SELF']-->

                  <div style=" float: left;">
                    <strong>Desde:</strong> <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>" style="width: 125px;">

                    <strong>Hasta:</strong> <input type="date" name="fecha_final" id="fecha_final" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>" style="width: 125px;">

                  </div> 

                  <br>
                  <hr>
                  

                  <div style=" float: left;"> 

                  <strong>Empresa:</strong>
                    <select name="empresa" style="width: 126px;">
                        <?php  
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM empresas";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                            echo "<option value='".$misdatos['nombre_empresa']."' id='".$misdatos['id_empresa']."'>".$misdatos['nombre_empresa']."</option>"; 
                          }
                          mysqli_close ($connection);
                        ?>
                    </select>        


                    <strong>Cuenta</strong>
                    <select  class="js-example-basic-single4 form-control" name="cuenta">
                        <option value=""><?php echo ""; ?></option> 
                        <!-- js-example-basic-multiple name="states[]" multiple="multiple" para select multiple-->
                        <?php
                        include("conexion.php");
                        $consulta = "SELECT DISTINCT * FROM cuentas ORDER BY descripcion";
                        $resultado = mysqli_query($connection , $consulta);

                        while($misdatos = mysqli_fetch_assoc($resultado))
                        { 
                          echo "<option value='".$misdatos['descripcion']."'>".$misdatos['descripcion']."</option>"; 
                        }
                        mysqli_close ($connection);
                        ?>          
                    </select>

                  </div>

                   <input type="submit" name="listar" value="Listar" id="btnlistar" class="btn btn-success" style="margin-left: 15px;">
                  
                  <?php  
                    $f1 = "";
                    $f2 = "";
                    $emp = "";
                    $cta = "";
                    if(isset($_POST['fecha_inicial']))
                    {
                      $f1 = $_POST['fecha_inicial'];
                    }

                    if(isset($_POST['fecha_final']))
                    {
                      $f2 = $_POST['fecha_final'];
                    }

                    if(isset($_POST['empresa']))
                    {
                      $emp = $_POST['empresa'];
                    }

                    if(isset($_POST['cuenta']))
                    {
                      $cta = $_POST['cuenta'];
                    }
                    //factura/listado_op_cuenta.php
                    echo "<a href='factura/listado_op_cuenta.php?fecha_inicial=$f1&fecha_final=$f2&emp=$emp&cta=$cta' type='submit' name='print-listado'  id='print-listado'  target='_blank' style='float: right;  display: none;' class='btn btn-primary'>Imprimir</a>";
                  ?>
                  

              </form>

              <br>
              <br>
              
              <?php 

                $alerta = "";
                $total = 0.00;
                $tabla = "<table class='table table-striped'>
                              <thead>  
                                <tr>  
                                  <td><strong>Nº</strong></td>                              
                                  <td><strong>Fecha</strong></td>
                                  <td><strong>Empresa</strong></td>
                                  <td><strong>Obra</strong></td>
                                  <td><strong>Cuenta</strong></td>
                                  <td><strong>Detalle</strong></td>
                                  <td><strong>Importe</strong></td>     
                                </tr>
                              </thead>
                              <tbody id='tbody-datos'>";

                if(isset($_POST['listar']))
                {
                    $empresa = $_POST['empresa'];  
                    $cuenta = $_POST['cuenta'];         
                    $fecha_inicial = $_POST['fecha_inicial'];
                    $fecha_final   = $_POST['fecha_final'];
                    $res ="";
                    include('conexion.php');
                    include('funciones.php');
                        
                    if($numero_caja == 0 || $numero_caja == 12 || $numero_caja == 9 || $numero_caja == 2)
                    {
                      //Consulta de datos
                      $qry = "SELECT
                                  c.anulado anulado,
                                  c.numero_caja,
                                  o.numero_orden,
                                  o.fecha,
                                  o.empresa,
                                  o.obra,
                                  o.cuenta,
                                  o.detalle,
                                  o.importe
                                FROM orden_pago o inner join caja_gral c 
                                on o.numero_orden = c.numero
                                WHERE (o.cuenta = '$cuenta' or '$cuenta' = '')
                                AND (o.empresa = '$empresa' or '$empresa' = '')
                                AND o.fecha between '$fecha_inicial' and '$fecha_final'";

                      $res = mysqli_query($connection, $qry);

                      // Importe Total
                      $q = "SELECT sum(o.importe) as total FROM orden_pago o inner join caja_gral c 
                                on o.numero_orden = c.numero
                                WHERE (o.cuenta = '$cuenta' or '$cuenta' = '')
                                AND (o.empresa = '$empresa' or '$empresa' = '')
                                AND o.fecha between '$fecha_inicial' and '$fecha_final'
                                AND c.anulado = 0";

                      $r = mysqli_query($connection, $q);
                    }
                    else
                    {
                      //Consulta de datos
                      $qry = "SELECT
                                  c.anulado anulado,
                                  c.numero_caja,
                                  o.numero_orden,
                                  o.fecha,
                                  o.empresa,
                                  o.obra,
                                  o.cuenta,
                                  o.detalle,
                                  o.importe
                                FROM orden_pago o inner join caja_gral c 
                                on o.numero_orden = c.numero
                                WHERE (o.cuenta = '$cuenta' or '$cuenta' = '')
                                AND (o.empresa = '$empresa' or '$empresa' = '')
                                AND o.fecha between '$fecha_inicial' and '$fecha_final'
                                AND c.numero_caja = '$numero_caja'";

                      $res = mysqli_query($connection, $qry);

                      // Importe Total
                      $q = "SELECT sum(o.importe) as total FROM orden_pago o inner join caja_gral c 
                                on o.numero_orden = c.numero
                                WHERE (o.cuenta = '$cuenta' or $cuenta = '')
                                AND (o.empresa = '$empresa' or $empresa = '')
                                AND o.fecha between '$fecha_inicial' and '$fecha_final'
                                AND c.anulado = 0
                                AND c.numero_caja = '$numero_caja'";

                      $r = mysqli_query($connection, $q);
                    }  

                    if($r)
                    {
                      $get_total = mysqli_fetch_array($r);
                      $total = $get_total['total']; 
                    } 
                      
                    if($res->num_rows > 0)
                    {        
                      while($datos = mysqli_fetch_array($res))
                      {
                        $importe_op = $datos['anulado'] == 1 ? "<s class='canceled'>"."$".number_format($datos['importe'],2,',','.')."</s>"  : "$".number_format($datos['importe'],2,',','.');
                        
                        $tabla.= "<tr> 
                                  <td style='width: 4%;'>".$datos['numero_orden']."</td>
                                  <td style='width:7%;'>".fecha_min($datos['fecha'])."</td>
                                  <td>".$datos['empresa']."</td>
                                  <td>".$datos['obra']."</td>                                   
                                  <td>".limitar_cadena($datos['cuenta'],15)."</td>
                                  <td>".limitar_cadena($datos['detalle'],20)."</td>
                                  <td>".$importe_op."</td>
                                  </tr>";
                      }

                      $tabla.="<tr>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td>"."<strong style='float: right'>Total: </strong>"."</td>
                                  <td>"."<strong>"."$".number_format($total,2,',','.')."</strong>"."</td>   
                                  </tr>
                                  </tbody>";

                      mysqli_close ($connection);      
                      echo $tabla;
                      echo "<script>$('#print-listado').show();</script>";
                    }
                    else $alerta = "<strong style='color: #B21F00;''>No se encontraron ordenes de pago.</strong>";
                }
              ?>
           
            <div><?php echo $alerta; ?></div> 
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