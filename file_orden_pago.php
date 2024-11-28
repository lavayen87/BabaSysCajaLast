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
<!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->
<!-- Bootstrap core CSS -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<link rel="stylesheet" href="chosen_v1.8.5/chosen.css">
<script src="chosen_v1.8.5/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single').select2();

    //ORDEN DE PAGO
    var moneda = parseInt(1);
    var cuenta = "";
    var detalle_od = "";
    var importe_od = parseInt(0);
    var receptor_od = "";
    var empresa = "";
    var obra = "";
    var id_empresa = parseInt(0);
    var datasent = false;
    var tope = parseInt(0);

    $("#select-moneda-op").on('change', function(){
        moneda = parseInt($("#select-moneda-op").val());
        
        console.log("moneda: "+moneda);
    })
    $("#select-empresa").on('change', function(){
        empresa = $("#select-empresa").val();
        id_empresa = $("#select-empresa optionselected").attr('id');
        console.log(empresa)
    })

    $("#select-obra").on('change', function(){
        obra = $("#select-obra").val();
        console.log(obra)
    })

    $('#select-cuenta').on('change', function(){
        cuenta = $('#select-cuenta').val();
        console.log(cuenta)
    })

    $('#receptor-op').on('change', function(){
        receptor_od = $('#receptor-op').val();
        console.log('Receptor-od: '+receptor_od)
    })

    $("#importe-op").keyup(function(){
        importe_od = $('#importe-op').val();
        console.log('el importe es: '+importe_od)
    });

    $("#detalle-op").keyup(function(){
     detalle_od = $('#detalle-op').val();
     console.log(detalle_od)
    });        
     
    $('#aceptar-op').on('click', function(){                    
        if( (importe_od > parseInt(0))  && (importe_od !="") && (detalle_od !="") && (cuenta !="") 
            && (empresa !="") && (obra !="") && (receptor_od !=""))
        {
            if(parseInt($('#tope').val()) > parseInt(0))
            {
              tope = $('#tope').val();
              console.log('tope: '+tope)
              if(parseInt(importe_od) <= parseInt(tope))
              {
                  if(!datasent)
                  {
                    datasent = true;
                  }

                  $.post('orden_pago.php', {'moneda':moneda,'empresa': empresa,'obra': obra,'cuenta':cuenta, 'importe': importe_od, 'detalle': detalle_od, 'recibe':receptor_od}, (resp) =>{
                      console.log('Respuesta del servidor: '+resp)
                      if(resp == parseInt(1))
                      { 
                          //EFECTO LOADING   
                          //Añadimos la imagen de carga en el contenedor
                          $('#content').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                          $.ajax({
                              type: "POST",
                              url: "sleep.php",
                              success: function(data) {
                                  //Cargamos finalmente el contenido deseado
                                  $("#select-empresa").val("");
                                  $("#select-obra").val("");
                                  $('#select-cuenta').empty().trigger("change");
                                  $('#receptor-op').val("");
                                  $("#importe-op").val("");
                                  $("#detalle-op").val("");
                                  empresa    ="";
                                  obra       ="";
                                  cuenta      ="";
                                  receptor_od=""; 
                                  importe_od = "";
                                  detalle_od = ""; 
                                  datasent = false;
                                  $('#content').fadeIn(1000).html(data);
                                  $('#exito-op').fadeIn('slow'); 
                                  $('#aceptar-op').fadeOut();  
                              }
                          });
                          return false;
                                          
                      }
                      else
                      { 
                          /*$("#select-empresa").val("");
                          $("#select-obra").val("");
                          $('#select-cuenta').val("");
                          $('#receptor-op').val("");
                          $("#importe-op").val("");
                          $("#detalle-op").val("");

                          empresa    ="";
                          obra       ="";
                          cuenta      ="";
                          receptor_od=""; 
                          importe_od = "";
                          detalle_od = ""; */
                          
                          var info = "<strong>Ya generó una orden con estos datos.</strong>";
                          $('#modal-info').html(info);
                          $('#miModal').slideDown();

                          $('.close-modal').on('click', function(){
                              $('#miModal').slideUp();
                          })                  
                      }
                  });
              }
              else
              {
                var info = "<strong>No puede generar una órden de más de $"+new Intl.NumberFormat("de-DE").format(tope)+"</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
              }
            }
            else
            { 
              if(!datasent)
              {
                datasent = true;
              }

              $.post('orden_pago.php', {'moneda':moneda,'empresa': empresa,'obra': obra,'cuenta':cuenta, 'importe': importe_od, 'detalle': detalle_od, 'recibe':receptor_od}, (resp) =>{
                  console.log('Respuesta del servidor: '+resp)
                  if(resp == parseInt(1))
                  { 
                      //EFECTO LOADING   
                      //Añadimos la imagen de carga en el contenedor
                      $('#content').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                      $.ajax({
                          type: "POST",
                          url: "sleep.php",
                          success: function(data) {
                              //Cargamos finalmente el contenido deseado
                              $("#select-empresa").val("");
                              $("#select-obra").val("");
                              $('#select-cuenta').empty().trigger("change");
                              $('#receptor-op').val("");
                              $("#importe-op").val("");
                              $("#detalle-op").val("");
                              empresa    ="";
                              obra       ="";
                              cuenta      ="";
                              receptor_od=""; 
                              importe_od = "";
                              detalle_od = ""; 
                              datasent = false;
                              $('#content').fadeIn(1000).html(data);
                              $('#exito-op').fadeIn('slow'); 
                              $('#aceptar-op').fadeOut();  
                          }
                      });
                      return false;
                                      
                  }
                  else
                  { 
                      /*$("#select-empresa").val("");
                      $("#select-obra").val("");
                      $('#select-cuenta').val("");
                      $('#receptor-op').val("");
                      $("#importe-op").val("");
                      $("#detalle-op").val("");

                      empresa    ="";
                      obra       ="";
                      cuenta      ="";
                      receptor_od=""; 
                      importe_od = "";
                      detalle_od = ""; */
                      
                      var info = "<strong>Ya generó una orden con estos datos.</strong>";
                      $('#modal-info').html(info);
                      $('#miModal').slideDown();

                      $('.close-modal').on('click', function(){
                          $('#miModal').slideUp();
                      })                  
                  }
              });
            }   
       
        }
        else{
            //alert('Debe llenar todos los campos.');
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            })
        }  
          
    })     
 
    //Cancelar orden de pago
    $('#cancelar-op').on('click', function(){
        $("#select-empresa").val("");
        $("#select-obra").val("");
        $('#select-cuenta').val();
        $('#receptor-op').val("");
        $("#importe-op").val("");
        $("#detalle-op").val("");
        empresa    ="";
        obra       ="";
        cuenta      ="";
        receptor_od=""; 
        importe_od = "";
        detalle_od = ""; 
       // window.location = "file_orden_pago.php";      
    }) 

    // cerrar ventana exito orden de pago
    $('#cerrar-content-op').on('click',function(){
        $('#content').html("");
        $('#exito-op').fadeOut('fast');
        $("#select-empresa option[value='']").attr("selected",true);
        $("#select-obra option[value='']").attr("selected",true);
        $("#select-cuenta option[value='']").attr("selected",true);
        $("#importe-op").val("");
        $("#detalle-op").val("");
        empresa = "";
        obra = "";
        cuenta = "";
        importe_od = "";
        detalle_od = ""; 
        window.location = "file_orden_pago.php";   
    })

    // Nueva orden de pago
    $('#nueva-op').on('click',function(){
        window.location = "file_orden_pago.php";
    })

    $('#show-op-pdf').on('click', function(){
        $('#content').slideUp();                 
        $('#exito-op').slideUp();
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
  .opciones-op ul li{
    list-style: none;
    padding: 5px;
  }

  .opciones-op ul li a{
    text-decoration: none;
    color: white;
    font-family: verdana;
    margin-left: 5px;
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

    <!-- Modal -->
    <div id="miModal" class="modal">
        <div class="modal-contenido">
            
            
            <p id="modal-info"></p>
            <div style="width:100%; height: 35px; margin: 0 auto; text-align: center; ">
                <button class="btn btn-success close-modal">Aceptar</button>
            </div>
        </div>  
    </div>

    <div class="container">
      <h2>Órden de pago</h2>
      <hr>
      <div class="row">  
        <div class="form-group col-md-12">     
          <div class="alert alert-success" role="alert"> 
 
            <!--menu horizontal-->
            <nav class="navbar navbar-expand-lg navbar-light " style="background-color: #22A49D; border-radius: 6px;">
              <div class="container-fluid">                    
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                  <span class="navbar-toggler-icon"></span>
                </button>        
                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav">
                      <?php
                        if($numero_caja == 0 )
                        {
                          echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>
                                <a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                        }
                        if(tiene_permiso($numero_caja,3))
                        {
                          echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>";
                        }         
                        if(tiene_permiso($numero_caja,4))
                        {
                          echo "<a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                        }
                      ?>                    
                         
                      <div class='nav-item dropdown'>
                        <a href='#' class='nav-link dropdown-toggle' data-bs-toggle='dropdown' style='color: white;'>
                          Solicitud
                        </a>
                        <div class='dropdown-menu'>
                          <?php
                            if($numero_caja == 0)
                            {  
                              echo "<a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>
                                    <a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>
                                    <a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>
                                    <a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                            }
                            if(tiene_permiso($numero_caja,5))
                              echo "<a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>";
                            if(tiene_permiso($numero_caja,6))
                              echo "<a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>";
                            if(tiene_permiso($numero_caja,7))
                              echo "<a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>";
                            if(tiene_permiso($numero_caja,8))
                              echo "<a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                          ?>
                            
                        </div>
                      </div>
                          
                      <?php
                        if($numero_caja == 0)
                          echo "<a href='file_solicitud_transferencia.php' class='nav-item nav-link' style='color: white;'>Solicitud de fondos</a>";
                        if(tiene_permiso($numero_caja,42))
                          echo "<a href='file_solicitud_transferencia.php' class='nav-item nav-link' style='color: white;'>Solicitud de fondos</a>";
                        if($numero_caja == 0 || $numero_caja == 12)
                        {
                          echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>
                                <a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                        }
                        if(tiene_permiso($numero_caja,9)) 
                        {
                          echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>";
                        }
                                  
                        if(tiene_permiso($numero_caja,10))
                        {
                          echo "<a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                        }
                      ?>
                     
                    </div>
                    <?php 
                      echo "<div class='navbar-nav ms-auto'>
                                <div  class='nav-item nav-link info_opcion_opt'></div>
                            </div>";
                    ?>
                </div>
              </div>
            </nav>
            <!--fin menu horizontal-->
              
              <div style="width: 100%; padding-top: 4mm;" id="formulario-orden-pago">
                <?php
                  include('conexion.php');
                  $tope = 0;
                  $qry = "SELECT tope FROM tope_op 
                          WHERE numero_caja = '$numero_caja'";
                  $res = mysqli_query($connection, $qry);
                  if($res->num_rows > 0){
                    $dato = mysqli_fetch_array($res);
                    $tope = $dato['tope'];
                  }
                  mysqli_close($connection);
                ?> 
                <input type="number" id="tope" value="<?php echo $tope;?>" style="width: 40%; display: none;">

                <div class="form-group col-md-6 form-orden-pago" style="width: 40%; display: none;"> 
                      
                      <strong>Moneda</strong>
                      <div class="row-fluid">
                        <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" id="select-moneda-op">
                          <option value="1">Pesos($)</option>
                          <option value="2">Dolares($US)</option>
                          <option value="3">Euros($USDE)</option>                   
                        </select>
                      </div>
                      <strong>Empresa</strong>
                      <div class="row-fluid">
                          <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" id="select-empresa">
                            <option value=""><?php echo ""; ?></option> 
                            <?php  
                            include("conexion.php");
                            $consulta = "SELECT DISTINCT * FROM empresas";
                            $resultado = mysqli_query($connection , $consulta);

                            while($misdatos = mysqli_fetch_assoc($resultado))
                            { 
                              echo "<option value='".$misdatos['nombre_empresa']."' id='".$misdatos['id_empresa']."'>".$misdatos['nombre_empresa']."</option>"; 
                            }
                            ?>
                          </select>
                      </div>
                      
                      <strong>Obra</strong>
                      <div class="row-fluid">
                          <select style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-obra"> 
                            <option value=""><?php echo ""; ?></option>
                            <?php  
                            include("conexion.php");
                            $consulta = "SELECT DISTINCT * FROM obras";
                            $resultado = mysqli_query($connection , $consulta);

                            while($misdatos = mysqli_fetch_assoc($resultado))
                            { 
                              echo "<option value='".$misdatos['nombre_obra']."' id='".$misdatos['id_obra']."'>".$misdatos['nombre_obra']."</option>"; 
                            }
                            ?>
                          </select>
                      </div>
                      
                      <strong>Cuenta Contable</strong>
                                    
                      <div class="row-fluid">
                        <select style="width: 100%;" class="js-example-basic-single form-control" id="select-cuenta">
                          <option value=""><?php echo ""; ?></option> 
                          <!-- js-example-basic-multiple name="states[]" multiple="multiple" para select multiple-->
                          <?php
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM cuentas ORDER BY descripcion";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                            echo "<option value='".$misdatos['descripcion']."'>".$misdatos['descripcion']."</option>"; 
                          }

                          ?>          
                        </select>
                      </div>
                       
                      <strong>Recibe</strong>
                      <input id="receptor-op" type="text" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" maxlength="30">                    

                      <strong>Importe</strong>
                      <input id="importe-op" type="number" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                      <br>
                      <strong>Detalle</strong>
                      <input id="detalle-op" type="text" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" maxlength="30">                    
                    
                      <br><br>
                    
                      <!--seccion de botones Aceptar  - cancelar  - nueva orden de pago-->
                        <button id="aceptar-op" class="btn btn-primary" style="display: inline-block;">Aceptar</button>
                        <button id="cancelar-op" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>                            
                        <button id="nueva-op" class="btn btn-success" style="float: right;"><i class="fas fa-plus-circle"></i> Nueva</button>
                   
                        
                 <?php
                     if($numero_caja == 0 || $numero_caja == 12)
                     {
                        echo "<script>$('.form-orden-pago').show();</script>";
                     }

                     if(tiene_permiso($numero_caja,3))
                     {
                        echo "<script>$('.form-orden-pago').show();</script>";
                     }
                 ?>   


                  </div> <!--div formulario-->
                   

                  <div class="form-group col-md-6 opciones-op" style="width: 40%; float: right;"> 
                      <!--ul>
                          
                        <li>
                          <?php
                            if($numero_caja <> 22 && $numero_caja <> 1)
                            {
                              echo "<a href='file_orden_pago.php' class='btn btn-success'>Órden de pago</a>";
                            }
                          ?>
                        </li>
                        <li>
                          <?php
                            if($numero_caja <> 22 && $numero_caja <> 1)
                            {
                              echo "<a href='file_orden_pago_cheque.php' class='btn btn-success'>Órden de pago con cheque</a>";
                            }
                          ?>
                          
                        </li>
                        <li>
                          <?php
                            if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                            {
                              echo "<a href='file_solicitud_op.php' class='btn btn-success'>Solicitud de orden de pago</a>";
                            }
                          ?>
                          
                        </li>
                        <li>
                          <?php
                            if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                            {
                              echo "<a href='file_autorizar_op.php' class='btn btn-success'>Autorizar Solictud</a>";
                            }
                          ?>
                          
                        </li>
                        <li>
                          <?php
                            if($numero_caja == 1 || $numero_caja == 3 || $numero_caja == 9 || $numero_caja == 12)
                            {
                              echo "<a href='file_emitir_orden.php' class='btn btn-success'>Emitir órden de pago</a>";
                            }
                          ?>
                          
                        </li>
                      </ul-->
                  </div> <!--div opciones-->

                          
              </div> <!--div contenedor del formulario y opciones de operacion-->
              <?php
                if($numero_caja == 2 || $numero_caja == 11 || $numero_caja == 13) //11 caja buen clima, 13 autorizante
                {
                  echo "<script>
                        $('#formulario-orden-pago').hide();
                        </script>";
                }
              ?>
              <br>
              <div class="form-group col-md-6">
                     
                    <div id="content" class="col-lg-12"></div>
                        
                    <div class="" role="alert" id="exito-op" style="display: none;">                 
                      <!--strong>Órden de pago realizada con exito !</strong-->                
                      <div class="button-close">
                        <a href="factura/orden_pago_pdf.php" id="show-op-pdf" class="btn btn-primary" target="_blank">
                            Imprimir 
                            <i class="fas fa-print"></i>
                        </a>
                        <button id="cerrar-content-op" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                      </div>
                    </div>                             
              </div> <!--div resultado-->   
                         
          </div> <!--div alert--> 
        </div>       
      </div> <!--div row-->
    </div>  <!--div container-->                
  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>