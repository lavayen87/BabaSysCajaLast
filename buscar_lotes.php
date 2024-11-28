<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

$result = "";
$filtro = "";
$op = 0;
$cabecera = "<table class='table table-bordered' style='background: #ffff;'>
            <thead>
            <th>Loteo</th>
            <th>Codigo</th>
            <th>Titular</th>
            <th>Accion</th>
            </thead>
            <tbody>";
include('conexion.php');

if(isset($_POST['buscar']))
{
    if($_POST['inputcodigo'])
    {
        $filtro = $_POST['inputcodigo'];
        $qry = "SELECT t2.loteo, t2.lote, t1.nombre FROM clientes as t1 INNER JOIN det_servicio as t2 
        ON t1.lote = t2.lote
        WHERE t1.lote = '$filtro'
        GROUP BY t1.lote";
        $op++;
    }
    else
    {
        if($_POST['inputdni'])
        {
            $filtro = $_POST['inputdni'];
            $qry = "SELECT t2.loteo, t2.lote, t1.nombre FROM clientes as t1 INNER JOIN det_servicio as t2 
            ON t1.lote = t2.lote
            WHERE t1.lote = '$filtro'
            GROUP BY t1.lote";
            $op++;
        }
        else 
            if($_POST['inputapellido'])
            {
                $filtro = $_POST['inputapellido'];
                $qry = "SELECT t2.loteo, t2.lote, t1.nombre FROM clientes as t1 INNER JOIN det_servicio as t2 
                ON t1.lote = t2.lote
                WHERE t1.nombre LIKE '%$filtro%'
                GROUP BY t1.lote";
                $op++;
            }
    }
    
    if($op == 1)
    {
        $res = mysqli_query($connection, $qry);
        if($res->num_rows > 0)
        {
            $result.=$cabecera;
            while($datos = mysqli_fetch_array($res))
            {
                $lote = $datos['lote'];
                $result.="<tr>
                        <td>".$datos['loteo']."</td>
                        <td>".$datos['lote']."</td>
                        <td>".$datos['nombre']."</td>
                        <td>"."<a href='file_ficha_cliente.php?lote=$lote' class='btn btn-primary'>Ver</a>"."</td>
                        </tr>";  
            }
            $result.="</tbody></table>";
        }
        else{
            $result = "<div class='alert alert-dark' role='alert'><strong>No se encontraron resultados.</strong></div>";
        }
    }
    else $result = "<div class='alert alert-dark' role='alert'><strong>Debe ingresar un filtro de busqueda.</strong></div>";

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar lotes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <script src="js/jquery-3.5.1.min.js"></script> 
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->
    <!-- Bootstrap core CSS -->
    <script src="js/main-style.js"></script>
    <script src="js/main.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {

    })
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
      <h2>Busqueda de lotes</h2>
      <hr>
                     
        <div class="alert alert-success" role="alert"> 
            
                <div style="margin-left: 22px;">
                    <strong>Filtros de busqueda</strong>
                </div> 
               
            <div class="m-4" style="width: 62%; border: 1px solid green; border-radius:8px; padding: 4px;">
               
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                    <div class="mb-3" style="width: 100px; display:inline-block; margin-left: 4px; text-align: center;">
                        <strong>Codigo</strong>
                        <input type="text" class="form-control" name="inputcodigo" maxlength="6">
                    </div>
                    
                    <div class="mb-3" style="width: 100px; display:inline-block; margin-left: 4px; text-align: center;">
                        <strong>Nº D.N.I.</strong>
                        <input type="number" class="form-control" name="inputdni" maxlength="8">
                    </div>
                    <div class="mb-3" style="display:inline-block; margin-left: 4px; text-align: center;">
                        <strong>Apellido</strong>
                        <input type="text" class="form-control" name="inputapellido" maxlength="30">
                    </div>
                    <button type="submit" class="btn btn-primary" name="buscar" style="display:inline-block; margin-left: 4px;">Buscar</button>
                </form>
            </div>
            <div><?php echo $result;?></div>
        </div>
         
    </div>
</main>
</body>
</html>