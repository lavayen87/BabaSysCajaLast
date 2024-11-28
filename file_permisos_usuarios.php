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
   // boton permiso
   var id_permiso = parseInt(0);
    $('.btn-permitir').on('click', function(){

        id_permiso = $(this).attr('id');

        if( $('button[id='+id_permiso+']').text() == 'Permitir')
        {
            $(this).val('1');

            var num_caja = $('#content-num-caja').val();
            var valor = $(this).val();
            
            $.post('generar_permiso.php', {'num_caja': num_caja, 'id_permiso': id_permiso, 'valor': valor}, (resp)=>{
              console.log(resp) 
              if(resp == parseInt(1))
              {
                $('button[id='+id_permiso+']').removeClass('btn-primary'); 
                $('button[id='+id_permiso+']').addClass('btn-danger');
                $('button[id='+id_permiso+']').text("Quitar");
              }
            }) 
              
        }
        else
        {
            $(this).val('0');

            var num_caja = $('#content-num-caja').val();
            
            $.post('quitar_permiso.php', {'num_caja': num_caja, 'id_permiso': id_permiso}, (res)=>{
              
              if(res == parseInt(1))
              {
                $('button[id='+id_permiso+']').removeClass('btn-danger'); 
                $('button[id='+id_permiso+']').addClass('btn-primary');
                $('button[id='+id_permiso+']').text("Permitir"); 
                
              } 
            })
        }
        
    })
  });
</script>
<style>

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
      <h2>Permisos a usuarios</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <div class="container">
              <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                  
              </form>
                <input style="display: none;" id="content-num-caja" value="<?php echo $num_caja;?>">
                <?php
                    include('conexion.php');
                    
                    $tabla = "<table class='table  table-hover table-responsive'>        
                    <thead>  
                    <tr>
                    <th>#</th> 
                    <th>Descripción</th>
                    <th>Acción</th>
                    </tr>
                    </thead>
                    <tbody id='tbody-datos'>";
                   

                    // datos de usuario

                    $qry = "SELECT * from usuarios 
                            where numero_caja = '$num_caja'";
                    $res = mysqli_query($connection, $qry);
                  
                    $datos=mysqli_fetch_assoc($res);

                    $usuario = $datos['nombre'];

                    echo "<div class='' style='width: 50%;'>
                            <div class='input-group mb-3'>
                              <label style='width: 25%; background: #15EFD6;' class='input-group-text' for='inputGroupSelect01'>Usuario</label>
                              <input type='text' class='form-control' value='".$usuario."' readonly style='background: white;'>
                            </div>
                          </div>";

                    /*echo "<div id='".$num_caja."' class='div-usuario'>
                         Usuario: "."<strong style='background: #11F3D2'>".$datos['nombre']."</strong>"."<hr>
                         </div>"; */
                    
                    // datos de permisos
                    
                    $qry = "SELECT * from permisos
                            order by id_permiso";
                    $res = mysqli_query($connection, $qry);
                  
                    while($datos=mysqli_fetch_array($res))
                    {
                      
                      $id_permiso = $datos['id_permiso'];

                      $qry_btn_valor = "SELECT btn_accion from det_permisos
                                        WHERE numero_caja = '$num_caja'
                                        AND id_permiso = '$id_permiso'
                                        GROUP BY numero_caja";
                      $res_valor = mysqli_query($connection, $qry_btn_valor);
                      $get_valor = mysqli_fetch_array($res_valor);

                      $valor = $get_valor['btn_accion'];

                      $tabla.="<tr>
                              <td style='width:6%;'>".$datos['id_permiso']."</td>
                              <td style='width:30%;'>".$datos['descripcion']."</td>";
                              if($valor == 0) // valor boton genera permiso!!
                              {
                                $tabla.="<td style='width:25%;'>"."<button class='btn btn-primary btn-permitir' id='".$id_permiso."' value='0'>Permitir</button>"."</td>
                                </tr>";
                              }
                              else
                              {
                                $tabla.="<td style='width:25%;'>"."<button class='btn btn-danger btn-permitir' id='".$id_permiso."' value='1'>Quitar</button>"."</td>
                                  </tr>";                               
                              }
                      /*<td style='width:25%;'>"."<button class='btn btn-primary btn-permitir' id='".$id_permiso."' value='0'>Permitir</button>"."</td>
                      </tr>*/ 
                       
                    }

                    $tabla.="</tbody></table>";
                    echo "<div style='overflow: auto;  height: 500px; border: 1px solid green;'>".$tabla."</div>"
                   
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