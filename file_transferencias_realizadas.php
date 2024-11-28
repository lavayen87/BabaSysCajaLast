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
      <h2>Transferencias Realizadas</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">


            <div class="table-responsive">
                <?php  
                  include('conexion.php');
                  include('funciones.php');

                  $qry = "SELECT * from transferencias WHERE numero_caja_origen = '$numero_caja'";
                  $res = mysqli_query($connection, $qry);
                  if($res->num_rows > 0)
                  {  
                              
                      $tabla = "<table class='table table-striped'>
                                <thead>  
                                  <tr>                             
                                    <td><strong>N°</strong></td>  
                                    <td><strong>Fecha</strong></td>                        
                                    <td><strong>Caja destino</strong></td>
                                    <td><strong>Detalle</strong></td>
                                    <td><strong>Moneda</strong></td>
                                    <td><strong>Pesos</strong></td>
                                    <td><strong>Dolares</strong></td>
                                    <td><strong>Euros</strong></td>
                                    <td><strong>Cheques</strong></td>
                                  </tr>
                                </thead>
                                <tbody id='tbody-datos'>";
                      $cabecera = "<table class='table table-striped'>
                                <thead>  
                                  <tr>                             
                                    <td><strong>N°</strong></td>  
                                    <td><strong>Fecha</strong></td>                        
                                    <td><strong>Caja destino</strong></td>
                                    <td><strong>Detalle</strong></td>
                                    <td><strong>Moneda</strong></td>
                                    <td><strong>Pesos</strong></td>
                                    <td><strong>Dolares</strong></td>
                                    <td><strong>Euros</strong></td>
                                    <td><strong>Cheques</strong></td>
                                    <td><strong>Acción</strong></td>
                                  </tr>
                                </thead>
                                <tbody id='tbody-datos'>";
                        
                      while($datos = mysqli_fetch_array($res))
                      {
                          $tabla.= "<tr>  
                                    <td style='width:4%;'>".$datos['numero_tr']."</td>                             
                                    <td style='width:7%;'>".fecha_min($datos['fecha'])."</td>  
                                    <td>".$datos['nombre_caja_destino']."(".$datos['numero_caja_destino'].")"."</td>                  
                                    <td>".limitar_cadena(ucfirst(strtolower($datos['observaciones'])),15)."</td>
                                    <td>".$datos['moneda']."</td>
                                    <td>"."$".number_format($datos['pesos'],2,',','.')."</td>
                                    <td>"."US".number_format($datos['dolares'],2,',','.')."</td>
                                    <td>"."€".number_format($datos['euros'],2,',','.')."</td>
                                    <td>"."$".number_format($datos['cheques'],2,',','.')."</td>
                                    </tr>";
                          
                      } 
                      $tabla.="</tbody>";
                      echo $tabla;
                  }               
                  else echo "<strong>No se realizaron transferencias.</strong>";
                  
                ?>
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