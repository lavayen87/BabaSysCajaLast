<?php

date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$num_recibo = $_POST['num_recibo'];

include('conexion.php');
include('funciones.php');

$fecha = date('Y-m-d');
$ultimo_ingreso = 0;
$monto = 0;
$monto_serv = 0;

// Consigo el iporte del recibo 
$sql = "SELECT importe FROM recibo 
        WHERE numero = '$num_recibo'
        AND fecha = '$fecha'";

$res = mysqli_query($connection, $sql);
$dato = mysqli_fetch_array($res);
$importe = $dato['importe'];

// Actualizo el estado del recibo
$update = "UPDATE recibo 
           SET estado = 0
           WHERE numero = '$num_recibo'
           AND fecha = '$fecha'";

mysqli_query($connection, $update);


// Actualizo el ingresos por servicios
$update_serv = "UPDATE ingresos_servicios 
           SET importe = (importe - '$importe')
           WHERE numero_caja = '$numero_caja'
           AND fecha = '$fecha'";

mysqli_query($connection, $update_serv);

// Actualizamos la ficha del cliente:

// Consigos los conceptos usados en el recibo (COD001,COD002,...,DOD00N)
$sql_cod = "SELECT codigo,lote FROM det_recibo 
            WHERE numero = '$num_recibo'
            AND fecha = '$fecha'";

$res_cod = mysqli_query($connection, $sql_cod);

while ($cod = mysqli_fetch_array($res_cod))
{
    $lote = $cod['lote'];
    switch($cod['codigo'])
    {
        case '001': $servicio = "Agrimensor"; break;
        case '002': $servicio = "Agua"; break;
        case '003': $servicio = "Cloacas"; break;
        case '004': $servicio = "Red de Cloacas"; break;
        case '005': $servicio = "Desmalezado"; break; 
    }
    if($servicio == 'Red de Cloacas'){
        $update = "UPDATE det_servicio 
                SET fecha_pago = '0000-00-00',
                fecha_abonado = '0000-00-00',
                recibo = 0,
                forma_pago = ''
                WHERE (servicio = '$servicio')
                AND (lote = '$lote')";  
    }
    else
    {
        $update = "UPDATE det_servicio 
               SET fecha_pago = '0000-00-00',
               fecha_solicitud = '0000-00-00',
               estado = '',
               recibo = 0
               WHERE (servicio = '$servicio')
               AND (lote = '$lote')";
    }
    
    mysqli_query($connection, $update);
    
}
 

// Actualizamos el saldo en pesos

// consigo saldo anterior en pesos, dolares, euros y cheques
$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);

//Consigo total del dia en pesos desde mi caja

$pesos_hoy = get_total(1,$numero_caja,$fecha);

//Consigo total del dia en dolares desde mi caja
  
$dolares_hoy = get_total(2,$numero_caja,$fecha);

//Consigo total del dia en euros desde mi caja
  
$euros_hoy = get_total(3,$numero_caja,$fecha);

//Consigo total del dia en cheques desde mi caja
  
$cheques_hoy = get_total(4,$numero_caja,$fecha);

//cargo la tabla de totales generales:

// ultima cobranza
$qry = "SELECT  importe from cobranza
    WHERE fecha = '$fecha' 
    AND numero_caja = '$numero_caja'
    order by numero limit 1";
$res = mysqli_query($connection, $qry);
$datos2 = mysqli_fetch_array($res);
$ultimo_cobro = $datos2['importe'];

if($ultimo_cobro<>[]){
    $monto = $ultimo_cobro;
}

// ultimo ingreso por servicios
$ultimo_ingreso = 0;
$qry2 = "SELECT  importe from ingresos_servicios
         WHERE fecha = '$fecha' 
         AND numero_caja = '$numero_caja'
         order by id DESC limit 1";
$res2 = mysqli_query($connection, $qry2);
$datos_ingresos = mysqli_fetch_array($res2);
$ultimo_ingreso = $datos_ingresos['importe'];

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
}

mysqli_close($connection);
echo 1;
?>