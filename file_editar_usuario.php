<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

$num_caja = $_GET['num_caja'];
include('conexion.php');

$qry = "SELECT * FROM usuarios WHERE numero_caja = '$num_caja'";

$res = mysqli_query($connection, $qry);

if($res->num_rows > 0)
{
    $datos = mysqli_fetch_array($res);
    $n_caja  = $datos['numero_caja'];
    $nombre  = $datos['nombre'];
    $usuario = $datos['usuario'];
    $rol_user= $datos['rol'];
    $pass    = $datos['pass'];
    $nombre_caja = $datos['nombre_caja'];
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="img/logo-sistema.png">  
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <script src="js/jquery-3.5.1.min.js"></script> 
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->
    <!-- Bootstrap core CSS -->
    <script src="js/main-style.js"></script>
    <script src="js/main.js"></script>
    <script>
        var nombre = "";
        var usuario= "";
        var rol    = "";
        var pass   = "";
        var nombre_caja = "";
        var valores = [];
        var i = parseInt(0);
        var checkbox = false;
        $(document).ready(function(){

            $("input[type='checkbox']").on('click',function(){
                if( $(this).val() == '0' )
                {
                    $(this).val('1');
                    $("#div-pass-hidden").slideDown();
                    checkbox = true;
                    console.log("check seleccionado");
                }
                else{
                    $(this).val('0');
                    $("#div-pass-hidden").slideUp();
                    checkbox = false;
                    console.log("check DESELECCIONADO "+ checkbox);
                }
            })
                
        

            $('.editar-user').on('click', function(){
                //$(".mb-3 label").css('background', 'red');
                nombre = $('#nombre').val();
                usuario= $('#usuario').val();
                rol    = $('#rol').val();
                pass   = $('#pass').val();
                nombre_caja   = $('#nombre_caja').val();

                if(checkbox)
                {
                
                    if(nombre!="" && usuario!="" && rol!="" && pass!="" && nombre_caja!="")
                    {
                        
                        $('.user-edicion').each(function() {
                            //console.log($(this).val()+"\n");
                            i++;
                            switch(i)
                            {
                                case parseInt(1):
                                    valores.push(
                                        {'numero_caja':$(this).val(),}
                                    );
                                    break;
                                case parseInt(2):
                                    valores.push(
                                        {'nombre':$(this).val(),}
                                    );
                                    break;
                                case parseInt(3):
                                    valores.push(
                                        {'usuario':$(this).val(),}
                                    );
                                    break;
                                case parseInt(4):
                                    valores.push(
                                        {'rol':$(this).val(),}
                                    );
                                    break;
                                case parseInt(5):
                                    valores.push(
                                        {'nombre_caja':$(this).val(),}
                                    );
                                    break;
                                case parseInt(6):
                                    valores.push(
                                        {'pass':$(this).val(),}
                                    );
                                    break;
                                
                            }    

                        })
                        console.log(JSON.stringify(valores));
                        $.post('actualizar_usuario.php',{'datos_usuario':JSON.stringify(valores)},resp =>{

                            console.log(resp);

                            if($('.edicion-succes').is(':visible')){
                                $('.edicion-succes').hide();
                                
                            }

                            if(resp == parseInt(1)){
                                $('#content-edicion-ok').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                                $.ajax({
                                    type: "POST",
                                    url: "sleep.php",
                                    success: function(data)
                                    {
                                        
                                        $('#content-edicion-ok').fadeIn(1000).html("");
                                        info = "<h5 style='color: white; text-align: center;'>Datos actualizados con exito <i class='fas fa-check-circle'></i></h5>";
                                        
                                        $('.edicion-succes').html(info); 
                                        $('.edicion-succes').slideDown();
                                        
                                        i = parseInt(0);
                                        valores = [];
                                        
                                    }
                                });
                                return false;
                            }
                        })
                        
                    }
                    else{
                        info = "<h5 style='color: white; text-align: center;'><i class='fas fa-exclamation-triangle'></i> Debe llenar todos los campos !</h5>";
                        if($('.edicion-succes').is(':visible')){
                            $('.edicion-succes').hide();
                            $('.edicion-succes').html(info);
                            $('.edicion-succes').slideDown();
                        }
                        else{
                            $('.edicion-succes').html(info);
                            $('.edicion-succes').slideDown();
                        }
                    }
                }
                else{
                    if(nombre!="" && usuario!="" && rol!="" && nombre_caja!="")
                    {
                        
                        $('.user-edicion').each(function() {
                            //console.log($(this).val()+"\n");
                            i++;
                            switch(i)
                            {
                                case parseInt(1):
                                    valores.push(
                                        {'numero_caja':$(this).val(),}
                                    );
                                    break;
                                case parseInt(2):
                                    valores.push(
                                        {'nombre':$(this).val(),}
                                    );
                                    break;
                                case parseInt(3):
                                    valores.push(
                                        {'usuario':$(this).val(),}
                                    );
                                    break;
                                case parseInt(4):
                                    valores.push(
                                        {'rol':$(this).val(),}
                                    );
                                    break;
                                case parseInt(5):
                                    valores.push(
                                        {'nombre_caja':$(this).val(),}
                                    );
                                    break;
                                case parseInt(6):
                                    valores.push(
                                        {'pass':"",}
                                    );
                                    break;
                                
                            }    

                        })
                        console.log(JSON.stringify(valores));
                        $.post('actualizar_usuario.php',{'datos_usuario':JSON.stringify(valores)},resp =>{

                            console.log(resp);

                            if($('.edicion-succes').is(':visible')){
                                $('.edicion-succes').hide();
                                
                            }

                            if(resp == parseInt(1)){
                                $('#content-edicion-ok').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                                $.ajax({
                                    type: "POST",
                                    url: "sleep.php",
                                    success: function(data)
                                    {
                                        
                                        $('#content-edicion-ok').fadeIn(1000).html("");
                                        info = "<h5 style='color: white; text-align: center;'>Datos actualizados con exito <i class='fas fa-check-circle'></i></h5>";
                                        
                                        $('.edicion-succes').html(info); 
                                        $('.edicion-succes').slideDown();
                                        
                                        i = parseInt(0);
                                        valores = [];
                                        
                                    }
                                });
                                return false;
                            }
                        })
                        
                    }
                    else{
                        info = "<h5 style='color: white; text-align: center;'><i class='fas fa-exclamation-triangle'></i> Debe llenar todos los campos  ppp!</h5>";
                        if($('.edicion-succes').is(':visible')){
                            $('.edicion-succes').hide();
                            $('.edicion-succes').html(info);
                            $('.edicion-succes').slideDown();
                        }
                        else{
                            $('.edicion-succes').html(info);
                            $('.edicion-succes').slideDown();
                        }
                    }

                }
            })

            /*$('.cancel-edition-user').on('click', function(){

            })*/
        })
    </script>
    <style>
        input[type=checkbox]{
            width: 16px;
            height: 16px;
            background: red;
        }
    </style>
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
      <h2>Editar usuario</h2>
      <hr>
                     
        <div class="alert alert-success" role="alert"> 
               
            <div class="m-4" style="width: 65%; border: 3px solid green; border-radius:8px; padding: 6px;">
                
                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text nombre" for="inputGroupSelect01">Numero de caja</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo $n_caja;?>" id="n_caja" name="n_caja" readonly>
                </div>

                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text nombre" for="inputGroupSelect01">Nombre y apellido</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo $nombre?>" id="nombre" name="nombre" maxlength="30">
                </div>
                
                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text usuario" for="inputGroupSelect01">Nombre de usuario</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo $usuario?>" id="usuario" name="usuario" maxlength="30">
                </div>
                
                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text pass" for="inputGroupSelect01">Rol de usuario</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo $rol_user?>" id="rol"  name="rol" maxlength="30">
                </div>

                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text nombre_caja" for="inputGroupSelect01">Nombre de caja</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo $nombre_caja?>" id="nombre_caja"  name="nombre_caja" maxlength="30">
                </div>
                
                <input type="checkbox" value="0">  Nueva contraseña
                <br>
                <div class="input-group mb-3" id="div-pass-hidden" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text pass" for="inputGroupSelect01">Contraseña</label>
                    <input type="text" class="form-control user-edicion" value="" id="pass"  name="pass" maxlength="30">
                </div>

                <div style="margin: 0px auto; text-align: center;">
                    <button type="submit" class="btn btn-success editar-user"  style="display:inline-block; margin-left: 4px;">Aceptar</button>
                    <button type="submit" class="btn btn-secondary cancel-edition-user"  style="display:inline-block; margin-left: 4px;">Cancelar</button>
                </div>
            </div>          
            
            <div class="m-4" style="text-align: center; width: 65%;">
                <div id="content-edicion-ok" ></div>
            </div>

            <div class="m-4 edicion-succes" style="width: 65%; background: #22A199; border-radius:8px; padding: 6px; display: none;"></div>

        
        </div>
         
    </div>
</main>
</body>