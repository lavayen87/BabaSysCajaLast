<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
  $nombre = $_SESSION['nombre'];
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
    max-height: 250px;
    width: 100%;  
    display: flex;
  }

  #tabla_so_chq{
    display: none;
  }

  #importe-sop-cheq{
    margin-top: 58px;
    margin-left: 17px;
    display: none;
    border-radius: 6px;
    width: 150px;
    height: 27px;
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
      <h2>Banco</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert"> 
          
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
                                <div  class='nav-item nav-link info_opcion_opt'>
                                  <label style='color: #01FBEB'></label>
                                </div>
                            </div>";
                    ?>
                </div>
              </div>
            </nav>
            <!--fin menu horizontal-->
            <br>
          
            <div style="100%; overflow: hidden;">
              <!--Formulario-->
              <div class="form-group col-md-6"style="width: 40%; float: left; "> 
  
                  <input type="text" value="<?php echo $rol?>" style="display:none;" id="select-solisitante">
                  
                  <?php 
                    /*if($numero_caja == 34||$numero_caja == 22 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3 )
                    {
                      echo "<strong>Caja</strong>
                          <div class='row-fluid div-select-caja'>
                            <select name='' id='select-caja' style='width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;'>
                                <option value=''></option>
                                <option value='1'>Caja 1</option>
                                <option value='12'>Tesoro</option>
                                <option value='9'>Liliana T.</option>
                                <option value='3'>Banco</option>
                            </select>
                          </div>
                          ";
                    }*/
                  ?>
                
                  <strong>Empresa</strong>
                  <div class="row-fluid">
                      <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-empresa-banco">
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
                      <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-obra-banco"> 
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
                  <br>               
                  <div class="row-fluid">
                    <select style="width: 100%;"  class="js-example-basic-single form-control" id="select-cuenta-banco">
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
                                                  
                  <strong>Importe</strong>
                  <input id="importe-solicitud-banco" type="number"  style='width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;'>
                  <strong>Recibe</strong>
                  <input id="recibe-banco" type="text" maxlength="30" style='width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;'>                 
                  <strong>Detalle</strong>
                  <input id="detalle-solicitud-banco" type="text"  maxlength="30" style='width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;'>                    
                  <br>
                  <br>
                

              </div>

              
            </div>
            
            <div class="form-group col-md-6" style="width: 40%; float: left;">
              <button id="aceptar-solicitud-banco" name="btn-banco" class="btn btn-primary" style="display: inline-block;">Aceptar</button>
              <button id="cancelar-solicitud-banco" name="btn-cancel-banco" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
              <button id="nueva-solicitud" class="btn btn-success" style="float: right;"><i class="fas fa-plus-circle"></i>Nueva</button>
            </div>
            <br>
            <br>

            <div class="form-group col-md-6">
                <div class="row">
                  <div id="content-solicitud-banco" class="col-lg-12">
                      
                  </div>
                </div>
                                             
            </div>
              
            <div id="info-solicitud-banco"></div>

            
          </div>
        </div>
      </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>

