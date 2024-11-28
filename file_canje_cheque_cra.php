
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
<!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->
<!-- Bootstrap core CSS -->
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        
        var datos = []; // array de datos
        var lista = []; // array id de fila
        var contador = parseInt(1);
        var datasent = false;

        // Aguregar una nueva fila
        $('.add_tr').on('click', function(){
            
            if(contador < parseInt(8))
            {
                var cant = parseInt(0);
                $('.tabla_cheques tr:last').find(".valor_cheque").each(function(){
                    if($(this).val() == "")
                    {
                        cant++;
                    }
                });

                if(cant == parseInt(0))
                {
                    // cargo datos en la lista
                    
                    var id_fila = parseInt($(".tabla_cheques tr:last").prop('id'));
                    
                    if(lista.includes(id_fila) == false)
                    {
                        lista.push(id_fila);
                        console.log('fila anterior: '+id_fila) 
                            
                        $('tr[id='+id_fila+']').find(".valor_cheque").each(function() {
                        
                            datos.push($(this).val());   
                        });
                    }


                    // agrego una nueva fila
                    //<td><input type="text" id="n_banco" class="valor_cheque" maxlength="20"></td>
                    
                    /*var select ="<select id='n_banco' class='valor_cheque' style='height: 28px;'>";
                    $("#n_banco option").each(function(){
                        select+=`<option value='${$(this).attr('value')}'>${$(this).attr('value')}</option>`;
                    });
                    select+="</select>";*/
                    var select =`<select id='n_banco' class='valor_cheque' style='height: 28px;'>
                                <option value=''></option>
                                <option value='BBVA'>BBVA</option>
                                <option value='ICBC'>ICBC</option>
                                <option value='HSVC'>HSVC</option>
                                <option value='Galicia'>Galicia</option>
                                <option value='Industrial'>Industrial</option>
                                <option value='Macro'>Macro</option>
                                <option value='Santander'>Santander</option>
                                <option value='Nación'>Nación</option>
                                <option value='Credicoop'>Credicoop</option>
                                <option value='Itaú'>Itaú</option>
                                <option value='Hipotecario'>Hipotecario</option>
                                </select>
                                `;
                    
                    var id = parseInt($('.tabla_cheques tr:last').prop('id'))+parseInt(1);
                    var fila = `<tr id='${id}'>
                                <td><input type="number" id="n_cheque" class="valor_cheque"></td>
                                <td>${select}</td>
                                <td><input type="text" id="n_entrega" class="valor_cheque" maxlength="20"></td>
                                <td><input type="date" id="n_vence" class="valor_cheque" maxlength="20"></td>
                                <td><input type="number" id="n_importe" class="valor_cheque" onKeyUp="Suma()"></td>
                                </tr>`;
                    $('.tabla_cheques').append(fila);
                   
                    contador++;
                    console.log('contador: '+contador);
                    console.log(datos);
                }
                else{
                    //alert('Campos vacios !');
                    var info = "<strong>Debe llenar todos los campos !</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();
                
                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })
                }
            }
            else{
                //alert('No puede cargar mas de 8 cheques.');
                var info = "<strong>No puede cargar mas de 8 cheques.</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();
            
                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
            }
        });

        // Quitar la ultima fila
        $('.delete_tr').on('click', function() {
            
            var fila = parseInt($('.tabla_cheques tr:last').prop('id'));
            console.log('fila eliminada: '+fila)  
            if(fila == parseInt(1))
            {
                //alert('No se puede borrar la primer fila');
                var info = "<strong>No se puede borrar la primer fila.</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();
            

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
            }  
            else{
                /*var cant_items = parseInt(lista.length) - parseInt(1);
                for(i = cant_items; i>=(cant_items-parseInt(4)); i--)
                    lista.splice(i,1);
                console.log(lista);*/
                var total = $('#n_total').val();        
                var ultimo_valor = $('.tabla_cheques tr:last').find("#n_importe").val();
                var nuevo_importe = Number(total) - Number(ultimo_valor);
                $('.tabla_cheques tr:last').closest('tr').remove();
                $('#n_total').val(nuevo_importe);
                contador--;
                console.log('contador: '+contador);
                console.log(datos)
            }
        });

        // Realizar la carga de datos de cheques
        $('.save_tr').on('click', function() {
            // Una sola fila de cheques
            /*var filas = parseInt($(".tabla_cheques tr").length);
            if(filas == parseInt(1)){
                var id_fila = parseInt($(".tabla_cheques tr:last").prop('id'));
                    
                if(lista.includes(id_fila) == false)
                {
                    lista.push(id_fila);
                    console.log('fila anterior: '+id_fila) 
                        
                    $('tr[id='+id_fila+']').find(".valor_cheque").each(function() {
                    
                        datos.push($(this).val());   
                    });
                }
            }*/
            
            if($('#print-canje-cheq').is(':visible')){
                    $('#print-canje-cheq').hide();
                    $('#info-load-cheques').show();
            }
            var cant = parseInt(0);


            $('.tabla_cheques tr:last').find(".valor_cheque").each(function()
            {
                if($(this).val() == "")
                {
                    cant++;
                    
                }
            });

            if(cant == parseInt(0))
            {
                if($('#detalle-cheques').val() != "")
                {
                    var detalle = $('#detalle-cheques').val();
                    var id_fila = parseInt($(".tabla_cheques tr:last").prop('id'));
                        
                    if(lista.includes(id_fila) == false)
                    {
                        lista.push(id_fila);
                        console.log('fila anterior: '+id_fila) 
                                
                        $('tr[id='+id_fila+']').find(".valor_cheque").each(function() {
                            
                            datos.push($(this).val()); 
                            console.log(datos);
                            
                        });
                    }
                        
                    var cantidad = $('#n_total').val();
                    if(!datasent)
                    {
                        datasent = true;
                    }
                    $.post('cargar_cheques.php', {'datos':JSON.stringify(datos),'cantidad':cantidad,'detalle':detalle}, resp =>{
                        console.log('Resultado de la carga: '+resp);
                        /*if(parseInt(resp) == 1)
                        {
                            $('#info-load-cheques').html('<div class="loading"><img src="img/loader.gif"/>Cargando datos...</div>');
                            $.ajax({
                                type: "GET",
                                url: "sleep.php",
                                success: function(data) 
                                {
                                        
                                    var cant_filas = parseInt($('.tabla_cheques tr').length) - parseInt(1);
                                    for(i = cant_filas; i > parseInt(1); i--)
                                    {
                                        $('tr[id='+i+']').closest('tr').remove();       
                                    }
                                    $('.tabla_cheques tr').find(".valor_cheque").each(function(){
                                        $(this).val("");
                                            
                                    });

                                    var print = "<button class='btn btn-primary' id='btn-print-cheq'>Imprimir<i class='fas fa-print'></i></button> ";

                                    print+="<button id='cerrar-print-cheq' class='btn btn-secondary' style='display: inline-block;'>Cancelar</button>";
                                    
                                    $('.button-close').html(print);
                                    var cheques = JSON.stringify(datos);
                                    
                                    datos = [];
                                    lista = [];
                                    datasent = false;
                                    $('#n_total').val("");
                                    $('#detalle-cheques').val("");
                                    
                                    $('#info-load-cheques').fadeIn(1000).html("");
                                    $('#print-canje-cheq').slideDown();

                                    $('#btn-print-cheq').on('click', function(e){
                                        e.preventDefault();
                                        window.open('factura/canje_cheque.php?cheques='+cheques, '_blank');
                                        $('#info-load-cheques').slideUp();
                                        $('#print-canje-cheq').slideUp();
                                    })
                                    $('#cerrar-print-cheq').on('click', function(){
                                        $('#info-load-cheques').slideUp();
                                        $('#print-canje-cheq').slideUp();
                                    })
                                }
                            })
                            return false
                        }
                        else{
                            var info = "<strong><i class='fas fa-exclamation-circle'></i> No hay fondos suficientes.</strong>";
                            $('#modal-info-danger').html(info);
                            $('#miModal-danger').slideDown();
                            $('.close-modal-danger').on('click', function(){
                                $('#miModal-danger').slideUp();
                            })
                            var cant_filas = parseInt($('.tabla_cheques tr').length) - parseInt(1);
                            for(i = cant_filas; i > parseInt(1); i--)
                            {
                                $('tr[id='+i+']').closest('tr').remove();       
                            }

                            $('.tabla_cheques tr').find(".valor_cheque").each(function(){
                                $(this).val("");
                                    
                            });

                            datos = [];
                            lista = [];
                            $('#n_total').val("");
                            $('#detalle-cheques').val("");
                        }*/
                    })
                }
                else {
                    //alert('Debe ingresar el detalle !');
                    var info = "<strong>Complete el detalle del canje !</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();
            

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })
                }
            }
            else{
                //alert('Campos vacios !');
                var info = "<strong>Debe llenar todos los campos !</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();
            

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
                
            }
            $('#cerrar-print-cheq').on('click', function(){
                //$('#info-load-cheques').hide();
                $('#print-canje-cheq').slideUp();
            })

            $('#btn-print-cheq').on('click', function(){
                //$('#info-load-cheques').hide();
                $('#print-canje-cheq').slideUp();
            })
        })

    });

    function Suma()
    {

        var i = parseInt(0);
        var importe = parseInt(0);
        
        var cant_filas = parseInt($(".tabla_cheques tr").length) - parseInt(1);
            
        for(i = parseInt(1); i<=cant_filas; i++)
        {
            $('tr[id='+i+']').find("#n_importe").each(function() {
                var valor = $(this).val();
                importe+=Number(valor);
            });
        }    
            
        $('#n_total').val(importe);
        
    }
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
        .modal-contenido-danger{
        background-color: white;
        border: 4px solid red;
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
        #miModal, #miModal-danger{ /**target */
        opacity:1;
        pointer-events:auto;
        }
        #modal-info, #modal-info-danger strong{
            font-size:17px;
        }
  /** end modal */
  #n_cheque{
      width: 100px;
  }
  #n_banco{
      width: 160px;
  }
  #n_entrega{
      width: 160px;
  }
  #n_vence{
      width: 130px;
  }
  #n_importe{
      width: 112px;
  }
  #n_total{
    float: right;
    width: 115px;
    height: 25px;
    margin-right:5px;
    
  }
  #n_strong_total{
    float: right;
    margin-right: 18px; 
  }
  .valor_cheque{
    height: 28px;
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
    <!-- Modal danger -->
    <div id="miModal-danger" class="modal">
        <div class="modal-contenido-danger">
            
            <p id="modal-info-danger"></p>
            <div style="width:100%; height: 35px; margin: 0 auto; text-align: center; ">
                <button class="btn btn-danger close-modal-danger">Aceptar</button>
            </div>
        </div>  
    </div>

    <div class="container">
      <h2>Canje de cheques</h2>
      <hr>
      <div class="row">  
             
        <div class="alert alert-success" role="alert"> 
            <div style="">
                <table  style="" class="table table-striped tabla_cheques">
                    <thead>
                        <th>#</th>
                        <th>Banco</th>
                        <th>Entregó</th>
                        <th>Vence</th>
                        <th>importe</th>
                    </thead>
                    <tbody>
                        <tr id='1'>
                            <td><input type="number" id="n_cheque" class="valor_cheque"></td>
                            <!--td><input type="text" id="n_banco" class="valor_cheque" ></td-->
                            <td>
                                <select  id="n_banco" class="valor_cheque" style="height: 28px;">
                                    <option value=""></option> 
                                    <?php
                                    include("conexion.php");
                                    $consulta = "SELECT DISTINCT * FROM bancos ORDER BY id_banco";
                                    $resultado = mysqli_query($connection , $consulta);

                                    while($datos = mysqli_fetch_assoc($resultado))
                                    { 
                                    echo "<option value='".$datos['nombre']."'>".$datos['nombre']."</option>"; 
                                    }

                                    ?>          
                                </select>
                            </td>
                            <td><input type="text" id="n_entrega" class="valor_cheque" maxlength="20"></td>
                            <td><input type="date" id="n_vence" class="valor_cheque"</td>
                            <td><input type="number" id="n_importe" class="valor_cheque" onKeyUp="Suma()" ></td>
                        </tr>
                    </tbody>
                </table>
                <button style="" class="btn btn-success add_tr" title="Agregar cheque">agregar</i></button>
                
                <button class="btn btn-secondary delete_tr">Quitar</button>

                
                <input id="n_total" name="resultado" readonly>

                <strong id="n_strong_total">Total </strong>
                <hr>
                
            </div>

            <strong>Detalle</strong>
            <input type="text" id="detalle-cheques" class="form-control" style="width: 40%;" maxlength="30">
            <br>
            <button class="btn btn-primary save_tr">Realizar</button>
            <br>

            <div id="info-load-cheques"></div> 
            <br>
            <div id="print-canje-cheq" style="display: none;">                   
                <strong style='color: green;'> Cheque/s cargado/s con exito!</strong>
                <br>
                <div class="button-close">
                    
                </div>  
            </div>    
        </div> <!--div alert--> 
               
      </div> <!--div row-->
    </div>  <!--div container-->                
  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>
                    <?php
                    
                    /*echo "<a href='factura/canje_cheque.php' class='btn btn-primary' id='btn-print-cheq' target='_blank'>
                        Imprimir 
                        <i class='fas fa-print'></i>
                        </a>

                        <button id='cerrar-print-cheq' class='btn btn-secondary' style='display: inline-block;'>Cancelar
                        </button>";*/
                    ?>