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
      <h2>Reimprimir Tansferencia</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            
              <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div style="margin-bottom: 10px;">
                  <strong>Buscar por Nº de Trasnferencia.</strong>
                </div>
                
                <input type="text" name="filtro-tr" id="filtro-tr">
                
                <button id="buscar-tr" name ="buscar-tr" class="btn btn-success">Buscar</button>
                <button id="cancelar-busqueda-tr" class="btn btn-secondary" style="display: inline-block;">Cancelar
                </button>
              </form>
              <hr>
                <?php  
                  $alerta = "";
                  $filtro = "";
                 if(isset($_POST['buscar-tr']))
                 {
                  $filtro = $_POST['filtro-tr'];
                  if($filtro != "")
                  {
                    include('conexion.php');
                    include('funciones.php');
                    $tabla = "<table class='table table-striped'>
                              <thead>
                                <tr>
                                  <th><strong>#</strong></th>
                                  <th><strong>Fecha</strong></th>
                                  <th><strong>Caja origen</strong></th>
                                  <th><strong>Caja destino</strong></th>
                                  <th><strong>Moneda</strong></th>
                                  <th><strong>Pesos</strong></th>
                                  <th><strong>Dolares</strong></th>
                                  <th><strong>Euros</strong></th>
                                  <th><strong>Cheques</strong></th>
                                  <th><strong>Acción</strong></th>
                                </tr>
                              </thead>
                              <tbody id='res-tr'>
                              </tbody>
                            ";
                            
                    $qry = "SELECT * FROM transferencias
                            WHERE numero_tr = '$filtro'
                            order by numero_tr";
                    $res = mysqli_query($connection, $qry);

                    if($res->num_rows > 0)
                    {
                      while($datos = mysqli_fetch_array($res))
                      {
                        $num_tr = $datos['numero_tr'];
                        $tabla.="<tr>
                        <td>".$datos['numero_tr']."</td>
                        <td>".fecha_min($datos['fecha'])."</td>
                        <td>".$datos['nombre_caja_origen']."(".$datos['numero_caja_origen'].")"."</td>
                        <td>".$datos['nombre_caja_destino']."(".$datos['numero_caja_destino'].")"."</td>
                        <td>".$datos['moneda']."</td>
                        <td>"."$".number_format($datos['pesos'],2,',','.')."</td>
                        <td>"."US".$datos['dolares']."</td>
                        <td>"."€".$datos['euros']."</td>
                        <td>"."$".$datos['cheques']."</td>
                        <td>"."<a href='factura/Reimprimir_tr.php?num_tr=$num_tr' target='_blank' class='btn  btn-primary btn-solicitud-sop' id='".$datos['numero_tr']."'>
                          <i class='fas fa-print'></i>
                          </a>"."</td>
                        </tr>
                        </table>";

                            

                      }
                      echo $tabla;
                    }
                    else
                    {
                      $alerta = "<strong>No se encontraron transferencias.</strong>";
                    }
                  }
                  else
                  {
                    $alerta = "<strong><i class='fas fa-exclamation-triangle'></i>Debe ingresar un filtro de busqueda.</strong>";
                  }
                 }
                ?>
                <div style="display: none;" id="resultado-busqueda"></div>
               
                <div style="" id="alerta-resultado"><?php echo $alerta; ?></div>
              
            
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