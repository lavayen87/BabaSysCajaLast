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
<!--link rel="stylesheet" href="chosen/chosen.css"-->
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<!--script src="chosen/chosen.jquery.js" type="text/javascript"></script-->
 
</head>
<body>
<div class="page-wrapper chiller-theme toggled">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  <?php include('menu_main.php');?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">
    <div class="container">
      <h2>Cargar servicios</h2>
      <hr>

      <div class="alert alert-success" role="alert"> 

        <div class="row">
          <div id="content" class="col-lg-12">       

            <div style="overflow: none;">

              <div class="form-group">
                <strong style="float: left; margin-right:5px;">Cliente</strong>
                <br>
                  <div class="input-group" style="width: 42%;">
                      <input type="text" class="form-control" name="nombre" id="input-nom-cliente" value="" maxlength="32">
                  </div>
              </div>

              <div class="form-group">
                <strong style="float: left; margin-right:5px;">Teléfono</strong>
                <br>
                  <div class="input-group" style="width: 20%;">
                      <input type="text" class="form-control" name="telefono" id="input-telefono" value="" maxlength="12">
                  </div>
              </div>

              <div style="float:left; width: 20%;"> 
                      <strong style="float: left;">Loteo</strong>   
                      <select id="select-loteo" name="select-loteo" class="form-control form-select">
                        <option value=""></option>  
                        <?php  
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM loteos";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                             echo "<option value='".$misdatos['nombre']."' id='".$misdatos['id']."'>".$misdatos['nombre']."</option>"; 
                          }
                        ?>
                      </select>
              </div>
                    
              <div style="display:inline-block; width: 13%; margin-left: 5px;">   
                  <p>
                      <strong style="float: left;">Lote</strong> 
                      <input  type="text"  name="lote" id="input-lote" class="form-control" maxlength="6">                 
                  </p>
                        
              </div>
       
              <div id="check-format" style="display: inline-block"></div>  

            </div>
          </div>
        </div>
          <!-------------------------------------------------->

            <?php 
        
                // CARGRA DE SERVCIOS

                $serv = "SELECT * FROM servicios";
                $res  = mysqli_query($connection, $serv);
                $tabla="<table style='width: 100%;' id='mi-tabla'>
                        <thead style='border-bottom: 2px solid black;'>
                        <th>Servicio</th>
                        <th>Recibo</th>  
                        <th>Fecha pago</th>             
                        <th>Fecha solicitud</th>
                        <th>Estado</th>
                        <th>Forma de pago</th>
                        </thead>
                        <tbody>";
                $id=0;
                while($d = mysqli_fetch_array($res)) 
                {
                    $id++;
                    $tabla.= "
                            <tr style='border-bottom: 1px solid black;' id='".$id."'>

                            <td class='valor'>
                            <input type='text'value='".$d['nombre']."' readonly style='width:100px;'>
                            </td> 

                            <td class='valor'>
                            <input type='text' name='n_recibo' id='input-recibo' style='width:60px;' maxlength='6'>
                            </td>
                            
                            <td class='valor'>
                            <input type='date' id='input-fecha-pago' style='width:125px;'>
                            </td>";

                            if($d['id'] <> 4)
                            {
                              $tabla.="<td class='valor'>
                              <input type='date' id='input-fecha-solicitud' style='width:125px;'>
                              </td>
                              
                              <td class='valor'>
                              <select name='select-estado' id='select-load-estado' style='width: 110px; height: 30px;'>
                                  <option value=''></option>
                                  <option value='Pendiente'>Pendiente</option>
                                  <option value='Solicitado'>Solicitado</option>
                                  <option value='Realizado'>Realizado</option>
                              </select>
                              </td>
                              "; 
                            }
                            else{
                              $tabla.="<td></td><td></td>";
                            }


                            if($d['id'] == 4)
                            {
                              $tabla.="<td class='valor'>
                              <select name='select-pago' id='select-forma-pago' style='width: 110px; height: 30px;'>
                                  <option value=''></option>
                                  <option value='En boleto'>En boleto</option>
                                  <option value='Contado'>Contado</option>
                                  <option value='Financiado'>Financiado</option>
                              </select>
                              </td>";
                            }
                            else{
                              $tabla.="<td></td></tr>";
                            }
                           
                            
                            /*<td style='text-align: right;'>
                            <button class='btn btn-success load-serv' title='Cargar' id='".$id."'>
                            <i class='fas fa-check-circle'></i>
                            </button>
                            </td>*/ // boton de accion de carga
                }

                $tabla.="</tbody></table>";
                echo $tabla;
                
            
            ?> 
      <br>
      <div style="display: flex; heigth: 50px; padding: 5px;">
          <button class='btn btn-success load-serv'>
            Aceptar
          </button>
          <button class='btn btn-secondary cancel-serv' style="margin-left: 5px;">
            Cancelar
          </button>

          <div id="info-load-service" style="display: inline-block; width: 50%; height: 32px; padding: 3px; text-align: center;"></div>
        </div>
      </div>  
    
    </div>
      
  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>