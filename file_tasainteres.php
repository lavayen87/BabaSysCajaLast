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
<style>
  .indice{
    text-align: center;
  }
  /** modal */
  .modal-contenido{
        background-color: white;
        border: 4px solid #22A49D;
        border-radius: 8px;
        width:300px;
        padding: 10px 20px;
        margin: 20% auto;
        position: relative;
        }
        .close-modal{
            text-decoration: none;
        }
        .modal{
        background-color: #CCC8;/*rgba(0,0,0,.8);*/
        position:fixed;
        top:0;
        right:0;
        bottom:0;
        left:0;
        opacity:0.5;
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
                <button class="btn btn-success close-modal" id="close-modal">Aceptar</button>
            </div>
        </div>  
    </div>
    <div class="container">
      <h2>Administrar tasas de interes</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="m-4" style="width: 65%; border: 3px solid #22A199; border-radius:8px; padding: 6px;">

                <?php  
                    include('conexion.php');

                    $qry = "SELECT * FROM indices_fn";

                    $res = mysqli_query($connection, $qry);

                    if($res->num_rows > 0)
                    {
                        while ($datos = mysqli_fetch_array($res)) 
                        {
                            
                        
                            $id = $datos['id'];
                            $loteo  = $datos['loteo'];
                            $indice = $datos['indice']; 
                            ?>
                            <div class="input-group mb-3 precios-servicios">
                                <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">
                                    <?php echo $loteo;?>                            
                                </label>
                                <input type="text" class="form-control indice" value="<?php echo "%".number_format($indice,2,',','.');?>" id="<?php echo $id;?>" class="precio" readonly>
                                <input type="number" class="nuevo_indice" id="<?php echo $id;?>" value="">

                            </div>

                        <?php
                        }
                
                    }
                ?>
                <div style="text-align: right;">
                    <button class="btn btn-primary" id="btnRealizar">Guardar</button>
                
                    <button class="btn btn-secondary" id="clear">Cancelar</button>
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
<script>
  $(document).ready(function() {

    datos = []; 
    var info = '';


     //confirmar actualizacion de tasa d einteres
    $('#btnRealizar').on('click', function(){

      $("input[class='nuevo_indice']").each(function(){
        if(parseInt($(this).val()) >= 0)
        {
            datos.push({
                "id":$(this).prop('id'),
                "indice":$(this).val()
            });
        }  
      }); 

      if(parseInt(datos.length) > 0)
      {
        
        $.post('actualizar_tasainteres.php',{'datos':JSON.stringify(datos)},resp=>{
          console.log(resp)
          if(resp == parseInt(1))
          {
              setTimeout(function() {
                  window.location = "file_tasainteres.php";
              }, 1000); 
          }
          else
          {
            info = 'No se pudo realizar la actualización, intente nuevamente.';
            $('#modal-info').html(info)
            $('#miModal').slideDown();    
          }

        })    
      }
      else
      {            
        info = 'Debe completar al menos un campo con números mayores a cero.';
        $('#modal-info').html(info);
        $('#miModal').slideDown();    
        
      }
           

      // Close modal
      $('#close-modal').on('click', function(){
        $('#modal-info').html('');
        $('#miModal').slideUp();
      }) 

      // limpiar campos
      $("#clear").on('click',function(){
          $("input[class='nuevo_indice']").each(function(){
             $(this).val("");
              
         }); 
      });
     

    })

  })
</script>
</body>
</html>