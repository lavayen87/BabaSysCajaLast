<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

include('conexion.php');
include('funciones.php');

$result = "";
$servicio = "";
$k = 0;


if(isset($_POST['cargar']))
{
    if( ($_POST['select-loteo']!="") && ($_POST['input-desde']!="") && ($_POST['input-hasta']!=""))
    { 

        $loteo = $_POST['select-loteo'];

        $i = $_POST['input-desde'];
        $n = $i;
        $j = $_POST['input-hasta'];
        $cant = ($j - $i + 1);
        
        if( ($i > 0) && ($i <= $j))
        {
            // includes
      
            for($i; $i <= $j; $i++)
            {
                               
                if($i < 10)
                    $lote = set_codigo($loteo).'000'.$i;
                else{
                    if($i>= 10 && $i<100)
                        $lote = set_codigo($loteo).'00'.$i; 
                    else{
                        if($i>= 100 && $i<1000)
                            $lote = set_codigo($loteo).'0'.$i;
                        else
                            $lote = set_codigo($loteo).$i;
                    }
                }

                $k = $i-1;

                $insert = "INSERT IGNORE INTO det_lotes VALUES
                ('$i','$loteo','$lote','',0,'',0,'0000-00-00')";

                mysqli_query($connection, $insert);

                for($t=0; $t<4; $t++)
                {
                    if($t==0)
                        $servicio ='Agrimensor';
                    else
                        if($t==1)
                            $servicio ='Agua';
                        else
                            if($t==2)
                                $servicio ='Cloacas';
                            else
                                $servicio ='Red de Cloacas';
                                

                    $insert2 = "INSERT IGNORE INTO det_servicio VALUES
                    ('',
                    '$i',
                    '$loteo',
                    '$lote',
                    '$servicio',
                    '0000-00-00',
                    0,
                    '',
                    '0000-00-00',
                    '0000-00-00',
                    '0000-00-00',
                    '')";

                    mysqli_query($connection, $insert2);
                }
                
            }
            
            //mysqli_close($connection);

            $result="<div class='alert alert-primary' role='alert' style='border: 2px solid Blue;'>
                    Carga exitosa! "."<b>".$cant."</b>"." lotes nuevos en "."<b>".$loteo."</b>"." del "."<b>".$n."</b>"." al "."<b>".$j."</b> 
                    </div>";
        }

        else{
            $result="<div class='alert alert-dark' role='alert'>
                    Error en el ingreso de datos. Formato correcto : Desde > 0  y Desde < Hasta.
                    </div>";
        }

        
    }
    else{
        $result="<div class='alert alert-dark' role='alert'>
                <strong>Debe completar todos los campos!</strong>
                </div>";
    }


}

/************************/

    if(isset($_POST['nuevo-loteo']))
    {
        if( ($_POST['nuevo-loteo']!="") && ($_POST['codigo-loteo']!=""))
        {

            $nuevoloteo = $_POST['nuevo-loteo'];
            $codigoloteo = strtoupper($_POST['codigo-loteo']);

            $insert = "INSERT IGNORE INTO loteos VALUES ('','$nuevoloteo','$codigoloteo')";
            $result = mysqli_query($connection,$insert);

            $result="<div class='alert alert-primary' role='alert' style='border: 2px solid Blue;'>
                <strong>Loteo "."'".$nuevoloteo."'"." creado con exito !</strong>
                </div>";

        }
        else{
            $result="<div class='alert alert-dark' role='alert'>
                    <strong>Debe completar todos los campos!</strong>
                    </div>";
        }
        
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar lotes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <script src="js/jquery-3.5.1.min.js"></script> 
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->
    <!-- Bootstrap core CSS -->
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
      <h2>Cargar lotes</h2>
      <hr>
                     
        <div class="alert alert-success" role="alert">             
            
            <strong>Esta sección permite crear nuevos loteos.</strong>
            <div class="m-4" style="width: 65%; border: 1px solid green; border-radius:8px; padding: 4px;">

                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    
                    <div class="col-md-4" style="display:inline-block; width: 30%; text-align: center;">
                        <strong>Nuevo Loteo</strong>
                        <input type="text" name="nuevo-loteo" class="form-control" maxlength="30">
                    </div>

                    <div class="col-md-4" style="display:inline-block; width: 15%; text-align: center;">
                        <strong>Codigo Loteo</strong>
                        <input type="text" name="codigo-loteo" class="form-control" maxlength="5">
                    </div>

           
                    <button type="submit" class="btn btn-primary" name="crear-loteo" style="display:inline-block; margin-left: 4px;">Crear</button>
                    
                </form>
            </div>

            <hr>

            <strong>Esta sección permite agregar lotes.</strong>
            <div class="m-4" style="width: 65%; border: 1px solid green; border-radius:8px; padding: 4px;">
                    
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                    
                    <div class="row-fluid" style="display:inline-block; width: 30%; text-align: center;">
                        <strong>Loteo</strong>
                        <select  name="select-loteo" class="form-select">
                          <option value=""><?php echo ""; ?></option> 
                          <?php
                            include("conexion.php");
                            $consulta = "SELECT DISTINCT * FROM loteos ORDER BY id";
                            $resultado = mysqli_query($connection , $consulta);

                            while($misdatos = mysqli_fetch_assoc($resultado))
                            { 
                                echo "<option value='".$misdatos['nombre']."'>".$misdatos['nombre']."</option>"; 
                            }
                          ?>          
                        </select>
                    </div>

                    <div class="row-fluid" style="display:inline-block; width: 20%; text-align: center;">
                        <strong>Desde</strong>
                        <input type="number" name="input-desde" class="form-control">   
                    </div>

                    <div class="row-fluid" style="display:inline-block; width: 20%; text-align: center;">
                        <strong>Hasta</strong>
                        <input type="number" name="input-hasta"class="form-control">   
                    </div>
                    
                    <button type="submit" class="btn btn-primary" name="cargar" style="display:inline-block; margin-left: 4px;">Cargar</button>
                </form>
            </div>

            <div id="load"></div>
            <div class="m-4" style="width: 65%; padding: 4px;"><?php echo $result;?></div>
        </div>
         
    </div>
</main>
</body>