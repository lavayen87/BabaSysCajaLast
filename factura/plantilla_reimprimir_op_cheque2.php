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
	$num_op = $_GET['num_op']; 
	include('../conexion.php');
	include('../funciones.php');

	//datos de la orden de pago con cheque.
	$query = "SELECT * from orden_pago 
			  WHERE numero_orden = '$num_op'  
	          order by numero_orden desc limit 1";    
	$result = mysqli_query($connection, $query);    
	$datos = mysqli_fetch_array($result);

	$fecha_actual = date('Y-m-d'); // fecha actual

	$numero_orden = $datos['numero_orden'];
    $numero_caja_emisor = $datos['numero_caja'];
	$fecha = $datos['fecha']; 
	$recibe = $datos['recibe'];
	$empresa= $datos['empresa'];
	$obra = $datos['obra'];
	$cuenta = $datos['cuenta'];
	$detalle = $datos['detalle'];
	$moneda = $datos['moneda'];
	$importe = $datos['importe'];
    
	//
	$qry_emisor = "SELECT rol from usuarios 
			    WHERE numero_caja = '$numero_caja_emisor'";   
	$res_emisor = mysqli_query($connection, $qry_emisor);
    $datos_emisor = mysqli_fetch_array($res_emisor);
    $emisor = $datos_emisor['rol'];

	// Datos del solicitante
	$qry = "SELECT num_solicitud
			FROM cheques_cartera 
			WHERE num_orden_pago = '$numero_orden'";

	$res = mysqli_query($connection, $qry);
	$datos_num_solicitud = mysqli_fetch_array($res);
	$num_solicitud = $datos_num_solicitud['num_solicitud'];
	
	if($num_solicitud != 0){
		// Solicitante
		$qry = "SELECT solicitante, numero_caja from solicitud_orden_pago
				WHERE numero_orden = '$num_solicitud'";
		$res_solicitante = mysqli_query($connection, $qry);
		$datos_nombre_solicitante = mysqli_fetch_array($res_solicitante);
		$nombre_solicitante = $datos_nombre_solicitante['solicitante'];
		$numero_caja_solicitante = $datos_nombre_solicitante['numero_caja'];
	}
	

	// Datos de cheques
	$qry = "SELECT fecha_vto,banco,importe,num_cheque,num_solicitud
	FROM cheques_cartera 
	WHERE num_orden_pago = '$numero_orden'
	ORDER BY fecha_vto";

	$res_ch = mysqli_query($connection, $qry);

	$p = "<table>"; 
	$k = "<table>";
	$band = 0; // existencia de cheques
	$c = 0; // cantidad de cheques
	$j = 0; // contador para los primeros cheques

	if($res_ch->num_rows > 0)
	{
		$c = $res_ch->num_rows; 
		$band = 1; 

		if($c <= 4)
		{	
					
			while($datos_cheq = mysqli_fetch_array($res_ch))
			{
                $p.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($datos_cheq['fecha_vto'])."</strong></td>
                         <td style='; text-align: left;'><strong>".$datos_cheq['banco']." "."</strong></td>
                         <td style='text-align: right;'><strong>"."$".number_format($datos_cheq['importe'],2,',','.')."</strong></td>
                         <td style='width: 70px; text-align: right;'><strong>".$datos_cheq['num_cheque']."</strong></td>
                    </tr>";
				//$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
				
			}
            $p.="</table>";
		}
		else{
			
			while($datos_cheq = mysqli_fetch_array($res_ch))
			{	
				
				if($j < 4 )
				{
					//$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
					$p.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($datos_cheq['fecha_vto'])."</strong></td>
                         <td style='; text-align: left;'><strong>".$datos_cheq['banco']." "."</strong></td>
                         <td style='text-align: right;'><strong>"."$".number_format($datos_cheq['importe'],2,',','.')."</strong></td>
                         <td style='width: 70px; text-align: right;'><strong>".$datos_cheq['num_cheque']."</strong></td>
                    </tr>";
					$j++;
				}
				else{
					//$k.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
					$k.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($datos_cheq['fecha_vto'])."</strong></td>
                         <td style='; text-align: left;'><strong>".$datos_cheq['banco']." "."</strong></td>
                         <td style='text-align: right;'><strong>"."$".number_format($datos_cheq['importe'],2,',','.')."</strong></td>
                         <td style='width: 70px; text-align: right;'><strong>".$datos_cheq['num_cheque']."</strong></td>
                    </tr>";
				}
				
			}
			$p.="</table>";
            $k.="</table>";
		}
		
	}
	
	$hora = date('G').':'.date('i').':'.date('s');

	$aux = 0;
	$texto1 = '';
	$texto2 = '';
	$findme = "CERO";
	require "../conversor.php";

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
		
	}	 
	mysqli_close($connection);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Comprobante</title>
<style>
    
    @page { 
        margin-left: 4px;
        margin-right: 4px; 
        margin-top: 2px; 
        box-sizing: border-box;
    } 
    p, label, span{
        font-family: 'BrixSansRegular';
        font-size: 11pt;
        line-height:6px;
    }
    #page {
        width: 100%; 
    }
    #header {
        height: 85px;
        background:white;
        
    }
    #left {
        width: 44%;
        float: left;
         
    }

    #right {
        width: 55%;
        float: right;
        
    }
    #content-datos {
        padding: 4px;
        clear: both;
        border-radius: 10px;
        border: 1px solid #049776;
    }
    #titulo{
        display: inline-block; 
        height: 45px; 
        width: 35%;
        padding-top: 35px;
        margin-left: 30%;
        text-align: center;
        
    }
    #content-cheques{
        width: 100%; 
        /* height: 90px; */
        overflow: hidden; 
        border: 1px solid blue;
        align-items: flex-start;
    }
    #content-firma{
        width: 100%; 
        height: 30px;
        padding-top: 50px;
        padding-left: 4px;
        overflow: hidden; 
        
    }
    
