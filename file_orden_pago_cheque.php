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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single').select2();
  });
</script>
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
  .opciones-op ul li{
    list-style: none;
    padding: 5px;
  }

  .opciones-op ul li a{
    text-decoration: none;
    color: white;
    font-family: verdana;
    margin-left: 5px;
  }
  .my-custom-scrollbar {
    display: flex;
    flex-direction: row;
    justify-content: center;
    position: relative;
    max-height: 200px;
    overflow: auto;
    margin: 5px;
  }
  
  .table-wrapper-scroll-y {
    display: block;
  }
  .content-table-scroll{
    margin-top:8px;
    margin-bottom:8px;
    /*height: 100px;*/
    max-height: 220px;
    width: 100%;  
    display: flex;
  }

 
  input[type="checkbox"]{
    width: 20px;
    height: 20px;
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
      <h2>Órden de pago con Cheque</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert" style="width: 100%;">        
            
            <!--menu horizontal-->
            <nav class="navbar navbar-expand-lg navbar-light " style="background-color: #22A49D; border-radius: 6px;">
              <div class="container-fluid">                    
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                  <span class="navbar-toggler-icon"></span>
                </button>        
                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav">
                      <?php
                        if($numero_caja == 0 )
                        {
                          echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>
                                <a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                        }
                        if(tiene_permiso($numero_caja,3))
                        {
                          echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>";
                        }         
                        if(tiene_permiso($numero_caja,4))
                        {
                          echo "<a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                        }
                        ?>                    
                         
                        <div class='nav-item dropdown'>
                          <a href='#' class='nav-link dropdown-toggle' data-bs-toggle='dropdown' style='color: white;'>
                            Solicitud
                          </a>
                          <div class='dropdown-menu'>
                            <?php
                              if($numero_caja == 0)
                              {  
                                echo "<a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>
                                      <a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>                                     
                                      <a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>
                                      <a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                              }
                              if(tiene_permiso($numero_caja,5))
                                echo "<a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>";
                              if(tiene_permiso($numero_caja,6))
                                echo "<a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>";
                              if(tiene_permiso($numero_caja,42))
                                echo "<a href='file_solicitud_transferencia.php' class='dropdown-item item1'>Transferencia</a>";
                              if(tiene_permiso($numero_caja,7))
                                echo "<a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>";
                              if(tiene_permiso($numero_caja,8))
                                echo "<a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                            ?>
                              
                          </div>
                        </div>
                           
                        <?php
                          if($numero_caja == 0)
                            echo "<a href='file_solicitud_transferencia.php' class='nav-item nav-link' style='color: white;'>Solicitud de fondos</a>";
                          if(tiene_permiso($numero_caja,42))
                            echo "<a href='file_solicitud_transferencia.php' class='nav-item nav-link' style='color: white;'>Solicitud de fondos</a>";
                          if($numero_caja == 0)
                          {
                            echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>
                                  <a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                          }
                          if(tiene_permiso($numero_caja,9)) 
                          {
                            echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>";
                          }
                                    
                          if(tiene_permiso($numero_caja,10))
                          {
                            echo "<a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                          }
                        ?>
                     
                    </div>
                    <?php 
                      echo "<div class='navbar-nav ms-auto'>
                                <div  class='nav-item nav-link info_opcion_opt'></div>
                            </div>";
                    ?>
                </div>
              </div>
            </nav>
            <!--fin menu horizontal-->
              <br>                
          
              <div style="width:100%; overflow: hidden;">
                <div class="form-group col-md-6" style="width: 40%; float: left;"> 
                    <strong>Empresa</strong>
                    <div class="row-fluid">
                        <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-empresa-cheq">
                          <option value=""><?php echo ""; ?></option> 
                          <?php  
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM empresas";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                            echo "<option value='".$misdatos['nombre_empresa']."' id='".$misdatos['id_empresa']."'>".$misdatos['nombre_empresa']."</option>"; 
                          }
                          ?>
                        </select>
                    </div>
                   
                    <strong>Obra</strong>
                    <div class="row-fluid">
                        <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-obra-cheq"> 
                          <option value=""><?php echo ""; ?></option>
                          <?php  
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM obras";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                            echo "<option value='".$misdatos['nombre_obra']."' id='".$misdatos['id_obra']."'>".$misdatos['nombre_obra']."</option>"; 
                          }
                          ?>
                        </select>
                    </div>
                    
                    <strong>Cuenta Contable</strong>
                                  
                    <div class="row-fluid">
                      <select style="width: 100%;" class="js-example-basic-single form-control" id="select-cuenta-cheq">
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
                    <strong>Recibe</strong>
                    <input id="receptor-op-cheq" type="text" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" maxlength="30">                                               
                    <strong>Importe</strong>
                    <input id="importe-op-cheq" type="number"  readonly style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                    <?php
                      echo  "<script>
                              $('#importe-op-cheq').prop('disabled', 'disabled');
                              $('#importe-op-cheq').css('background-color', 'white');
                              </script>";
                    ?>
                    
                    <br>
                    <strong>Detalle</strong>
                    <input id="detalle-op-cheq" type="text" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" maxlength="30">                    
                  
                  
                </div>
                
                <div class="form-group col-md-6 opciones-op" style="width: 58%; text-align: left; float: right;"> 
                  <!--ul>
                          
                          <li>
                            <?php
                              if($numero_caja <> 22 && $numero_caja <> 1)
                              {
                                echo "<a href='file_orden_pago.php' class='btn btn-success'>Órden de pago</a>";
                              }
                            ?>
                          </li>
                          <li>
                            <?php
                              if($numero_caja <> 22 && $numero_caja <> 1)
                              {
                                echo "<a href='file_orden_pago_cheque.php' class='btn btn-success'>Órden de pago con cheque</a>";
                              }
                            ?>
                            
                          </li>
                          <li>
                            <?php
                              if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                              {
                                echo "<a href='file_solicitud_op.php' class='btn btn-success'>Solicitud de orden de pago</a>";
                              }
                            ?>
                            
                          </li>
                          <li>
                            <?php
                              if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                              {
                                echo "<a href='file_autorizar_op.php' class='btn btn-success'>Autorizar Solictud</a>";
                              }
                            ?>
                            
                          </li>
                          <li>
                            <?php
                              if($numero_caja == 1 || $numero_caja == 3 || $numero_caja == 9 || $numero_caja == 12)
                              {
                                echo "<a href='file_emitir_orden.php' class='btn btn-success'>Emitir órden de pago</a>";
                              }
                            ?>
                            
                          </li>
                  </ul-->
                  <!--tabla de cheques-->
                  <div id="no-cheq" style="display: none; text-align: center;">
                    <strong>No hay cheques para mostrar.</strong>
                  </div>
                  <div class="form-group col-md-6 content-table-scroll" >
                    <div class="table-wrapper-scroll-y my-custom-scrollbar">
                      <form  method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <table class="table table-striped tabla-chq">
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
                                      order by fecha_vto";
                              $res = mysqli_query($connection, $qry);
                              if($res->num_rows > 0)
                              {
                                while($datos = mysqli_fetch_array($res))
                                {
                                  echo "<tr id='".$datos['id_cheque']."'>
                                  <td>".fecha_min($datos['fecha_vto'])."</td>
                                  <td>".$datos['num_cheque']."</td>
                                  <td>".$datos['banco']."</td>
                                  <td class='importe-chq'>"."<input style='width: 110px;' readonly value='".$datos['importe']."'>"."</td>";
                                  if($datos['activo'] <= 2)
                                    echo "<td style='text-align: left;'>"."<input type='checkbox' id='".$datos['id_cheque']."' name='check' value='0'>"."</td></tr>";
                                  else  echo "<td style='text-align: right;'></td></tr>";
                                }
                                
                              }
                              else{
                                /*echo "<script>
                                $('#importe-op-cheq').attr('readonly',false);
                                $('.content-table-scroll').hide();
                                </script>";*/
                                echo "<script>
                                $('.content-table-scroll').hide();
                                $('#no-cheq').show();
                                </script>";
                              }
                            
                            ?>
                          </tbody>
                        </table>
                      </form>
                    </div>
                  </div>              
                  <!--fin tabla cheques-->
                </div> <!--div opciones-->
              </div> 
               
              <br>

              <!--tabla cheques aqui-->             

              <div class="form-group col-md-6" style="width: 40%; float: left;">
                <button id="aceptar-op-cheq" class="btn btn-primary" style="display: inline-block;">Aceptar</button>
                <button id="cancelar-op-cheq" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                <!--button id="nueva-op-cheq" class="btn btn-success" style="float: right;">Nueva orden</button-->
                <button id="nueva-solicitud" class="btn btn-success" style="float: right;"><i class="fas fa-plus-circle"></i>Nueva</button>
              </div>

              <br> 
              <br>

              <div class="form-group col-md-6">

                  <div class="row">
                    <div id="content-cheq" class="col-lg-12">
                        
                    </div>
                  </div>

                  <br>

                  <div class="" role="alert" id="exito-op-cheq" style="display: none;">                 
                    <strong>Órden de pago realizada con exito !</strong>                
                    <br>
                    <div class="button-close">
                      <a href="factura/orden_pago_cheque_pdf.php" id="btn-print-op-cheq" class="btn btn-primary" target="_blank">
                          Imprimir 
                          <i class="fas fa-print"></i>
                      </a>
                      <button id="cerrar-content-op-cheq" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                    </div>
                  </div>  

              </div> <!--div resultado--> 
            
          </div>
        </div>
      </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>