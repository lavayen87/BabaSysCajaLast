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

<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-multiple').select2();

    //$(".chosen-select").chosen({max_selected_options: 4});
  });
</script>
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
      <h2>Servicios</h2>
      <hr>
       
      <div class="alert alert-success" role="alert"> 
        <form id="form-serv" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                 
            <div style="float:left; width: 20%;"> 
                    <strong style="float: left;">Loteo</strong>   
                    <select id="select-loteo" name="select-loteo" class="form-control form-select">
                      <option value=""></option>
                      <option value="Airampo">Airampo</option>
                      <option value="Buen Clima">Buen Clima</option>
                      <option value="Terranova">Terranova</option>
                    </select>
            </div>
                  
            <div style="display:inline-block; width: 13%; margin-left: 5px;">   
                    <p>
                      <strong style="float: left;">Lote</strong> 
                      <input  type="text" name="lote" id="form-lote" class="form-control">                   
                    </p>
                      
            </div>

            <!--div style="display:inline-block; margin-left: 3px;">   
                  <button class="btn btn-primary" name="btn-buscar" style="display:inline-block;">Buscar</button>   
            </div-->
              
            <hr>

          <!-------------------------------------------------->

        </form>
        <?php
          
            if(isset($_POST['btn-buscar']))
            {
              $loteo = $_POST['select-loteo'];
              $lote  = $_POST['lote'];

              if($loteo !="" && $lote !="")
              {
                include('conexion.php');

                $qry = "SELECT * FROM det_servicio
                      where loteo = '$loteo'
                      and lote = '$lote'";
                $res = mysqli_query($connection, $qry);
                
                if($res->num_rows > 0)
                {
                  
                  $lote ="<strong>". strtoupper(substr($lote,0,2)).substr($lote,2,4) ."</strong>";
                  $loteo = "<strong>".$loteo."</strong>";
                  echo "<p>".$loteo." - ".$lote."</p>";

                  while($datos=mysqli_fetch_array($res))
                  {
                    if($datos['recibo']<>"")
                    {
                      if($datos['servicio'] == "Agua")
                      {
                        echo "Conexión de agua / Recibo Nº ".$datos['recibo'];
                        echo "<hr>";
                      }
                      else{
                        if($datos['servicio'] == "Agrimensor")
                        {
                          echo "Agrimensor / Recibo Nº ".$datos['recibo'];
                          echo "<hr>";
                        }
                        else{
                          echo "Conexión dom. de cloacas / Recibo Nº ".$datos['recibo'];
                          echo "<hr>";
                        }
                      }
                      
                    }
                    else
                    {
                      echo "<select name='select-servicio'>
                            <option value='Agua'>Agua</option>
                            <option value='Agrimensor'>Agrimensor</option>
                            <option value='Cloacas'>Cloacas</option>
                            </select>
                            </br>
                            <input type='text' name='n_recibo'>";

                    }
                  }
                
                }
                else // CARGRA DE SERVCIOS
                {
                  $lote = "<strong>".strtoupper(substr($lote,0,2)).substr($lote,2,4)."</strong>";
                  $loteo = "<strong>".$loteo."</strong>";
                  echo "<p>".$loteo." - ".$lote."</p>";

                  $serv = "SELECT * FROM servicios";
                  $res  = mysqli_query($connection, $serv);
                  $tabla="<table style='width: 100%;' id='mi-tabla'>
                          <thead style='background: #ABC441;border-bottom: 2px solid black;'>
                          <th>Servicio</th>
                          <th>Nº recibo</th>               
                          <th>Fecha solicitud</th>
                          <th>Estado</th>
                          <th>Accion</th>
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
                          <input type='text' name='n_recibo' style='width:60px;'>
                        </td>

                        <td class='valor'>
                          <input type='date' style='width:125px;'>
                        </td>
                        
                        <td class='valor'>
                          <select name='select-estado'>
                            <option value=''></option>
                            <option value='Pendiente'>Pendiente</option>
                            <option value='Solicitado'>Solicitado</option>
                            <option value='Realizado'>Realizado</option>
                          </select>
                        </td>
                        
                        <td>
                          <button class='btn btn-success load-serv' title='Cargar' id='".$id."'>
                          <i class='fas fa-check-circle'></i>
                          </button>
                        </td>

                        </tr>";

                  }
                  $tabla.="</tbody></table>";
                  echo $tabla;
                  
                } 
              }
              else{
                echo "Debe Ingresar un Loteo y el codigo del lote";
              }  
            }
          
          ?> 
      </div>    
         
    </div>
      
  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>