</style>
<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>

<body>
    <div id="page">
        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong>ÓRDEN DE PAGO CON CHEQUES</strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;"><?php echo "N° de órden: ".$numero_orden; ?></strong>
                    <strong style="display: inline-block; margin-left: 35%;"><?php echo "Fecha: ".fecha_min($fecha)?></strong>
                    <strong style="float: right;"><?php echo "Hora: ".$hora; ?></strong>
                    </p>
                </div>
                <hr>
                <?php 
                    if($num_solicitud != 0){
                        echo "<p> <strong>Solicitante: ".$nombre_solicitante." (caja ".$numero_caja_solicitante.")</strong></p>";
                    }
                ?>
                <p><strong><?php echo 'Emitida por: '.$emisor." (caja $numero_caja_emisor)"; ?></strong></p>
                <p><strong><?php echo "Recibe: ".$recibe; ?></strong></p>
                <p><strong><?php echo "Empresa: ".$empresa. " - "." Obra: ".$obra;?></strong></p>
                <p><strong><?php echo "Cuenta: ".$cuenta;?></strong></p>
                <p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
                <?php 
                    $space = 0; // variable para texto largo
                    if( strlen($texto1." ".$texto2) > 100 )
                    {            
                        echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$cantidad." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                        $space = 1;
                    }
                    else
                    echo "<p><strong>Son: ".$cantidad." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                ?>
                <?php 
					$div="";
					if($band == 1)
                    {
						 $div="<div>
								<div id='left'>".$p."</div>";
						if($c > 4){
							$div.= "<div id='right'>".$k."</div></div>";
						}
						else $div.="</div>";
						echo $div;
						if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
					}							
				?>
                <!--div id="content-cheques33" >
                    <div id="left">
                        <?php if($band == 1) echo $p;?>
                    </div>
                    <div id="right">
                        <?php 
                            if($band == 1)
                            {
                                if($c > 4)
                                {
                                    echo $k;
                                }
                                
                            }
                        ?>
                    </div>
                    <?php 
                        if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
                    ?>
                     
                </div-->
            </div>
            
            <div id="content-firma">
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <strong style="float: right;">Autorizó</strong>
                </p>
            </div>
        </div>
        <!-------------------------- Espacio ------------------------------>
        <!--div style="height: 38px;"></div-->
        <!--div style="height: 35px; border:1px solid red;"> <br><br> </div-->
        <?php
            if($c >=1 && $c <4)
            {
                switch($c)
                {
                    case 1: echo "<br><br><br><br><br><br><br><br><br>";
                    break;
                    case 2: echo "<br><br><br><br><br><br>";
                    break;
                    case 3: echo "<br><br><br><br><br>";
                    break;
                }
                
            }
            else{
                echo "<br> x aqui 87";
            }
        ?>
        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong>ÓRDEN DE PAGO CON CHEQUES</strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;"><?php echo "N° de órden: ".$numero_orden; ?></strong>
                    <strong style="display: inline-block; margin-left: 35%;"><?php echo "Fecha: ".fecha_min($fecha)?></strong>
                    <strong style="float: right;"><?php echo "Hora: ".$hora; ?></strong>
                    </p>
                </div>
                <hr>
                <?php 
                    if($num_solicitud != 0){
                        echo "<p> <strong>Solicitante: ".$nombre_solicitante." (caja ".$numero_caja_solicitante.")</strong></p>";
                    }
                ?>
                <p><strong><?php echo 'Emitida por: '.$emisor." (caja $numero_caja_emisor)"; ?></strong></p>
                <p><strong><?php echo "Recibe: ".$recibe; ?></strong></p>
                <p><strong><?php echo "Empresa: ".$empresa. " - "." Obra: ".$obra;?></strong></p>
                <p><strong><?php echo "Cuenta: ".$cuenta;?></strong></p>
                <p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
                <?php 
                    $space = 0; // variable para texto largo
                    if( strlen($texto1." ".$texto2) > 100 )
                    {            
                        echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$cantidad." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                        $space = 1;
                    }
                    else
                    echo "<p><strong>Son: ".$cantidad." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong></p>";
                ?>
                <?php 
					$div="";
					if($band == 1){
						 $div="<div>
								<div id='left'>".$p."</div>";
						if($c > 4){
							$div.= "<div id='right'>".$k."</div></div>";
						}
						else $div.="</div>";
						echo $div;
						if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
					}							
				?>
                <!--div id="content-cheques33" >
                    <div id="left">
                        <?php if($band == 1) echo $p;?>
                    </div>
                    <div id="right">
                        <?php 
                            if($band == 1)
                            {
                                if($c > 4)
                                {
                                    echo $k;
                                }
                                
                            }
                        ?>
                    </div>
                    <?php 
                        if($c == 1)
                            echo "<br>";
                        else 
                            if($c == 2)
                                echo "<br><br><br>";
                            else
                                if($c == 3)
                                    echo "<br><br><br><br>";
                                else echo "<br><br><br><br><br>";
                    ?>
                     
                </div-->
            </div>
            
            <div id="content-firma">
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <strong style="float: right;">Autorizó</strong>
                </p>
            </div>
        </div>
        
    </div>

    
</body>
</html>
