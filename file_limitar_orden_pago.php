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
<script>
    $(document).ready(function(){

        var caja_usuario = parseInt(0);
        var monto = "";

        $('#select-user').on('change', function(){
            caja_usuario = $('#select-user').val();
            console.log('caja usuario: '+caja_usuario);
        })

        $('#monto-tope').on('change', function(){
            monto = $('#monto-tope').val();
            console.log('Monto: '+monto);
        })

        $('#confirmar-tope').on('click', function(){
            if( monto > 0 && monto !="")
            {
                $.post('cargar_tope_op.php', {'caja_usuario':caja_usuario, 'monto': monto}, resp => {
                    console.log(resp)
                    
                    $('#content-limit').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "GET",
                        url: "sleep.php",
                        success: function(data) 
                        {
                            //Cargamos finalmente el contenido deseado
                            $('#content-limit').fadeIn(1000).html(data+"<strong style='color: green;'> Limite de órden de pago actualizado !</strong>");
                            $('#select-user').val("");
                            $('#monto-tope').val("");
                            caja_usuario = parseInt(0);
                            monto = "";
        
                        }
                    });
                    return false;            
                })
            }
            else alert('¡ Debe ingresar un valor !');
        })

        // cancelar cobranza
        $('#cancelar-tope').on('click', function(){
            $('#select-user').val("");
            $('#monto-tope').val("");
            caja_usuario = parseInt(0);
            monto = "";
            
            if($('#content-limit').is(':visible')){
              $('#content-limit').slideUp();
            }
        })

    })

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
      <h2>Limitar órden de pago</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">
              <div class="form-group col-md-6" style="width: 30%;">
                <strong>Usuario</strong>
                <select class="form-select" id="select-user">
                  <option value="-1"></option>
                  <?php
                    include('conexion.php');
                    $sql = "SELECT * FROM usuarios
                            WHERE numero_caja <> 0 and block_caja = 1";
                    $res = mysqli_query($connection, $sql);
                    if($res->num_rows > 0){
                      while($datos = mysqli_fetch_array($res)){
                        echo "<option value='".$datos['numero_caja']."'>".$datos['rol']."</option>";
                      }
                      mysqli_close($connection);
                    } 
                  ?>
                </select>

                <br>

                <strong>Monto a cargar</strong>
                <input type="number" class="form-control" id="monto-tope" >
              
                <br>
                <button id="confirmar-tope" class="btn btn-success">Confirmar</button>
                <button id="cancelar-tope" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                <br><br>
                
                  <div class="row">
                    <div id="content-limit" class="col-lg-12">
                        
                    </div>
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