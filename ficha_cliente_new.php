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
    .validation{ display: none; }   
</style>

</head>
<body>
<div class="page-wrapper chiller-theme toggled">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  <!-- recibir un id deindice de menu  -->
  <?php include('menu_lateral.php');  ?>
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
                    if($numero_caja == 0 || tiene_permiso($numero_caja,41))        
                        echo "<button class='btn btn-success edicion' value='0'>Editar</button>";                    
                ?>
            </div>
        </div>
        <br>
        <div class="row">
            <div id="content" class="col-lg-12">
            
                <?php
                include('conexion.php');
                if($_GET['lote'] != "")
                {
                    $codigo = $_GET['lote'];
                    echo "<input type='text' style='display: none;' 
                           name='lote' id='lote-hidden' value='".$codigo."'>";
                    
                ?>
                    <?php  
                    $qry = "SELECT * FROM det_lotes where lote = '$codigo'";
                    
                    $result = $connection->query($qry);

                    if ($result->num_rows > 0) 
                    {
                        $row = $result->fetch_array();
                        $loteo = $row['loteo'];
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
                                <input type="text" disabled style="display: inline-block; background: #E7F5F2;" class="form-control div-loteo" name="loteo" id="<?=$row['id']?>"  value="<?=$row['loteo']?>" >
                                
                        </div>
                        
                        <div class="input-group">
                            <div style='width: 20%;'>Lote</div>
                                
                                <!--div class="input-group-prepend">
                                    <span class="input-group-text validation" ></span>
                                </div-->
                                <input type="text" disabled style="display: inline-block; background: #E7F5F2;" class="form-control div-lote" name="lote" id="<?=$row['id']?>" value="<?=$row['lote']?>" maxlength="6">
                            
                        </div>  
                        
                        <div class="input-group">
                            <div style='width: 20%;'>Fecha posesión</div>
                                
                                <div class="input-group-prepend">
                                    <!--span class="input-group-text validation" ></span-->
                                </div>
                                <input type="date" readonly style="display: inline-block; background: white;" class="form-control" name="posesion" id="<?=$row['id']?>" value="<?=$row['posesion']?>" >
                            
                        </div> 

                        <?php

                        // Datos de servicios
                        $qry_servicios = "SELECT * FROM det_servicio 
                        where lote = '$codigo'";
                        
                        $res_servicios = mysqli_query($connection, $qry_servicios);

                        // Datos de posesion
                        $qry_posesion = "SELECT * FROM det_servicio
                                        WHERE (lote = '$codigo') 
                                        AND servicio = 'Agrimensor'
                                        AND (fecha_pago !='0000-00-00')
                                        GROUP BY lote";

                        $res_posesion = mysqli_query($connection, $qry_posesion);
                        
                        // forma de pago
                        $forma_pago = "";
                        $qry_forma_pago = "SELECT forma_pago FROM det_servicio
                                               WHERE lote = '$codigo' 
                                               AND servicio = 'Red de Cloacas'";

                        $res_forma_pago = mysqli_query($connection, $qry_forma_pago);
                        $datos_forma_pago = mysqli_fetch_array($res_forma_pago);
                        $forma_pago = $datos_forma_pago['forma_pago'];
                        
                        $div = ""; // div de posesion
                       
                        if($res_posesion->num_rows > 0)
                        {
                            /*$qry_forma_pago = "SELECT forma_pago FROM det_servicio
                                               WHERE lote = '$codigo' 
                                               AND servicio = 'Red de Cloacas'";

                            $res_forma_pago = mysqli_query($connection, $qry_forma_pago);
                            $datos_forma_pago = mysqli_fetch_array($res_forma_pago);
                            $forma_pago = $datos_forma_pago['forma_pago'];*/
                           
                            $div.= "<p style='width: 45%; margin-left: 20%; margin-top:5px;'>
                                    <span style='float: left; background: #11F3D2'>Posesión: <strong>SI <i class='fas fa-check-circle'></i></strong></span>
                                    <span style='float: right;background: #11F3D2'>Forma de pago red: <strong>".$forma_pago."</strong></span>
                                    <p>
                                    </br>";
                        }
                        else{
                            $div.= "<p style='width: 45%; margin-left: 20%; margin-top:5px;'>
                                    <span style='background: #11F3D2'>Posesión: <strong>NO</strong></span>
                                    <span style='float: right;background: #11F3D2'>Forma de pago red: <strong>".$forma_pago."</strong></span>
                                    <p>
                                    </br>";
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
                                    <div class='form-group ficha-cli'>
                                    <strong>".$serv['servicio']."</strong>
                                    </br></br>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>Nº de recibo</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='text' readonly style='background: white;' class='form-control' name='recibo' value='".$serv['recibo']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."' maxlength='10'>
                                        
                                    </div>

                                    <div class='input-group'>

                                        <div style='width: 20%;'>fecha de pago</div>
                                        <div class='input-group-prepend'>
                                            
                                        </div>
                                        <input type='date' readonly style='background: white;' class='form-control' name='fecha_pago' value='".$serv['fecha_pago']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                        
                                    </div>";

                                    if($serv['servicio']!='Red de Cloacas')
                                    {
                                        $ficha.="<div class='input-group'>

                                            <div style='width: 20%;'>fecha de solicitud</div>
                                            <div class='input-group-prepend'>
                                                
                                            </div>
                                            <input type='date' readonly style='background: white;' class='form-control' name='fecha_solicitud'  value='".$serv['fecha_solicitud']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                            
                                        </div>

                                        <div class='input-group'>

                                            <div style='width: 20%;'>fecha de realizado</div>
                                            <div class='input-group-prepend'>
                                                
                                            </div>
                                            <input type='date' readonly style='background: white;' class='form-control' name='fecha_realizado'  value='".$serv['fecha_realizado']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                            
                                        </div>

                                        <div class='input-group'>

                                            <div style='width: 20%;'>fecha de abonado</div>
                                            <div class='input-group-prepend'>
                                                
                                            </div>
                                            <input type='date' readonly style='background: white;' class='form-control' name='fecha_abonado'  value='".$serv['fecha_abonado']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                            
                                        </div>
                                        <div class='input-group'>

                                                <div style='width: 20%;'>Estado</div>
                                                <div class='input-group-prepend'>
                                                    
                                                </div>
                                                <input type='text' readonly style='background: white;' class='form-control' name='estado'  value='".$serv['estado']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."' maxlength='12'>
                                                
                                            </div>";
                                    }
                                
                                

                                    
                                    if($serv['servicio'] == 'Red de Cloacas')
                                    {
                                        $ficha.="<div class='input-group'>

                                                    <div style='width: 20%;'>fecha de abonado</div>
                                                    <div class='input-group-prepend'>
                                                        
                                                    </div>
                                                    <input type='date' readonly style='background: white;' class='form-control' name='fecha_abonado'  value='".$serv['fecha_abonado']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                                    
                                                </div>
                                        
                                                <div class='input-group'>

                                                        <div style='width: 20%;'>Forma de pago</div>
                                                        <div class='input-group-prepend'>
                                                            
                                                        </div>
                                                        <input type='text' readonly style='background: white;' class='form-control' name='forma_pago'  value='".$serv['forma_pago']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."' maxlength='15'>
                                                        
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
            <br>
            <button class='btn btn-primary red-clo' id="<?php echo $codigo; ?>" 
                data-toggle="modal" data-target="#myModal">
                Rec. Red Cloacas
            </button>
        </div>
    
     </div> 
          
    </div>
    <?php
        // precio contado
        $let = substr($codigo,0,2);
        $let = strtoupper($let);
        switch($let){
            case 'BC':
                $qry = "SELECT precio FROM precios_servicios
                        WHERE codigo = '004'";
                break;
            case 'TE':
                $qry = "SELECT precio FROM precios_servicios
                        WHERE codigo = '005'";
                break;
            case 'AI':
                $qry = "SELECT precio FROM precios_servicios
                        WHERE codigo = '006'";       

        }

        $res = mysqli_query($connection,$qry);
        $dato_precio = mysqli_fetch_array($res);
        $precio = $dato_precio['precio']; 

        // indice de financiacion
        $qry_indice = "SELECT * FROM indices_fn 
                       WHERE id_loteo = (SELECT id from loteos WHERE nombre = '$loteo')";
        $res_indice = mysqli_query($connection, $qry_indice);
        $dato_indice = mysqli_fetch_array($res_indice);
        $indice_fn = $dato_indice['indice']; // % TNA

        // Convertir la fecha actual a un objeto DateTime
        $fecha_obj = new DateTime(date('Y-m-d'));

        // Agregar un mes
        $fecha_obj->modify('+1 month');

        // Establecer el día específico (por ejemplo, el día 15)
        $fecha_obj->setDate($fecha_obj->format('Y'), $fecha_obj->format('m'), 10);

        // Convertir de nuevo a formato de fecha
        $nueva_fecha = $fecha_obj->format('Y-m-d');

    ?>

    <!-- The Modal -->
      <div class="modal" id="myModal">
          <div class="modal-dialog">
            <div class="modal-content">

              <!-- Modal Header -->
              <div class="modal-header">
                <h4 class="modal-title">Plan de Finansición</h4>
                <!-- <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button> -->
                <button type="button" class="btn btn-danger" data-dismiss="modal">X</button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">
                <div class="input-group mb-2">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Precio contado</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo "$".number_format($precio,2,',','.');?>"  readonly>                    
                </div>
                <input type="text" style="display: none;" id="precio" name="precio" value="<?php echo $precio?>">

                <!--anticipo-->
                <div class="input-group mb-2">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Anticipo</label>
                    <input type="number" class="form-control user-edicion" value="" id="anticipo" name="anticipo">
                </div>
                <!--monto a financiar-->
                <div class="input-group mb-2" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Monto a financiar</label>
                    <input type="text" class="form-control user-edicion" value="" id="monto_afn" readonly>
                </div>
                <!--% TNA indice de finan.-->
                <div class="input-group mb-2" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">% NTA</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo $indice_fn?>" id="tna" name="" readonly>
                </div>
                 <!--% Diario financiacion-->
                <div class="input-group mb-2" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text ">% Diario finan.</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo $diario_fn?>" id="diario_fn" readonly>
                </div>
                 <!--% Diario-->
                <div class="input-group mb-2" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">% Diario</label>
                    <input type="text" class="form-control user-edicion" value="" id="diario" readonly>
                </div>
                 <!--N° Cuotas-->
                <div class="input-group mb-2">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">N° de cuotas</label>
                    <input type="number" class="form-control user-edicion" value="" id="n_cuotas" >
                </div>
                <!--Dias financiacion-->
                <div class="input-group mb-2" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Dias Finan.</label>
                    <input type="number" class="form-control user-edicion" value="" id="dias_fn" readonly>
                </div>
                <!--Interes total-->
                <div class="input-group mb-2" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Interes total</label>
                    <input type="number" class="form-control user-edicion" value="" id="interes_total" readonly>
                </div>
                <!--Monto financiado.-->
                <div class="input-group mb-2" style="display: none;">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Monto financiado</label>
                    <input type="number" class="form-control user-edicion" value="" id="monto_fdo" readonly>
                </div>
                <!--valor Cuota-->
                <div class="input-group mb-2">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Valor cuota</label>
                    <input type="text" class="form-control user-edicion" value="" id="valor_cuota" readonly>
                </div>
                <!--Total operaci{on-->
                <div class="input-group mb-2">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Total operación</label>
                    <input type="text" class="form-control user-edicion" value="<?php echo "$".number_format($precio,2,',','.'); ?>" id="total_op" readonly>
                </div>
                <!--Vto primer cuota-->
                    <div class="input-group mb-2">
                    <label style="width: 35%; background: #22A199; color: white;" class="input-group-text">Vto primer cuota</label>
                    <input type="date" class="form-control user-edicion" value="<?php echo $nueva_fecha ?>" id="vto_cuota">
                </div>
              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                <div style="margin: 0 auto">
                    <button class="btn btn-primary simular">Simular</button>
                    <button class="btn btn-secondary limpiar">Limpiar</button>
                    <button class="btn btn-success realizar">Realizar</button>
                </div>
                <!-- <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> -->
              </div>

            </div>
          </div>
      </div>
      <!-- End Modal -->    

    
  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>
<script type="text/javascript">

    $(document).ready(function() 
    {
        var vprecio = "<?php echo number_format($precio,2,',','.');?>";
        var precio = "<?php echo $precio; ?>"*1; 
        var tasaint = "<?php echo $indice_fn; ?>"*1;
        var nueva_fecha = "<?php echo $nueva_fecha ?>";
        var anticipo; 
        var n_cuotas;
        var x1 , x2;        
        var monto_afn;       
        var monto_fdo;
        var valor_cuota;
        var total_op;
        var vencimiento = "";
        var propietario = "";
        var smonto_fdo, svalor_cuota, stotal_op;
        var op = parseInt(0);
        var band = false;
       

        $('#vto_cuota').on('change', function(){
            vencimiento = $('#vto_cuota').val();
            console.log("cambio: "+vencimiento)
        });    
            
        //Simular 
        $('.simular').on('click', function(){
               
            // obtenemos anticipo y nro de cuotas
            anticipo = $('#anticipo').val() > parseInt(0) ? $('#anticipo').val()*1 : parseInt(0);
            n_cuotas = $('#n_cuotas').val() > parseInt(0) ? $('#n_cuotas').val()*1 : parseInt(0);
            $('#n_cuotas').removeClass('is-invalid');

            if((anticipo == parseInt(0) && n_cuotas == parseInt(0))
            || (anticipo == parseInt(0) && n_cuotas  > parseInt(0))
            || (anticipo  > parseInt(0) && n_cuotas  > parseInt(0)))          
            {
                if(anticipo == parseInt(0) && n_cuotas == parseInt(0)) {op = parseInt(0)} // contado

                if(anticipo == parseInt(0) && n_cuotas  > parseInt(0)) {op = parseInt(1)} // sin anticipo

                if(anticipo  > parseInt(0) && n_cuotas  > parseInt(0)) {op = parseInt(2)} // con antisipo

                // Monto a financiar :
                monto_afn = (precio - anticipo)*1;
                
                // variables de financiacion
                x1 = (n_cuotas/12)*1; 
                x2 = ((monto_afn*tasaint)/100)*1;  

                // Monto financiado :
                monto_fdo = ( (x1*x2) + monto_afn )*1;

                // valor cuota :                    
                valor_cuota = ( monto_fdo/(n_cuotas > parseInt(0) ? n_cuotas*1 : parseInt(1)) )*1;                 
                
                // Total operacion :
                total_op = (anticipo + monto_fdo)*1;  

                console.log("Total_op: "+total_op)

                // fecha de vencimiento primer cuota
                vencimiento = $('#vto_cuota').val() != "" ? $('#vto_cuota').val() : "";   

                console.log("vto :"+vencimiento)

                $.post("setvalor.php", {'valor':valor_cuota}, res =>{ 
                    svalor_cuota = res; 
                    $('#valor_cuota').val('$' + svalor_cuota);
                });

                $.post("setvalor.php", {'valor':total_op}, res =>{ 
                    stotal_op = res 
                    console.log("seteo total de op. "+stotal_op)
                    $('#total_op').val('$' + stotal_op);
                });

                band = true;
            }
            else { $('#n_cuotas').addClass('is-invalid'); }
                
        })

        function limpiarBoxes()
        {
            $('#anticipo').val("");
            $('#n_cuotas').val("");
            $('#monto_fdo').val("");
            $('#valor_cuota').val("");
            $('#total_op').val("$" + vprecio); 
            $('#vto_cuota').val(nueva_fecha);          

            anticipo  = parseInt(0);
            monto_afn = parseInt(0);
            n_cuotas  = parseInt(0);  
            monto_fdo = parseInt(0);
            valor_cuota = parseInt(0);
            total_op = parseInt(0);
            stotal_op = parseInt(0);
            op = parseInt(0);
            band = false;

            console.log("Clean");
        }

         // Limpiar
        $('.limpiar').on('click', function(){

            limpiarBoxes();
        })

        // Realizar
        $('.realizar').on('click', function(){

            var alerta = false;

            if( band )
            {

                var loteo = $('.div-loteo').val();
                var lote = $('.div-lote').val();
                var datos = [
                    lote,
                    loteo,
                    precio,
                    anticipo,
                    monto_afn,
                    n_cuotas,
                    valor_cuota,
                    vencimiento
                ];
                switch(op)
                {
                    
                    case parseInt(0): // CONTADO
                        window.open('html-pdf/examples/rec_contado.php?datos='+JSON.stringify(datos),
                                '_blank');
                        $('#anticipo').val("");
                
                    break;

                    case parseInt(1): // SIN ANTICIPO
                       
                        if( $('#vto_cuota').val().length <= parseInt(1))
                        {                       
                            $('#vto_cuota').addClass('is-invalid');
                            alerta = true;
                        }
                        else
                        {
                            
                            $('#vto_cuota').removeClass('is-invalid');
                            alerta = false;
                            window.open('html-pdf/examples/rec_sinanticipo.php?datos='+JSON.stringify(datos),'_blank');
                        }
                        break;

                    case parseInt(2): // CON ANTICIPO  
                       
                        if($('#vto_cuota').val().length <= parseInt(1))
                        {

                           $('#vto_cuota').addClass('is-invalid');
                            alerta = true;                           
                        } 
                        else                         
                        {   
                            $('#vto_cuota').removeClass('is-invalid');
                            alerta = false;                           
                            window.open('html-pdf/examples/rec_anticipo.php?datos='+JSON.stringify(datos),'_blank');
                        }
                        break;
                    
                }

                if( !alerta )
                {
                    limpiarBoxes();
                }
            }

        })

        
        // Edicion datos en ficha del cliente
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

                       var valuelote = $("#lote-hidden").val();

                       var dataString = 'id='+field.attr('id')+'&id_cliente='+field.attr('id_cliente'); //'&id='+field.attr('id')
 
                       dataString+= '&value='+field.val()+'&field='+field.attr('name')+'&valuelote='+valuelote;
                        
                       console.log('data: '+dataString);
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