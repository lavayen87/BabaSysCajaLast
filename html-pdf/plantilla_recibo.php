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

    $lote = (string)strip_tags($_GET['lote']); // Dato enviados x url
    $num  = $_GET['concepto']; // Dato enviados x url
    $importe = $_GET['importe']; // Dato enviado x url

    if($num == 1)
    {
        $concepto = 'CONEXION DE AGUA';
    }
    else
    {
        if($num == 2) 
        {
            $concepto = 'CONEXION DE CLOACAS';
        }
        else $concepto = 'RED DE CLOACAS';
    }
    
    $fecha = date('Y-m-d');
    $hora = date('G').':'.date('i')." Hs.";
    $ultimo_cobro = 0;
    $monto = 0;
    $cobranza = 0;

    include('./conexion.php');
    include('./funciones.php');

    // Verificacion de recibo emitido
    $verific = "SELECT estado FROM recibo 
               WHERE lote = '$lote' 
               AND concepto = '$concepto'
               AND estado = 1";
    $res_verific = mysqli_query($connection, $verific);

    if($res_verific->num_rows > 0)
    {
        header('Location: ../../buscar_lotes_new.php');
        
    }
    else
    {
        $qry = "SELECT * FROM det_lotes WHERE lote = '$lote'";
        $res = mysqli_query($connection, $qry);
        $datos_cli = mysqli_fetch_array($res);
        $titular = $datos_cli['titular'];
        $dni = $datos_cli['dni'];
        $loteo = $datos_cli['loteo'];
        $lote  = $datos_cli['lote'];

        $insert = "INSERT IGNORE INTO recibo VALUES
        ('',
        '$fecha',
        '$titular',
        '$dni',
        '$loteo',
        '$lote',
        '$concepto',
        '$importe',
        1
        )";

        mysqli_query($connection, $insert);

        $get_num = "SELECT numero FROM recibo ORDER BY numero DESC LIMIT 1";
        $res_num = mysqli_query($connection, $get_num);
        $dato_num = mysqli_fetch_array($res_num);
        $num_recibo = $dato_num['numero'];

        if($num == 1)
        {
            $update = "UPDATE det_servicio 
                    SET fecha_pago = '$fecha',
                    fecha_solicitud = '$fecha',
                    estado = 'Solicitado',
                    recibo = '$num_recibo'
                    WHERE (servicio = 'Agrimensor')
                    AND lote = '$lote'";
            mysqli_query($connection, $update);
            
            $update = "UPDATE det_servicio 
                    SET fecha_pago = '$fecha',
                    fecha_solicitud = '$fecha',
                    estado = 'Solicitado',
                    recibo = '$num_recibo'
                    WHERE (servicio = 'Agua')
                    AND lote = '$lote'";
            mysqli_query($connection, $update);
        }
        else 
        {
            if($num == 2){
                $update = "UPDATE det_servicio 
                            SET fecha_pago = '$fecha',
                            fecha_solicitud = '$fecha',
                            estado = 'Solicitado',
                            recibo = '$num_recibo'
                            WHERE servicio = 'Cloacas' 
                            AND lote = '$lote'";
                mysqli_query($connection, $update);   
            }  
            else{
                $update = "UPDATE det_servicio 
                            SET fecha_pago = '$fecha',
                            fecha_solicitud = '$fecha',
                            estado = 'Solicitado',
                            recibo = '$num_recibo'
                            WHERE servicio = 'Red Cloacas' 
                            AND lote = '$lote'";
                mysqli_query($connection, $update);  
            }
            
        }
    
        // Actualizamos la cobranza
        $qry = "SELECT  importe from cobranza
                WHERE fecha = '$fecha' 
                AND numero_caja = '$numero_caja'
                order by numero limit 1";
        $res = mysqli_query($connection, $qry);
        $datos = mysqli_fetch_array($res);
    
        $ultimo_cobro = $datos['importe']; // consigo el ultimo cobro en caja cobranza
        if($ultimo_cobro <> [])
        {
            $cobranza = ($ultimo_cobro + $importe);
            $qry = "UPDATE cobranza SET importe = '$cobranza' 
                            WHERE fecha = '$fecha'
                            and numero_caja = '$numero_caja'";
            $res = mysqli_query($connection, $qry);

        }
        else{
            $cobranza = $importe;
            $qry = "INSERT INTO cobranza values('','$numero_caja','$fecha','$cobranza')";
            $res = mysqli_query($connection, $qry);
        }


        //Recalculamos la columna pesos
        $query_empty = "UPDATE caja_gral SET pesos = 0 
                        where numero_caja = '$numero_caja' 
                        AND operacion = 1
                        AND fecha = '$fecha'";
        $result_empty = mysqli_query($connection, $query_empty);// vacio la columna pesos

        $qr = "SELECT numero FROM caja_gral 
                where numero_caja = '$numero_caja' 
                AND operacion = 1
                AND fecha = '$fecha'";
        $res = mysqli_query($connection, $qr); // busqueda de numeros
        $cantidad = $res->num_rows; // cantidad de numeros obtenido

        if($cantidad > 0)
        {
            $k = 0;
            $lista = array();
            while ($r = mysqli_fetch_array($res))
            {
                $lista[$k] = $r['numero'];
                $k++;	// obtengo una lista con los numeros
            }

            $inicial = $lista[0];
        }

        // datos para actualizar columna pesos (la primer fila)
        $query_get_data = "SELECT * FROM caja_gral 
                        where numero_caja = '$numero_caja'
                        and operacion = 1
                        AND fecha = '$fecha'
                        ORDER BY numero asc LIMIT 1"; 
        $result_get_data = mysqli_query($connection, $query_get_data);
        $data = mysqli_fetch_array($result_get_data);
        if($data['ingreso'] > 0)
        {
            $pesos = $data['ingreso'];
            $update = "UPDATE caja_gral SET pesos = '$cobranza' + '$pesos'  
                    WHERE numero = '$inicial'";
            $result_update = mysqli_query($connection, $update); // actualizo la primer fila en el campo pesos
        }
        else
            if($data['egreso'] > 0)
            {
                $pesos = $data['egreso'];
                $update = "UPDATE caja_gral SET pesos = '$cobranza' - '$pesos'  
                        WHERE numero = '$inicial'";
                $result_update = mysqli_query($connection, $update);// actualizo la primer fila en el campo pesos
            }

        // Actualizamos el resto de las filas
        for($i=0; $i <= $cantidad; $i++)
        {
            if(($i+1) <= ($cantidad-1))
            {
                
                $n = $lista[$i+1]; // fila inferior
                $m = $lista[$i]; // fila superior
                $qry = "SELECT * FROM caja_gral
                        WHERE numero = '$n'
                        and numero_caja = '$numero_caja'"; 
                $res = mysqli_query($connection,$qry);
                $dta = mysqli_fetch_array($res);
                $ingreso = $dta['ingreso'];
                $egreso = $dta['egreso'];

                if($ingreso > 0)
                {
                    $qry = "SELECT * FROM caja_gral
                            WHERE numero = '$m'
                            and numero_caja = '$numero_caja'"; 
                    $res = mysqli_query($connection,$qry);
                    $dta = mysqli_fetch_array($res);
                    $pesos = $dta['pesos'];

                    $update = "UPDATE caja_gral SET pesos = '$pesos' + '$ingreso'  
                            WHERE numero = '$n' 
                            and numero_caja = '$numero_caja'
                            AND operacion = 1
                            AND fecha = '$fecha'";
                    $result_update = mysqli_query($connection, $update); // actualizo las filas (campo pesos)
                }
                else
                    if($egreso > 0)
                    {
                        $qry = "SELECT * FROM caja_gral
                                WHERE numero = '$m'
                                and numero_caja = '$numero_caja'"; 
                        $res = mysqli_query($connection,$qry);
                        $dta = mysqli_fetch_array($res);
                        $pesos = $dta['pesos'];

                        $update = "UPDATE caja_gral SET pesos = '$pesos' - '$egreso' 
                                WHERE numero = '$n'
                                and numero_caja = '$numero_caja'
                                AND operacion = 1
                                AND fecha = '$fecha'";
                        $result_update = mysqli_query($connection, $update); // actualizo las filas (campo pesos)
                    }
            }

        }

        // Cargamos totales generales

        $saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha);
        $saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha);
        $saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha);
        $saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha);

        // consigo total pesos, dolares, euros y cheques del dia
        $pesos_hoy = get_total(1,$numero_caja,$fecha);
        $dolares_hoy = get_total(2,$numero_caja,$fecha);
        $euros_hoy = get_total(3,$numero_caja,$fecha);
        $cheques_hoy = get_total(4,$numero_caja,$fecha);

        $qry = "SELECT  importe from cobranza
                WHERE fecha = '$fecha' 
                AND numero_caja = '$numero_caja'
                order by numero limit 1";
        $res = mysqli_query($connection, $qry);
        $datos = mysqli_fetch_array($res);
        $ultimo_cobro = $datos['importe'];

        if( $ultimo_cobro<>[] )
        { 
            $monto = $ultimo_cobro;
        }

        if( ($pesos_hoy<>[]) && ($monto>=0) )
        {
            $total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
        }
        else
            if( ($pesos_hoy==[]) && ($monto>=0) )
            {
                $total_gral_pesos = ($saldo_anterior + $monto);
            }
            else $total_gral_pesos = $saldo_anterior;

        $total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
        $total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
        $total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);


        $qry = "SELECT * from caja_gral_temp
                where operacion = 1 
                and numero_caja = '$numero_caja'
                and fecha = '$fecha'	
                order by numero desc limit 1";    
        $res = mysqli_query($connection, $qry);

        if($res->num_rows>0)
        {
            $set = "UPDATE caja_gral_temp
            SET pesos = '$total_gral_pesos',
            dolares = '$total_gral_dolares',
            euros = '$total_gral_euros',
            cheques = '$total_gral_cheques'
            WHERE numero_caja = '$numero_caja'
            and fecha = '$fecha'
            and operacion = 1";
            $res = mysqli_query($connection, $set);
        }
        else{
            $insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
            ('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

            $result_insert = mysqli_query($connection, $insert);
        }

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
                            <!--div style="border:1px solid green; text-align: center; width: 15px; margin-left: 135px; margin-top:5px;">X</div-->
                            <?php
                                /*echo "<strong>ORIGINAL</strong>
                                      <br><br>
                                      <div style='border:1px solid green; width: 15px; margin:0px auto;'>
                                        X
                                      </div>";*/
                            ?>
                        </td>
                         
                        <td style="width: 243px; text-align: right;">
                            <?php
                                echo "<strong>RECIBO Nº: ".$num_recibo."</strong>
                                      <br>
                                      Fecha: ".fecha_min($fecha)." - ".$hora."";
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
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green;">
                
                <?php
                    echo "Recibimos de <b>".$titular."</b>"." (D.N.I. ".number_format($dni,0,',','.').")<br>";
                    echo "La suma de ".$texto1." ".$texto2."<br>";
                    echo "Lote: "."<b>".$lote."</b> - Loteo: "."<b>".$loteo."</b><br>";
                ?>
                <div style="">
                    
                </div>
                
            </div>
            
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-top: 4px;">
                <table style='width: 100%;'>
                    <thead>
                    <tr>
                        <th style='width: 85%;'>Concepto</th>
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
                </table>
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
                <div style="width: 100%; padding-top: 120px;">
                    <div style="height: 25px; background: #CAE5E2; text-align: right;"><?php echo "Fecha de impresion: ".fecha_min($fecha);?></div>
                </div>
            </div>
        </div>

        <!-- ESPACIO -->
        <?php
            if(strlen($texto1." ".$texto2)>95){
                echo "<br>
                      <br>
                      <br>
                      <br>";
            }
            else{
                echo "<br>
                      <br>
                      <br>
                      <br>
                      <br>
                      <br>";
            }
        ?>
        

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
                            <div style="text-align: center;"><strong>DUPLICADO</strong></div>
                            <br>
                            <div>
                                <div style="border:1px solid green; width: 15px; margin-left: 140px; text-align:center;">X</div>
                            </div>
                            <?php
                                /*echo "<strong>DUPLICADO</strong>
                                      <br><br>
                                      <div style='border:1px solid green; width: 15px; margin:0px auto;'>
                                        X
                                      </div>";*/
                            ?>
                        </td>
                         
                        <td style="width: 243px; text-align: right;">
                            <?php
                                echo "<strong>RECIBO Nº: ".$num_recibo."</strong>
                                      <br>
                                      Fecha: ".fecha_min($fecha)." - ".$hora."";
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
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green;">
                
                <?php
                    echo "Recibimos de <b>".$titular."</b>"." (D.N.I. ".number_format($dni,0,',','.').")<br>";
                    echo "La suma de ".$texto1." ".$texto2."<br>";
                    echo "Lote: "."<b>".$lote."</b> - Loteo: "."<b>".$loteo."</b><br>";
                ?>
                <div style="">
                    
                </div>
                
            </div>
            
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green; margin-top: 4px;">
                <table style='width: 100%;'>
                    <thead>
                    <tr>
                        <th style='width: 85%;'>Concepto</th>
                        <th style='width: 15%; text-align: right;'>Importe</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style='width: 85%;'><?php echo $concepto;?></td>
                        <td style='width: 15%; text-align: right;'><?php echo "$".number_format($importe,2,',','.');?></td>
                    </tr>
                    <tr style='border-collapse: collapse;'>
                        <td style='width: 85%; text-align: right; border-top:1px solid green;'><b>Total</b></td>
                        <td style='width: 15%; text-align: right; border-top:1px solid green;'><b><?php echo "$".number_format($importe,2,',','.');?></b></td>
                    </tr>
                    </tbody>
                </table>
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
            <div style="width: 100%; padding-top: 120px;">
            <div style="height: 25px; background: #CAE5E2; text-align: right;"><?php echo "Fecha de impresion: ".fecha_min($fecha);?></div>
                </div>
            </div>
        </div>
         
    </div>    
     
</page>
