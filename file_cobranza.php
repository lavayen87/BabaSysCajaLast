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
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<script>
  $(document).ready(function(){

    var monto = "";

    $('#monto').on('change', function(){
        monto = $('#monto').val();
        console.log('Monto a cargar en cobranza: '+new Intl.NumberFormat("de-DE").format(monto));
        //alert(new Intl.NumberFormat("de-DE", {style: "currency", currency: "ARS"}).format(monto));
    })

    $('#confirmar-monto').on('click', function(){
        if( monto > 0 && monto !="")
        {
            $.post('cargar_cobranza.php', {'monto': monto}, resp => {
                console.log(resp)
                
                $('#content-monto').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                $.ajax({
                    type: "GET",
                    url: "sleep.php",
                    success: function(data) 
                    {
                        //Cargamos finalmente el contenido deseado
                        $('#content-monto').fadeIn(1000).html(data+"<strong style='color: green;'> Cobranza actualizada !</strong>");
                        monto = "";
                        $('#monto').val("");
    
                    }
                });
                return false;            
            })
        }
        else alert('¡ Debe ingresar un valor !');
    })

    // cancelar cobranza
    $('#cancelar-monto').on('click', function(){
        monto = ""; 
        $('#monto').val("");
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
      <h2>Cargar cobranza</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">
              <div class="form-group col-md-6">
                <strong>Monto a cargar</strong>
                <input type="number" class="form-control" id="monto" >
              
                <br>
                <button id="confirmar-monto" class="btn btn-success">Confirmar</button>
                <button id="cancelar-monto" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                <br><br>
                
                  <div class="row">
                    <div id="content-monto" class="col-lg-12">
                        
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