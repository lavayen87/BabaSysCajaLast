<?php
        
    date_default_timezone_set("America/Argentina/Salta");
    session_start();
    if($_SESSION['active'])
    {
        $micaja = $_SESSION['nombre_caja'];
        $nombre = $_SESSION['nombre'];
        $rol = $_SESSION['rol'];
        $numero_caja = $_SESSION['numero_caja'];
    }
    
    $fecha = date('Y-m-d');
    $hora = date('G').':'.date('i')." Hs.";
    $ultimo_cobro = 0;
    $monto = 0;
    $monto_serv = 0;
    $cobranza = 0;
    $servicio = "";
    $tabla = "<table style='width: 100%;'>
              <thead>
              <tr>
              <th style='width: 85%;'>Concepto</th>
              <th style='width: 15%; text-align: right;'>Importe</th>
              </tr>
              </thead>
              <tbody>";
    $tabla2 = $tabla;
    $tabla3 = $tabla;
    $tabla4 = $tabla;
              
    include('../../conexion.php');
    include('../../funciones.php');
    include('../../conversor.php');
    
    $fecha = date('Y-m-d');
    $datos = json_decode($_GET['datos']); 
    $lote = $datos[0];
    $loteo = $datos[1]; 
    $precio = number_format($datos[2],2,',','.');
    $anticipo = number_format($datos[3],2,',','.');
    $saldo_afn = number_format($datos[4],2,',','.');
    $cuotas = $datos[5];
    $valor_cuota = number_format($datos[6],2,',','.');
    $vnto = date("d-m-Y", strtotime($datos[7]));
    $propietario = $loteo == "Buen Clima" ? "Buen Clima S.R.L." : "ÑANN S.R.L.";  

    // datos del titular: 
    $tqry = "SELECT * FROM det_lotes WHERE lote = '$lote'"; 
    $tres = mysqli_query($connection, $tqry);
    $tdatos = mysqli_fetch_array($tres);

    $titular = $tdatos['titular'];
    $tdni = $tdatos['dni'];

    // Actualizo fomra de pago:
    $update = "UPDATE det_servicio SET forma_pago = 'CONTADO' 
               WHERE lote = '$lote' AND servicio = 'Red de Cloacas'";
    mysqli_query($connection,$update);

    // Textos del recibo: 
    $textoprecio   = TextoPrecio($precio);
    $textoanticipo = TextoPrecio($anticipo);
    $textosafn = TextoPrecio($saldo_afn);
    $textovalorcuota = TextoPrecio($valor_cuota);
    $textocantcuotas = TextoCuotas($cuotas);
    
?>


<style type="text/css">

    div.zone { 
        border: none; 
        background: #FFFFFF; 
        padding: 2mm;
        margin-left: 5px;
        font-size: 15px;
        width: 95%;
    }
    h1 { padding: 0; margin: 0;  font-size: 5mm; }
     
    p, label, span strong{     
        font-size: 11pt;
        line-height:20px;
    }
    #content-firma{
        width: 100%; 
        height: 40px;
        padding-top: 45px;
        padding-left: 4px;
        overflow: hidden; 
        margin-bottom: 20px;
        
    }
    .anulada{
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translateX(-50%) translateY(-50%);
}
    
</style>


