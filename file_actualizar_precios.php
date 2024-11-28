<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

$resp = "";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="img/logo-sistema.png">  
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar preicos servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <script src="js/jquery-3.5.1.min.js"></script> 
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->
    <!-- Bootstrap core CSS -->
    <script src="js/main-style.js"></script>
    <script src="js/main.js"></script>
    <script>
       
       var datos = Array();
       var band  = false;
       var c = parseInt(0);
        $(document).ready(function(){

            $("#set-precios").on('click',function(){
               $("input[class='nuevo-precio']").each(function(){
                    if(parseInt($(this).val()) > 0)
                    {
                        datos.push({
                            "id":$(this).prop('id'),
                            "precio":$(this).val()
                        });

                    }

               }); 

               if(parseInt(datos.length) > 0)
                {
                    band = true;
                    
                }

                if(band)    
                {
                   $.post('editar_precios_servicios.php',{'datos':JSON.stringify(datos)},resp=>{
                    console.log(resp)
                    if(resp == parseInt(1))
                    {
                        setTimeout(function() {
                            window.location = "file_actualizar_precios.php";
                        }, 1000); 
                    }
                    else alert('No se pudo realizar la actualización, intente nuevamente');
                   })
                }
                else
                {
                    
                    alert('Debe completar al menos un campo con números mayores a cero.');
                }
            })

            // limpiar campos

            $("#clear").on('click',function(){
                $("input[class='nuevo-precio']").each(function(){
                   $(this).val("");
                    
               }); 
            });
            
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
      <h2>Actualizar precios de servicios</h2>
      <hr>
                     
        <div class="alert alert-success" role="alert"> 
            
              
            <div class="m-4" style="width: 65%; border: 3px solid green; border-radius:8px; padding: 6px;">

                <?php  
                    include('conexion.php');

                    $qry = "SELECT * FROM precios_servicios";

                    $res = mysqli_query($connection, $qry);

                    if($res->num_rows > 0)
                    {
                        while ($datos = mysqli_fetch_array($res)) 
                        {
                            
                        
                            $id = $datos['id'];
                            $servicio  = $datos['servicio'];
                            $precio = $datos['precio']; 
                            ?>
                            <div class="input-group mb-3 precios-servicios">
                                <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">
                                    <?php echo $servicio;?>                            
                                </label>
                                <input type="text" class="form-control user-edicion" value="<?php echo "$".number_format($precio,2,',','.');?>" id="<?php echo $id;?>" class="precio" readonly>
                                <input type="number" class="nuevo-precio" id="<?php echo $id;?>" value="">

                            </div>

                        <?php
                        }
                
                    }
                ?>
                <div style="text-align: right;">
                    <button class="btn btn-primary" id="set-precios">Guardar</button>
                
                    <button class="btn btn-secondary" id="clear">Cancelar</button>
                </div>
                
            </div>       
            

            <br>

            <div class="m-4" id="resp" style="text-align: center; width: 65%;">
                
            </div>

           
        
        </div>
         
    </div>
</main>
</body>