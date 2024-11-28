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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single5').select2();

    $('.eliminar_usuario').on('click', function(event){
        event.preventDefault();
        caja = $(this).attr('id'); 
        elem = $(this);
        console.log('caja a eliminar: '+$(this).attr('id'))
        var info = "<strong>¿Realmente desea eliminar el usuario con caja N° "+$(this).attr('id')+"? </strong>";
        $('#modal-info').html(info);
        $('#miModal').slideDown();

        $('.ok-modal-delete').on('click', function(){
                      
            
            $.post('eliminar_usuario.php', {'num_caja': caja,}, resp =>{
                console.log(resp)
                if(resp == parseInt(1))
                {
                    $('#miModal').slideUp();
                    elem.closest('tr').remove();  
                    console.log('caja '+caja+' eliminada');
                }
                else console.log('No se eliminó la caja '+caja+' en la BD');
              
            })
        })

        $('.close-modal-delete').on('click', function(){
            $('#miModal').slideUp();
        })
    })

  });
</script>
<style>
     /** modal */
      .modal-contenido{
        background-color: white;
        border: 4px solid #22A49D;
        border-radius: 8px;
        width:300px;
        padding: 10px 20px;
        margin: 20% auto;
        position: relative;
        /*box-shadow: 0 0 0 0.4rem rgba(40, 167, 69, 0.25);*/
        box-shadow: 0 0 0 2px rgb(255,255,255),
                    0.3em 0.3em 1em rgba(0,0,0,0.6);
      }
      .close-modal{
        text-decoration: none;
      }
      .modal{
        /*background-color: #CCC8;/*rgba(0,0,0,.8);
        background-color: transparent;/*rgba(0,0,0,.8);*/
    
        position:fixed;
        top:0;
        right:0;
        bottom:0;
        left:0;
        /*opacity:0.5;*/
        pointer-events:none;
        /*transition: all 1s;*/
        }
        #miModal{ /**target */
        opacity:1;
        pointer-events:auto;
    
        }
        #modal-info strong{
            font-size:17px;
      }
  /** end modal */
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

    <!-- Modal -->
    <div id="miModal" class="modal">
        <div class="modal-contenido">
            
            
            <p id="modal-info"></p>
            <div style="width:100%; height: 35px; margin: 0 auto; text-align: center; ">
                <button class="btn btn-success ok-modal-delete">Aceptar</button>
                <button class="btn btn-secondary close-modal-delete">Canelar</button>
            </div>
        </div>  
    </div>

    <div class="container">
      <h2>Administrar usuarios</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">

                <div style="padding: 4px; float:right;">
                  <a href="crear_usuario.php"  class="btn btn-success">
                    <i class="fas fa-user"></i> Nuevo
                  </a>
                </div>
               
                <?php
                    include('conexion.php');
                    $tabla = "<table class='table table-responsive table-hover'>        
                    <thead>  
                    <tr> 
                    <td><strong>Nombre</strong></td>
                    <td><strong>N° caja</strong></td>
                    <td><strong>Rol</strong></td>
                    <td style='text-aling:center'><strong>Acciones</strong></td>
                    </tr>
                    </thead>
                    <tbody id='tbody-datos'>";
                    
                    $qry = "SELECT * from usuarios 
                            where numero_caja <> 0
                            order by numero_caja";
                    $res = mysqli_query($connection, $qry);
                    if($res->num_rows > 0)
                    {
                       while($datos=mysqli_fetch_array($res))
                       {
                            $num_caja = $datos['numero_caja'];
                            $tabla.="<tr>
                            <td style='width:15%;'>".$datos['nombre']."</td>";

                            if($datos['numero_caja'] > 0)
                              $tabla.="<td style='width:8%;'>".$datos['numero_caja']."</td>";
                            else
                              $tabla.="<td style='width:8%;'></td>";
                        
                            
                             $tabla.="<td style='width:15%;'>".$datos['rol']."</td>
                                <td style='width:25%;'>"."<a href='file_permisos_usuarios.php?num_caja=$num_caja' class='btn btn-primary'>Permisos</a> 
                                                          <a href='file_editar_usuario.php?num_caja=$num_caja' class='btn btn-dark' title='Editar'><i class='fas fa-edit'></i></a>
                                                          <button class='btn btn-danger eliminar_usuario sml' id='".$num_caja."' title='Eliminar'><i class='fas fa-trash-alt'></i></button>"."</td>
                                </tr>
                                </tbody>";
                           
                       } 

                       echo $tabla;
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