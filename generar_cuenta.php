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
    var cuenta = "";
    var detalle_od = "";
    var importe_od = parseInt(0);
    var receptor_od = "";
    var empresa = "";
    var obra = "";
    var id_empresa = parseInt(0);
    var datasent = false;
    var tope = parseInt(0);
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
            }
            /*else
              var tope = parseInt(2000);*/
            if(parseInt(importe_od) <= parseInt(tope))
            {
                if(!datasent)
                {
                  datasent = true;
                }

                $.post('orden_pago.php', {'empresa': empresa,'obra': obra,'cuenta':cuenta, 'importe': importe_od, 'detalle': detalle_od, 'recibe':receptor_od}, (resp) =>{
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
            else{
                var info = "<strong>No puede generar una órden de más de $"+new Intl.NumberFormat("de-DE").format(tope)+"</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
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
      <h2>Nueva cuenta corriente</h2>
      <hr>
      <div class="form-group col-md-12">     
        <div class="alert alert-success" role="alert"> 
 
            
              
             
                         
        </div> <!--div alert--> 
      </div>       
     
    </div>  <!--div container-->                
  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>