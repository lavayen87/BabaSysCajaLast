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
	
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<link rel="stylesheet" href="css/styles-update.css">

<!--link rel="stylesheet" href="chosen/chosen.css"-->
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<style>
    .validation{
        display: none;
    }
    
</style>
<script type="text/javascript">
$(document).ready(function() {
    
    // Edicion
    $('.edicion').on('click', function(){
        if($(this).val() == '0') // habilitar edicion
        {
            $('.edicion').removeClass('btn-success');
            $('.edicion').addClass('btn-secondary')
            $('.edicion').text('Guardar');
            $(this).val('1');
            $('input').removeAttr("readonly");
            var span= "<span class='input-group-text validation' style='background: #AAF139;'></span>";
            $('.input-group-prepend').html(span);

            
        }
        else{
            $(this).val('0'); // Guardar edicion
            $('.edicion').addClass('btn-success');

            $('input').each(function() {
                if( !$(this).attr("disabled") )
                {
                    var field = $(this);
                    var validationField = field.parent().find('.validation');
                    var dataString = 'id='+field.attr('id')+'&id='+field.attr('id');
                    console.log('data: '+dataString);
                    dataString+= '&value='+field.val()+'&field='+field.attr('name');
                    $.ajax({
                        type: "POST",
                        url: "update_record.php",
                        data: dataString,
                        success: function(data) {
                            field.val(data);
                            validationField.hide().empty();
                            setTimeout(function() {
                                validationField.append('<i class="fa fa-check"></i>');
                                validationField.show();
                            }, 500);
                            
                        }
                    });
                }
            });


            $('.edicion').text('Editar');
            
            $('input').each(function(){
                if( $(this).attr("disabled") )
                    $(this).css('background', '#E7F5F2');
                else
                {
                    $(this).prop('readonly', true);
                    $(this).css('background', 'white');
                }
                    
            })
            //$('input').css('background', 'white');
            $('.input-group-prepend').html("");
            
        }
        
        
    })

    // Accion para editar
	/*$('input').on('blur', function() {
        
        var field = $(this);
        var validationField = field.parent().find('.validation');
        var dataString = 'id_cliente='+field.attr('id_cliente')+'&id='+field.attr('id');
        dataString+= '&value='+field.val()+'&field='+field.attr('name');
        $.ajax({
            type: "POST",
            url: "update_service.php",
            data: dataString,
            success: function(data) {
                field.val(data);
                validationField.hide().empty();

                setTimeout(function() {
                    validationField.append('<i class="fa fa-check"></i>');
                    validationField.show();
                }, 500); 
            }
        });
        
	});*/

});
</script>

