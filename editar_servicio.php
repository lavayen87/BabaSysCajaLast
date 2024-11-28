<?php  
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}


$lote = $_POST['lote'];
$estado = $_POST['estado'];
/*$fecha_solicitud = $_POST['fecha_solicitud'];
$fecha_realizado = $_POST['fecha_realizado'];*/
$fecha_abonado = $_POST['fecha_abonado'];
$servicio = json_decode($_POST['servicio']);
$fecha = date('Y-m-d');

//echo $servicio[0];exit;
include('conexion.php');

$let = strtoupper(substr($lote,0,2)); 
$tam = count($servicio);

if ( $tam > 1 ){
    for ($i = 0; $i < $tam; ++$i)
    {
        switch( $servicio[$i])
        {
            case 'Agrimensor':

                // editamos en tabla agrimensor
                if( $let != 'BC')
                {
                    $fecha_abonado = '0000-00-00';
                }

                $edit = "UPDATE agrimensor 
                            set estado = '$estado',
                            fecha_solicitud = '0000-00-00',
                            fecha_realizado = '0000-00-00',
                            fecha_abonado = '$fecha_abonado'
                            where lote = '$lote'";
                    
                $res = mysqli_query($connection, $edit);
                break;
            
            case 'Agua':

                //editamos en tabla Agua 
                if( $let != 'BC')
                {
                    $fecha_abonado = '0000-00-00';
                }

                $edit = "UPDATE agua
                            set estado = '$estado',
                            fecha_solicitud = '0000-00-00',
                            fecha_realizado = '0000-00-00',
                            fecha_abonado = '$fecha_abonado'
                            where lote = '$lote'";
                        
                $res = mysqli_query($connection, $edit);  
                break;

            case 'Cloacas':

                // editamos en tabla cloacas

                if( $let != 'BC')
                {
                    $fecha_abonado = '0000-00-00';
                }

                $edit = "UPDATE cloacas 
                            set estado = '$estado',
                            fecha_solicitud = '0000-00-00',
                            fecha_realizado = '0000-00-00',
                            fecha_abonado = '$fecha_abonado'
                            where lote = '$lote'";
                    
                $res = mysqli_query($connection, $edit);
                break;
            
            case 'Red Cloacas':

                // editamos en tabla red cloacas

                if( $let != 'BC')
                {
                    $fecha_abonado = '0000-00-00';
                }

                $edit = "UPDATE red_cloacas 
                            set estado = '$estado',
                            fecha_solicitud = '0000-00-00',
                            fecha_realizado = '0000-00-00',
                            fecha_abonado = '$fecha_abonado'
                            where lote = '$lote'";
                    
                $res = mysqli_query($connection, $edit);
                break;
        } 
        
       
    }
    echo 1;
    
}
else{
    switch( $servicio[0])
    {
        case 'Agrimensor':

            // editamos en tabla agrimensor
            if( $let != 'BC')
            {
                $fecha_abonado = '0000-00-00';
            }

            $edit = "UPDATE agrimensor 
                        set estado = '$estado',
                        fecha_solicitud = '0000-00-00',
                        fecha_realizado = '0000-00-00',
                        fecha_abonado = '$fecha_abonado'
                        where lote = '$lote'";
                    
            $res = mysqli_query($connection, $edit);
            break;
            
        case 'Agua':

            //editamos en tabla Agua 
            if( $let != 'BC')
            {
                $fecha_abonado = '0000-00-00';
            }

            $edit = "UPDATE agua
                    set estado = '$estado',
                    fecha_solicitud = '0000-00-00',
                    fecha_realizado = '0000-00-00',
                    fecha_abonado = '$fecha_abonado'
                    where lote = '$lote'";
                        
            $res = mysqli_query($connection, $edit);  
            break;

        case 'Cloacas':

            // editamos en tabla cloacas

            if( $let != 'BC')
            {
                $fecha_abonado = '0000-00-00';
            }

            $edit = "UPDATE cloacas 
                        set estado = '$estado',
                        feca_solicitud = '0000-00-00',
                        fecha_realizado = '0000-00-00',
                        fecha_abonado = '$fecha_abonado'
                        where lote = '$lote'";
                    
            $res = mysqli_query($connection, $edit);
            break;
            
        case 'Red Cloacas':

            // editamos en tabla red cloacas

            if( $let != 'BC')
            {
                $fecha_abonado = '0000-00-00';
            }

            $edit = "UPDATE red_cloacas 
                        set estado = '$estado',
                        fecha_solicitud = '0000-00-00',
                        fecha_realizado = '0000-00-00',
                        fecha_abonado = '$fecha_abonado'
                        where lote = '$lote'";
                    
            $res = mysqli_query($connection, $edit);
            break;
    }

    echo 1;
}


?>