
<?php 
  date_default_timezone_set('America/Argentina/Salta');
  session_start();
  if($_SESSION['active'])
  {
    $nombre_usuario = $_SESSION['nombre'];
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

<!-- cdn para alertas y confirmacion jquery -->

<!---->

<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script> 
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<link rel="stylesheet" href= 
"https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity= 
"sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
        crossorigin="anonymous" />
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
    /*box-shadow: 0 0 0 0.4rem rgba(40, 167, 69, 0.25);*/
    box-shadow: 0 0 0 2px rgb(255,255,255),
                0.3em 0.3em 1em rgba(0,0,0,0.6);
  }
  .close-modal{
    text-decoration: none;
  }
  .modal{
    /*background-color: #CCC8;/*rgba(0,0,0,.8);
    background-color: transparent;/*rgba(0,0,0,.8);*/
    
    position:fixed;
    top:0;
    right:0;
    bottom:0;
    left:0;
    /*opacity:0.5;*/
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
   
        thead tr th{ 
          position: sticky;
          top: 0;
          z-index: 10;
          background-color: #ffffff;
        }
        
        .table-responsive { 
          height:300px;
          overflow:scroll;
        }
        
     

</style>
</head>
<body>
<div class="page-wrapper chiller-theme toggled">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  <?php include('menu_lateral.php'); // menu_main.php?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">

    

    <div class="container">
      <h2>Listado Red de Cloacas</h2>
      <hr>
       
      <div class="alert alert-success" role="alert">
       
        <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
        
          <div class="" style="width: 50%; border: 3px solid #22A199; border-radius:8px; padding: 6px;">
              
              <div class="input-group mb-3">
                  <label style="width: 35%; background: #22A199; color: white;" class="input-group-text nombre" for="inputGroupSelect01">Loteos</label>
                  <?php
              
                    include('conexion.php');
                    $qry = "SELECT * FROM loteos";
                    $res = mysqli_query($connection,$qry);
                    echo "<select name='loteo' class='form-select'>
                          <option value=''></option>";
                    while($datos = mysqli_fetch_array($res))
                    {
                      echo "<option value='".$datos['nombre']."'>".$datos['nombre']."</option>";
                    }
                    echo "</select>";
                    mysqli_close($connection);
                  ?>
              </div>

              <div class="input-group mb-3">
                  <label style="width: 35%; background: #22A199; color: white;" class="input-group-text nombre" for="inputGroupSelect01">Desde</label>
                  <input type="date" name='fecha_ini' class="form-control">
              </div>
              
              <div class="input-group mb-3">
                  <label style="width: 35%; background: #22A199; color: white;" class="input-group-text usuario" for="inputGroupSelect01">Hasta</label>
                  <input type="date" name='fecha_fin' class="form-control">
              </div>
              
              <div class="input-group mb-3">
                  <label style="width: 35%; background: #22A199; color: white;" class="input-group-text pass" for="inputGroupSelect01">Red Cloacas</label>
                  <select name="red" class='form-select'>
                    <option value=""></option>
                    <option value="1">Si</option>
                    <option value="0">No</option>
                  </select>
              </div>
              
              
              <div style="margin: 0px auto; text-align: center;">
              
                  <button type="submit" class="btn btn-success" name="btn-listar"  style="display:inline-block; margin-left: 4px;">Listar</button>
                  <button type="submit" class="btn btn-secondary" name="btn-cancelar"  style="display:inline-block; margin-left: 4px;">Cancelar</button>
                   
              </div>
          </div> 
        </form>         
                
        <br>

        <?php
          $fecha_ini  = "";
          $fecha_fin  = "";                   
          $hoy = date('Y-m-d');
          
          $tabla1 = "<table class='table table-striped table-hover'>        
                    <thead class='thead-dark'>  
                    <tr> 
                    <th>Titular</th>
                    <th>Loteo</th>
                    <th>Lote</th>
                    <th>Fecha</th>
                    <th>Forma pago</th>
                    </tr>
                    </thead>
                    <tbody id='tbody-datos'>";
          
          
          $lote = "";
          $datos1 = "";
          $datos2 = "";
          $datos3 = "";
          $respuesta = "";
          $op = 0;
          include('conexion.php');
          include('funciones.php');

          // Lotes con red de cloacas
          if(isset($_POST['btn-listar']))
          {
            // 1 - loteo , fechas y red (1,0)
            if($_POST['loteo']!="" && isset($_POST['fecha_ini']) && $_POST['fecha_ini']!="" && isset($_POST['fecha_fin']) && $_POST['fecha_fin']!="" && $_POST['red']!="")
            {
              if($_POST['fecha_ini'] <= $_POST['fecha_fin'])
              {
                $fecha_ini = $_POST['fecha_ini'];
                $fecha_fin = $_POST['fecha_fin'];
                $loteo = $_POST['loteo'];
                
                if($_POST['red'] == '1') // si pagó la red
                {
                  $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago 
                        FROM det_lotes as t1 inner join det_servicio as t2
                        on t1.lote = t2.lote
                        WHERE servicio = 'Red de Cloacas'
                        AND fecha_pago BETWEEN '$fecha_ini' AND '$fecha_fin'
                        AND t2.forma_pago <> ''
                        AND t1.loteo = '$loteo'
                        group by t1.lote";
                  $red = 1;
                  $op = 1;
                  $print = "<a href='factura/listado_red.php?loteo=$loteo&f1=$fecha_ini&f2=$fecha_fin&op=$op' class='btn btn-primary print' target='_blan'>Imprimir</a>";
                }
                else
                {
                  $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                        FROM det_lotes as t1 inner join det_servicio as t2
                        on t1.lote = t2.lote
                        WHERE servicio = 'Red de Cloacas'
                        AND fecha_pago BETWEEN '$fecha_ini' AND '$fecha_fin' 
                        AND t1.titular <>''
                        AND t2.forma_pago = ''
                        AND t1.loteo = '$loteo'
                        group by t1.lote";
                  $red = 0;
                  $op = 2;
                  $print = "<a href='factura/listado_red.php?loteo=$loteo&f1=$fecha_ini&f2=$fecha_fin&op=$op' class='btn btn-primary print' target='_blan'>Imprimir</a>";
                } 
              }
              else
                $respuesta = "<strong>Fechas incorrectas.</strong>";
            }
            else
            {
              // 2 - Loteo , fechas
              if($_POST['loteo']!="" && isset($_POST['fecha_ini']) && $_POST['fecha_ini']!="" && isset($_POST['fecha_fin']) && $_POST['fecha_fin']!="")
              {
                if($_POST['fecha_ini'] <= $_POST['fecha_fin'])
                {
                    $fecha_ini = $_POST['fecha_ini'];
                    $fecha_fin = $_POST['fecha_fin'];
                    $loteo = $_POST['loteo'];
                    
                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                            FROM det_lotes as t1 inner join det_servicio as t2
                            on t1.lote = t2.lote
                            WHERE (servicio = 'Red de Cloacas')
                            AND (t2.fecha_pago BETWEEN '$fecha_ini' AND '$fecha_fin')
                            AND (t1.loteo = '$loteo')
                            AND t1.titular <> ''
                            group by t1.lote";
                      
                    $op = 3;
                    $print = "<a href='factura/listado_red.php?loteo=$loteo&f1=$fecha_ini&f2=$fecha_fin&op=$op' class='btn btn-primary print' target='_blan'>Imprimir</a>";
                    
                    
                }
                else $respuesta = "<strong>Fechas incorrectas.</strong>";           
                
              }
              else // loteo y red:
              {
                             
                if($_POST['loteo']!="" && $_POST['red']!="")
                {
                  if($_POST['red'] == '1')
                  {
                    $loteo = $_POST['loteo'];
                    $red = $_POST['red'];

                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                              FROM det_lotes as t1 inner join det_servicio as t2
                              on t1.lote = t2.lote
                              WHERE (servicio = 'Red de Cloacas') 
                              AND t1.loteo = '$loteo'
                              AND t2.forma_pago <> ''
                              group by t1.lote
                              order by t1.lote";
                    
                    $op = 4;
                    $print = "<a href='factura/listado_red.php?loteo=$loteo&red=$red&op=$op' class='btn btn-primary print' target='_blan'>Imprimir</a>";
                  }
                  else  
                  {
                    $loteo = $_POST['loteo'];
                    $red = $_POST['red'];

                    $qry = "SELECT t1.titular, t1.loteo, t1.lote, t2.fecha_pago, t2.forma_pago  
                              FROM det_lotes as t1 inner join det_servicio as t2
                              on t1.lote = t2.lote
                              WHERE (servicio = 'Red de Cloacas') 
                              AND t1.loteo = '$loteo'
                              AND t1.titular <> ''
                              AND t2.forma_pago = ''
                              group by t1.lote
                              order by t1.lote";
                    
                    $op = 5;
                    $print = "<a href='factura/listado_red.php?loteo=$loteo&red=$red&op=$op' class='btn btn-primary print' target='_blan'>Imprimir</a>";
                  }
                }
                
              }
              
            }
            
            if($op > 0)
            {
              
              $res = mysqli_query($connection,$qry);
              if($res->num_rows > 0)
              {
                echo $print;
                echo "<br>";
                echo "<hr>";
                echo "<div class='table-responsive'>";
                  while($datos1 = mysqli_fetch_array($res))
                  {
                    $tabla1.="<tr>
                              <td>".$datos1['titular']."</td>
                              <td>".$datos1['loteo']."</td>
                              <td>".$datos1['lote']."</td>
                              <td>".fecha_min($datos1['fecha_pago'])."</td>
                              <td>".$datos1['forma_pago']."</td>
                              </tr>";
                    
                  }
                  $tabla1.="</tbody></table>";
                  echo $tabla1;
                  echo "<script>$('.print').show();</script>";
                echo "</div>"; 
              }
              else{
                echo "<hr>";
                $respuesta = "<strong>No se encontraron lotes con red de Cloacas.</strong>";
              }
                
            }
            
              
          }

          
        ?>
        <div><?php echo $respuesta;?></div>
                

      </div>        
                
    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>