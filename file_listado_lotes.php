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
<style>
  table tr td a{
    text-decoration: none;
    color: green;
  }
</style>
<script type="text/javascript">
  
  $(window).ready(function(){

    var check1,check2 = parseInt(0);
    var servicio = "";
    $('input[name=check1]').on('click', function(e){
            e.preventDefault;
            if($(this).val() == '0')
            {
                $(this).val('1'); // seleccionado
                $(this).prop("checked", true);
                check1 = parseInt(1);
                servicio = "Agua";

                // deseleccionar el checkbox 2
                $('input[name=check2]').val('0');
                $('input[name=check2]').prop("checked", false);
            }
            else
            {
                $(this).val('0'); // deseleccionado
                $(this).prop("checked", false);
                check1 = parseInt(0);
                servicio = "";
            }
    })

    $('input[name=check2]').on('click', function(e){
            e.preventDefault;
            if($(this).val() == '0')
            {
                $(this).val('1'); // seleccionado
                $(this).prop("checked", true);
                check2 = parseInt(1);
                servicio = "Cloacas";

                // deseleccionar el checkbox 1
                $('input[name=check1]').val('0');
                $('input[name=check1]').prop("checked", false);
            }
            else
            {
                $(this).val('0'); // deseleccionado
                $(this).prop("checked", false);
                check2 = parseInt(0);
                servicio = "";
            }
      })

    // guardar lote pendiente:
    var texto = "";
    $('.btnGuardar').on('click', function(e){

      var lote = $('.txtLote').val();
      if(lote != "" && (parseInt(check1) > parseInt(0)) || (parseInt(check2) > parseInt(0))) 
      {
        $.post('verificar_lote.php',{'lote': lote, 'servicio': servicio}, (resp) =>{
          if(parseInt(resp) == parseInt(1))
          {
            $.post("guardar_lote_pendiente.php",{'lote': lote, 'check1': check1, 'check2': check2}, (resp) =>{
              console.log('Respuesta: '+resp)
              if(parseInt(resp) == parseInt(1))
              {
                if($('.txtOk').is(":visible"))
                {                 
                  $(".txtOk").fadeIn(2000); 
                  texto = "Lote "+lote+" cargado con exito.";
                  $(".txtOk").hide();
                  $(".txtOk").css("background-color","#04D73A");
                  $(".txtOk").css("color","black");
                  $(".txtOk").html(texto);
                  $(".txtOk").show(); 

                  $('input[name=check1]').val('0');  
                  $('input[name=check1]').prop("checked", false);
                  check1 = parseInt(0);
                  $('input[name=check2]').val('0');  
                  $('input[name=check2]').prop("checked", false);
                  check2 = parseInt(0);
                  servicio = "";             
                }
                else
                {
                  texto = "Lote "+lote+" cargado con exito.";
                  $(".txtOk").hide();
                  $(".txtOk").css("background-color","#04D73A");
                  $(".txtOk").css("color","black");
                  $(".txtOk").html(texto);
                  $(".txtOk").show();

                  $('input[name=check1]').val('0');  
                  $('input[name=check1]').prop("checked", false);
                  check1 = parseInt(0);
                  $('input[name=check2]').val('0');  
                  $('input[name=check2]').prop("checked", false);
                  check2 = parseInt(0);
                  servicio = "";      
                }
              }
              else
              {
                console.log('Respuesta: '+resp)
                texto = "Error al guardar el lote, intente nuevamente.";
                $(".txtOk").css("background-color","#FA3720");
                $(".txtOk").css("color","white");
                $(".txtOk").html(texto);
                $(".txtOk").show();

                $('input[name=check1]').val('0');  
                $('input[name=check1]').prop("checked", false);
                check1 = parseInt(0);
                $('input[name=check2]').val('0');  
                $('input[name=check2]').prop("checked", false);
                check2 = parseInt(0);
                servicio = "";     
              }
            })
          }
          else
          {
            if(parseInt(resp) == parseInt(2))
            {
              console.log('Respuesta: '+resp)
              texto = "El lote "+lote+ " ya se encuentra en lotes sin terminar.";
              $(".txtOk").css("background-color","#FA3720");
              $(".txtOk").css("color","white");
              $(".txtOk").html(texto);
              $(".txtOk").show();

              $('input[name=check1]').val('0');  
              $('input[name=check1]').prop("checked", false);
              check1 = parseInt(0);
              $('input[name=check2]').val('0');  
              $('input[name=check2]').prop("checked", false);
              check2 = parseInt(0);
              servicio = "";     
            }
            else
            {
              if(parseInt(resp) == parseInt(3))
              {
                console.log('Respuesta: '+resp)
                texto = "El servicio del lote "+lote+ " ya fué realizado.";
                $(".txtOk").css("background-color","#FA3720");
                $(".txtOk").css("color","white");
                $(".txtOk").html(texto);
                $(".txtOk").show();

                $('input[name=check1]').val('0');  
                $('input[name=check1]').prop("checked", false);
                check1 = parseInt(0);
                $('input[name=check2]').val('0');  
                $('input[name=check2]').prop("checked", false);
                check2 = parseInt(0);
                servicio = "";   
              }
              else
              {
                console.log('Respuesta: '+resp)
                texto = "El lote "+lote+ " no existes.";
                $(".txtOk").css("background-color","#FA3720");
                $(".txtOk").css("color","white");
                $(".txtOk").html(texto);
                $(".txtOk").show();

                $('input[name=check1]').val('0');  
                $('input[name=check1]').prop("checked", false);
                check1 = parseInt(0);
                $('input[name=check2]').val('0');  
                $('input[name=check2]').prop("checked", false);
                check2 = parseInt(0);
                servicio = "";     
              }
            }
          }
        })
      }
      else alert("Debe ingresar el codigo de lote y seleccionar al menos un servicio.");

    })
  
    // Finalizar Servicio
    $('.btnFinalizar').on('click', function(){
      var id = $(this).prop('id'); 
      console.log("id: "+id)
      $.post('actualizar_estado_lote_pendiente.php',{'id_servicio':id}, resp =>{
            if(resp = 'state ok')
            {
                $(this).fadeOut(500);
                $('td[id_estado_pendiente='+id+']').css("color" ,"#17802F");
                $('td[id_estado_pendiente='+id+']').html("<strong>Realizado</strong>");

                // var d = new Date();
                // var month = d.getMonth()+1;
                // var day = d.getDate();
                // var year = d.getFullYear();
                // var output = day + '/' + month + '/' + year.toString().substring(2);
                  
                //$('td[fr_id='+id_link+']').html(output);
            }
            else{
                alert('Error inesperado...Intente nuevamente');
            }
      });

    });

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
      <h2>Lotes y servicios</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->    

            <!-- inicio seccion lotes pendientes -->
            <div class="row">
              
              <div class="col">
                <strong>Lote</strong>
                <input type="text" class="txtLote" maxlength="6" style="width: 113px;">
              </div>

              <div class="col-md-4">

                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox"  name ="check1" value="0" style="width: 20px; height: 20px">
                  <label class="form-check-label" for="inlineCheckbox1"><strong>Agua</strong></label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox"  name ="check2" value="0" style="width: 20px; height: 20px">
                  <label class="form-check-label" for="inlineCheckbox2"><strong>Cloacas</strong></label>
                </div>

                <button class="btn btn-success btn-sm btnGuardar">Guardar</button>
                <!-- <button class="btn btn-success  btn-sm btnListar" name="btnListar">Listar</button>  originalmente-->
                
              </div>

              <div class="col-md-2">
                  <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">   
                    <button class="btn btn-success  btn-sm btnListar" name="btnListar">Listar</button>
                  </form>
              </div>
              

              <div class="col-md-4"> 
                
                <!-- <button class="btn btn-primary  btnImprimir" style="float: right;"><i class='fas fa-print'></i></button> -->
                <?php
                  echo "<a href='factura/listado_lotes_pendientes.php' 
                          type='submit'  target='_blank' 
                          style='float: right;' 
                          class='btn btn-primary' title='Imprimir'>
                          <i class='fas fa-print'></i>
                          </a>";
                ?>
              </div>

              <br><br>
              <div class="col-md-12 txtOk" style="display: none;"></div>
            </div>

            <hr>
            <!-- fin seccion lotes pendientes -->

            <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">               

               <strong>Loteo</strong>
                <select name="loteo" id="select-loteo">
                   <option value="">Todos</option>
                   <?php  
                      include("conexion.php");
                      $consulta = "SELECT DISTINCT * FROM loteos";
                      $resultado = mysqli_query($connection , $consulta);

                      while($misdatos = mysqli_fetch_assoc($resultado))
                      { 
                        echo "<option value='".$misdatos['nombre']."' id='".$misdatos['id']."'>".$misdatos['nombre']."</option>"; 
                      }
                    ?>
               </select> 

               <strong>Servicios</strong>
                <select name="servicio" id="servicio-lote">
                   <option value="">Todos</option>
                   <option value="agua">Agua</option>
                   <option value="agrimensor">Agrimensor</option>
                   <option value="cloacas">Cloacas</option>
               </select> 

               <strong>Estado</strong>
                   <select name="estado" id="estado-lote">
                   <option value="">Todos</option>
                   <option value="Pendiente">Pendiente</option>
                   <option value="Solicitado">Solicitado</option>
                   <option value="Realizado">Realizado</option>
               </select>
               
               <button id="btn-lotes" name="btn-lotes" class="btn btn-success btn-sm">Listar</button>
                
                <?php

                    $serv="";
                    $loteo ="";
                    $est ="Solicitado";

                    if(isset($_POST['servicio']))
                    {
                        $serv = $_POST['servicio'];
                    }

                    if(isset($_POST['loteo']))
                    {
                        $loteo = $_POST['loteo'];
                    }

                    if(isset($_POST['estado']))
                    {
                        $est = $_POST['estado'];
                    }

                    echo "<a href='factura/listado_lotes.php?loteo=$loteo&serv=$serv&est=$est' 
                          type='submit' name='listado-lotes' 
                          id='listado-lotes'  target='_blank' 
                          style='float: right; display: none;' 
                          class='btn btn-primary' title='Imprimir'>
                          <i class='fas fa-print'></i>
                          </a>";

                    echo "</br></br><hr>";
                ?>
                                                             
            </form>
            <?php
                    $cabecera = "<table class='table table-striped table-hover tabla_conexiones'>
                    <thead>  
                    <tr>                   
                    <td><strong>Loteo</strong></td>  
                    <td><strong>Lote</strong></td>
                    <td><strong>Servicio</strong></td>                                  
                    <td><strong>Solicitado</strong></td>
                    <td><strong>Realizado</strong></td>
                    <td><strong>Estado</strong></td>
                    <td><strong>Acción</strong></td>
                    </tr>
                    </thead>
                    <tbody id='tbody-datos'>";

                    include('conexion.php');
                    include('funciones.php');  


                    // filtro 

                    if(isset($_POST['btn-lotes']))
                    {
                      $tabla = $cabecera;
                      // caso 1
                      if($_POST['loteo']=="" && $_POST['servicio']=="" && $_POST['estado']=="")
                      {
                        $qry = "SELECT * FROM det_servicio
                                WHERE servicio <> 'Red Cloacas'
                                ORDER BY fecha_solicitud, lote";
                      }
                      else
                      {
                        // caso 2
                        if($_POST['loteo']!="" && $_POST['servicio']!="" && $_POST['estado']!="")
                        {
                          $loteo    = $_POST['loteo'];
                          $servicio = $_POST['servicio'];
                          $estado   = $_POST['estado'];

                          $qry = "SELECT * FROM det_servicio
                                  WHERE loteo = '$loteo'
                                  AND servicio = '$servicio'
                                  AND estado = '$estado'
                                  AND servicio <> 'Red Cloacas'
                                  ORDER BY fecha_solicitud, lote";
                        }
                        else
                        {
                          // caso 3
                          if($_POST['loteo']!="" && $_POST['servicio']=="" && $_POST['estado']=="")
                          {
                            $loteo    = $_POST['loteo'];

                            $qry = "SELECT * FROM det_servicio
                                    WHERE loteo = '$loteo'
                                    AND servicio <> 'Red Cloacas'
                                    ORDER BY fecha_solicitud, lote ";
                          }
                          else
                          {
                            // caso 4
                            if($_POST['loteo']=="" && $_POST['servicio']!="" && $_POST['estado']=="")
                            {
                              $servicio = $_POST['servicio'];

                              $qry = "SELECT * FROM det_servicio
                                      WHERE servicio = '$servicio'
                                      AND servicio <> 'Red Cloacas'
                                      ORDER BY fecha_solicitud, lote";
                            }
                            else
                            {
                              // caso 5
                              if($_POST['loteo']=="" && $_POST['servicio']=="" && $_POST['estado']!="")
                              {
                                $estado   = $_POST['estado'];

                                $qry = "SELECT * FROM det_servicio
                                        WHERE estado = '$estado'
                                        AND servicio <> 'Red Cloacas'
                                        ORDER BY fecha_solicitud, lote";
                              }
                              else
                              {
                                // caso 6
                                if($_POST['loteo']=="" && $_POST['servicio']!="" && $_POST['estado']!="")
                                {
                                  $servicio   = $_POST['servicio'];
                                  $estado   = $_POST['estado'];

                                  $qry = "SELECT * FROM det_servicio
                                          WHERE servicio = '$servicio'
                                          AND estado = '$estado'
                                          AND servicio <> 'Red Cloacas'
                                          ORDER BY fecha_solicitud, lote";
                                }
                                else
                                {
                                  //caso 7
                                  if($_POST['loteo']!="" && $_POST['servicio']!="" && $_POST['estado']=="")
                                  {
                                    $loteo   = $_POST['loteo'];
                                    $servicio   = $_POST['servicio'];

                                    $qry = "SELECT * FROM det_servicio
                                            WHERE loteo = '$loteo'
                                            AND servicio = '$servicio'
                                            AND servicio <> 'Red Cloacas'
                                            ORDER BY fecha_solicitud, lote";
                                  }
                                  else{
                                    //caso 8
                                    if($_POST['loteo']!="" && $_POST['servicio']=="" && $_POST['estado']!="")
                                    {
                                      $loteo   = $_POST['loteo'];
                                      $estado   = $_POST['estado'];

                                      $qry = "SELECT * FROM det_servicio
                                              WHERE loteo = '$loteo'
                                              AND estado = '$estado'
                                              AND servicio <> 'Red Cloacas'
                                              ORDER BY fecha_solicitud, lote";
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                      

                      $res = mysqli_query($connection, $qry);
                         
                      if($res->num_rows > 0)
                      {
                        while($datos = mysqli_fetch_array($res))
                        {
                          $id_fr = ($datos['id']+1);  
                          $tabla.="<tr id='".$datos['id']."'>
                          <td>".$datos['loteo']."</td>
                          <td>".$datos['lote']."</td>
                          <td>".$datos['servicio']."</td>
                          <td>".fecha_min($datos['fecha_solicitud'])."</td>
                          <td fr_id='".$datos['id']."'>".fecha_min($datos['fecha_realizado'])."</td>";

                          if($datos['estado'] == 'Pendiente')
                          {
                            $tabla.="<td style='background: #F18C24;'>".$datos['estado']."</td>
                            </tr>";  
                          }
                          else
                          {
                            // Solicitado
                            if($datos['estado'] == 'Solicitado')
                            {
                              $tabla.="<td style='background: #E3D822;' id='".$datos['id']."'>".$datos['estado']."</td>
                              <td><a href='#' class='link-realizar' id='".$datos['id']."'><strong>Realizar</strong></a></td>
                              </tr>";  
                            }
                            else 
                            {
                              // Pendiente (en caso de que el campo este vacio)
                              if($datos['estado'] == "")
                              {
                                $tabla.="<td style='background: #F18C24;'></td>
                                </tr>";
                              }
                              else
                              {
                                // Realizado
                                $tabla.="<td style='background: #91EC7F;'>".$datos['estado']."</td>
                                </tr>";
                              }                                         
                            }
                          }
                          
                        }
                         
                        $tabla.="</tbody></table>";

                        echo"<script>
                            $('#listado-lotes').show();
                            </script>";
                        // Agregado:
                        echo "<script>
                            if( $('.tabla_pendientes').is(':visible') )
                              $('.tabla_pendientes').hide();
                            </script>";
                      
                        echo $tabla;
                        
                      }
                      else
                      {
                        echo "<strong>No se encontraron lotes.</strong>";
                      }
                        
                      
                      
                    }
                    else // Listado de solicitados
                    {
                      
                      $qry = "SELECT * FROM det_servicio
                            WHERE estado = 'Solicitado'
                            AND servicio <> 'Red Cloacas'
                            ORDER BY fecha_solicitud, lote";
                      
                      $res = mysqli_query($connection, $qry);

                      if($res->num_rows > 0)
                      {

                        $tabla=$cabecera;
                
                        while($datos = mysqli_fetch_array($res))
                        {
                          $id_fr = ($datos['id']+1);
                          $tabla.="<tr id='".$datos['id']."'>
                          <td>".$datos['loteo']."</td>                      
                          <td>".$datos['lote']."</td>
                          <td>".$datos['servicio']."</td>               
                          <td>".fecha_min($datos['fecha_solicitud'])."</td>
                          <td fr_id='".$datos['id']."'>".fecha_min($datos['fecha_realizado'])."</td>
                          <td style='background: #E3D822;' id='".$datos['id']."'>".$datos['estado']."</td>
                          <td><a href='#' class='link-realizar' id='".$datos['id']."'><strong>Realizar</strong></a></td>
                          </tr>";
                          //<i class='fas fa-check-circle'></i> 
                        }
                        $tabla.="</tbody></table>";

                        echo"<script>
                            $('#listado-lotes').show();
                            </script>";
                        //agregado:
                        echo "<script>
                            if( $('.tabla_pendientes').is(':visible') )
                              $('.tabla_pendientes').hide();
                            </script>";
                        echo $tabla;
                      }
                      else{
                        echo "<strong>No hay lotes solicitados.</strong>";
                      }
                    }

                    // listar lotes sin terminar.
                    if(isset($_POST['btnListar']))
                    {
                      $tabla_pendientes = "<table class='table table-striped table-hover tablapendientes'>
                      <thead>  
                      <tr>                   
                      <td><strong>#</strong></td>  
                      <td><strong>Loteo</strong></td>
                      <td><strong>Lote</strong></td>                                  
                      <td><strong>Servicio</strong></td>
                      <td><strong>Estado</strong></td>                     
                      <td><strong>Acción</strong></td>
                      </tr>
                      </thead>
                      <tbody id='tbody-datos'>";

                      $consulta = "SELECT * FROM lotes_pendientes WHERE estado = 'P' ORDER BY 3";
                      $respuesta = mysqli_query($connection, $consulta);
                      while($datos_consulta = mysqli_fetch_array($respuesta))
                        {
                          
                          if($datos_consulta['estado'] == 'P')
                          {
                            $boton = "<button class='btn btn-success btn-sm btnFinalizar' id='".$datos_consulta['id_lote']."'>Finalizar <i class='fa fa-check-circle'></i></button>";

                            $estado = "<strong style='color: red;'>Sin Terminar</strong>";
                          }
                          else
                          {
                            $boton = "Realizado";
                            $estado = "Finalizado";
                          }

                       
                          $tabla_pendientes.="<tr id='".$datos_consulta['id_lote']."'>
                          <td>".$datos_consulta['id_lote']."</td> 
                          <td>".$datos_consulta['loteo']."</td>                      
                          <td>".$datos_consulta['lote']."</td>
                          <td>".$datos_consulta['servicio']."</td>               
                          <td id_estado_pendiente='".$datos_consulta['id_lote']."'>".$estado."</td>                     
                          <td>".$boton."</td>
                          </tr>";
                          //<i class='fas fa-check-circle'></i> 
                        }
                        $tabla_pendientes.="</tbody></table>";

                      echo "<script>$('.tabla_conexiones').hide();</script>";
                      echo $tabla_pendientes;
                    }

                ?> 
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