
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
      <h2>Listado de retiros</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">
              <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                <div style=" float: left;">
                  <strong>Desde:</strong> 
                  <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>" style="width: 125px;">

                  <strong>Hasta:</strong> 
                  <input type="date" name="fecha_final" id="fecha_final" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>" style="width: 125px;">

                  <strong>Personal:</strong>
                  <select name="name-personal">
                        <?php  
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM personal_habilitado";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                            echo "<option value='".$misdatos['nombre']."' id='".$misdatos['id_personal']."'>".$misdatos['nombre']."</option>"; 
                          }
                          mysqli_close ($connection);
                        ?>
                  </select>

                  <input type="submit" name="listar-retiros-porfecha" value="Listar" id="btn-listar-porfecha" class="btn btn-success" style="margin-left: 30px;">

                </div>
                <br><br>
                <hr>
                <input type="submit" name="listar-todos-retiros" value="Listar Todos" id="btn-listar-toto" class="btn btn-success">
                  <!--input type="submit" name="prueba" value="prueba" id="prueba" class="btn btn-primary"-->
                  <?php
                    if(isset($_POST['name-personal'])){
                      $personal = $_POST['name-personal'];
                    } 
                    else $personal = ""; 
                    if(isset($_POST['fecha_inicial'])){
                      $f1 = $_POST['fecha_inicial'];
                    }
                    else $f1 = "";
                    if(isset($_POST['fecha_final'])){
                      $f2 = $_POST['fecha_final'];
                    }
                    else $f2 = "";
                   echo "<a href='factura/listado_retiros.php?personal=$personal&fecha1=$f1&fecha2=$f2' name='print-listado'  id='listado-retiros'  target='_blank' style='float: right;  display: none;' class='btn btn-primary'>Imprimir</a>";

                   echo "<a href='factura/listado_todos_los_retiros.php' name='print-listado'  id='listado-todos-retiros'  target='_blank' style='float: right;  display: none;' class='btn btn-primary'>Imprimir</a>";
                  ?>
              </form>
               
              
              <br>
              <strong style='color: blue;' id="fechas-listar">Seleccione fechas para listar.</strong>
              <br>
              <?php 
                $total = 0.00;
                $tabla = "<table class='table table-striped'>
                          <thead>  
                          <tr>                                
                          <td><strong>Fecha</strong></td>
                          <td style='width: 20px;'><strong>Nº caja</strong></td>
                          <td><strong>Personal</strong></td>
                          <td><strong>Concepto</strong></td>
                          <td><strong>Cuenta</strong></td>
                          <td><strong>Detalle</strong></td>
                          <td><strong>Importe</strong></td>
                          </tr>
                          </thead>
                          <tbody id='tbody-datos'>";
                              
                // Todos los retiros
                if(isset($_POST['listar-todos-retiros']))
                { 
                  include('conexion.php');
                  include('funciones.php');
                  $qry = "SELECT * from retiros
                          order by fecha_retiro";

                  $res = mysqli_query($connection, $qry);
                  if($res->num_rows > 0)
                  {             
                    $q = "SELECT sum(importe) as total FROM retiros";
                    $r = mysqli_query($connection, $q);
                    if($r){
                          $get_total = mysqli_fetch_array($r);
                          $total = $get_total['total'];
                    } 
                    while($datos = mysqli_fetch_array($res))
                    {
                        $tabla.="<tr>    
                                <td>".fecha_min($datos['fecha_retiro'])."</td>
                                <td>".$datos['numero_caja']."</td>
                                <td>".$datos['personal_habilitado']."</td>                        
                                <td>".$datos['concepto']."</td>
                                <td>".$datos['cuenta']."</td>
                                <td>".limitar_cadena(ucfirst(strtolower($datos['observaciones'])),27)."</td>
                                <td>"."$".number_format($datos['importe'],2,',','.')."</td>
                                </tr>";               
                    }
                    mysqli_close ($connection);
                    $tabla.="<tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>"."Total:"."</td>
                                <td>"."$".number_format($total,2,',','.')."</td>
                                
                              </tr>
                              </tbody>";
                    echo $tabla; 
                    
                    echo "<script>$('#fechas-listar').hide();</script>";
                    echo "<script>$('#listado-retiros').hide();</script>";
                    echo "<script>$('#listado-todos-retiros').show();</script>";

                  }               
                  else echo "<strong>No se realizaron retiros.</strong>";
                }
                

                // retiros por fecha y personal
                if(isset($_POST['listar-retiros-porfecha']))
                {
                           
                    $fecha_inicial = $_POST['fecha_inicial'];
                    $fecha_final   = $_POST['fecha_final'];
                    $personal =  $_POST['name-personal'];

                    if($fecha_inicial == $fecha_final)
                    {

                      include('conexion.php');
                      include('funciones.php');

                      $qry = "SELECT * FROM retiros 
                              WHERE personal_habilitado = '$personal'
                              AND fecha_retiro = '$fecha_inicial'
                              order by fecha_retiro";
                      $res = mysqli_query($connection, $qry);
                      if($res->num_rows > 0)
                      {
                        $q = "SELECT sum(importe) as total FROM retiros
                              WHERE  personal_habilitado = '$personal'
                              AND fecha_retiro = '$fecha_inicial' ";
                        $r = mysqli_query($connection, $q);
                        if($r){
                          $get_total = mysqli_fetch_array($r);
                          $total = $get_total['total'];
                        } 

                        while($datos = mysqli_fetch_array($res))
                        {
                            $tabla.="<tr>    
                                    <td>".fecha_min($datos['fecha_retiro'])."</td>
                                    <td>".$datos['numero_caja']."</td>
                                    <td>".$datos['personal_habilitado']."</td>                        
                                    <td>".$datos['concepto']."</td>
                                    <td>".$datos['cuenta']."</td>
                                    <td>".limitar_cadena(ucfirst(strtolower($datos['observaciones'])),27)."</td>
                                    <td>"."$".number_format($datos['importe'],2,',','.')."</td>
                                    </tr>";               
                        }
                        mysqli_close ($connection);
                        $tabla.="<tr>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td>"."Total: "."</td>
                                  <td>"."$".number_format($total,2,',','.')."</td>
                                  </tr>
                                </tbody>";
                        echo $tabla;

                        echo "<script>$('#fechas-listar').hide();</script>";

                        echo "<script>$('#listado-retiros').show();</script>";

                      }
                      else
                      {
                       echo "<script>$('#fechas-listar').hide();</script>"; 
                       echo "<strong>No se encontraron retiros para '$personal' en las fechas indicadas.</strong>";
                      }

                    }  
                    else
                    {

                      if($fecha_inicial <= $fecha_final)
                      {
                        include('conexion.php');
                        include('funciones.php');
                        $qry = "SELECT * FROM retiros 
                                WHERE personal_habilitado = '$personal'
                                AND fecha_retiro BETWEEN '$fecha_inicial' AND '$fecha_final'
                                order by fecha_retiro";
                        $res = mysqli_query($connection, $qry);
                        if($res->num_rows > 0)
                        {
                          $q = "SELECT sum(importe) as total FROM retiros
                                WHERE  personal_habilitado = '$personal'
                                AND fecha_retiro BETWEEN '$fecha_inicial' AND '$fecha_final'";
                          $r = mysqli_query($connection, $q);
                          if($r){
                            $get_total = mysqli_fetch_array($r);
                            $total = $get_total['total'];
                          } 

                          while($datos = mysqli_fetch_array($res))
                          {
                              $tabla.="<tr>    
                                      <td>".fecha_min($datos['fecha_retiro'])."</td>
                                      <td>".$datos['numero_caja']."</td>
                                      <td>".$datos['personal_habilitado']."</td>                        
                                      <td>".$datos['concepto']."</td>
                                      <td>".$datos['cuenta']."</td>
                                      <td>".limitar_cadena(ucfirst(strtolower($datos['observaciones'])),27)."</td>
                                      <td>"."$".number_format($datos['importe'],2,',','.')."</td>
                                      </tr>";               
                          }
                          mysqli_close ($connection);
                          $tabla.="<tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>"."Total: "."</td>
                                    <td>"."$".number_format($total,2,',','.')."</td>
                                    </tr>
                                  </tbody>";
                          echo $tabla;

                          echo "<script>$('#fechas-listar').hide();</script>";

                          echo "<script>$('#listado-retiros').show();</script>";

                        }
                        else
                        {
                         echo "<script>$('#fechas-listar').hide();</script>"; 
                         echo "<strong>No se encontraron retiros para '$personal' en las fechas indicadas.</strong>";
                        }
                      }
                      else{
                        echo "<script>$('#fechas-listar').hide();</script>"; 
                        echo "<strong>Fechas incorrectas.</strong>";
                      }
                    } 

                                 
                }

              ?>
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