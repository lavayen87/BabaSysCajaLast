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
    
    
    $fecha = date('Y-m-d');
    $datos = json_decode($_GET['datos']); 
    
    /*$precio = convert_two_digit(strval($datos->precio),3); //precio
    $anticipo = convert_two_digit(strval($datos->anticipo),3);  // anticipo
    $monto_afn = $datos->monto_afn; // monto_afn
    $diario_fn = $datos->diario_fn; // diario_fn
    $diario = convert_two_digit(strval($datos->diario),3); // diario
    $n_cuotas = $datos->n_cuotas; // n_cuotas
    $dias_fn = $datos->dias_fn; // dias_fn
    $interes_total = round(convert_two_digit(strval($datos->interes_total),3)); // interes total
    $monto_fdo = round(convert_two_digit(strval($datos->monto_fdo),3)); // monto_fdo*/
    $valor_cuota = round(convert_two_digit(strval($datos[0]),3)); // valor_cuota
    $total_op = round(convert_two_digit(strval($datos[1]),3)); // total operacion
    $loteo = $datos[2]; // opcion
    $lote = $datos[3];
    //$op = $datos->op; // opcion
    
    //echo print_r($datos);exit;
    
    //echo " Total operacion: ".$diario_fn; 
     
    $aux = 0;
    $texto1 = '';
    $texto2 = '';
    $aux2 = 0;
    $texto3 = '';
    $texto4 = '';
    $findme = "CERO";
    
    // Texto total operacion
    if($total_op > 0)
    {
        $cantidad = '$'.number_format($total_op,2,',','.');
        $aux = $total_op;
        
        if( parte_entera(strval($aux)) <> 0)
        {	
            $texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
            $pos = strpos($texto1, $findme);		
            if ($pos > 0)
            {
                $texto1 = str_replace($findme, "", $texto1);
            }
            
        }
        if( parte_decimal(strval($aux)) <> 0) 
        {	
    
            $texto1.= " CON ";		
            $texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
            $pos = strpos($texto2, $findme);
            if ($pos === true){
                $texto2 = str_replace($findme, "", $texto2);
            }
        }
        
    }	 
    
    // texto valor de la cuota
    if($valor_cuota > 0)
    {
        $cantidad2 = '$'.number_format($valor_cuota,2,',','.');
        $aux2 = $valor_cuota;
        
        if( parte_entera(strval($aux2)) <> 0)
        {	
            $texto3 = convertir(parte_entera(strval($aux2))).' '."PESOS";				
            $pos = strpos($texto3, $findme);		
            if ($pos > 0)
            {
                $texto3 = str_replace($findme, "", $texto3);
            }
            
        }
        if( parte_decimal(strval($aux2)) <> 0) 
        {	
    
            $texto3.= " CON ";		
            $texto4 = convertir(parte_decimal(strval($aux2)))." CENTAVOS";
            $pos = strpos($texto2, $findme);
            if ($pos === true){
                $texto4 = str_replace($findme, "", $texto4);
            }
        }
        
    }
    
            
        
        // Actualizamos ingresos por servicios:

        // consigo ultimo importe de cobranza
        /*$qry_cobranza = "SELECT  importe from cobranza
                WHERE fecha = '$fecha' 
                AND numero_caja = '$numero_caja'
                order by numero desc limit 1";
        $res_cobranza = mysqli_query($connection, $qry_cobranza);

        $datos_cobranza = mysqli_fetch_array($res_cobranza);

        if($datos_cobranza['importe']<>[])
        {
            $cobranza = $datos_cobranza['importe'];
        }
        else
            $cobranza = 0;

        $qry = "SELECT  importe from ingresos_servicios
                WHERE fecha = '$fecha' 
                AND numero_caja = '$numero_caja'
                order by id DESC limit 1";
        $res = mysqli_query($connection, $qry);
        $datos = mysqli_fetch_array($res);
    
        $ultimo_ingreso = $datos['importe']; // consigo el ultimo cobro en caja cobranza
        
        if($ultimo_ingreso <> [])
        {
            $total_servicios = ($ultimo_ingreso + $importe);
            $total_ingresos = ($cobranza + $ultimo_ingreso + $importe);
            $qry = "UPDATE ingresos_servicios SET importe = '$total_servicios' 
                    WHERE fecha = '$fecha'";
            $res = mysqli_query($connection, $qry);

        }
        else{
            $total_servicios = $importe;
            $total_ingresos = ($cobranza + $importe);
            $qry = "INSERT INTO ingresos_servicios values('','$fecha','$numero_caja','$total_servicios')";
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
            $update = "UPDATE caja_gral SET pesos = '$total_ingresos' + '$pesos'  
                    WHERE numero = '$inicial'";
            $result_update = mysqli_query($connection, $update); // actualizo la primer fila en el campo pesos
        }
        else
            if($data['egreso'] > 0)
            {
                $pesos = $data['egreso'];
                $update = "UPDATE caja_gral SET pesos = '$total_ingresos' - '$pesos'  
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

        // ultimo monto de cobranza
        $qry = "SELECT  importe from cobranza
                WHERE fecha = '$fecha' 
                AND numero_caja = '$numero_caja'
                order by numero limit 1";
        $res = mysqli_query($connection, $qry);
        $datos = mysqli_fetch_array($res);
        $ultimo_cobro = $datos['importe'];

        // ultimo ingreso por servicios
        $ultimo_ingreso = 0;
        $qry2 = "SELECT  importe from ingresos_servicios
                WHERE fecha = '$fecha' 
                AND numero_caja = '$numero_caja'
                order by id DESC limit 1";
        $res2 = mysqli_query($connection, $qry2);
        $datos_ingresos = mysqli_fetch_array($res2);
        $ultimo_ingreso = $datos_ingresos['importe'];

        if( $ultimo_cobro<>[])
        { 
            $monto = $ultimo_cobro;
        }

        if( $ultimo_ingreso<>[])
        { 
            $monto_serv = $ultimo_ingreso;
        }

        if( ($pesos_hoy<>[]) && ($monto>=0) && ($monto_serv)>=0)
        {
            $total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto + $monto_serv);
        }
        else
            if( ($pesos_hoy==[]) && ($monto>=0) && ($monto_serv)>=0)
            {
                $total_gral_pesos = ($saldo_anterior + $monto + $monto_serv);
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
        }*/

        /* ---------------------------------------------------------- */
 
    
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
                                echo "<strong>RECONOCIENTO DE DEUDA Y PLAN DE PAGO</strong>
                                      <br><br>
                                      <strong>RECIBO Nº: ".''."</strong>
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

            <!--titulat, lote e importe-->
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
                Por la presente, el/la Señor/a GOMEZ INES, D.N.I. Nº 35142110, comprador/a por boleto
                de compra-venta del lote Nº <strong><?php echo $lote;?></strong>, del loteo Buen 
                Clima, reconoce adeudar a Buen Clima S.R.L., la suma de 
                <strong>PESOS <?php echo "$texto1 $texto2 ($cantidad)";?></strong>
                en concepto de costo proporcional a las obras de red cloacal efecutadas en el loteo.
                Dicha suma será abomada en doce (12) cuotas mensuales, iguales y consecutivas de 
                <strong>PESOS <?php echo "$texto3 $texto4 ($cantidad2)";?></strong> cada una actualizable con el índice de la 
                construcción (ICC), con vencimiento la primera de ellas en fecha 10/01/2022 y así sucesivamente las posteriores.-- 
                </p>
                 
                <p align="justify">
                El importe reconocido no incluye el costo de la conexión de la red cloacal al domicilio del titular (conexión domiciliaria),
                el que deberá ser abonado por separado a Buen Clima S.R.L. (única autorizada a efectuar aquélla).
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
