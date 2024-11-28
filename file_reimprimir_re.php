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
      <h2>Reimprimir Retiros</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">
              <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div style="margin-bottom: 10px;">
                  <strong>Buscar por Nº de retiro.</strong>
                </div>
                
                <input type="text" name="filtro-re" id="filtro-op">
                
                <button id="buscar-re" name ="buscar-re" class="btn btn-success">Buscar</button>
                <button id="cancelar-busqueda-op" class="btn btn-secondary" style="display: inline-block;">Cancelar
                </button>
              </form>
              <hr>
                <?php  
                  $alerta = "";
                  $filtro = "";
                 if(isset($_POST['buscar-re']))
                 {
                  $filtro = $_POST['filtro-re'];
                  if($filtro != "")
                  {
                    include('conexion.php');
                    include('funciones.php');
                    $tabla = "<table class='table table-striped'>
                              <thead>
                                <tr>
                                  <th><strong>#</strong></th>
                                  <th><strong>Fecha</strong></th>
                                  <th><strong>Personal</strong></th>
                                  <th><strong>Concepto</strong></th>                              
                                  <th><strong>Cuenta</strong></th>
                                  <th><strong>Importe</strong></th>
                                  <th><strong>Acción</strong></th>
                                </tr>
                              </thead>
                              <tbody id='res-tr'>
                              </tbody>
                            ";
                    $qry = "SELECT * FROM retiros
                            WHERE numero_retiro = '$filtro'
                            order by numero_retiro";
                    $res = mysqli_query($connection, $qry);

                    if($res->num_rows > 0)
                    {
                      while($datos = mysqli_fetch_array($res))
                      {
                        $num_re = $datos['numero_retiro'];
                        $tabla.="<tr>
                        <td>".$datos['numero_retiro']."</td>
                        <td>".fecha_min($datos['fecha_retiro'])."</td>
                        <td>".$datos['personal_habilitado']."</td>
                        <td>".$datos['concepto']."</td>
                        <td>".$datos['cuenta']."</td>
                        <td>"."$".number_format($datos['importe'],2,',','.')."</td>
                        <td>"."<a href='factura/retiro_pdf.php?num_re=$num_re' target='_blank' class='btn  btn-primary btn-solicitud-sop' id='".$datos['numero_retiro']."'>
                          <i class='fas fa-print'></i>
                          </a>"."</td>
                        </tr>
                        </table>";

                            

                      }
                      echo $tabla;
                    }
                    else
                    {
                      $alerta = "<strong>No se encontraron retiros.</strong>";
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
    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>