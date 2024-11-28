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

    $num_recibo = $_GET['num_recibo'];
    
    //$num  = $_GET['concepto']; // Dato enviados x url
    $lote = (string)strip_tags($_GET['lote']); // Dato enviados x url
    
    $codigos = json_decode($_GET['codigos']); // lista de codigos enviados x url
    

    $importe = $_GET['importe']; // Dato enviado x url

    $cant_codigos = count($codigos); // Cantidad de codigos recibidos
    
    $fecha = date('Y-m-d');
    $hora = date('G').':'.date('i')." Hs.";
    $ultimo_cobro = 0;
    $monto = 0;
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
              
    include('../../conexion.php');
    include('../../funciones.php');

    // Verificacion de recibo emitido
    /*foreach ($codigos as $cod)
    {
        $codigo_servicio = $cod->codigo;
        $verific = "SELECT count(*) FROM det_recibo 
            WHERE lote = '$lote' 
            AND codigo = '$codigo_servicio'";
        $res_verific = mysqli_query($connection, $verific);  
        if($res_verific->num_rows > 0){
            $c++;
        }
    } */
    
    
        //header('Location: ../../buscar_lotes_new.php');
    
        $qry = "SELECT * FROM det_lotes WHERE lote = '$lote'";
        $res = mysqli_query($connection, $qry);
        $datos_cli = mysqli_fetch_array($res);
        $titular = $datos_cli['titular'];
        $dni = $datos_cli['dni'];
        $loteo = $datos_cli['loteo'];
        $lote  = $datos_cli['lote'];

        

        $get_num = "SELECT numero FROM recibo ORDER BY numero DESC LIMIT 1";
        $res_num = mysqli_query($connection, $get_num);
        $dato_num = mysqli_fetch_array($res_num);
        $num_recibo = genera_num($dato_num['numero']);

        // Cargamos codigos en det_recibo
        foreach ($codigos as $cod)
        {
            $codigo_servicio = $cod->codigo;

            $insert = "INSERT IGNORE INTO det_recibo VALUES
            ('',
            '$num_recibo',
            '$lote',
            '$codigo_servicio'
            )";
            mysqli_query($connection, $insert);
        } 

        
        // Actualizamos los servicios 
        foreach ($codigos as $cod)
        {
            switch($cod->codigo)
            {
                case '001': $servicio = "Agrimensor"; break;
                case '002': $servicio = "Agua"; break;
                case '003': $servicio = "Cloacas"; break;
                case '004': $servicio = "Red Cloacas"; break;
                case '005': $servicio = "Desmalezado"; break; 
            }

            $update = "UPDATE det_servicio 
                        SET fecha_pago = '$fecha',
                        fecha_solicitud = '$fecha',
                        estado = 'Solicitado',
                        recibo = '$num_recibo'
                        WHERE (servicio = '$servicio')
                        AND lote = '$lote'";
            mysqli_query($connection, $update);
            
        }
            
        
        // Actualizamos la cobranza

        /* ---------------------------------------------------------- */

        require "../../conversor.php";
        $aux = 0;
        $texto1 = '';
        $texto2 = '';
        $findme   = "CERO";
        $aux = number_format($importe,2,',','');
            
        if( parte_entera(strval($aux)) <> 0)
        {	
            $texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
            $pos = strpos($texto1, $findme)."</br>";		
            if ($pos > 0)
            {
                $texto1 = str_replace($findme, "", $texto1);
            }
            
        }
        if( parte_decimal(strval($aux)) <> 0) 
        {	
            //echo "parte decimal: ".parte_decimal(strval($aux))."</br>";

            $texto1.= " CON ";		
            $texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
            $pos = strpos($texto2, $findme);
            if ($pos === true){
                $texto2 = str_replace($findme, "", $texto2);
            }
        }
    
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
        line-height:1em;
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
        position: relative;
        /*left: 50%;
        top: 50%;*/
        margin: 0px auto;
	    height: 300px;
        width: 21cm;
        transform: translateX(-50px) translateY(-50px);
    }
    
</style>


