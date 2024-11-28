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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single').select2();
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
      <h2>Retiros</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">        
            <div class="container">
              <div class="form-group col-md-6"> 
                  <strong>Personal Habilitado</strong>
                  <div class="row-fluid">
                      <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-persona-retiro">
                        <option value=""></option>
                        <option value="Daniel Barzola">Daniel Barzola</option>
                        <option value="Sergio Barzola">Sergio Barzola</option>
                        <option value="Luis Barzola">Luis Barzola</option>
                        <option value="Alfredo Boden">Alfredo Boden</option> 
                        <option value="Edgar Mesones">Edgar Mesones</option> 
                        <option value="Maxi Mosqueira">Maxi Mosqueira</option>  
                      </select>
                  </div>
                  <br>
                  <strong>Concepto</strong>
                  <div class="row-fluid">
                      <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-concepto-retiro"> 
                        <option value=""></option>
                        <option value="Adelantos a cta. rendición">Adelantos a cta. rendición</option>
                        
                      </select>
                  </div>
                  <br>
                  <strong>Cuenta Contable</strong>
                  <br>               
                  <div class="row-fluid">
                    <select style="width: 100%;" class="js-example-basic-single form-control" id="select-cuenta-retiro">
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

                      ?>          
                    </select>
                  </div>
                  <br>                                
                  <strong>Importe</strong>
                  <input id="importe-retiro" type="number" class="form-control">
                  <br>
                  <strong>Observaciones</strong>
                  <input id="detalle-retiro" type="text" class="form-control">                    
                 
                  <br>

                <button id="aceptar-retiro" class="btn btn-primary" style="display: inline-block;">Aceptar</button>
                <button id="cancelar-retiro" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                <button id="nuevo-retiro" class="btn btn-success" style="float: right;">Nuevo retiro</button>
              </div>

              <br>
              <div class="form-group col-md-6">
                <div class="row">
                  <div id="content-retiro" class="col-lg-12">
                      
                  </div>
                </div>
                <div class="form-control" role="alert" id="exito-retiro" style="display: none;">                 
                  <strong>Retiro realizado con exito !</strong>                
                  <br><br>
                  <!--div class="button-close">
                    <a href="factura/retiro_pdf.php" id="show-op-pdf" class="btn btn-primary" target="_blank">
                        Imprimir 
                        <i class="fas fa-print"></i>
                    </a>
                    <button id="cerrar-exito-retiro" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                  </div-->
                  <div class="button-close">
                    <?php  
                    echo "<a href='factura/retiro_pdf.php?num_re=' id='show-op-pdf' class='btn btn-primary' target='_blank'>
                        Imprimir 
                        <i class='fas fa-print'></i>
                    </a>"
                    ?>
                    <button id="cerrar-exito-retiro" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                  </div>
                </div> 

              </div>
            </div>   
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