

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


include('../conexion.php');
include('../funciones.php');
require "../conversor.php";
	
//datos de la orden de pago con cheque.
$query = "SELECT * from orden_pago 
          WHERE numero_caja = '$numero_caja'  
          order by numero_orden desc limit 1";    
$result = mysqli_query($connection, $query);    
$datos = mysqli_fetch_array($result);

$fecha_actual = date('Y-m-d'); // fecha actual

$numero_orden = $datos['numero_orden'];
$fecha = $datos['fecha']; 
$emisor = $datos['numero_caja'];
$empresa= $datos['empresa'];
$obra = $datos['obra'];
$cuenta = $datos['cuenta'];
$detalle = $datos['detalle'];
$importe = $datos['importe'];

//datos de los cheques usados para la orden de pago
$query = "SELECT * from cheques_cartera
          WHERE num_caja_origen = '$numero_caja'  
          and fecha_entrega = '$fecha_actual'
          and num_orden_pago= '$numero_orden'";   
$result = mysqli_query($connection, $query);    

$p = "";
$band = 0;

// Datos de cheques
$qry = "SELECT t1.id_cheque, t1.banco, t1.num_cheque, t1.importe, t1.fecha_vto
        FROM cheques_cartera as t1
        WHERE t1.num_orden_pago = '$numero_orden'
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

$hora = date('G').':'.date('i').':'.date('s');

$aux = 0;
$texto1 = '';
$texto2 = '';
$findme = "CERO";


if($datos['importe'] > 0)
{
    $cantidad = '$'.number_format($datos['importe'],2,',','.');
    $aux = $datos['importe'];

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

    $texto1.= " CON ";		
    $texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
    $pos = strpos($texto2, $findme);
    if ($pos === true){
    $texto2 = str_replace($findme, "", $texto2);
    }
}
/*if( parte_entera(strval($aux)) <> 0){	
$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";
}

if( parte_decimal(strval($aux)) <> 0) 
{	
$texto1 = convertir(parte_entera(strval($aux))).' '."PESOS CON ";		
$texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
}*/

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
<title>Orden de pago</title>
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
           <strong style="margin-left: 160px;">ÓRDEN DE PAGO</strong>  
        </p>
    </div>
    
	<hr>
    
	<div class="date">
        <p style="display: inline-block;">N°. de órden: 
            <strong><?php echo $numero_orden; ?></strong> 
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
        <p> Emitida por: <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
        <p>
			<label align="left">
				Empresa: 
				<strong><?php echo limitar_cadena($empresa,16);?></strong>
			</label>
			<label style="margin-left: 1px; margin-right: 1px;"> / </label>
			<label style="margin-left: 1px;">
				Obra: 
				<strong><?php echo limitar_cadena($obra,16);?></strong>
			</label>
		</p>
		<p> Cuenta: <strong><?php echo ' '.$cuenta;?></strong></p>
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
           <strong style="margin-left: 160px;">ÓRDEN DE PAGO</strong>  
        </p>
    </div>
    
	<hr>
    
	<div class="date">
        <p style="display: inline-block;">N°. de órden: 
            <strong><?php echo $numero_orden; ?></strong> 
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
        <p> Emitida por: <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
        <p>
			<label align="left">
				Empresa: 
				<strong><?php echo limitar_cadena($empresa,16);?></strong>
			</label>
			<label style="margin-left: 1px; margin-right: 1px;"> / </label>
			<label style="margin-left: 1px;">
				Obra: 
				<strong><?php echo limitar_cadena($obra,16);?></strong>
			</label>
		</p>
		<p> Cuenta: <strong><?php echo ' '.$cuenta;?></strong></p>
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