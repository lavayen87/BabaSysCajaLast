<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

// Obtenemos datos del cliente:
$datos_cliente = json_decode($_POST['datos_cliente']);
$nombre = $datos_cliente[0];
$telefono = $datos_cliente[1];
$loteo = $datos_cliente[2];
$lote = $datos_cliente[3];
/* --------------------------------------------------- */

// Datos de servicios: 
$datos_servicios = json_decode($_POST['datos_servicios']);
$cant = count($datos_servicios);
$fecha_pago = date('Y-m-d');

include('conexion.php');

$let = strtoupper(substr($lote,0,2)); // los dos primeros caracteres
$lote = strtoupper(substr($lote,0,2)).substr($lote,2,(strlen($lote)-2)); // codigo completo


//$tam = count($servicios); cantidad de datos del array

// verificamos que el recibo no exista

/*$qry = "SELECT * FROM det_servicio
        WHERE recibo = '$recibo'";
$res = mysqli_query($connection, $qry);

if($res->num_rows>0){
    echo 0;
}
else
{*/
    $qry = "INSERT IGNORE INTO clientes VALUES ('', '$nombre', '$telefono', '$lote')";
    $res = mysqli_query($connection, $qry);

    $get_id_cliente = "SELECT id_cliente FROM clientes
                    ORDER BY id_cliente DESC LIMIT 1";
    $res = mysqli_query($connection, $get_id_cliente);
    $dato_id_cliente = mysqli_fetch_assoc($res);
    $id_cliente = $dato_id_cliente['id_cliente'];

    $c = 1;
    $j = 0; 
        
    while($c <= 4)
    {
        if($j <> 15)
        {
            $servicio = $datos_servicios[$j];
            $recibo   = $datos_servicios[$j+1];
            $fecha_pago = $datos_servicios[$j+2];
            $fecha_solicitud = $datos_servicios[$j+3];
            $estado   = $datos_servicios[$j+4];
            $forma_pago = "";
            $fecha_realizado = '0000-00-00';
            $fecha_abonado = '0000-00-00';
        }
        else
        {
            //$forma_pago = $datos_servicios[$j+3];
            $servicio = $datos_servicios[$j];
            $recibo   = $datos_servicios[$j+1];
            $fecha_pago = $datos_servicios[$j+2];
            $fecha_solicitud = "";
            $estado   = "";
            $forma_pago = $datos_servicios[$j+3];
            $fecha_realizado = '0000-00-00';
            $fecha_abonado = '0000-00-00';
        }
            
        /*$servicio = $datos_servicios[$j];
        $recibo   = $datos_servicios[$j+1];
        $fecha_pago = $datos_servicios[$j+2];
        $fecha_solicitud = $datos_servicios[$j+3];
        $estado   = $datos_servicios[$j+4];
        $fecha_realizado = '0000-00-00';
        $fecha_abonado = '0000-00-00';*/
           
        $insert_agr = "INSERT IGNORE INTO det_servicio VALUES 
        ('',
        '$id_cliente',
        '$loteo',
        '$lote',
        '$servicio',
        '$fecha_pago',
        '$recibo',
        '$estado',
        '$fecha_solicitud',
        '$fecha_realizado',
        '$fecha_abonado',
        '$forma_pago')";

        $res_agr = mysqli_query($connection, $insert_agr);

        $c++;
        $j+=5;
    }
       
    
    echo 1;
//}


/*if ( $tam > 1 )
{
    for ($i = 0; $i < $tam; ++$i)
    {
        switch($servicio[$i])
        {
            case 'Agrimensor':


            // cargo datos en tabla agrimensor

            $insert_agr = "INSERT INTO agrimensor VALUES 
            ('','$lote','$fecha','$recibo','Solicitado','$fecha','','')";

            $res_agr = mysqli_query($connection, $insert_agr);
            
            break;

            case 'Agua':
            
                // cargo datos en tabla agua

            $insert_agu = "INSERT INTO agua VALUES 
            ('','$lote','$fecha','$recibo','Solicitado','$fecha','','')";

            $res_agu = mysqli_query($connection, $insert_agu);

            break;

            case 'Cloacas':

                // cargo datos en tabla cloacas

                $insert_clo = "INSERT INTO cloacas VALUES 
                ('','$lote','$fecha','$recibo','Solicitado','$fecha','','')";

                $res_clo = mysqli_query($connection, $insert_clo);

                break;
        
            case 'Red Cloacas':

                // cargo datos en tabla red cloacas

                $insert_red = "INSERT INTO red_cloacas VALUES 
                ('','$lote','$fecha','$recibo','Solicitado','$fecha','','','Financiado')";

                $res_red = mysqli_query($connection, $insert_red);

            
        }    
    }
}
else{
    switch($servicio[0])
    {
        case 'Agrimensor':


        // cargo datos en tabla agrimensor
        
        $insert_agr = "INSERT INTO agrimensor VALUES 
        ('','$lote','$fecha','$recibo','Solicitado','$fecha','','')";

        $res_agr = mysqli_query($connection, $insert_agr);
        
        break;

        case 'Agua':
        
            // cargo datos en tabla agua

        $insert_agu = "INSERT INTO agua VALUES 
        ('','$lote','$fecha','$recibo','Solicitado','$fecha','','')";

        $res_agu = mysqli_query($connection, $insert_agu);

        break;

        case 'Cloacas':

            // cargo datos en tabla cloacas
            
            $insert_clo = "INSERT INTO cloacas VALUES 
            ('','$lote','$fecha','$recibo','Solicitado','$fecha','','')";

            $res_clo = mysqli_query($connection, $insert_clo);

            break;
    
        case 'Red Cloacas':

            // cargo datos en tabla red cloacas

            $insert_red = "INSERT INTO red_cloacas VALUES 
            ('','$lote','$fecha','$recibo','Solicitado','$fecha','','','Financiado')";

            $res_red = mysqli_query($connection, $insert_red);

        
    }
}
*/

/*if($nombre<>"" and $telefono<>"")
{
    $insert_cl = "INSERT INTO clientes VALUES ('','$nombre','$telefono','$lote')";
    $result_cl = mysqli_query($connection, $insert_cl);
}*/


?>