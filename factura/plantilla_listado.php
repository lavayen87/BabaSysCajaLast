
<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $nombre = $_SESSION['nombre']; 
  $rol = $_SESSION['rol'];
}

if($_GET['caja'])
{   
    $numero_caja = $_GET['caja'];
    include('../conexion.php');
    $qry = "SELECT nombre,rol FROM usuarios WHERE numero_caja = '$numero_caja'";
    $res = mysqli_query($connection, $qry);
    $datos = mysqli_fetch_array($res);
    $nombre_usuario = $datos['nombre'];
    $rol = $datos['rol'];
    mysqli_close($connection);
}
else 
    $nombre_usuario = $nombre;

if($_GET['f1'] && $_GET['f2']){
    $fecha1 = $_GET['f1'];
    $fecha2 = $_GET['f2'];
}
?>
 
<!DOCTYPE html>
<html>
<head>
<!--link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous"-->

<link rel="stylesheet" href="../css/table-style.css">
<style>
    @page { margin: 4px; } 
    body { margin: 4px; } 
</style>
</head>
<body>

<div style="background-color: #d6e9c6; width: 100%; height: 4%; padding: 8px 8px;">
    <div style="float: left;">
        <img src="img/baba-img2.png" style="height: 40px; width: 40px; padding-top: 8px;">
        <span style="display: inline-block; padding-bottom: 8px;">Baba Urbanizaciones </span>
        <span style="display: inline-block; padding-bottom: 8px; padding-left: 18%;">
            <?php echo $rol." (caja "."<label style='font-size: 13px;'>".$numero_caja."</label>".")"; ?>
        </span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            include('../conexion.php');
            include('../funciones.php');
            /*$get_fechas = "SELECT fecha FROM lista_temp 
                           WHERE numero_caja = '$numero_caja'";
            $res = mysqli_query($connection, $get_fechas);
            $n = $res->num_rows;
            if($n > 0)
            {
                $k = 0;
                $lista = array();
                while ($fechas = mysqli_fetch_array($res))
                {
                    $lista[$k] = $fechas['fecha'];
                    $k++; // obtengo una lista de fechas
                }

                $fecha1 = fecha_min($lista[0]);
                $fecha2 = fecha_min($lista[$n-1]);
                if($n > 1)
                {
                    echo "Listado de caja (".$fecha1." - ".$fecha2.")";
                }
                else echo "Listado de caja (".$fecha1." - ".$fecha1.")";
            }
            else{
                echo "Listado de caja (".fecha_min(date('Y-m-d'))." - ".fecha_min(date('Y-m-d')).")";
            }
            //$fecha1 = fecha_min($lista[0]);
            //$fecha2 = fecha_min($lista[$n-1]);
            /*if($n > 1)
            {
                echo "Listado de caja (".$fecha1." - ".$fecha2.")";
            }
            else echo "Listado de caja (".$fecha1." - ".$fecha1.")";*/
            echo "Listado de caja (".fecha_min($fecha1)." - ".fecha_min($fecha2).")";
        ?>
    </div>
</div>

