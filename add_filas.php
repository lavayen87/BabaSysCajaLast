
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    
    var datos = []; // array de datos
    var lista = []; // array id de fila
    
    // Aguregar una nueva fila
	$('.add_tr').on('click', function(){
     
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
            var select ="<select id='n_banco' class='valor_cheque' style='height: 28px;'>";
            $("#n_banco option").each(function(){
                select+=`<option value='${$(this).attr('value')}'>${$(this).attr('value')}</option>`;
            });
            select+="</select>";
            var id = parseInt($('.tabla_cheques tr:last').prop('id'))+parseInt(1);
            var fila = `<tr id='${id}'>
                        <td><input type="number" id="n_cheque" class="valor_cheque"></td>
                        <td>${select}</td>
                        <td><input type="text" id="n_entrega" class="valor_cheque" maxlength="20"></td>
                        <td><input type="date" id="n_vence" class="valor_cheque" maxlength="20"></td>
                        <td><input type="number" id="n_importe" class="valor_cheque" onKeyUp="Suma()"></td>
                        </tr>`;
            $('.tabla_cheques').append(fila);

            console.log(datos);
        }
        else{
            alert('Campos vacios !');
        }
	});

    // Quitar la ultima fila
    $('.delete_tr').on('click', function() {
        
        var fila = parseInt($('.tabla_cheques tr:last').prop('id'));
        console.log('fila eliminada: '+fila)  
        if(fila == parseInt(1))
        {
            alert('No se puede borrar la primer fila');
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
            console.log(datos)
        }
	});

    // Realizar la carga de datos de cheques
    $('.save_tr').on('click', function() {
            
            var cant = parseInt(0);

            $('.tabla_cheques tr:last').find(".valor_cheque").each(function(){
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
                        });
                    }
                    
                    var cantidad = $('#n_total').val();

                    $.post('cargar_cheques.php', {'datos':JSON.stringify(datos),'cantidad':cantidad,'detalle':detalle}, resp =>{
                        console.log('cant: '+resp);
                        if(parseInt(resp) == 1)
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
                                    datos = [];
                                    lista = [];
                                    $('#n_total').val("");
                                    $('#detalle-cheques').val("");
                                    $('#info-load-cheques').fadeIn(1000).html("<strong style='color: green;'> Cheque/s cargado/s con exito!</strong>");
                                    $('#print-canje-cheq').slideDown();
                                }
                            })
                            return false
                        }
                        else {
                            alert('Error');
                            console.log(resp);
                        }
                    })
                }
                else alert('Campos vacios !');
            }
            else{
                alert('Campos vacios !');
            }
        
    })

});

function Suma()
{

    var i = parseInt(0);
    var importe = parseInt(0);
    
    var cant_filas = parseInt($(".tabla_cheques tr").length)-parseInt(1);
        
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

  <?php include('menu_main.php');?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">
    <div class="container">
      <h2>Carga de cheques</h2>
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
            <br><br>

            <div id="info-load-cheques" style=""></div> 
            <br>
            <div id="print-canje-cheq" style="display: none;">                   
                
                <div class="button-close">
                    <?php
        
                    echo "<a href='factura/canje_cheque.php' class='btn btn-primary' target='_blank'>
                        Imprimir 
                        <i class='fas fa-print'></i>
                        </a>

                        <button id='cerrar-print-cheq' class='btn btn-secondary' style='display: inline-block;'>Cancelar
                        </button>";
                    ?>
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