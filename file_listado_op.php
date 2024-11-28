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
    $('.js-example-basic-single7').select2();
  });
</script>
<style>

table thead tr td{
  padding-bottom: 5px; 
}
table thead{
  border-bottom: 1px solid black; 
}
tr:nth-child(odd){
    background:;
}
  tr:nth-child(even){
    background: #B6CCB2;
}

td{
  font color: #60655F;
}
</style>
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
                  
              <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                   
                <div style=" width: 100%;" id="div-bc">
                      <strong>Desde:</strong> 
                      <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>" style="width: 125px;">

                      <strong>Hasta:</strong> 
                      <input type="date" name="fecha_final" id="fecha_final" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>" style="width: 125px;">
                      
                </div> 
           
                <br>
                <hr>
                  
                <div style="width: 100%;" id="div-gral">

                    <strong>Empresa:</strong>
                    <select name="name-empresa" style="width: 126px;">
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

                    <strong>Obra:</strong>
                    <select name="name-obra" style="width: 115px;">
                        <option value="">todas</option>
                        <?php  
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM obras";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                            echo "<option value='".$misdatos['nombre_obra']."' id='".$misdatos['id_obra']."'>".$misdatos['nombre_obra']."</option>"; 
                          }
                          mysqli_close ($connection);
                        ?>
                    </select>
          
                    <input type="submit" name="listar-porfecha" value="Listar" id="btn-listar-porfecha" class="btn btn-success" style="margin-left: 15px;">
                  
                    <br>
                    <br>
                    <hr>
                    
                    
                    
                    <?php  
                      if(isset($_POST['fecha_inicial']))
                      {
                        $f1 = $_POST['fecha_inicial'];
                      }
                      else $f1 = "";
                      if(isset($_POST['fecha_final']))
                      {
                        $f2 = $_POST['fecha_final'];
                      }
                      else $f2 = "";

                      if(isset($_POST['name-empresa']))
                        $emp = $_POST['name-empresa'];
                      else $emp = "";

                      if(isset($_POST['name-obra']))
                        $ob = $_POST['name-obra'];
                      else $ob = "";
                     
                        
                      // imprimir listado de todas las ordenes de pago
                      //factura/listado_op_gral.php
                      echo "<a href='factura/listado_op_gral.php' type='submit'  id='print-listado-gral'  target='_blank' style='float: right;  display: none;' class='btn btn-primary'>Imprimir</a>";
                      
                      // Boton Listar todas las ordenes de pago
                      if($numero_caja == 0 || $numero_caja == 12 || $numero_caja == 9 || $numero_caja == 2)
                      {
                        echo "<input type='submit' name='listar-todo' value='Listar Todas' id='btn-listar-todo' class='btn btn-success'>
                          ";
                      }
                        
                      // imprimir listado
                      //echo "<a href='factura/listado_op_em_ob.php?num_caja=$numero_caja&fecha1=$f1&fecha2=$f2' type='submit'  id='print-listado'  target='_blank' style='float: right;  display: none;' class='btn btn-primary'>Imprimir</a>";
                      echo "<a href='factura/listado_op_em_ob.php?num_caja=$numero_caja&fecha1=$f1&fecha2=$f2&emp=$emp&ob=$ob' type='submit'  id='print-listado'  target='_blank' style='float: right;  display: none;' class='btn btn-primary'>Imprimir</a>";
                    ?>
                </div>

                <br>
                  
              </form>

              <!--strong style="color: #217DB1;"  id="fechas-listar">
                Ingrese fechas para listar.
              </strong-->
              
              <?php 
                $total = 0.00; // class='table table-striped'>
                $res ="";
                $res_eo="";
                $tabla = "<table style='width: 100%;'>
                          <thead>  
                          <tr> 
                          <td><strong>Nº</strong></td>                               
                          <td><strong>Fecha</strong></td>
                          <td><strong>Empresa</strong></td>
                          <td><strong>Obra</strong></td>
                          <td><strong>Cuenta contable</strong></td>
                          <td><strong>Detalle</strong></td>
                          <td style='float: right;'><strong>Importe</strong></td>      
                          </tr>
                          </thead>
                          <tbody id='tbody-datos'>";
                            
                // Todas las ordenes de pago
                if(isset($_POST['listar-todo']))
                { 
                  include('conexion.php');
                  include('funciones.php');

                  //echo "<script>alert('Todas las ordenes de pago');</script>";

                  //$qry = "SELECT * from orden_pago order by fecha";

                  $qry = "SELECT *
                          FROM orden_pago op inner join caja_gral cj
                          on op.numero_orden = cj.numero
                          order by op.fecha";

                  $res = mysqli_query($connection, $qry);
                  if($res->num_rows > 0)
                  {             
                    //$q = "SELECT sum(importe) as total FROM orden_pago"; 
                    
                    $q = "SELECT ( 
                          (SELECT sum(importe) FROM orden_pago) - (SELECT sum(op.importe)
                          FROM orden_pago op inner join caja_gral cj
                            on op.numero_orden = cj.numero
                            WHERE cj.anulado = 1)
                          ) as total"; 

                    $r = mysqli_query($connection, $q);

                    
                    $get_total = mysqli_fetch_array($r);
                    $total = $get_total['total'];
                    

                    while($datos = mysqli_fetch_array($res))
                    {
                      $importe_op = $datos['anulado'] == 1 ? "<s class='candeled'>"."$".number_format($datos['importe'],2,',','.')."</s>"  : "$".number_format($datos['importe'],2,',','.');

                      $tabla.= "<tr>  
                      <td style='width: 4%'>".$datos['numero_orden']."</td>  
                      <td style='width: 6%'>".fecha_min($datos['fecha'])."</td>
                      <td style='width:12%;'>".limitar_cadena($datos['empresa'],12)."</td>
                      <td style='width:12%;'>".limitar_cadena($datos['obra'],12)."</td>                                                           
                      <td style='width:20%;'>".limitar_cadena($datos['cuenta'],18)."</td>
                      <td style='width:32%;'>".limitar_cadena($datos['detalle'],35)."</td>
                      <td style='width:28%; text-align: right;'>".$importe_op."</td>
                      </tr>";
                                //<td><button class='btn btn-success btn-duplicate-op' id='".$datos['numero_orden']."'>Imprimir</button></td>
                    }
                    mysqli_close ($connection);
                    $tabla.="<tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>"."<strong style='float: left'>Total: </strong>"."</td>
                    <td style='text-align: right;'>"."<strong>"."$".number_format($total,2,',','.')."</strong>"."</td>          
                    </tr>
                    </tbody>";
                    echo $tabla; 
                    
                    echo "<script>$('#fechas-listar').hide();</script>";
                    echo "<script>$('#print-listado').hide();</script>";
                    echo "<script>$('#print-listado-gral').show();</script>";
                  }               
                  else echo "<strong style='color: #B21F00;''>No se encontraron ordenes de pago.</strong>";
                }
                

                // ORDENES DE PAGO POR EMPRESA Y OBRA
                if(isset($_POST['listar-porfecha']))
                {
                  
                  $empresa = $_POST['name-empresa'];  
                  $obra = $_POST['name-obra'];       
                  $fecha_inicial = $_POST['fecha_inicial'];
                  $fecha_final   = $_POST['fecha_final'];
                    
                  include('conexion.php');
                  include('funciones.php');

                 

                  if($numero_caja == 0 || $numero_caja == 12 || $numero_caja == 9 || $numero_caja == 2)
                  {

                    // BUSCO ORDENES 
                    $select_gral = "SELECT 
                                      cj.anulado anulado,
                                      cj.numero_caja,
                                      op.numero_orden,
                                      op.fecha,
                                      op.empresa,
                                      op.obra,
                                      op.cuenta,
                                      op.detalle,
                                      op.importe
                                    FROM orden_pago op inner join caja_gral cj
                                    on op.numero_orden = cj.numero
                                    WHERE op.fecha between '$fecha_inicial' and '$fecha_final'
                                    and (op.empresa = '$empresa' or '$empresa' = '') 
                                    and (op.obra = '$obra' or '$obra' = '')                                   
                                    order by op.numero_orden";

                    $res_gral = mysqli_query($connection, $select_gral);
                    
                    //calculo importe total
                    $q = "SELECT sum(op.importe) as total 
                          FROM orden_pago op inner join caja_gral cj
                            on op.numero_orden = cj.numero
                          WHERE op.fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                            and (op.empresa = '$empresa' or '$empresa' = '') 
                            and (op.obra = '$obra' or '$obra' = '')
                            and cj.anulado = 0";
                    $r = mysqli_query($connection, $q);


                  }
                  else
                  {
                   
                    $select_gral = "SELECT 
                                      cj.anulado anulado,
                                      cj.numero_caja,
                                      op.numero_orden,
                                      op.fecha,
                                      op.empresa,
                                      op.obra,
                                      op.cuenta,
                                      op.detalle,
                                      op.importe
                                    FROM orden_pago op inner join caja_gral cj
                                    on op.numero_orden = cj.numero
                                    WHERE op.fecha between '$fecha_inicial' and '$fecha_final'
                                    and (op.empresa = '$empresa' or '$empresa' = '') 
                                    and (op.obra = '$obra' or '$obra' = '')                                   
                                    and cj.numero_caja = '$numero_caja'
                                    order by op.numero_orden";                    
                    
                    $res_gral = mysqli_query($connection, $select_gral);

                    //calculo importe total 
                    $q = "SELECT sum(op.importe) as total 
                          FROM orden_pago op inner join caja_gral cj
                            on op.numero_orden = cj.numero
                          WHERE cj.numero_caja = '$numero_caja'                        
                            and op.fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                            and (op.empresa = '$empresa' or '$empresa' = '') 
                            and (op.obra = '$obra' or '$obra' = '')
                            and cj.anulado = 0
                            and cj.numero_caja = '$numero_caja'";
                    $r = mysqli_query($connection, $q);
                      
                  }
                  
                  if($r)
                  {
                      $get_total = mysqli_fetch_array($r);
                      $total = $get_total['total'];
                  } 

                  // VOLCADO DE DATOS EN PANTALLA
                      
                  if($res_gral->num_rows > 0)
                  {
                        
                      while($datos = mysqli_fetch_array($res_gral))
                      {
                        $importe_op = $datos['anulado'] == 1 ? "<s class='candeled'>"."$".number_format($datos['importe'],2,',','.')."</s>"  : "$".number_format($datos['importe'],2,',','.');
                        
                        $tabla.= "<tr>   
                        <td style='width: 4%'>".$datos['numero_orden']."</td>  
                        <td style='width: 6%'>".fecha_min($datos['fecha'])."</td>
                        <td style='width:12%;'>".$datos['empresa']."</td>
                        <td style='width:12%;'>".$datos['obra']."</td>                                                           
                        <td style='width:20%;'>".limitar_cadena($datos['cuenta'],18)."</td>
                        <td style='width:32%;'>".limitar_cadena($datos['detalle'],35)."</td>
                        <td style='width:28%; text-align: right;'>".$importe_op."</td>
                        </tr>";
                                    // <td><button class='btn btn-success btn-duplicate-op' id='".$datos['numero_orden']."'>Imprimir</button></td>
                      }
                      mysqli_close ($connection); 
                      $tabla.="<tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td>"."<strong style='float: left;'>Total: </strong>"."</td>
                              <td style='text-align: right;'>"."<strong>"."$".number_format($total,2,',','.')."</strong>"."</td>          
                              </tr>
                              </tbody>";
                      echo $tabla;
                      echo "<script>$('#print-listado').show();</script>";
                      echo "<script>$('#fechas-listar').hide();</script>";
                  }
                  else
                  {
                    echo "<script>$('#fechas-listar').hide();</script>"; 
                    echo "<strong style='color: #B21F00;''>No se encontraron ordenes de pago.</strong>";
                  } 
                  
                }
                
              ?>
              
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