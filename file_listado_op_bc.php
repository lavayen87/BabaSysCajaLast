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
                  
              <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> <!--$_SERVER['PHP_SELF']-->
                   
                <div style=" width: 100%;" id="div-bc">
                      <strong>Desde:</strong> 
                      <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>" style="width: 125px;">

                      <strong>Hasta:</strong> 
                      <input type="date" name="fecha_final" id="fecha_final" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>" style="width: 125px;">
                      <input type="submit" name="listar-porfecha" value="Listar" id="btn-listar-bc" class="btn btn-success" style="margin-left: 15px;">
                      <?php 
                        echo "<a href='factura/listado_op_em_ob.php?num_caja=$numero_caja' type='submit'  id='print-listado'  target='_blank' style='float: right;  display: none;' class='btn btn-primary'>Imprimir</a>";

                      ?>
                </div>            
            
                <hr>

              </form>
              
              <?php 
                $total = 0.00;
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
                          <td style='text-align: right;'><strong>Importe</strong></td> 
                          </tr>
                          </thead>                        
                          <tbody id='tbody-datos'>";               

                // ORDENES DE PAGO POR EMPRESA Y OBRA BC
                if(isset($_POST['listar-porfecha']))
                {

                    $fecha_inicial = $_POST['fecha_inicial'];
                    $fecha_final   = $_POST['fecha_final'];
                    
                    include('conexion.php');
                    include('funciones.php');

                    $qry = "SELECT * FROM orden_pago 
                            where fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                            AND empresa = 'Buen clima' AND obra = 'Buen clima'
                            order by numero_orden";
                    $res = mysqli_query($connection, $qry);

                    // TOTAL A MOSTRAR EN PANTALLA
                    $q = "SELECT sum(importe) as total FROM orden_pago                   
                          where fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                          AND empresa = 'Buen clima' AND obra = 'Buen clima'";

                    $r = mysqli_query($connection, $q);
                    $get_total = mysqli_fetch_array($r);
                    $total = $get_total['total']; 

                    // VACIO LISTA TEMPORARL
                    $delete = "DELETE FROM orden_pago_temp";
                              //AND empresa = 'Buen Clima' AND obra = 'Buen clima'";
                    $res_delete = mysqli_query($connection, $delete);

                    // BUSCO ORDENES 
                    $select_gral = "SELECT * from orden_pago
                              WHERE empresa = 'Buen clima' AND obra = 'Buen clima'
                              AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                              order by numero_orden";
                    $res_gral = mysqli_query($connection, $select_gral);

                    while($array = mysqli_fetch_array($res_gral))
                    {
                      $num      = $array['numero_orden'];
                      $fecha    = $array['fecha'];
                      $num_caja = $array['numero_caja'];
                      $cuenta   = $array['cuenta'];
                      $detalle  = $array['detalle'];
                      $importe  = $array['importe'];  
                      $empresa  = $array['empresa'];
                      $obra     = $array['obra'];
                              
                      $insert_temp = "INSERT INTO orden_pago_temp 
                                      VALUES 
                                      ('$num',
                                      '$fecha',
                                      '$num_caja',
                                      '$cuenta',
                                      '$detalle',
                                      '$importe',
                                      0,
                                      '$empresa',
                                      '$obra')";
                      $res_insert_temp = mysqli_query($connection, $insert_temp);
                    }
                    /** aqui */

                    if($res->num_rows > 0)
                    {
                      while($datos = mysqli_fetch_array($res))
                      {
                        $tabla.= "<tr> 
                        <td style='width: 4%'>".$datos['numero_orden']."</td>  
                        <td style='width: 6%'>".fecha_min($datos['fecha'])."</td>
                        <td style='width:12%;'>".limitar_cadena($datos['empresa'],12)."</td>
                        <td style='width:12%;'>".limitar_cadena($datos['obra'],12)."</td>                                                           
                        <td style='width:20%;'>".limitar_cadena($datos['cuenta'],18)."</td>
                        <td style='width:32%;'>".limitar_cadena($datos['detalle'],35)."</td>
                        <td style='width:28%; text-align: right;'>"."$".number_format($datos['importe'],2,',','.')."</td>
                        </tr>";
                                    
                            
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