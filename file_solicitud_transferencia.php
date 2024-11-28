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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main-style.js"></script>
<script>

    var op = parseInt(1);
    var caja_pago = parseInt(0);
    var caja_destino = "";
    var solicita = "";
    var cantidad = parseInt(0);
    var detalle = "";
    var numero_caja_solicitante = parseInt(0); 
    var datasend_tr = false; 
    var lista_num_caja ={
        'Admin-Luis':0,
        'Cajero1':1,
        'Admin2':2, 
        'Banco':3, 
        'Tesoro':4,
        'Luis B.':5,
        'Sergio B.':6,
        'Daniel B.':7,
        'Ariel M.':8,
        'Admin1':9,
        'Legales':10,
        'AdminBC':11,
        'Administrador':12
    };
    $(document).ready(function(){

        solicita = $('#nom-solicitante').val();
        console.log('solicita: '+solicita)
        numero_caja_solicitante = parseInt($('#nom-solicitante').attr('caja'));//lista_num_caja[solicita]; version anterior
        console.log('numero de caja destino: '+numero_caja_solicitante)

        console.log('moneda : pesos')
        $('#select_moneda_sf').on('change', function() {
            op = parseInt($(this).val());
            switch(op){
              case 1: console.log('moneda : pesos');
              break;
              case 2: console.log('moneda : dolares');
              break;
              case 3: console.log('moneda : euros');
              break;
            }
            console.log('moneda : '+op);
            
        })

        $('#select-caja').on('change', function() {
            caja_pago = $(this).val();
            console.log('caja : '+caja_pago);
            
        })

        $('#cantidad-transfer').on('change', function() {
            cantidad = $('#cantidad-transfer').val(); 
            console.log(cantidad)
        })
        $('#detalle-transfer').on('change', function() {
             detalle = $('#detalle-transfer').val();
             console.log(detalle)
        })

        $('#aceptar-solic_tr').on('click', function(){ 
            if( (caja_pago != parseInt(0)) && (cantidad > parseInt(0)) && (cantidad !="") && (detalle!="")) 
            {
                if($('#exito-tr').is(':visible'))
                {
                    $('#exito-tr').hide();
                    $('#content-transfer').show();
                }

                if(!datasend_tr)
                {
                    datasend_tr = true;
                }

                $.post('solicitud_transferencia.php',{'op': op,'caja_pago':caja_pago,'recibe':solicita, 'numero_caja_solicitante':numero_caja_solicitante, 'cantidad':cantidad, 'detalle':detalle}, (resp)=>{                 
                    console.log(resp)
                    if(resp = 'ok')
                    {
                        caja_destino ='';
                        numero_caja_destino ='';
                        cantidad = parseInt(0);
                        detalle ='';
                      
                        $('#content-transfer').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                        $.ajax({
                            type: "GET",
                            url: "sleep.php",
                            success: function(data) {
                                //Cargamos finalmente el contenido deseado
                                $('#content-transfer').fadeIn(1000).html(data);
                                $('#exito-tr').slideDown();

                                $('#select-caja').val("");
                                $('#detalle-transfer').val("");
                                $('#cantidad-transfer').val(""); 
                               
                                caja_pago = parseInt(0);
                                solicita ='';
                                numero_caja_solicitante = parseInt(0);
                                cantidad = parseInt(0);
                                detalle ='';
                                datasend_tr = false;
                            }
                        });
                        return false;
                       
                    }
                    else 
                    {   
                        $('#select-caja').val("")
                        $('#detalle-transfer').val("");
                        $('#cantidad-transfer').val(""); 
                        caja_pago = parseInt(0);
                        solicita ='';
                        numero_caja_solicitante = parseInt(0);
                        cantidad = parseInt(0);
                        detalle ='';
                        console.log(resp);
                        //alert(resp);
                        var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                        $('#modal-info').html(info);
                        $('#miModal').slideDown();

                        $('.close-modal').on('click', function(){
                            $('#miModal').slideUp();
                        })
                    }
                });
                        
                $('#cerrar-tr-pdf').on('click', function(){
                    $('#content-transfer').hide();
                    $('#exito-tr').slideUp();

                })
            }
            else
            {
                
                var info = "<strong>Debe llenar todos los campos !</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
                
            } 
        })
        
        $('#cancelar-solic_tr').on('click', function(){
          $('#select-caja').val("");
          $('#detalle-transfer').val("");
          $('#cantidad-transfer').val(""); 
          
          caja_pago = parseInt(0);
          solicita ='';
          numero_caja_solicitante = parseInt(0);
          cantidad = parseInt(0);
          detalle ='';
        })

        $('#nueva-solic_tr').on('click', function(){
          location.reload();
        })

        $('#close-sesion').on('click',function(){
        $.get('close-sesion.php', (resp)=>{
            console.log(resp)
            if(resp == 'ok')
                window.location = 'index.php'; 
        })
        
    });
    })
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
  input[name=check_transfer]{
    width: 20px;
    height: 20px;
  }
  .my-custom-scrollbar {
    position: relative;
    height: 220px;
    overflow: auto;
    margin: 5px;
  }
  
  .table-wrapper-scroll-y {
    display: block;
  }
  .content-table-scroll{
    margin-top:8px;
    margin-bottom:8px;
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
      <h2>Solicitud de fondos</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert" style="padding-left: 6px;">        
            
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
                                    <a href='file_solicitud_transferencia.php' class='dropdown-item item1'>Transferencia</a>
                                    <a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>
                                    <a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                            }
                            if(tiene_permiso($numero_caja,5))
                              echo "<a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>";
                            if(tiene_permiso($numero_caja,6))
                              echo "<a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>";
                            if(tiene_permiso($numero_caja,42))
                              echo "<a href='file_solicitud_transferencia.php' class='dropdown-item item1'>Transferencia</a>";
                            if(tiene_permiso($numero_caja,7))
                              echo "<a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>";
                            if(tiene_permiso($numero_caja,8))
                              echo "<a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                          ?>
                            
                        </div>
                      </div>
                          
                      <?php
                        if($numero_caja == 0)
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
            <br>
            <div style="width:100%; overflow: hidden;">
              <div class="form-group col-md-6" style="float: left; width: 40%; margin-left: 4px; margin-bottom: 4px;">
                    <!--form id="form-transfer"-->
                      <!--p><strong>Seleccione moneda</strong>
                              <select  id='select-moneda' style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                                  <option value=""></option>
                                  <option value="pesos">Pesos ($)</option>
                                  <option value="dolares">Dolares ($USD)</option>
                                  <option value="euros">Euros (€EUR)</option>
                                  <option value="cheques">Cheques ($)</option>
                              </select>
                      </p-->
                      <input type="text" id="nom-solicitante" caja="<?php echo $numero_caja;?>" value="<?php echo $rol;?>" style="display:none;">

                      <p>
                        <strong>Moneda</strong>
                        <select  id="select_moneda_sf" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                          <option value="1">Pesos</option>
                          <option value="2">Dolares($UD)</option>
                          <option value="3">Euros($USDE)</option>
                        </select>
                      </p>

                      <p>
                        <strong>Caja</strong>
                        <select id='select-caja' style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                            <option value=""></option>
                            <!--option value=""></option>
                            <option value="1">Caja 1</option>
                            <option value="4">Tesoro viejo garca</option-->  
                            <?php
                                include('conexion.php');
                                $qry = "SELECT * FROM usuarios u INNER JOIN asignaciones a 
                                                   on u.numero_caja = a.numero_caja 
                                                   where a.block_sf = 1 ";
                                $res = mysqli_query($connection, $qry);

                                if( $res->num_rows > 0 )
                                {
                                    while($datos = mysqli_fetch_array($res))
                                    {
                                        echo " <option value='".$datos['numero_caja']."'>".$datos['rol']."</option>";
                                    }
                                }
                            ?>
                        </select>
                      </p>
                      
                      <p><strong>Cantidad</strong><input type="number" id="cantidad-transfer" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;"></p>
                      <p><strong>Detalle</strong><input type="text" id="detalle-transfer" maxlength="30" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;"></p>
                    <!--/form-->
                    <br>
                    <div class="button-close">
                        <button id="aceptar-solic_tr" class="btn btn-primary">Aceptar</button>
                        <button id="cancelar-solic_tr" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                        <button id="nueva-solic_tr" class="btn btn-success" style="float: right;">Nueva</button>
                      </div>
              </div>     
            </div>

            <br>
            <div id="content-transfer" class="col-lg-12"></div>
            <div  id="exito-tr" style="display: none;">                 
              <strong style="margin-top: 5px;">Solicitud realizada con exito !</strong>          
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