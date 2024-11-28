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
<link rel="shortcut icon" href="img/logo-sistema.png">
<head>
	
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
    $('.js-example-basic-single5').select2();
  });
</script>
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
      <h2>Editar una Obra</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">
              <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <strong>Seleccione la obra</strong>

                <div style="width: 80%;">

                  <div style=" float: left;"> 

                    <select  class="js-example-basic-single5 form-control" name="obras">
                        <option value=""><?php echo ""; ?></option> 
                        <!-- js-example-basic-multiple name="states[]" multiple="multiple" para select multiple-->
                        <?php
                        include("conexion.php");
                        $consulta = "SELECT DISTINCT * FROM obras ORDER BY id_obra";
                        $resultado = mysqli_query($connection , $consulta);

                        while($misdatos = mysqli_fetch_assoc($resultado))
                        { 
                          echo "<option value='".$misdatos['nombre_obra']."'>".$misdatos['nombre_obra']."</option>"; 
                        }

                        ?>          
                    </select>

                  </div>

                 

                  <div style="margin-left: 6%; display: inline-block;">
                    <button id="editar-cuenta" name="select-nomb-ob" class="btn btn-success">Selecionar</button>
                    <button id="eliminar-obra" name="eliminar-obra" class="btn btn-danger">Eliminar</button>    
                    

                  </div>

                </div>

                <br>
                                          
              </form>

                <?php  

                  $ob = "";
                  if(isset($_POST['select-nomb-ob']))
                  {
                    
                    if($_POST['obras'] != "")
                    {

                      include('conexion.php');

                      $ob = $_POST['obras'];
                      $qry = "SELECT * from obras where nombre_obra = '$ob'";
                      $res = mysqli_query($connection, $qry);
                      $datos = mysqli_fetch_array($res);


                      $id = $datos['id_obra']; 
                      //$cuenta = $datos['descripcion'];

                      echo "
                            <div id='result-obra'>

                              <input type='text' style='width: 36%;' id='nueva_desc_obra' value='".$ob."'/>

                              <div style='margin-left: 4%; display: inline-block;'>

                                <button id='".$id."' name='editar-obra' class='btn btn-primary editar-obra'>Confirmar</button>

                                <button id='cancelar-edicion-obra' class='btn btn-secondary' style='display: inline-block; '>Cancelar</button>

                              </div>

                            </div>
                            "; 


                    }
                    else
                    {
                      echo "<strong style='color: #B21F00;'>Debe seleccionar una obra.</strong>";
                    }

                        
                  }

                  if(isset($_POST['eliminar-obra']))
                  {

                    if($_POST['obras'] != "")
                    {
                      include('conexion.php');

                      $ob = $_POST['obras'];
                      $qry = "DELETE from obras where nombre_obra = '$ob'";
                      $res = mysqli_query($connection, $qry);

                      $query ="SELECT * from cuentas";
                      $result = mysqli_query($connection, $query);
                      $datos = mysqli_fetch_array($result);                      
                                  
                      echo "<strong>Obra ''".$ob."'' eliminada.</strong>";
                  
                    }
                    else echo "<strong style='color: #B21F00;'>Debe seleccionar una obra.</strong>";
                  }

                ?>
                
                <br>
                <div class="row" id="content-edicion-obra">
                    <div  class="col-lg-12">
                        
                    </div>
                </div>


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