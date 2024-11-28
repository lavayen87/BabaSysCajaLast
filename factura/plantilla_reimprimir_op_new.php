

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

    $num_op = $_GET['num_op']; // Dato enviado por post

    include('../conexion.php');
    include('../funciones.php');

    $query = "SELECT * from orden_pago 
             WHERE numero_orden = '$num_op'";    
    $result = mysqli_query($connection, $query);    
    $datos = mysqli_fetch_array($result);

    $numero_orden = $datos['numero_orden'];
    $fecha = date('Y-m-d');
    $emisor = $datos['numero_caja'];
    $empresa= $datos['empresa'];
    $obra = $datos['obra'];
    $cuenta = $datos['cuenta'];
    $detalle = $datos['detalle'];
    $moneda = $datos['moneda'];
    $importe = $datos['importe'];// consigo el importe de orden
    $op = 0;

	//$num_solicitud = $_GET["id"]; // id es el numero de solicitud 

    if($numero_caja == 3)
    {
        $rol = "Banco";
    }
		
    $p = "";
    $band = 0;
    $c = 0;
	$s = 0;
    $e = 0;
    // Datos de la solicitud si es en pesos ( IMPORTANTE !!)
   $query1 = "SELECT t1.numero_orden, t1.solicitante, t1.caja_pago
            from solicitud_orden_pago as t1  inner join ids_check_list as t2 
            on t1.numero_orden = t2.num_orden
            where t2.orden = '$num_op'
            group by t1.numero_orden";
    $res1 = mysqli_query($connection, $query1);

    // Datos de la solicitud con cheque (si existe)
	$query2 = "SELECT t1.solicitante 
            from solicitud_orden_pago as t1  inner join ids_check_list as t2 
            WHERE t2.num_orden = '$num_op'";
        
    $res2 = mysqli_query($connection, $query2);

    if($res1->num_rows > 0)
    {
        $datos_solic1 = mysqli_fetch_array($res1);
        $solicitante = $datos_solic1['solicitante'];
        $caja_pago = $datos_solic1['caja_pago'];
        $select = "SELECT rol from usuarios WHERE numero_caja = '$caja_pago'";
        $res_select = mysqli_query($connection, $select);
        $datos_select = mysqli_fetch_array($res_select);
        $emitio = $datos_select['rol']; 
        $e++;
    }
    else{
        if($res2->num_rows > 0)
        {
            $datos_solic2 = mysqli_fetch_array($res);
            $solicitante = $datos_solic2['solicitante'];
            $s++;
        }
    }

    // Datos de la solicitud con cheque (si existe)
	$query3 = "SELECT t1.solicitante, t1.recibe, t1.numero_orden, t2.num_orden_pago 
              from solicitud_orden_pago as t1 INNER JOIN 
			  cheques_cartera as t2
              ON t1.numero_orden = t2.num_solicitud 
              WHERE t2.num_orden_pago = '$num_op'
			  GROUP BY t2.id_cheque";
              
    $res3 = mysqli_query($connection, $query3);

    if($res3->num_rows > 0)
    {
        $datos_solic3 = mysqli_fetch_array($res);
        $solicitante = $datos_solic3['solicitante'];
        $recibe = $datos_solic3['recibe']; 
        $c++;
    }

    // Datos de cheques
	$qry = "SELECT t1.id_cheque, t1.banco, t1.num_cheque, t1.importe, t1.fecha_vto
			FROM cheques_cartera as t1 INNER JOIN ids_check_list as t2
			ON t1.id_cheque = t2.id_cheque 
			WHERE t1.num_orden_pago = '$num_op'
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
	
    
    /*-------*/

    
    // Plantilla de vista

    $hora = date('G').':'.date('i').':'.date('s');

    $aux = 0;
    $texto1 = '';
    $texto2 = '';
    $findme = "CERO";
    require "../conversor.php";

    if($importe > 0)
    {
        $cantidad = $importe;
        $aux = $importe;
        
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
<title>Orden d epago</title>
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
            <strong><?php echo $num_op; ?></strong> 
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
        <?php
        
        if($s > 0) 
        {
            echo "<p> Solicitante: <strong>".$solicitante."</strong></p>";
        }
        if($e > 0)
        {
            echo "<p> Solicitante: <strong>".$solicitante."</strong></p>";
        }

        if($c > 0)
        {
            echo "<p> Solicitante: <strong>".$solicitante."</strong></p>";
        }
        ?>
		
		<p> Emitida por: <strong><?php echo ' '.$emitio." (caja $caja_pago)"; ?></strong></p>

		<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
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
		<p> Cuenta: <strong><?php echo "$cuenta";?></strong></p>
        <?php 
        if($c > 0)
        {
           echo "<p> Recibe : <strong>".$recibe."</strong></p>"; 
        }
        ?>
		
		<p> Detalle : <strong><?php echo ' '.$detalle; ?></strong></p>
    </div>
    
    <div id="2">
        
        <?php
        if($band == 1)
            echo $p;
        ?>
    </div>

    <div class="importe">
    <p>Recibi(mos): 
        <strong>
            <?php 
                echo "$".number_format($cantidad,2,',','.')." "."($texto1 $texto2)";
            ?>
        </strong>
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
            <strong><?php echo $num_op; ?></strong> 
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

    <?php
        
        if($s > 0) 
        {
            echo "<p> Solicitante: <strong>".$solicitante."</strong></p>";
        }
        if($e > 0)
        {
            echo "<p> Solicitante: <strong>".$solicitante."</strong></p>";
        }

        if($c > 0)
        {
            echo "<p> Solicitante: <strong>".$solicitante."</strong></p>";
        }
        ?>
		
		<p> Emitida por: <strong><?php echo ' '.$emitio." (caja $caja_pago)"; ?></strong></p>

		<!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
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
		<p> Cuenta: <strong><?php echo "$cuenta";?></strong></p>
        <?php 
        if($c > 0)
        {
           echo "<p> Recibe : <strong>".$recibe."</strong></p>"; 
        }
        ?>
		
		<p> Detalle : <strong><?php echo ' '.$detalle; ?></strong></p>
    </div>
    
    <div id="2">
        
        <?php
        if($band == 1)
            echo $p;
        ?>
    </div>

    <div class="importe">
    <p>Recibi(mos): 
        <strong>
            <?php 
                echo "$".number_format($cantidad,2,',','.')." "."($texto1 $texto2)";
            ?>
        </strong>
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