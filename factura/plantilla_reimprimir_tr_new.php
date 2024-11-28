

<?php
date_default_timezone_set("America/Argentina/Salta");
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$nombre = $_SESSION['nombre'];
	$rol = $_SESSION['rol'];
	$numero_caja_origen = $_SESSION['numero_caja'];
}

$num_tr = $_GET['num_tr']; // Dato enviado por post

include('../conexion.php');
include('../funciones.php');
require "../conversor.php";

// Datos de la transferencia
$query = "SELECT * from transferencias
          WHERE numero_tr = '$num_tr'     
          order by numero_tr desc limit 1";    
$result = mysqli_query($connection, $query);    
$datos = mysqli_fetch_array($result);

$numero_tr = $datos['numero_tr'];
$fecha = $datos['fecha']; 
$hora = date('G').':'.date('i').':'.date('s');
$numero_caja_origen = $datos['numero_caja_origen'];
$nombre_caja_origen = $datos['nombre_caja_origen'];
$nombre_caja_destino = $datos['nombre_caja_destino'];
$numero_caja_destino = $datos['numero_caja_destino'];
$moneda = $datos['moneda'];
$detalle = $datos['observaciones'];
$p = "";
$band = 0;
// Datos de cheques
$qry = "SELECT t1.id_cheque, t1.banco, t1.num_cheque, t1.importe, t1.fecha_vto
        FROM cheques_cartera as t1
        WHERE t1.num_tr = '$numero_tr'
        GROUP BY t1.id_cheque";

$res_ch = mysqli_query($connection, $qry);

if($res_ch->num_rows > 0)
{

    $band = 1;

    while($datos_cheq = mysqli_fetch_array($res_ch))
    {
        $p.= "<p>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</p>";
    }
}
	
$aux = 0;
$texto1 = '';
$texto2 = '';
$findme = "CERO";
 

$aux = 0;
$texto1 = '';
$texto2 = '';
$findme = "CERO";