<page width:="21cm" height="29.7cm"  style="font: arial;">

    <div style="width: 100%; border: none;" cellspacing="4mm" cellpadding="0">
        
        <div style="height: 50%; margin-top:10px;">
            
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-bottom:1mm; background: #CAE5E2;">
                <table border=0 style='width: 100%;'>  
                    <tr>
                        <td style="width: 90px;">
                            <img src="./res/favicon-72x72.png" alt="logo" style='float: left;'>
                            <div style='display: inline-block; padding-top:40px; padding-left:4px; font-size: 15px;'>
                                <strong>Baba S.R.L.</strong>
                                
                            </div>
                        </td>
                        
                        <td style="width: 300px;">
                            
                            <div style="text-align: center;"><strong>ORIGINAL</strong></div>
                            <br>
                            <div>
                                <div style="border:1px solid green; width: 15px; margin-left: 140px; text-align:center;">X</div>
                            </div>
                           
                        </td>
                         
                        <td style="width: 243px; text-align: right;">
                            <?php
                                echo "<strong>RECIBO Nº: 750</strong>
                                      <br>
                                      Fecha: "."19-11-21"."";
                            ?>
                            
                        </td>
                    </tr>
                    <tr>
                        <td style='font-size: 9px;'>Mendoza Nº 230 - (4400) SALTA - ARGENTINA</td>
                        <td style='font-size: 9px; text-align: center;'>Documento no valido como factura</td>
                        <td></td>
                    </tr>
                </table>
                
            </div>

            <!--titulat, lote e importe-->
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green;">
                
                <?php
                    echo "Recibimos de <b>"."Juan Perez"."</b>"." (D.N.I. ".number_format(32150785,0,',','.').")<br>";
                    
                   
                        echo "La suma de ".strtoupper("veinticinco mil setecientos ochenta pesos")."<br>";
                    
                    
                    echo "Lote: "."<b>"."LI1560"."</b> - Loteo: "."<b>"."Libertad"."</b><br>";
                ?>
                <div style="">
                    
                </div>
                
            </div>
            
            <!-- codigo e importe -->
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-top: 4px;">
                
                <?php
                
                    
                    echo "COD 1 ------------------------------$2590<br>";
                    echo "COD 2 ------------------------------$2590<br>";
                    echo "COD 3 ------------------------------$2590<br>";
                
                ?>
                
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
                    </tbody>
                </table>

                
            </div>
            <!--pie de pagina-->
            
            <div class="zone" style=" vertical-align: middle; text-align: justify; margin-top: 4px;">             
            </div>
        </div>

        <!-- ESPACIO -->
        <br><br><br><br>
        
        <div style="height: 50%; margin-top:10px;">
            
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-bottom:1mm; background: #CAE5E2;">
                <table border=0 style='width: 100%;'>  
                    <tr>
                        <td style="width: 90px;">
                            <img src="./res/favicon-72x72.png" alt="logo" style='float: left;'>
                            <div style='display: inline-block; padding-top:40px; padding-left:4px; font-size: 15px;'>
                                <strong>Baba S.R.L.</strong>
                                
                            </div>
                        </td>
                        
                        <td style="width: 300px;">
                            
                            <div style="text-align: center;"><strong>ORIGINAL</strong></div>
                            <br>
                            <div>
                                <div style="border:1px solid green; width: 15px; margin-left: 140px; text-align:center;">X</div>
                            </div>
                           
                        </td>
                         
                        <td style="width: 243px; text-align: right;">
                            <?php
                                echo "<strong>RECIBO Nº: 750</strong>
                                      <br>
                                      Fecha: "."19-11-21"."";
                            ?>
                            
                        </td>
                    </tr>
                    <tr>
                        <td style='font-size: 9px;'>Mendoza Nº 230 - (4400) SALTA - ARGENTINA</td>
                        <td style='font-size: 9px; text-align: center;'>Documento no valido como factura</td>
                        <td></td>
                    </tr>
                </table>
                
            </div>

            <!--titulat, lote e importe-->
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green;">
                
                <?php
                    echo "Recibimos de <b>"."Juan Perez"."</b>"." (D.N.I. ".number_format(32150785,0,',','.').")<br>";
                    
                   
                        echo "La suma de ".strtoupper("veinticinco mil setecientos ochenta pesos")."<br>";
                    
                    
                    echo "Lote: "."<b>"."LI1560"."</b> - Loteo: "."<b>"."Libertad"."</b><br>";
                ?>
                <div style="">
                    
                </div>
                
            </div>
            
            <!-- codigo e importe -->
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-top: 4px;">
                
                <?php
                
                    
                    echo "COD 1 ------------------------------$2590<br>";
                    echo "COD 2 ------------------------------$2590<br>";
                    echo "COD 3 ------------------------------$2590<br>";
                
                ?>
                
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
                    </tbody>
                </table>

                
            </div>
            <!--pie de pagina-->
            
            <div class="zone" style=" vertical-align: middle; text-align: justify; margin-top: 4px;">             
            </div>
        </div>
         
    </div>    
     
</page>
