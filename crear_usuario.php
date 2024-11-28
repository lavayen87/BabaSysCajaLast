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

$qry = "SELECT * FROM usuarios";

$res = mysqli_query($connection, $qry);

if($res->num_rows == 0)
{
    $num_caja = 1;
}
else
{
    if($res->num_rows >= 1)
    {
        $qry = "SELECT numero_caja FROM usuarios
                ORDER BY numero_caja DESC LIMIT 1";
        $res = mysqli_query($connection,$qry);
        $dato = mysqli_fetch_array($res);

        $num_caja = ($dato['numero_caja'] + 1);
    }
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
        var pass   = "";
        $(document).ready(function(){

            $('.create-user').on('click', function(){
                
                var num_caja = parseInt(0);

                if(parseInt($('#select_show_cajas').val()) > 0){
                    num_caja = parseInt($('#select_show_cajas').val());
                    console.log(num_caja)
                }
                
                nombre = $('#nombre').val();
                usuario= $('#usuario').val();
                pass   = $('#pass').val();

                console.log("valor caja: "+num_caja)
                //block  = parseInt($('#accion_config').val());
                if(nombre!="" && usuario!="" && pass!="")
                {
                    if($('.user-ok').is(':visible')){
                        $('.user-ok').hide();
                        
                    }
                     
                    $.post('nuevo_usuario.php',{'nombre':nombre, 'usuario':usuario, 'pass':pass, 'num_caja':num_caja}, resp => {
                        console.log(resp)
                        if(resp == parseInt(1))
                        {
                            
                            $('#load-info').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                            $.ajax({
                                type: "POST",
                                url: "sleep.php",
                                success: function(data)
                                {
                                    $('#load-info').fadeIn(1000).hide();

                                    $('#nombre').val("");
                                    $('#usuario').val("");
                                    $('#pass').val("");
                                    nombre = "";
                                    usuario= "";
                                    pass   = "";
                                    
                                    info = "<h5 style='color: white; text-align: center;'>Usuario creado con exito <i class='fas fa-check-circle'></i></h5>";
                                    
                                    $('.user-ok').html(info);
                                    $('.user-ok').slideDown();

                                    var nuevo_num_caja = parseInt($('#select_show_cajas').val()) + parseInt(1);
                                    $('#select_show_cajas').val(nuevo_num_caja);

                                }
                            });
                            return false;
                            
                        }
                        else{
                            if($('.user-ok').is(':visible')){
                                $('.user-ok').hide();
                                info = "<h5 style='color: white; text-align: center;'><i class='fas fa-exclamation-triangle'></i> Ya existe el usuario o la contraseña !</h5>";
                                $('.user-ok').html(info);
                                $('.user-ok').slideDown();
                            }
                            else{
                                info = "<h5 style='color: white; text-align: center;'><i class='fas fa-exclamation-triangle'></i> Ya existe el usuario o la contraseña !</h5>";
                                $('.user-ok').html(info);
                                $('.user-ok').slideDown();
                            } 
                        }
                    })
                }
                else{
                    if($('.user-ok').is(':visible')){
                        $('.user-ok').hide();
                        info = "<h5 style='color: white; text-align: center;'><i class='fas fa-exclamation-triangle'></i> Debe llenar todos los campos !</h5>";
                        $('.user-ok').html(info);
                        $('.user-ok').slideDown();
                    }
                    else{
                        info = "<h5 style='color: white; text-align: center;'><i class='fas fa-exclamation-triangle'></i> Debe llenar todos los campos !</h5>";
                        $('.user-ok').html(info);
                        $('.user-ok').slideDown();
                    } 
                }
            })
            
            $('.cancel-user').on('click', function(){
                $('#nombre').val("");
                $('#usuario').val("");
                $('#pass').val("");
                nombre = "";
                usuario= "";
                pass   = "";
                if($('.user-ok').is(':visible')){
                    $('.user-ok').slideUp();
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
      <h2>Alta de usuario</h2>
      <hr>
                     
        <div class="alert alert-success" role="alert"> 
            
            <div class="m-4" style="width: 65%; border: 3px solid #22A199; border-radius:8px; padding: 6px;">
                
                <div class="input-group mb-3">
                    <?php
                        include("conexion.php");
                        $div = "";
                        $qry_disp = "select * FROM usuarios WHERE nombre = ''";
                        $qry_res = mysqli_query($connection, $qry_disp);
                        if($qry_res->num_rows > 0)
                        {
                       
                           $div.= "<div class='input-group mb-3'>
                                    <label style='width: 35%; background: #22A199; color: white;' class='input-group-text nombre' for='inputGroupSelect01'>Numero de caja</label>                              
                                    <select class='form-select' id='select_show_cajas'>
                                        <option value=''></option>";
                                    
                                        while($datos = mysqli_fetch_array($qry_res))
                                        {
                                           $div.= "<option value='".$datos['numero_caja']."'>Caja ".$datos['numero_caja']."</option>";
                                        }
                                        $div.= "<option value='".$num_caja."'>Caja ".$num_caja."</option>";
                                    $div.= "</select></div>";
                            echo $div;
                        }
                        else
                        {
                            echo "<label style='width: 35%; background: #22A199; color: white;' class='input-group-text nombre' for='inputGroupSelect01'>Numero de caja</label>".
                                    "<input type='text' class='form-control' id='select_show_cajas' value='".$num_caja."' readonly>";
                        }
                    ?>  
                    <!--descomentar -->
                    <!--label style="width: 35%; background: #22A199; color: white;" class="input-group-text nombre" for="inputGroupSelect01">Numero de caja</label>
                    <input type="text" class="form-control" id="num_caja" value="<?php echo $num_caja;?>" readonly-->
                </div>

                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text nombre" for="inputGroupSelect01">Nombre y apellido</label>
                    <input type="text" class="form-control" id="nombre" maxlength="30">
                </div>
                
                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text usuario" for="inputGroupSelect01">Nombre de usuario</label>
                    <input type="text" class="form-control" id="usuario" maxlength="30">
                </div>
                
                <div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text pass" for="inputGroupSelect01">Contraseña</label>
                    <input type="password" class="form-control" id="pass"  name="pass" maxlength="30">
                </div>
                
                <!--div class="input-group mb-3">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text pass" for="inputGroupSelect01">Acción Config.</label>
                    <select id="accion_config" class="form-select" aria-label="Default select example"> 
                        <option value="0"></option>
                        <option value="1"> Si </option>
                        <option value="0"> No </option>
                    </select>
                </div-->
                
                <div style="margin: 0px auto; text-align: center;">
                    <button type="submit" class="btn btn-success create-user"  style="display:inline-block; margin-left: 4px;">Aceptar</button>
                    <button type="submit" class="btn btn-secondary cancel-user"  style="display:inline-block; margin-left: 4px;">Cancelar</button>
                </div>


            </div>          

            <div class="m-4" id="load-info" style="text-align: center; width: 65%;"></div>

            <div class="m-4 user-ok" style="width: 65%; background: #22A199; border-radius:8px; padding: 6px; display: none;"></div>

             
        </div>
         
    </div>
</main>
</body>