</head>
<body>
<div class="page-wrapper chiller-theme toggled">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  <?php include('menu_main.php');?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">
    <div class="container">
      <h2>Ficha del cliente</h2>
      <hr>
       
      <div class="alert alert-success" role="alert"> 
        <div style="width:100%; overflow: hidden;">
            <div style="float: left;">
                <a href='buscar_lotes_new.php' class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Volver</a>
            </div>
            <div style="float: right;">
                <?php 
                    if($numero_caja == 34 || $numero_caja == 2) 
                ?>
                    <button class="btn btn-success edicion" value="0">Editar</button>
            </div>
        </div>
        <br>
        <div class="row">
            <div id="content" class="col-lg-12">
            
                <?php
                if($_GET['lote'] != "")
                {
                    $codigo = $_GET['lote'];
                ?>
                    <?php  
                    $qry = "SELECT * FROM det_lotes2 where lote = '$codigo'";
                    
                    $result = $connection->query($qry);

                    if ($result->num_rows > 0) 
                    {
                        $row = $result->fetch_array();
                    ?>
                        
                        <div class="input-group">
                            <div style='width: 20%;'>Titular</div>
                            
                                <div class="input-group-prepend">
                                    <!--span class="input-group-text validation"></span-->
                                </div>
                                <input type="text" readonly style="display: inline-block; background: white;" class="form-control" name="titular" id="<?=$row['id']?>" value="<?=$row['titular']?>" >
                            
                        </div>
                        
                        <div class="input-group">
                            <div style='width: 20%;'>D.N.I.</div>
                                
                                <div class="input-group-prepend">
                                    <!--span class="input-group-text validation"></span-->
                                </div>
                                <input type="text" readonly style="display: inline-block; background: white;" class="form-control" name="dni" id="<?=$row['id']?>"  value="<?=$row['dni']?>" >
                                
                        </div>

                        <div class="input-group">
                            <div style='width: 20%;'>Domicilio</div>
                                
                                <div class="input-group-prepend">
                                    <!--span class="input-group-text validation"></span-->
                                </div>
                                <input type="text" readonly style="display: inline-block; background: white;" class="form-control" name="domicilio" id="<?=$row['id']?>"  value="<?=$row['domicilio']?>" >
                                
                        </div>

                        <div class="input-group">
                            <div style='width: 20%;'>Teléfono</div>
                                
                                <div class="input-group-prepend">
                                    <!--span class="input-group-text validation"></span-->
                                </div>
                                <input type="text" readonly style="display: inline-block; background: white;" class="form-control" name="telefono" id="<?=$row['id']?>"  value="<?php echo $row['telefono'];?>" >
                                
                        </div>

                        <div class="input-group">
                            <div style='width: 20%;'>Loteo</div>
                                
                                <div class="input-group-prepend">
                                    <!--span class="input-group-text validation"></span-->
                                </div>
                                <input type="text" disabled style="display: inline-block; background: #E7F5F2;" class="form-control" name="loteo" id="<?=$row['id']?>"  value="<?=$row['loteo']?>" >
                                
                        </div>
                        
                        <div class="input-group">
                            <div style='width: 20%;'>Lote</div>
                                
                                <!--div class="input-group-prepend">
                                    <span class="input-group-text validation" ></span>
                                </div-->
                                <input type="text" disabled style="display: inline-block; background: #E7F5F2;" class="form-control" name="lote" id="<?=$row['id']?>" value="<?=$row['lote']?>" maxlength="6">
                            
                        </div>                 

                        <?php

                        // Datos de servicios
                        /*$qry_servicios = "SELECT * FROM det_lotes 
                        where lote = '$codigo'";*/
                        $qry_servicios = "SELECT cl.id, cl.titular, cl.telefono, cl.lote, ds.id, ds.servicio, ds.recibo,
                        ds.fecha_pago, ds.fecha_solicitud, ds.fecha_realizado, ds.fecha_abonado, ds.estado, ds.forma_pago 
                        FROM det_lotes2 as cl INNER JOIN det_servicio as ds
                        on cl.id = ds.id_cliente
                        where cl.lote = '$codigo'";
                        
                        $res_servicios = mysqli_query($connection, $qry_servicios);

                        // Datos de posesion
                        $qry_posesion = "SELECT * FROM det_servicio
                        WHERE (lote = '$codigo') AND (fecha_pago!= '0000-00-00')";

                        $res_posesion = mysqli_query($connection, $qry_posesion);
                        
                        $div = ""; // div de posesion
                        if($res_posesion->num_rows > 0)
                        {
                            $qry_forma_pago = "SELECT forma_pago FROM det_servicio
                            WHERE lote = '$codigo'";

                            $res_forma_pago = mysqli_query($connection, $qry_forma_pago);
                            $datos_forma_pago = mysqli_fetch_array($res_forma_pago);
                            $forma_pago = $datos_forma_pago['forma_pago'];
                           
                            $div.= "<p style='width: 45%; margin-left: 20%; margin-top:5px;'>
                                    <span style='float: left; background: #11F3D2'>Posesión: <strong>SI <i class='fas fa-check-circle'></i></strong></span>
                                    <span style='float: right;background: #11F3D2'>Forma de pago red: <strong>".$forma_pago."</strong></span>
                                    <p>
                                    </br>";
                        }
                        else{
                            $div.= "<p style='margin-left: 20%; margin-top:5px;'>
                                    <span style='background: #11F3D2'>Posesión: <strong>NO</strong></span>
                                    <p>";
                        }

                        echo $div;

                        $div_serv = ""; // datos de los servicios

                        $qry_nom_servicios = "SELECT nombre FROM servicios";
                        $res_nom_servicios = mysqli_query($connection, $qry_nom_servicios);
                        $servicios = [];
                        $i = 0;
                        while($row = mysqli_fetch_assoc($res_nom_servicios)){
                            $servicios[$i] = $row['nombre'];
                            $i++;
                        }
                        
                        $ficha = "";
                        while($serv = mysqli_fetch_array($res_servicios))
                        {
                            $ficha.= "<hr>
                                    <div class='form-group'>
                                    <strong>".$serv['servicio']."</strong>
                                    </br></br>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>Nº de recibo</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='text' readonly style='background: white;' class='form-control' name='recibo' value='".$serv['recibo']."' id='".$serv['id']."' id_cliente='' maxlength='10'>
                                        
                                    </div>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>fecha de pago</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='date' readonly style='background: white;' class='form-control' name='fecha_pago' value='".$serv['fecha_pago']."' id='".$serv['id']."' id_cliente=''>
                                        
                                    </div>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>fecha de solicitud</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='date' readonly style='background: white;' class='form-control' name='fecha_solicitud'  value='".$serv['fecha_solicitud']."' id='".$serv['id']."' id_cliente=''>
                                        
                                    </div>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>fecha de realizado</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='date' readonly style='background: white;' class='form-control' name='fecha_realizado'  value='".$serv['fecha_realizado']."' id='".$serv['id']."' id_cliente=''>
                                        
                                    </div>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>fecha de abonado</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='date' readonly style='background: white;' class='form-control' name='fecha_abonado'  value='".$serv['fecha_abonado']."' id='".$serv['id']."' id_cliente=''>
                                        
                                    </div>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>Estado</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='text' readonly style='background: white;' class='form-control' name='estado'  value='".$serv['estado']."' id='".$serv['id']."' id_cliente='' maxlength='12'>
                                        
                                    </div>";

                                /*</div>*/
                                

                                    if($serv['servicio'] == 'Red Cloacas')
                                    {
                                        $ficha.="<div class='input-group'>

                                                <div style='width: 20%;'>Forma de pago</div>
                                                <div class='input-group-prepend'>
                                                    
                                                </div>
                                                <input type='text' readonly style='background: white;' class='form-control' name='forma_pago'  value='".$serv['forma_pago']."' id='".$serv['id']."' id_cliente='' maxlength='15'>
                                                
                                                </div>"; 

                                    }

                                    
                        }

                        $ficha.="</div>";
                        echo $ficha;

                        ?>
                    
                    
                    <?php
                    }
                    else{
                        echo "<strong>No se encontró la ficha del cliente.</strong>";
                        echo "<script>   
                              $('.edicion').hide();
                              </script>";
                    }
                    ?> 
                <?php      
                }
                else{
                    echo "<script>   
                          window.location = 'buscar_lotes_new.php';
                          </script>";
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