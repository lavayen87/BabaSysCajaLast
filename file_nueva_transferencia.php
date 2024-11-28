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
  input[name=check_transfer]{
    width: 20px;
    height: 20px;
  }
  .my-custom-scrollbar {
    position: relative;
    height: 220px;
    overflow: auto;
    margin: 5px;
  }
  
  .table-wrapper-scroll-y {
    display: block;
  }
  .content-table-scroll{
    margin-top:8px;
    margin-bottom:8px;
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
      <h2>Nueva Transferencia</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert" style="padding-left: 6px;">        
          
            <div style="width:100%; overflow: hidden;">
              <div class="form-group col-md-6" style="float: left; width: 40%; margin-left: 4px; margin-bottom: 4px;">
                    <!--form id="form-transfer"-->
                      <p><strong>Seleccione moneda</strong>
                              <select  id='select-moneda' style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                                  <option value=""></option>
                                  <option value="pesos">Pesos ($)</option>
                                  <option value="dolares">Dolares ($USD)</option>
                                  <option value="euros">Euros (€EUR)</option>
                                  <option value="cheques">Cheques ($)</option>
                              </select>
                      </p>
                      
                      <p><strong>Caja a transferir</strong>  
                              <select id='select-caja-destino' style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                                  <option value=""></option>
                                  
                                  <?php
                                  include('conexion.php');
                                  $qry = "Select * from usuarios where numero_caja <> 0 and block = 1";
                                  $res = mysqli_query($connection, $qry);
                                  if($res->num_rows > 0)
                                  {
                                      while($datos = mysqli_fetch_array($res)){
                                          echo "<option caja='".$datos["numero_caja"]."' value='".$datos["rol"]."'>"."Caja ".$datos["rol"]."</option>";
                                      }
                                  }                               
                                  ?>                 
                              </select>
                      </p>
                      <p><strong>Cantidad</strong><input type="number" id="cantidad-transfer" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;"></p>
                      <p><strong>Detalle</strong><input type="text" id="detalle-transfer" maxlength="30" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;"></p>
                    <!--/form-->
                    <br>
                    <div class="button-close">
                        <button id="aceptar-transfer" class="btn btn-primary">Aceptar</button>
                        <button id="cancelar-transfer" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                        <button id="nueva-transfer-check" class="btn btn-success" style="float: right;">Nueva</button>
                      </div>
              </div> 

              <!--div tabla cheques-->
              <div class="form-group col-md-6 div-tabla-chq" style="width: 58%; float: right; display: none;">
                    
                    <strong style="margin:0px auto;">Cheques en cartera</strong>
                    <div class="table-wrapper-scroll-y my-custom-scrollbar">
                      <table class="table table-striped tabla-chq-transfer">
                          <thead>
                            <tr>
                              <th scope="col">Vence</th>
                              <th scope="col">Cheque</th>
                              <th scope="col">Banco</th>
                              <th scope="col">Importe</th>
                              <th scope="col">Elegir</th>
                            </tr>
                          </thead>
                          <tbody> 
                            <?php
                              include('conexion.php');
                              include('funciones.php');
                              $qry = "SELECT * FROM cheques_cartera 
                                      WHERE estado = 'En cartera'
                                      and (num_caja_origen = '$numero_caja'
                                      or num_caja_destino = '$numero_caja')
                                      and activo < 3
                                      order by fecha_vto"; //originalmente  --> activo <> 3
                              $res = mysqli_query($connection, $qry);
                              if($res->num_rows > 0)
                              {
                                while($datos = mysqli_fetch_array($res))
                                {
                                  echo "<tr id='".$datos['id_cheque']."'>
                                  <td>".fecha_min($datos['fecha_vto'])."</td>
                                  <td>".$datos['num_cheque']."</td>
                                  <td>".$datos['banco']."</td>
                                  <td class='importe-chq'>"."<input style='width: 110px;' readonly value='".$datos['importe']."'>"."</td>
                                  <td>"."<input type='checkbox' id='".$datos['id_cheque']."' name='check_transfer' value='0'>"."</td>
                                  </tr>";
                                }
                              }
                                        
                            ?>
                          </tbody>
                        </table> 
                      </div>
                    </div>

                    <br>

                    <div class="form-group col-md-6 div-info-chq" style="width: 58%; text-align:center; display: none; float: right;">
                            <strong>No hay cheques para transferir.</strong>
                    </div><!--div info cheques-->
              </div><!--fin div tabla cheques-->
                   
              <br><br>
              <div id="content-transfer" class="col-lg-12"></div>
              <div  id="exito-tr" style="display: none;">                 
              <strong style="margin-top: 5px;">Transferencia realizada con exito !</strong>
                           
              <br><br>
              <div class="button-close">
                <?php
                  echo "<a href='factura/transferencia_pdf.php' id='show-tr-pdf' class='btn btn-primary' target='_blank'>
                  Imprimir 
                  <i class='fas fa-print'></i>
                  </a>
                  <button id='cerrar-tr-pdf' class='btn btn-secondary' style='display: inline-block;'>Cancelar
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