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
      <h2>Solicitud de órden de pago</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert"> 
          
            <!--menu horizontal-->
            <nav class="navbar navbar-expand-lg navbar-light " style="background-color: #22A49D; border-radius: 6px;">
              <div class="container-fluid">                    
                          
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav">
                      <?php
                        if($numero_caja <> 22 && $numero_caja <> 1)
                        {
                          echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>";
                        }
                                    
                        if($numero_caja <> 22 && $numero_caja <> 1)
                        {
                          echo "<a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                        }
                                            
                        if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                        {
                          //echo "<a href='file_solicitud_op.php' class='nav-item nav-link' style='color: white;'>Solicitud</a>";
                          echo "<div class='nav-item dropdown'>
                                    <a href='#' class='nav-link dropdown-toggle' data-bs-toggle='dropdown' style='color: white;'>Solicitud con</a>
                                    <div class='dropdown-menu'>
                                        <a href='#' class='dropdown-item item1'>Efectivo</a>
                                        <a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>
                                        <a href='#' class='dropdown-item item3'>Cheques en cartera</a>
                                    </div>
                                </div>";
                        }
                        if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                        {
                          echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>";
                        }
                                  
                        if($numero_caja == 1 || $numero_caja == 3 || $numero_caja == 9 || $numero_caja == 12)
                        {
                          echo "<a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                        }
                        
                      ?>
                    </div>
                    <?php 
                      echo "<div class='navbar-nav ms-auto'>
                                <div  class='nav-item nav-link valor_opcion_pago'></div>
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
  
                  <input type="text" value="<?php echo $nombre?>" style="display:none;" id="select-solisitante">
                  <!--p><strong>Seleccione moneda</strong> 
                    <select style='width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;' id='select-moneda-so'>
                      <option value=""></option>
                      <option value="pesos">Pesos ($)</option>
                      <option value="dolares">Dolares ($USD)</option>
                      <option value="euros">Euros (€EUR)</option>
                      <option value="cheques">Cheques ($)</option>
                    </select>
                  </p-->
                  <?php 
                    if($numero_caja == 34||$numero_caja == 22||$numero_caja == 12|| $numero_caja == 9 || $numero_caja == 10 || $numero_caja ==12 || $numero_caja ==3 )
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

                            <div id='div-caja-cheque' style='display: none;'></div>
                          </div>
                          ";
                    }
                  ?>

                  <strong>Empresa</strong>
                  <div class="row-fluid">
                      <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-empresa-solicitud">
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
                      <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-obra-solicitud"> 
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
                    <select style="width: 100%;" class="js-example-basic-single form-control" id="select-cuenta-solicitud">
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
                  <input id="importe-solicitud-op" type="number" style='width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;'>
                  
                  <strong>Detalle</strong>
                  <input id="detalle-solicitud-op" type="text"  maxlength="30" style='width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;'>                    
                  <br>
                  <br>


              </div>

              <!--tabla de cheques-->
              <div class="form-group col-md-6 content-table-scroll mis_cheques" style="width: 58%; text-align: left; float: right; display: none;">
                    <div class="table-wrapper-scroll-y my-custom-scrollbar">
                      <form  method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <table class="table table-striped tabla-mis-cheques">
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
                              $cero = 0;
                              if($res->num_rows > 0)
                              {
                                while($datos = mysqli_fetch_array($res))
                                {
                                  echo "<tr id='".$datos['id_cheque']."'>
                                  <td>".fecha_min($datos['fecha_vto'])."</td>
                                  <td>".$datos['num_cheque']."</td>
                                  <td>".$datos['banco']."</td>
                                  <td class='importe-chq'>"."<input style='width: 110px;' readonly value='".$datos['importe']."'>"."</td>
                                  <td>"."<input type='checkbox' id='".$datos['id_cheque']."' name='mi_check' value=''>"."</td>
                                  </tr>";
                                }
                                
                              }
                              else{
                                /*echo "<script>
                                $('#importe-op-cheq').attr('readonly',false);
                                $('.content-table-scroll').hide();
                                </script>";*/
                                echo "<script>
                                $('.content-table-scroll').hide();
                                </script>";
                              }
                            
                            ?>
                          </tbody>
                        </table>
                      </form>
                    </div>
              </div>   
              
              <div class="form-group col-md-6 content-table-scroll cheques_cartera" style="width: 58%; text-align: left; float: right; display: none;">
                    <div class="table-wrapper-scroll-y my-custom-scrollbar">
                     
                        <table class="table table-striped tabla-cheques-cartera">
                          <thead>
                            <tr>
                              <th scope="col">Vence</th>
                              <th scope="col">Cheque</th>
                              <th scope="col">Importe</th>
                              <th scope="col">Caja</th>
                              <th scope="col">Elegir</th>
                            </tr>
                          </thead>
                          <tbody> 
                            <?php
                              include('conexion.php');
                              //include('funciones.php');
                              $qry = "SELECT * FROM cheques_cartera 
                                      WHERE estado = 'En cartera'
                                      ORDER BY fecha_vto";
                              $res = mysqli_query($connection, $qry);
                              if($res->num_rows > 0)
                              {
                                while($datos = mysqli_fetch_array($res))
                                {
                                  if($datos['num_caja_origen'] <> 0 )
                                  {
                                    $caja = $datos['num_caja_origen'];
                                  }
                                  else{
                                    $caja = $datos['num_caja_destino'];
                                  }
                                  echo "<tr id='".$datos['id_cheque']."'>
                                  <td>".fecha_min($datos['fecha_vto'])."</td>
                                  <td>".$datos['num_cheque']."</td>
                                  <td class='importe-chq'>"."<input style='width: 110px;' readonly value='".$datos['importe']."'>"."</td>
                                  <td>".$caja."</td>
                                  <td style='text-align: right;'>"."<input type='checkbox' id='".$datos['id_cheque']."' name='check_cart' value='0'>"."</td>
                                  </tr>";
                                  
                                }
                                
                              }
                              else{
                                /*echo "<script>
                                $('#importe-op-cheq').attr('readonly',false);
                                $('.content-table-scroll').hide();
                                </script>";*/
                                echo "<script>
                                $('.content-table-scroll').hide();
                                </script>";
                              }
                            
                            ?>
                          </tbody>
                        </table>
                      
                    </div>
              </div>
              
              <input type='number' id="importe-sop-cheq" readonly>
              <!--fin tabla cheques-->
            </div>
            
            <div class="form-group col-md-6" style="width: 40%; float: left;">
              <button id="aceptar-solicitud-op" class="btn btn-primary" style="display: inline-block;">Aceptar</button>
              <button id="cancelar-solicitud-op" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
              <button id="nueva-solicitud" class="btn btn-success" style="float: right;"><i class="fas fa-plus-circle"></i>Nueva</button>
            </div>
            <br>
            <br>
            <div class="form-group col-md-6">
                <div class="row">
                  <div id="content-solicitud-op" class="col-lg-12">
                      
                  </div>
                </div>
                                             
            </div>
              
            <div id="info-solicitud"></div>
            
          </div>
        </div>
      </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>

