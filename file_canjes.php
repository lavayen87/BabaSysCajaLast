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
<style>
  /** modal */
  .modal-contenido{
        background-color: white;
        border: 4px solid #22A49D;
        border-radius: 8px;
        width:300px;
        padding: 10px 20px;
        margin: 20% auto;
        position: relative;
        }
        .close-modal{
            text-decoration: none;
        }
        .modal{
        background-color: #CCC8;/*rgba(0,0,0,.8);*/
        position:fixed;
        top:0;
        right:0;
        bottom:0;
        left:0;
        opacity:0.5;
        pointer-events:none;
        /*transition: all 1s;*/
        }
        #miModal{ /**target */
        opacity:1;
        pointer-events:auto;
        }
        #modal-info strong{
            font-size:17px;
        }
  /** end modal */
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

    <!-- Modal -->
    <div id="miModal" class="modal">
        <div class="modal-contenido">
            
            
            <p id="modal-info"></p>
            <div style="width:100%; height: 35px; margin: 0 auto; text-align: center; ">
                <button class="btn btn-success close-modal">Aceptar</button>
            </div>
        </div>  
    </div>
    <div class="container">
      <h2>Compras</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">
                <p style="width: 50%;">
                  Moneda a comprar 
                  <select id='select-moneda-compra' style="float: right; width: 40%;" class="form-control">
                        <option value=""></option>
                        <option value="pesos">Pesos ($)</option>
                        <option value="dolares">Dolares (US$)</option>
                        <option value="euros">Euros (€)</option>
                  </select>
                </p>
                <br>
                <p style="width: 50%;">
                  Cantidad 
                  <input type="number" id="cantidad-compra" style="float: right; width: 40%;" class="form-control">
                </p>
                <br>
                <p style="width: 50%;">
                  Moneda a pagar 
                  <select id='select-moneda-pagar' style="float: right; width: 40%;" class="form-control">
                        <option value=""></option>
                        <option value="pesos">Pesos ($)</option>
                        <option value="dolares">Dolares (US$)</option>
                        <option value="euros">Euros (€)</option>
                  </select>
                </p>
                <br>
                <p style="width: 50%;">
                  Cotización 
                  <input type="number" id="cotizacion" style="float: right; width: 40%;"  class="form-control">
                </p>  
                <br>
                <p style="width: 50%;">
                  Cantidad a pagar 
                  <input type="text"  value="" id="cantidad-resultado" style="float: right; width: 40%; background: white;"  readonly class="form-control">
                </p>   
                <p style="width: 50%;">
                  Detalle
                  <input type="text" id="detalle-canje" style=""  class="form-control" maxlength='30'>
                </p>  
                
                       
                <br>
                <p style="width: 50%;">
                  <button id="simular-compra" class="btn btn-primary">Simular</button>
                  <button id="cancelar-compra" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                  <button id="aceptar-compra" class="btn btn-success" style="float: right;">Realizar</button>
                </p>
                <br>
                <div class="form-group col-md-6">
                  <div class="row" id="container-compra">

                    <div id="content-compra" class="col-lg-12"></div>

                    <div id="print-cange" style="display: none;">                   
                      <strong>Canje realizado con exito !</strong>
                      <br>
                      <div class="button-close">
                        <?php
        
                          echo "<a href='factura/canje.php' id='btn-canje' class='btn btn-primary' target='_blank'>
                            Imprimir 
                            <i class='fas fa-print'></i>
                            </a>
                            <button id='cerrar-cange' class='btn btn-secondary' style='display: inline-block;'>Cancelar
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