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
<link rel="stylesheet" href="css/styles-update.css">

<!--link rel="stylesheet" href="chosen/chosen.css"-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<script>
    var id_servicio = parseInt(0);
    var importe_total = parseInt(0);
    var lote = "";
    var codigos_serv = [];

    function find_index(array, value){
        var i = parseInt(0);
        var pos = parseInt(0);
        while(i < array.length){
          if(array[i].codigo == value)
          { 
            pos = parseInt(i);
            i = parseInt(array.length)+ parseInt(10);
            
          }
          else i = i + parseInt(1);
        }
        if(i > parseInt(array.length))
        {
            return pos;
        }
        else return(parseInt(-1));
    };

    function ordenar(array, key) {
        array.sort(function (a, b) {
            return (a[key] - b[key]);
        });
    }

    $(document).ready(function(){

        // Servicio
        $('input[name=servicio]').on('click', function(e){
            e.preventDefault;
            if($(this).val() == '0')
            {
                $(this).val('1'); // seleccionado
                $(this).prop("checked", true);

                id_servicio = $(this).prop('id');
                 
                // codigo de servicio
                var code = $('.tabla-servicios tr[id='+parseInt(id_servicio)+']').attr('codigo');
                console.log(code);
                
                // importe seleccionado
                var valor = $('.tabla-servicios tr[id='+parseInt(id_servicio)+']').find(".importe-serv").children().val(); 
                importe_total+=Number(valor);
                 
                codigos_serv.push({
                    'codigo': code,
                    'precio': valor
                });
                
                ordenar(codigos_serv, 'codigo');
   
                console.log('codigos: '+JSON.stringify(codigos_serv));

                
                $('#total-servicios').val(importe_total);    
                          
            }
            else
            {
                $(this).val('0');// Deseleccionado
                $(this).prop("checked", false);

                id_servicio = $(this).prop('id'); 
            
                var code = $('.tabla-servicios tr[id='+parseInt(id_servicio)+']').attr('codigo');
                if(find_index(codigos_serv,code) >= parseInt(0) )
                {
                    var pos = find_index(codigos_serv,code);
                    codigos_serv.splice(pos,1);
                    ordenar(codigos_serv, 'codigo');
                    console.log('codigos: '+JSON.stringify(codigos_serv))
                }

                var valor = $('.tabla-servicios tr[id='+parseInt(id_servicio)+']').find(".importe-serv").children().val();               
                importe_total-=Number(valor);
            
                if(importe_total == parseInt(0))
                {
                    $('#total-servicios').val("");
                }
                else
                {
                    $('#total-servicios').val(importe_total);
                }
                     
            }
                        
        }) 
  

        // Realizar cobro de los servicios
        $('.cobro').on('click', function(){
            if( (importe_total != parseInt(0)))
            {
                lote = $('#code').val();
                window.open('html-pdf/examples/recibo.php?lote='+lote+'&importe='+importe_total+'&codigos='+JSON.stringify(codigos_serv) , '_blank');

                $('#select-concept').val("");

                $('.tabla-servicios tr').find(".chek-servicio").each(function(){
                    if($(this).val() == '1'){
                        $(this).val('0');// Deseleccionado
                        $(this).prop("checked", false);
                        $(this).hide();
                    }
                })

                $('#total-servicios').val("");

                importe_total = parseInt(0);
                codigos_serv.splice(0, codigos_serv.length);
                console.log(codigos_serv);
            }
            else
            {
                var info = "<strong>Para cobrar seleccione al menos un servicio.</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.ok-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
            }              
        })

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
    input[name=servicio]{
        width: 20px;
        height: 20px;
    }

    .my-custom-scrollbar {
        display: flex;
        flex-direction: row;
        justify-content: center;
        position: relative;
        max-height: 200px;
        overflow: auto;
        margin: 5px;
    }
    
    .table-wrapper-scroll-y {
        display: block;
    }
    .content-table-scroll{
        margin-top:8px;
        margin-bottom:8px;
        /*height: 100px;*/
        max-height: 250px;
        width: 100%;  
        
    }
    
</style>
<script type="text/javascript">
$(document).ready(function() {


});
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

    <!-- Modal -->
    <div id="miModal" class="modal">
        <div class="modal-contenido">
            
            
            <p id="modal-info"></p>
            <div style="width:100%; height: 35px; margin: 0 auto; text-align: center; ">
                <button class="btn btn-success ok-modal">Aceptar</button>
                
            </div>
        </div>  
    </div>

    <div class="container">
      <h2>Cobro de servicios</h2>
      <hr>
       
      <div class="alert alert-success" role="alert"> 
        <div style="100%; overflow: hidden; border:1px solid green;">
            
            <?php
                    include('conexion.php');
                    $codigo = $_GET['lote'];
                    $qry = "SELECT * FROM det_lotes WHERE lote= '$codigo'";
                    $res = mysqli_query($connection, $qry);
                    $datos_cli = mysqli_fetch_array($res);
                    echo "<table class='table'>
                            <tr><th style='width: 80px;'>Titular:</th><td>".$datos_cli['titular']."</td></tr>
                            <tr><th style='width: 80px;'>Nº d.n.i:</th><td>".number_format($datos_cli['dni'],0,',','.')."</td></tr>
                            <tr><th style='width: 80px;'>Loteo:</th><td>".$datos_cli['loteo']."</td></tr>
                            <tr><th style='width: 80px;'>Lote:</th><td>".$datos_cli['lote']."</td></tr>
                         </table>";

            ?>
            <hr>
            <input type="text" style='display:none;' id='code' value='<?php echo $codigo;?>'>
            <!--div style="width: 30%; padding: 7px;">
                <strong>Concepto</strong>
                <select name="" id="select-concept" class="form-select">
                    <option value="0"></option>
                    <option value="1">Conexión de agua</option>
                    <option value="2">Conexión de cloacas</option>
                    <option value="3">Red de cloacas</option>
                </select>
            </div-->
           
            <div class="form-group col-md-6 content-table-scroll mis_cheques" style="width: 100%;">

                <div class="table-wrapper-scroll-y my-custom-scrollbar">
                    <form  method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <table class="table table-striped tabla-servicios">
                        <thead>
                        <tr>
                            <th scope="col">Codigo</th>
                            <th scope="col">Importe</th>
                            <th scope="col">Elegir</th>
                        </tr>
                        </thead>
                        <tbody> 
                        <?php
                            include('conexion.php');
                            include('funciones.php');
                            $qry = "SELECT * FROM precios_servicios ORDER BY id";
                            $res = mysqli_query($connection, $qry);
                            
                            if($res->num_rows > 0)
                            {
                                while($datos = mysqli_fetch_array($res))
                                {
                                    echo "<tr id='".$datos['id']."' codigo='".$datos['codigo']."'>
                                    <td class='nombre-serv'><input type='text' readonly value='"."COD".$datos['codigo']." (".$datos['servicio'].")' style='background:transparent; border:none; width:185px;'></td>
                                    <td class='importe-serv'>"."<input style='width: 110px;' readonly value='".$datos['precio']."'>"."</td>        
                                    <td style='text-align: left;'>"."<input type='checkbox' class='chek-servicio' id='".$datos['id']."' name='servicio' value='0'>"."</td></tr>";
                                    
                                }
                                
                            }
                        
                        ?>
                        </tbody>
                    </table>
                    </form>
                </div>
            
                <div class="table-wrapper-scroll-y my-custom-scrollbar">
                    <table class="table">
                        <thead>
                            
                        </thead>
                        <tbody>
                                <tr>
                                    <td style="width: 560px; "><strong>Total</strong></td>
                                    <td style="width: 320px"><input readonly id="total-servicios" style="width: 110px; background: white;"></td>
                                    <?php 
                                     echo "<td style='width: 90px; float:left;'><a href='#' value='".$codigo."' class='btn btn-success cobro'>Cobrar</a></td>";
                                    ?>
                                    
                                </tr>
                        </tbody>
                    </table>
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