<main class="">
<div class="form-group col-md-12">
	<div class="alert alert-success" role="alert">
    	<div class="container">
                 
            <?php
                /*if($n > 1){  
                               
                    $fecha1 = $lista[0];
                    $fecha2 = $lista[$n-1];
                }
                else{
                    
                    $fecha1 = date('Y-m-d');
                    $fecha2 = date('Y-m-d'); 
                } */   
                $p="<p>";
                $p.="<label style='float: left;'>Fecha: ".fecha_min(date('Y-m-d'))." - Hora: ".date('G').':'.date('i').':'.date('s')."</label>";
                $p.="<label style='float: right;'>Emitido por: ".$_SESSION['rol']." (caja ".$_SESSION['numero_caja'].")</label></p>"; 
                echo $p."<br>";
                
                include('../conexion.php');
               
                $saldo_anterior = 0.00;
                $saldo_anterior_dolares = 0;
                $saldo_anterior_euros = 0;
                $saldo_anterior_cheques = 0.00;
                $monto = 0.00;
                $dolares = "dolares";
                $euros   = "euros";
                $hoy = date('Y-m-d');
                $ing_pesos = 0;
                $ing_dolares = 0;
                $ing_euros = 0;
                $ing_cheques = 0;
                $total_ingresos = 0;
                $total_egresos = 0;
                $total_cobranza = 0;
                $tabla ="<table>
                         <thead>
                            <tr>
                            <td id='td-fecha'><strong>Fecha</strong>
                            </td>                   
                            <td><strong>Detalle</strong></td>";
                            if($numero_caja == 3){
                                $tabla.="<td><strong>Nº cheque</strong></td>";
                            }
                            $tabla.="<td><strong>Ingresos</strong></td>
                            <td><strong>Egresos</strong></td>
                            <td><strong>Pesos</strong></td>";
                            if($numero_caja <> 3){
                            $tabla.="<td><strong>Dolares</strong></td>
                            <td><strong>Euros</strong></td>
                            <td><strong>Cheques</strong></td>
                            </tr>
                         </thead>
                         <tbody>";
                            }
               

                if($fecha1 == $fecha2)
                {

                    // Consigo datos de cobranza diaria
                    $cob = "SELECT importe from cobranza
                            WHERE fecha = '$fecha1'
                            AND numero_caja = '$numero_caja'
                            order by numero limit 1";
                    $res_cob = mysqli_query($connection, $cob);
                    $datos_cob = mysqli_fetch_array($res_cob);


                    $monto = $res_cob->num_rows > 0 ? $datos_cob['importe'] : 0;// $datos_cob['importe'];

                    // Consigo total servicios
                    $sql = "SELECT importe FROM ingresos_servicios
                            WHERE numero_caja = '$numero_caja'
                            AND fecha = '$fecha1'
                            order by id Desc Limit 1";
                    $sql_res = mysqli_query($connection, $sql);

                    if($sql_res->num_rows > 0){
                        $datos_servicios = mysqli_fetch_array($sql_res);
                        $monto_serv = $datos_servicios['importe'];
                    }
                    else $monto_serv = 0;

                    //Saldo anterior en pesos, dolares y euros
                    $saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha1);
                    $saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha1);
                    $saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha1);
                    $saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha1);

                    // total de pesos
                    $ing_pesos = get_total(1,$numero_caja, $fecha1); 

                    //orignalmente la consulta era en lista_temp
                    $query = "SELECT sum(ingreso) + (-1)*sum(egreso) as total_dia FROM caja_gral
                              WHERE numero_caja = '$numero_caja'
                              AND fecha BETWEEN '$fecha1' AND '$fecha1'
                              AND operacion = 1
                              AND anulado = 0";

                    $res = mysqli_query($connection, $query);
                    $datos = mysqli_fetch_array($res);
                    $total_dia = $datos['total_dia'];

                    // total de dolares
                    $ing_dolares=get_total(2,$numero_caja, $fecha1);
                    $query_dolares = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                        WHERE numero_caja = '$numero_caja'
                                        AND fecha BETWEEN '$fecha1' AND '$fecha1'
                                        AND operacion = 2
                                        AND anulado = 0";                
                    $result_total = mysqli_query($connection, $query_dolares);
                    $total = mysqli_fetch_array($result_total);
                    $total_dolares = round($total['total']);

                    //total de euros 
                    $ing_euros=get_total(3,$numero_caja,$fecha1);
                    $query_euros = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                        WHERE numero_caja = '$numero_caja'
                                        AND fecha BETWEEN '$fecha1' AND '$fecha1'
                                        AND operacion = 3
                                        AND anulado = 0";                
                    $result_total = mysqli_query($connection, $query_euros);
                    $total = mysqli_fetch_array($result_total);
                    $total_euros = round($total['total']);

                    //total de cheques 
                    $ing_cheques=get_total(4,$numero_caja, $fecha1);
                    $query_euros = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                        WHERE numero_caja = '$numero_caja'
                                        AND fecha BETWEEN '$fecha1' AND '$fecha1'
                                        AND operacion = 4
                                        AND anulado = 0";                
                    $result_total = mysqli_query($connection, $query_euros);
                    $total = mysqli_fetch_array($result_total);
                    $total_cheques = $total['total'];

                    // total de ingresos y egresos
                    $total_ingresos = total_ingresos(1,$numero_caja,$fecha1);
                    $total_egresos = total_egresos(1,$numero_caja,$fecha1);

                    $qry = "SELECT * FROM caja_gral
                            WHERE numero_caja = '$numero_caja'
                            and fecha BETWEEN '$fecha1' AND '$fecha2'";

                    $res = mysqli_query($connection, $qry);
                    if($res)
                    {
                        if($numero_caja == 1 || $numero_caja == 9 || $numero_caja == 10)
                        {
                            $total_cobranza = total_cobranza($numero_caja,$fecha1,$fecha2);
                            $tabla.= "<tr style='border-bottom: 1px solid black;'>                                     
                                    <td colspan='8'><strong>Total cobrado: "."$".number_format($total_cobranza,2,',','.')."</strong></td>
                                    
                                    </tr>";
                        }
                        else
                            $total_cobranza = total_cobranza($numero_caja,$fecha1,$fecha2);

                        while($datos = mysqli_fetch_array($res))
                        {   
                           
                            $ingreso = $datos['anulado'] == 1 && $datos['ingreso'] > 0 ? "<s class='candeled'>".number_format($datos['ingreso'],2,',','.')."</s>" : number_format($datos['ingreso'],2,',','.');

                            $egreso = $datos['anulado'] == 1 && $datos['egreso'] > 0 ? "<s class='candeled'>".number_format($datos['egreso'],2,',','.')."</s>" : number_format($datos['egreso'],2,',','.');    

                            $tabla.="<tr>
                                <td style='width:6%;'>".fecha_min($datos['fecha'])."</td>
                                <td style='width:18%; font-size: 11px;'>".limitar_cadena(ucfirst(strtolower($datos['detalle'])),30)."</td>";

                                if($numero_caja == 3)
                                {
                                    $tabla.="<td style='width:7%;'>".$datos['n_cheque']."</td>";
                                }   

                                $tabla.="<td style='text-align: right; width: 11%''>".$ingreso."</td>
                                         <td style='text-align: right; width: 11%''>".$egreso."</td>
                                         <td style='text-align: right; width: 11%'>".number_format($datos['pesos'],2,',','.')."</td>";

                                if($numero_caja <> 3)
                                {
                                    $tabla.="<td style='text-align: right; width: 11%''>".number_format($datos['dolares'],2,',','.')."</td>
                                    <td style='text-align: right; width: 11%''>".number_format($datos['euros'],2,',','.')."</td>
                                    <td style='text-align: right; width: 11%''>".number_format($datos['cheques'],2,',','.')."</td>
                                    </tr>";
                                }
                        }
                        
                        if($numero_caja <> 3)
                        {
                            $tabla.="<tr>
                                <td></td>
                                <td style='text-align: center;'>"."<strong >Totales</strong>"."</td>
                                <td style='text-align: right;'>"."<strong>$".number_format($total_ingresos,2,',','.')."</td>
                                <td style='text-align: right;'>"."<strong>$".number_format($total_egresos,2,',','.')."</td>
                                <td style='text-align: right;'>"."<strong>$".number_format($ing_pesos,2,',','.')."</td>
                                <td style='text-align: right;'>"."<strong>US".round($ing_dolares)."</strong>"."</td>
                                <td style='text-align: right;'>"."<strong>€".round($ing_euros)."</strong>"."</td>
                                <td style='text-align: right;'>"."<strong>$".number_format($ing_cheques,2,',','.')."</td>
                                </tr>";

                            $tabla.="<tr><td colspan = '8' height='5px;'></td></tr>
                                <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>"."<strong>Sdo. anterior</strong> "."</td>
                                <td style='text-align: right;'>"."$".number_format($saldo_anterior,2,',','.')."</td>
                                <td style='text-align: right;'>"."US".round($saldo_anterior_dolares)."</td>
                                <td style='text-align: right;'>"."€".round($saldo_anterior_euros)."</td>
                                <td style='text-align: right;'>"."$".number_format($saldo_anterior_cheques,2,',','.')."</td>
                                </tr>";

                            if($numero_caja==1 || $numero_caja==9 || $numero_caja==10)
                            {
                                $cobranza = "<strong>Cobranza</strong>";
                                $monto_cobranza = "<strong> $".number_format($monto,2,',','.')."</strong>";
                                $servicios = "<strong>Servicios</strong>";
                                $monto_servicios = "<strong> $".number_format($monto_serv,2,',','.')."</strong>";
                            }
                            else
                            {
                                $cobranza = "";
                                $monto_cobranza = "";
                                $servicios = "";
                                $monto_servicios = "";
                            }

                            $tabla.="<tr>
                                <td>".$cobranza."</td>
                                <td>".$monto_cobranza."</strong>"."</td>
                                <td></td>
                                <td>"."<strong>Total del día </strong>"."</td>
                                <td style='text-align: right;'>"."$".number_format(($total_cobranza+$total_dia),2,',','.')."</td>
                                <td style='text-align: right;'>"."US".number_format($total_dolares,2,',','.')."</td>
                                <td style='text-align: right;'>"."€".$total_euros."</td>
                                <td style='text-align: right;'>"."$".number_format($total_cheques,2,',','.')."</td>
                                </tr>

                                <tr>
                                <td>".$servicios."</td>
                                <td>".$monto_servicios."</strong></td>
                                <td></td>
                                <td>"."<strong>Total gral. </strong>"."</td>
                                <td style='text-align: right;'>"."$".number_format(($saldo_anterior+$total_cobranza+$total_dia),2,',','.')."</td>
                                <td style='text-align: right;'>"."US".number_format(($saldo_anterior_dolares+$total_dolares),2,',','.')."</td>
                                <td style='text-align: right;'>"."€".($saldo_anterior_euros+$total_euros)."</td>
                                <td style='text-align: right;'>"."$".number_format(($saldo_anterior_cheques+$total_cheques),2,',','.')."</td>
                                    </tr>";
                                $tabla.="</tbody>";
                        }
                        else
                        {
                            $tabla.="</tbody>";
                        }
                        echo $tabla;
                    }

                }
                else // caso en que las fechas sean distintas
                { 
                    if($fecha1 < $fecha2)
                    {
                        // Consigo datos de cobranza diaria
                        $cob = "SELECT sum(importe) as total_importe from cobranza
                                WHERE fecha BETWEEN '$fecha1' AND '$fecha2'
                                AND numero_caja = '$numero_caja'";
                        $res_cob = mysqli_query($connection, $cob);
                        $datos_cob = mysqli_fetch_array($res_cob);

                        $monto = $res_cob->num_rows > 0 ? $datos_cob['total_importe'] : 0;//$datos_cob['total_importe'];

                        // Consigo total servicios
                        $sql = "SELECT sum(importe) as total_serv FROM ingresos_servicios
                                WHERE numero_caja = '$numero_caja'
                                AND fecha BETWEEN '$fecha1' AND '$fecha2'";
                        $sql_res = mysqli_query($connection, $sql);

                        if($sql_res->num_rows > 0){
                            $datos_servicios = mysqli_fetch_array($sql_res);
                            $monto_serv = $datos_servicios['total_serv'];
                        }
                        else $monto_serv = 0;
                        
                        //Saldo anterior en pesos, dolares, euros y cheques
                        $saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha1);
                        $saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha1);
                        $saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha1);
                        $saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha1);
                        

                        $qry = "SELECT * FROM caja_gral
                                WHERE numero_caja = '$numero_caja'
                                and fecha BETWEEN '$fecha1' AND '$fecha2'";

                        $res = mysqli_query($connection, $qry);
                        if($res)
                        {
                            if($numero_caja == 1 || $numero_caja == 9 || $numero_caja == 10)
                            {
                              $total_cobranza = total_cobranza($numero_caja,$fecha1,$fecha2);
                              $tabla.= "<tr style='border-bottom: 1px solid black;'>                                     
                                        <td colspan='8'><strong>Total cobrado: "."$".number_format($total_cobranza,2,',','.')."</strong></td>                                    
                                        </tr>";
                            }
                            else
                                $total_cobranza = total_cobranza($numero_caja,$fecha1,$fecha2);

                            while($datos = mysqli_fetch_array($res))
                            {       
                                $ingreso = $datos['anulado'] == 1 && $datos['ingreso'] > 0 ? "<s class='candeled'>".number_format($datos['ingreso'],2,',','.')."</s>" : number_format($datos['ingreso'],2,',','.');

                                $egreso = $datos['anulado'] == 1 && $datos['egreso'] > 0 ? "<s class='candeled'>".number_format($datos['egreso'],2,',','.')."</s>" : number_format($datos['egreso'],2,',','.');

                                $tabla.="<tr>
                                        <td style='width:6%;'>".fecha_min($datos['fecha'])."</td>
                                        <td style='width:18%; font-size: 11px;'>".limitar_cadena(ucfirst(strtolower($datos['detalle'])),30)."</td>";

                                if($numero_caja == 3)
                                {
                                    $tabla.="<td style='width:7%;'>".$datos['n_cheque']."</td>";
                                }

                                $tabla.="<td style='text-align: right; width: 11%''>".$ingreso."</td>
                                         <td style='text-align: right; width: 11%''>".$egreso."</td>
                                         <td style='text-align: right; width: 11%'>".number_format($datos['pesos'],2,',','.')."</td>";

                                if($numero_caja <> 3)
                                {
                                    $tabla.="<td style='text-align: right; width: 11%''>".number_format($datos['dolares'],2,',','.')."</td>
                                    <td style='text-align: right; width: 11%''>".number_format($datos['euros'],2,',','.')."</td>
                                    <td style='text-align: right; width: 11%''>".number_format($datos['cheques'],2,',','.')."</td>
                                    </tr>";
                                }
                            }

                            //orignalmente la consulta era en lista_temp
                            $ing_pesos=get_total2(1,$numero_caja,$fecha1,$fecha2); 
                            $query = "SELECT sum(ingreso) - sum(egreso) as total_dia FROM caja_gral
                                      WHERE numero_caja = '$numero_caja'
                                      AND fecha BETWEEN '$fecha1' AND '$fecha2' 
                                      AND anulado = 0
                                      AND operacion = 1";
                            $res = mysqli_query($connection, $query);
                            $datos = mysqli_fetch_array($res);
                            $total_dia = $datos['total_dia'];

                            // total de dolares
                            $ing_dolares=get_total2(2,$numero_caja,$fecha1,$fecha2); 
                            $query_dolares = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                               WHERE numero_caja = '$numero_caja'
                                               AND fecha BETWEEN 'fecha1' AND '$fecha2'
                                               AND anulado = 0
                                               AND operacion = 2";                
                            $result_total = mysqli_query($connection, $query_dolares);
                            $total = mysqli_fetch_array($result_total);
                            $total_dolares = round($total['total']);

                            //total de euros 
                            $ing_euros=get_total2(3,$numero_caja,$fecha1,$fecha2); 
                            $query_euros = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                                   WHERE numero_caja = '$numero_caja'
                                                   AND fecha BETWEEN 'fecha1' AND '$fecha2'
                                                   AND anulado = 0
                                                   AND operacion = 3";                
                            $result_total = mysqli_query($connection, $query_euros);
                            $total = mysqli_fetch_array($result_total);
                            $total_euros = round($total['total']);

                            //total de cheques 
                            $ing_cheques=get_total2(4,$numero_caja,$fecha1,$fecha2); 
                            $query_euros = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                                   WHERE numero_caja = '$numero_caja'
                                                   AND fecha BETWEEN 'fecha1' AND '$fecha2'
                                                   AND anulado = 0
                                                   AND operacion = 4";                
                            $result_total = mysqli_query($connection, $query_euros);
                            $total = mysqli_fetch_array($result_total);
                            $total_cheques = $total['total'];

                            // total de ingresos y egresos
                            $total_ingresos = total_ingresos2(1,$numero_caja,$fecha1,$fecha2);
                            $total_egresos = total_egresos2(1,$numero_caja,$fecha1,$fecha2);

                            if($numero_caja <> 3)
                            {
                            $tabla.="<tr>
                                    <td></td>
                                    <td style='text-align: center;'>"."<strong >Totales</strong>"."</td>
                                    <td style='text-align: right;'>"."<strong>$".number_format($total_ingresos,2,',','.')."</td>
                                    <td style='text-align: right;'>"."<strong>$".number_format($total_egresos,2,',','.')."</td>
                                    <td style='text-align: right;'>"."<strong>$".number_format($ing_pesos,2,',','.')."</td>
                                    <td style='text-align: right;'>"."<strong>US".round($ing_dolares)."</strong>"."</td>
                                    <td style='text-align: right;'>"."<strong>€".round($ing_euros)."</strong>"."</td>
                                    <td style='text-align: right;'>"."<strong>$".number_format($ing_cheques,2,',','.')."</td>
                                    </tr>";

                            
                            $tabla.="<tr><td colspan = '8' height='5px;'></td></tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>"."<strong>Sdo. anterior</strong> "."</td>
                                        <td style='text-align: right;'>"."$".number_format($saldo_anterior,2,',','.')."</td>
                                        <td style='text-align: right;'>"."US".round($saldo_anterior_dolares)."</td>
                                        <td style='text-align: right;'>"."€".round($saldo_anterior_euros)."</td>
                                        <td style='text-align: right;'>"."$".number_format($saldo_anterior_cheques,2,',','.')."</td>
                                    </tr>";
                            if($numero_caja==1 || $numero_caja==9 || $numero_caja==10)
                            {
                                $cobranza = "<strong>Cobranza</strong>";
                                $monto_cobranza = "<strong> $".number_format($monto,2,',','.')."</strong>";
                                $servicios = "<strong>Servicios</strong>";
                                $monto_servicios = "<strong> $".number_format($monto_serv,2,',','.')."</strong>";
                            }
                            else{
                                $cobranza = "";
                                $monto_cobranza = "";
                                $servicios = "";
                                $monto_servicios = "";
                            }
                                    
                            $tabla.="<tr>
                                        <td>".$cobranza."</td>
                                        <td>".$monto_cobranza."</td>
                                        <td></td>
                                        <td>"."<strong>Total del día </strong>"."</td>
                                        <td style='text-align: right;'>"."$".number_format(($total_cobranza+$total_dia),2,',','.')."</td>
                                        <td style='text-align: right;'>"."US".number_format($total_dolares,2,',','.')."</td>
                                        <td style='text-align: right;'>"."€".number_format($total_euros,2,',','.')."</td>
                                        <td style='text-align: right;'>"."$".number_format($total_cheques,2,',','.')."</td>
                                    </tr>
                                    <tr>
                                        <td>".$servicios."</td>
                                        <td>".$monto_servicios."</td>
                                        <td></td>
                                        <td>"."<strong>Total gral. </strong>"."</td>
                                        <td style='text-align: right;'>"."$".number_format(($saldo_anterior+$total_cobranza+$total_dia),2,',','.')."</td>
                                        <td style='text-align: right;'>"."US".number_format(($saldo_anterior_dolares+$total_dolares),2,',','.')."</td>
                                        <td style='text-align: right;'>"."€".number_format(($saldo_anterior_euros+$total_euros),2,',','.')."</td>
                                        <td style='text-align: right;'>"."$".number_format(($saldo_anterior_cheques+$total_cheques),2,',','.')."</td>
                                    </tr>";
                            $tabla.="</tbody>";
                            }
                            else{
                                $tabla.="</tbody>"; 
                            }
                            echo $tabla;
                        }
                    }

                }
                            
    		?>  			
             
    	</div>
    </div>
</div>
</div>
</body>
</html>

