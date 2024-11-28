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
<link rel="shortcut icon" href="img/logo-sistema.png">
<head>
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
      <h2>Compras de cheques</h2>
      <hr>
      <div class="row">
        
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
           
                
                <br>
                <p style="width: 50%;">
                  <strong>Cantidad en cheque</strong>
                  <input type="number" id="cantidad-cheq" style="float: right; width: 40%;" class="form-control">
                </p>
          
                
                <br>
                <div style="display: flex;">
                  <p style="width: 50%;">
                    <strong>Cantidad a pagar </strong>
                    <input type="text"  value="" id="resultado-cheq" style="float: right; width: 40%;"  readonly class="form-control">                  
                  </p> 
                  <div style="margin-left: 5px; margin-top: 9px; display: none;" id="ok">
                    <i style="color: green;" class="fas fa-check-circle"></i>
                  </div>         
                </div>

                
                <p style="width: 50%;">
                  <strong>Observaciones</strong>
                  <input type="text"  value="" id="detalle-cheq" style="float: right; width: 60%;"   class="form-control">
                </p> 

                <br>
                <p style="width: 50%;">
                  <button id="simular-compra-cheq" class="btn btn-primary">Simular</button>
                  <button id="cancelar-compra-cheq" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                  <button id="aceptar-compra-cheq" class="btn btn-success" style="float: right;">Realizar</button>
                </p>
                <br>
                <div class="form-group col-md-6">
                  <div class="row">
                    <div id="content-compra-cheq" class="col-lg-12"></div>
                    <div id="print-canje-cheq" style="display: none;">                   
                      <strong>Canje realizado con exito !</strong>
                      <br>
                      <div class="button-close">
                        <?php
        
                          echo "<a href='factura/canje_cheque.php' class='btn btn-primary' target='_blank'>
                            Imprimir 
                            <i class='fas fa-print'></i>
                            </a>

                            <button id='cerrar-print-cheq' class='btn btn-secondary' style='display: inline-block;'>Cancelar
                            </button>";
                        ?>
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