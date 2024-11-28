
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


	

	$saldo_anterior = 0.00;
	$pesos_hoy = 0.00;
	$dolares_hoy = 0;
	$euros_hoy = 0;
	$ultimo_cobro = 0.00;
	$ing_servicio = 0;
	$total_gral = 0.00;
	$total_gral_pesos = 0.00;
	$total_gral_dolares = 0.00;
	$total_gral_euros = 0.00;
	$total_gral_cheques = 0.00;
	$monto = 0.00;

	$saldo_anterior_dolares = 0;
	$saldo_anterior_euros = 0;
	$saldo_anterior_cheques = 0;
	$monto_serv = 0;

	include('../conexion.php');
	include('../funciones.php');
	
	
	


	

	
	
		
		$numero_orden = 9999;
		$fecha = date('Y-m-d');
		$caja_solicitante = 87;
		$solicitante = "Luis Lavayen";
		$empresa= "Coprotab";
		$obra = "Puerta 7";
		$cuenta = "Mat. Construccion";
		$detalle = "Cinta Transportadora";
		$moneda = "pesos";
		$importe = 9999999;// consigo el importe de la solicitud
		$recibe = "Constructora Noa"; 
		$confirm_datos_solic = "";
		$op = 0;
		

		
			
			
       
			$titulo = "ÓRDEN DE PAGO X SOLICITUD";
		
		/*-------*/

		
		

		

		
		
		/*-------*/
		// Plantilla de vista

		$hora = date('G').':'.date('i').':'.date('s');

		$aux = 0;
		$texto1 = '';
		$texto2 = '';
		$findme = "CERO";
        $sim = "";
        switch ($moneda) {
          case 'pesos':
            $sim = '$';
            break;
          
          case 'dolares':
            $sim = '$US';
            break;

          case 'euros':
            $sim = '€';
            break;
        }
		require "../conversor.php";

		 //$textoprecio   = TextoPrecio(number_format($importe,2,',','.'));

		if($importe > 0)
		{
			$cantidad = $importe;
			$aux = $importe;//number_format($importe,2,',','.');
			
			if( parte_entera(strval($aux)) <> 0)
	        {	
	            $texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
	            $pos = strpos($texto1, $findme);		
	            if ($pos > 0)
	            {
	                $texto1 = str_replace($findme, "", $texto1);
	            }
	            
	        }
	      	
	        $texto1.= " CON ";		
	        $texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
	        $pos = strpos($texto2, $findme);
	        if ($pos === true)
	        {
	            $texto2 = str_replace($findme, "", $texto2);
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="js/jquery-3.5.1.min.js"></script> 
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
        
        <!-------------------------- Espacio ------------------------------>
        <!--div style="height: 38px;"></div-->
        <!--div style="height: 35px; border:1px solid red;"> <br><br> </div-->
       
        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong><?php echo $titulo;?></strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;"><?php echo "N° de órdenrrr: ".$numero_orden; ?></strong>
                    <strong style="display: inline-block; margin-left: 35%;"><?php echo "Fecha: ".fecha_min($fecha)?></strong>
                    <strong style="float: right;"><?php echo "Hora: ".$hora; ?></strong>
                    </p>
                </div>
                <hr>
                <?php 

                   // if($num_solicitud != 0){
                    if($confirm_datos_solic)
                    {
                        echo "<p> <strong>Solicitante: ".$solicitante." (caja ".$numero_caja_solicitante.")</strong></p>";
                        echo "<p> <strong>Emitida por: ".$rol_caja_pago." (caja ". $caja_pago.")</strong></p>";
                    }
                    else
                        echo "<p> <strong>Emitida por: ".$rol_caja_pago." (caja ". $caja_pago.")</strong></p>";
                ?>
                <p><strong><?php echo "Recibe: ".$recibe; ?></strong></p>
                <p><strong><?php echo "Empresa: ".$empresa. " - "." Obra: ".$obra;?></strong></p>
                <p><strong><?php echo "Cuenta: ".$cuenta;?></strong></p>
                <p><strong><?php echo 'Detalle: '.$detalle; ?></strong></p>
                <p>Son: 
                <?php 
                    $space = 0; // variable para texto largo
                    //if( strlen($texto1." ".$texto2) > 100 )
                    if( strlen($texto1." ".$texto2) > 100)
                    {            
                        echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$sim.number_format($importe,2,',','.')." "."(".strtolower($texto1)." ".strtolower($texto2).")"."</strong>";
                        // echo "<p><strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>Son: ".$sim.$importe." "."(".strtolower($textoprecio).")"."</strong></p>";
                        $space = 1;
                    }
                    else
                    	echo "<strong style='word-wrap: break-word; line-height: 11.5px;'>".$sim.number_format($importe,2,',','.')." "."(".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong>";
                    	// echo "<p><strong>Son: ".$sim.$importe." "."(".strtoupper($textoprecio).")"."</strong></p>";
                ?>
                </p>
                
                
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
