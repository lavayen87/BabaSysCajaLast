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
      <h2>Egresos</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">        
            <div class="container"> 
              <div class="form-group col-md-6">
                <!--form id="form-ingreso"-->

                  Seleccione moneda
                  <select id="select-moneda-egreso" class="form-control">
                    <!--option value=""></option-->
                    <option value="pesos" selected>Pesos ($)</option>
                    <option value="dolares">Dolares ($US)</option>
                    <option value="euros">Euros ($USDE)</option>
                    <!--option value="cheques">Cheques ($)</option-->
                  </select>
                  Importe
                  <input type="number" class="form-control" id="importe-egreso" >
                  Detalle
                  <input type="text" class="form-control" id="detalle-egreso" maxlength='30'>

                  <br>
                  <button id="aceptar-egreso" class="btn btn-success">Aceptar</button>
                  <button id="close-egreso" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                <!--/form-->
              </div>
                
              <br>

              <div class="form-group col-md-6">
                <div class="row">
                  <div id="content-egreso" class="col-lg-12"></div>
                  <div id="print-egre" style="display: none;">                   
                    <strong>Egreso con exito !</strong>
                    <br>
                    <div class="button-close">
                      <?php
      
                        echo "<a href='factura/print_egreso.php' id='show-tr-pdf' class='btn btn-primary' target='_blank'>
                          Imprimir 
                          <i class='fas fa-print'></i>
                          </a>
                          <button id='cerrar-print-egre' class='btn btn-secondary' style='display: inline-block;'>Cancelar
                          </button>";
                      ?>
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