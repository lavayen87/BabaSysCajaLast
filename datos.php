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
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single7').select2();
  });
</script>
<style>

table thead tr td{
  padding-bottom: 5px;  
}
table thead{
  border-bottom: 1px solid black; 
}
tr:nth-child(odd){
    background:;
}
  tr:nth-child(even){
    background: #B6CCB2;
}

td{
  font color: #60655F;
}
</style>
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
      <h2>Usuarios</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
              
              <?php 
                include('conexion.php');
                $total = 0.00;
                $res ="";
                $res_eo="";
                $tabla = "<table style='width: 100%;' class='table table-striped'>                        
                          <thead>  
                          <tr> 
                          <td><strong>Nº caja</strong></td>                               
                          <td><strong>Nombre</strong></td>
                          <td><strong>Usuario</strong></td>
                          <td><strong>Rol</strong></td>
                          <td><strong>Nombre de caja</strong></td>
                          <td><strong>Pass</strong></td>
                          </tr>
                          </thead>                        
                          <tbody id='tbody-datos'>";

                $qry = "SELECT * FROM usuarios 
                        WHERE numero_caja > 0
                        ORDER BY numero_caja";

                $res = mysqli_query($connection, $qry);            

                if($res->num_rows > 0)
                {
                    while($datos = mysqli_fetch_array($res))
                    {
                        $tabla.= "<tr> 
                        <td style='width:10%'>".$datos['numero_caja']."</td>  
                        <td style='width:15%'>".$datos['nombre']."</td>
                        <td style='width:12%;'>".$datos['usuario']."</td>
                        <td style='width:12%;'>".$datos['rol']."</td>                                                           
                        <td style='width:20%;'>".$datos['nombre_caja']."</td>
                        <td style='width:32%;'>".$datos['pass']."</td>
                        </tr>";                                          
                    }
                }
                echo $tabla;
              ?>
              
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