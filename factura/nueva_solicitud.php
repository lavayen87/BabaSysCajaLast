

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


	$num_solicitud = $_GET["id"]; // id es el numero de solicitud 

	if(isset($_GET["caja_pago"]))
	{
		if($_GET["caja_pago"] == 3)
		{
			$rol = "Banco";
			$numero_caja = $_GET["caja_pago"]; 
		}
		
	}


	$saldo_anterior = 0.00;
	$pesos_hoy = 0.00;
	$dolares_hoy = 0;
	$euros_hoy = 0;
	$ultimo_cobro = 0.00;
	$total_gral = 0.00;
	$monto = 0.00;
    $p = "";
    $band = 0;

	include('../conexion.php');
	include('../funciones.php');
	
	// Datos de la solicitud
	$query = "SELECT * from solicitud_orden_pago 
			  WHERE numero_orden = '$num_solicitud' 
	          order by numero_orden desc limit 1"; 


	// Datos de cheques
	$qry = "SELECT t1.id_cheque, t1.banco, t1.num_cheque, t1.importe, t1.fecha_vto
			FROM cheques_cartera as t1 INNER JOIN ids_check_list as t2
			ON t1.id_cheque = t2.id_cheque 
			WHERE t1.num_solicitud = $num_solicitud
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

	$result = mysqli_query($connection, $query);    
	$datos = mysqli_fetch_array($result);

	if($datos['estado'] == 'Realizada') 
	{
		header("Location: ../file_listado_solicitudes.php"); 
	}
	else{
		
		$numero_orden = $datos['numero_orden'];
		$fecha = date('Y-m-d');
		$emisor = $datos['numero_caja']; // Numero de la caja que emite la orden
		$solicitante = $datos['solicitante'];
		$empresa= $datos['empresa'];
		$obra = $datos['obra'];
		$cuenta = $datos['cuenta'];
		$detalle = $datos['detalle'];
		$moneda = $datos['moneda'];
		$importe = $datos['importe'];// consigo el importe de la solicitud

		if($datos['recibe'] != "" )
		{
			$recibe = $datos['recibe'];
		}
		else $recibe = "";

		$select_caja_solicitante = "SELECT numero_caja from usuarios 
									WHERE rol = '$solicitante'";
		$res_caja_solicitante = mysqli_query($connection, $select_caja_solicitante);
		$datos_caja_solicitante = mysqli_fetch_array($res_caja_solicitante);
		// Numero de la caja de solicita la orden de pago
		$num_caja_solicitante = $datos_caja_solicitante['numero_caja']; 

		$op = 0;
		
		/*---------*/
		
		if($moneda == 'pesos')
		{
			if($numero_caja == 3)
			{
				$qry = "SELECT pesos FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 1
					order by numero desc limit 1";
			}
			else
			{
				$qry = "SELECT pesos FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 1
					and fecha = '$fecha'
					order by numero desc limit 1";
			
			}

			$res = mysqli_query($connection, $qry);
			$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
			$pesos = $get_datos['pesos'];

			$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);

			if($datos['importe']<>[]){
				$ultimo_cobro = $datos['importe']; // consigo el ultimo cobro en caja cobranza
			}
					

			if($ultimo_cobro > 0.00)
			{
				if($pesos == [])
				{
					$pesos_a_restar = $ultimo_cobro - $importe;
				}
				else
				{
					$pesos_a_restar = $pesos - $importe;
				}
			}
			else
			{
				if($pesos == [])
				{
					$pesos_a_restar = 0 - $importe;
				}
				else
				{
					$pesos_a_restar = $pesos - $importe;
				}
			}

			$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,'$importe','$pesos_a_restar',0,0,0,1)";
			$insert_result = mysqli_query($connection, $insert);

			// setear campo 'orden' en ids_check_list con num_solic
			$op = 1;

		}
		else{
			
			if($numero_caja == 3)
			{
				$qry = "SELECT cheques FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 4
					order by numero desc limit 1";
			}
			else
			{
				$qry = "SELECT cheques FROM caja_gral 
					WHERE numero_caja = '$numero_caja'
					and operacion = 4
					and fecha = '$fecha'
					order by numero desc limit 1";
			}
			
			$res = mysqli_query($connection, $qry);
			$get_datos = mysqli_fetch_array($res); // consigo pesos de mi caja
			$cheques = $get_datos['cheques'];

			if($cheques == [])
			{
				$cheques_a_restar = (-1)*$importe;
			}
			else
			{
				$cheques_a_restar = ($cheques - $importe);
			}

			$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,'$importe',0,0,0,'$cheques_a_restar',4)";
			$insert_result = mysqli_query($connection, $insert);

			$op = 4;

			$set_cheques = "UPDATE cheques_cartera 
					SET fecha_entrega = '$fecha',
						persona_pago = '$recibe',
						estado = 'Entregado',
						activo = 4
					WHERE num_solicitud = '$numero_orden'";
			$res_cheque = mysqli_query($connection, $set_cheques);
		}
		//////////////////////////////////////////

		/*$insert = "INSERT INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','SO $detalle',0,'$importe','$pesos_a_restar',0,0,0,1)";
		$insert_result = mysqli_query($connection, $insert);*/

		$qry = "SELECT numero FROM caja_gral
				WHERE numero_caja = '$numero_caja'
				AND operacion = '$op'
				AND fecha = '$fecha'
				order by numero desc limit 1";
		$res_qry = mysqli_query($connection, $qry);
		$get_datos = mysqli_fetch_array($res_qry);
		$num = $get_datos['numero'];

		// seteo el campo 'orden' en ids_check_list
		$set = "UPDATE ids_check_list SET orden = '$num'
				WHERE num_orden = $num_solicitud";
		$res_set = mysqli_query($connection, $set);

		$insert2 = "INSERT  INTO orden_pago VALUES ('$num','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra','$moneda')";
		$result_insert2 = mysqli_query($connection, $insert2);

		$qry = "UPDATE solicitud_orden_pago  
				SET estado = 'Realizada' 
				WHERE numero_orden = '$numero_orden'";
		$res = mysqli_query($connection, $qry);

		$set_cheques = "UPDATE cheques_cartera 
					SET  num_orden_pago = '$num'
					WHERE num_solicitud = '$numero_orden'";
		$res_cheque = mysqli_query($connection, $set_cheques);

		//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
		$saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha);
		$saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha);
		$saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha);
		$saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha);
			
		// consigo total pesos, dolares, euros y cheques del dia
		$pesos_hoy = get_total(1,$numero_caja,$fecha);
		$dolares_hoy = get_total(2,$numero_caja,$fecha);
		$euros_hoy = get_total(3,$numero_caja,$fecha);
		$cheques_hoy = get_total(4,$numero_caja,$fecha);

		//cargo  totales generales

		$qry = "SELECT  importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res = mysqli_query($connection, $qry);
		$datos = mysqli_fetch_array($res);
		$ultimo_cobro = $datos['importe'];

		if($ultimo_cobro<>[]){ 
			$monto = $ultimo_cobro;
		}

		if( ($pesos_hoy<>[]) && ($ultimo_cobro>=0) )
		{
			$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
		}
		else
			if( ($pesos_hoy==[]) && ($ultimo_cobro>=0) )
			{
				$total_gral_pesos = ($saldo_anterior + $ultimo_cobro);
			}
			else $total_gral_pesos = $saldo_anterior;

		$total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
		$total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
		$total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);
		// cargo los totales generales
		$qry = "SELECT * from caja_gral_temp
				where fecha = '$fecha'
				and numero_caja = '$numero_caja'
				and operacion = 1	
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
		else
		{
			$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
			('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

			$result_insert = mysqli_query($connection, $insert);
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
	}
	
?>
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
            <strong><?php echo $num; ?></strong> 
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
		<p> Solicitante: <strong><?php echo ' '.$solicitante.' (caja '.$num_caja_solicitante.')';?></strong></p>
		<p> Emitida por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>

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
			if($recibe != ""){
				echo "<p> Recibe : <strong>".''.$recibe."</strong></p>";
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
            <strong><?php echo $num; ?></strong> 
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

    <p> Solicitante: <strong><?php echo ' '.$solicitante.' (caja '.$num_caja_solicitante.')'; ?></strong></p>
    <p> Emitida por: <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>

    <!--p> Nº de caja : <strong><?php echo ' '.$numero_caja; ?></strong></p-->
    <p>
        <label align="left">
            Empresa: 
            <strong><?php echo limitar_cadena($empresa,16);?></strong>
        </label>
        <label style="margin-left: 10px; margin-right: 10px;"> / </label>
        <label style="margin-left: 10px;">
            Obra: 
            <strong><?php echo limitar_cadena($obra,16);?></strong>
        </label>
    </p>
    <p> Cuenta: <strong><?php echo "$cuenta";?></strong></p>
    <?php
		if($recibe != ""){
			echo "<p> Recibe : <strong>".''.$recibe."</strong></p>";
		}
	?>
    <p> Detalle: <strong><?php echo ' '.$detalle; ?></strong></p>
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
			<label for="" style="float: right;">Autorizó</label>		</p>
    </div>
</div>
</body>
</html>