<page width:="21cm" height="29.7cm"  style="font: arial;">
    
    <div style="width: 100%; border: none;" cellspacing="4mm" cellpadding="0">
        
        <div style="height: 50%; margin-top:10px;">
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-bottom:1mm; background: #CAE5E2;">
                <table border=0 style='width: 100%;'>  
                    <tr>
                        <td style="width: 90px;">
                            <?php $dir_img="./res/logo".substr($lote,0,2).".jpg"; ?>                                                       
                            <!-- <img src="./res/favicon-72x72.png" alt="logo" style='float: left;'>  -->
                            <div style="display:  felx;">
                                <img src="<?php echo $dir_img ?>" alt="logo" style='float: left; width:190; height: 60px'>
                            </div>
                            <!-- <div style='display: inline-block; padding-top:40px; padding-left:4px; font-size: 15px;'>
                                <strong>Baba S.R.L.</strong>                               
                            </div> -->
                        </td>
                        
                        <td style="width: 300px;">
                            
                            <div style="text-align: center;"><strong>ORIGINAL</strong></div>
                            <br>
                            <div>
                                <div style="border:1px solid green; width: 15px; margin-left: 140px; text-align:center;">X</div>
                            </div>
                           
                        </td>
                         
                        <td style="width: 243px; text-align: right;">
                            <strong>RECONOCIENTO DE DEUDA Y PLAN DE PAGO</strong><br><br>                                                                         
                        </td>
                    </tr>
                    <tr>
                        <td style='font-size: 9px;'>Mendoza Nº 230 - (4400) SALTA - ARGENTINA</td>
                        <td style='font-size: 9px; text-align: center;'>Documento no valido como factura</td>
                        <td style='text-align: right;'> <?php echo "Fecha: ".fecha_min($fecha)." - ".$hora."";?> </td>
                    </tr>
                </table>
                
            </div>

            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green;">
                
                <?php            
                    echo "Lote: "."<b>".$lote."</b> - Loteo: "."<b>".$loteo."</b><br>";
                ?>
                <div style="">
                    
                </div>
                
            </div>
            
            <!-- codigo e importe -->
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-top: 4px; width: 97.3%;">
                
                <p align="justify">
                Por la presente, el/la Señor/a <strong><?php echo $titular;?></strong>, D.N.I. Nº <strong><?php echo $tdni;?></strong>, comprador/a por boleto de compra-venta del lote Nº <strong><?php echo $lote;?></strong>, del loteo <strong><?php echo $loteo;?></strong>, reconoce adeudar a <?php echo $propietario; ?>, la suma de <strong><?php echo $textoprecio;?></strong> en concepto de costo proporcional a las obras de red cloacal efecutadas en el loteo, el cual es abonado en éste acto en dinero en efectivo.-
                </p>
                 
                <p align="justify">
                El importe reconocido no incluye el costo de la conexión de la red cloacal al domicilio del titular (conexión domiciliaria), el que deberá ser abonado por separado a <?php echo $propietario; ?> (única autorizada a efectuar aquélla).-
                </p>
                <!--table style='width: 100%;'>
                    <thead>
                    <tr>
                        <th style='width: 85%;'>Codigo</th>
                        <th style='width: 15%; text-align: right;'>Importe</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style='background-color: transparent; border-bottom: 1px solid black;'>
                        <td style='width: 85%;'><?php echo $concepto;?></td>
                        <td style='width: 15%; text-align: right;'><?php echo "$".number_format($importe,2,',','.');?></td>
                    </tr>
                    <tr style='border-collapse: collapse;'>
                        <td style='width: 85%; text-align: right; border-top:1px solid green;'><b>Total</b></td>
                        <td style='width: 15%; text-align: right; border-top:1px solid green;'><b><?php echo "$".number_format($importe,2,',','.');?></b></td>
                    </tr>
                    </tbody>
                </table-->

                <!-- firma baba-->
                <br>
                <br>
                <table style='width: 100%;'>
                    
                    <tbody>
                    <tr>
                        <td style='width: 30%;'><hr></td>
                         
                    </tr>
                    <tr>
                        <td style='width: 30%; text-align: center;'>P/ Baba S.R.L.</td>
                        
                    </tr>
                    <tr style="border: 1px solid green;">
                        <td style='width: 20%; text-align: left;'></td>
                        <td style='width: 23%; margin-left: 2px; margin-rigth:2px; text-align: center;'><hr>De conformidad Frima titular</td>
                        <td style='width: 23%; margin-left: 2px; margin-rigth:2px; text-align: center;'><hr>Aclaración</td>
                        <td style='width: 23%; margin-left: 2px; margin-rigth:2px; text-align: center;'><hr>DNI</td>
                    </tr>
                    </tbody>
                </table>

                
            </div>
            <!--pie de pagina-->
            
            <div class="zone" style=" vertical-align: middle; text-align: justify; margin-top: 4px;">
                <?php 
                    /*switch($cant_codigos){
                        case 1: echo "<div style='width: 100%; padding-top:120px;'>
                                        <div style='height: 25px; background: #CAE5E2; text-align: right;'>Fecha de impresion: ".fecha_min($fecha)."</div>
                                      </div>"; 
                                      break;
                        case 2: echo "<div style='width: 100%; padding-top:90px;'>
                                        <div style='height: 25px; background: #CAE5E2; text-align: right;'>Fecha de impresion: ".fecha_min($fecha)."</div>
                                      </div>"; 
                                      break;
                        case 3: echo "<div style='width: 100%; padding-top:70px;'>
                                        <div style='height: 25px; background: #CAE5E2; text-align: right;'>Fecha de impresion: ".fecha_min($fecha)."</div>
                                      </div>";
                                      break;
                        case 4: echo "<div style='width: 100%; padding-top:60px;'>
                                       <div style='height: 25px; background: #CAE5E2; text-align: right;'>Fecha de impresion: ".fecha_min($fecha)."</div>
                                      </div>"; 
                                      break;
                        case 5: echo "<div style='width: 100%; padding-top:40px;'>
                                      <div style='height: 25px; background: #CAE5E2; text-align: right;'>Fecha de impresion: ".fecha_min($fecha)."</div>
                                     </div>"; 
                                     break;
                    }
                    */
                ?>
            
                
            </div>
        </div>

        <!-- ESPACIO -->
        <br><br><br><br>
        

        
         
    </div>    
     
</page>