if($datos['pesos'] > 0)
{
    $cantidad = '$'.number_format($datos['pesos'],2,',','.');
    //$cantidad = '$'.$datos['pesos'];
    $aux = $datos['pesos'];
    
    if( parte_entera(strval($aux)) <> 0){	
        $texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
        $pos = strpos($texto1, $findme)."</br>";		
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
else
{
    if($datos['dolares'] > 0)
    {
        $cantidad = '$US '.$datos['dolares'];
        $aux = $datos['dolares'];	
        if( parte_entera(strval($aux)) <> 0)
        {	
            $texto1 = convertir(parte_entera(strval($aux))).' '."DOLARES";
        }
        //$texto1 = convertir(parte_entera(strval($aux))).' '." DOLARES";	
    }	       
    else 
    {
        if($datos['euros'])
        {
            $cantidad = '€'.$datos['euros'];
            $aux = $datos['euros'];	
            $texto1 = convertir(parte_entera(strval($aux))).' '." EUROS";
        }
        else
            $cantidad = '$'.number_format($datos['cheques'],2,',','.');
            //$cantidad = '$'.$datos['pesos'];
            $aux = $datos['cheques'];
            
            if( parte_entera(strval($aux)) <> 0){	
                $texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
                $pos = strpos($texto1, $findme)."</br>";		
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
}	
mysqli_close($connection);

	
?>

<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Transferencia</title>
<link rel="stylesheet" href="new-style-recibo.css">
<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<style>
	@page { margin: 4px; } 
	body { 
		margin-top: 30px;
		margin-left: 2px;
		margin-right: 2px;
		margin-bottom: 4px; 
	} 
	.cabecera{
		margin:0px;
		width: 100%;
		height:65px;
		overflow: hidden;
	}     

	#contenedor{
		width: 100%;
		height: 43%;
		border: 1px solid green;
		overflow:hidden;
		border-radius: 8px;
	}
	
	#1{
	float: left;
	width: 48%;
	height: 160px;
	margin-left: 3px; 
	
	}
	
	#2{
	display: inline-block;
	width: 100%;
	height: 250px;
	margin-left: 3px;
	text-align: left;
	
	}
	.date{
		width: 100%;
		height: 40px;
		border: 1px solid transparent;
		margin-left: 3px;
	}
	.importe{
		width: 100%;
		margin-left: 3px;
		margin-top: -90px;
	}

	.firma{
		width: 100%;
		margin-left: 3px;
		height: 50px;
		overflow: hidden;
	}

	p {
	font:  80% monospace;
	margin: 10px;
	}
</style>
</head>

<body>
<div id="contenedor">

    <div class="cabecera">
        <p style="float: left; width: 150px; height: 50px;">
            <img src="../img/baba-img.png" style="margin-left: 2px; width: 100px; height:50px;">
        </p>

        <p style="float: right; margin-top: 25px; width: 80%; padding-top: 0px; height: 30px; ">
           <strong style="margin-left: 160px;">TRANSFERENCIA</strong>  
        </p>
    </div>
    
	<hr>
    
	<div class="date">
        <p style="display: inline-block;">N°. de Tr: 
            <strong><?php echo $num_tr; ?></strong> 
        </p>

        <p style="display: inline-block; margin-left:140px;">
            Fecha: <strong><?php echo fecha_min($fecha); ?></strong>
        </p>

        <p style="display: inline-block;  margin-left:190px;">
        Hora: <strong><?php echo $hora; ?></strong>
        </p>
    </div>

    <hr>
    
    <div id="1">
		
		<p> Caja orígen: <strong><?php echo $nombre_caja_origen." (caja $numero_caja_origen)"; ?></strong></p>
		<p> Caja destino: <strong><?php echo $nombre_caja_destino." (caja $numero_caja_destino)"; ?></strong></p>
		<p><strong><?php echo 'Transferencia en '.$moneda; ?></strong></p>
		<p>Detalle:<strong><?php echo ' '.$detalle; ?></strong></p>
    </div>
    
    <div id="2">
        
        <?php
        if($band == 1)
            echo $p;
        ?>
    </div>

    <div class="importe">
    
        <p>
            Cantidad:<strong><?php echo ' '.$cantidad." "."($texto1 $texto2)"; ?></strong>
        </p>
   
    </div>

    <div class="firma">
    
		<p>
            <label for="" style="float: left;">Emitió</label>
			<label for="" style="margin-left: 220px;">Recibió(firma y aclaración)</label>
			<label for="" style="float: right;">Autorizó</label>
		</p>
    </div>
</div>

<br><br><br><br><!--  espacio  -->

<div id="contenedor">

    <div class="cabecera">
        <p style="float: left; width: 150px; height: 50px;">
            <img src="../img/baba-img.png" style="margin-left: 2px; width: 100px; height:50px;">
        </p>

        <p style="float: right; margin-top: 25px; width: 80%; padding-top: 0px; height: 30px; ">
           <strong style="margin-left: 160px;">TRANSFERENCIA</strong>  
        </p>
    </div>
    
	<hr>
    
	<div class="date">
        <p style="display: inline-block;">N°. de Tr: 
            <strong><?php echo $num_tr; ?></strong> 
        </p>

        <p style="display: inline-block; margin-left:140px;">
            Fecha: <strong><?php echo fecha_min($fecha); ?></strong>
        </p>

        <p style="display: inline-block;  margin-left:190px;">
        Hora: <strong><?php echo $hora; ?></strong>
        </p>
    </div>

    <hr>
    
    <div id="1">
		
		<p> Caja orígen: <strong><?php echo $nombre_caja_origen." (caja $numero_caja_origen)"; ?></strong></p>
		<p> Caja destino: <strong><?php echo $nombre_caja_destino." (caja $numero_caja_destino)"; ?></strong></p>
		<p><strong><?php echo 'Transferencia en '.$moneda; ?></strong></p>
		<p>Detalle:<strong><?php echo ' '.$detalle; ?></strong></p>
    </div>
    
    <div id="2">
        
        <?php
        if($band == 1)
            echo $p;
        ?>
    </div>

    <div class="importe">
    
        <p>
            Cantidad:<strong><?php echo ' '.$cantidad." "."($texto1 $texto2)"; ?></strong>
        </p>
   
    </div>

    <div class="firma">
    
		<p>
            <label for="" style="float: left;">Emitió</label>
			<label for="" style="margin-left: 220px;">Recibió(firma y aclaración)</label>
			<label for="" style="float: right;">Autorizó</label>
		</p>
    </div>
</div>
</body>
</html>