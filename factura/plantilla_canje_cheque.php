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

	$cheques = json_decode($_GET['cheques']);
	//echo print_r($cheques);
	$cant = count(json_decode($_GET['cheques']))/5;// cantidad de cheques
	//echo "cantidad de cheques: ".$cant."</br>";
	$c = 1; // variables de iteracion de cheques
	$j = 0; // variable de posicion de cheques
	$t = 0; // contador para los primeros cheques
	$p = "<table>"; 
	$k = "<table>"; 
	include('../funciones.php');
	if($cant <= 4)
	{	
				
		while($c <= $cant)
		{
			$n_cheque = $cheques[$j];
			$banco   = $cheques[$j+1];
			$emisor = $cheques[$j+2];
			$vencimiento = $cheques[$j+3];
			$importe = $cheques[$j+4];

			$p.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($vencimiento)."</strong></td>
					<td style='; text-align: left;'><strong>".$banco." "."</strong></td>
					<td style='text-align: right;'><strong>"."$".number_format($importe,2,',','.')."</strong></td>
					<td style='width: 70px; text-align: right;'><strong>".$n_cheque."</strong></td>
				</tr>";
			//$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
			$c++;
			$j+=5; 
			
		}
		$p.="</table>";
	}
	else{
		
		while($c <= $cant)
		{	
			
			if($t < 4 )
			{
				$n_cheque = $cheques[$j];
				$banco   = $cheques[$j+1];
				$emisor = $cheques[$j+2];
				$vencimiento = $cheques[$j+3];
				$importe = $cheques[$j+4];
				//$p.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
				$p.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($vencimiento)."</strong></td>
					<td style='; text-align: left;'><strong>".$banco." "."</strong></td>
					<td style='text-align: right;'><strong>"."$".number_format($importe,2,',','.')."</strong></td>
					<td style='width: 70px; text-align: right;'><strong>".$n_cheque."</strong></td>
				</tr>";
				$t++;
				$c++;
				$j+=5;
			}
			else{
				$n_cheque = $cheques[$j];
				$banco   = $cheques[$j+1];
				$emisor = $cheques[$j+2];
				$vencimiento = $cheques[$j+3];
				$importe = $cheques[$j+4];
				//$k.= "<p><strong>"." * ".fecha_min($datos_cheq['fecha_vto'])." - ".$datos_cheq['banco']." - "."$".number_format($datos_cheq['importe'],2,',','.')." - ".$datos_cheq['num_cheque']."</strong></p>";
				$k.="<tr><td style='width: 65px; text-align: left;'><strong>".fecha_min($vencimiento)."</strong></td>
					<td style='; text-align: left;'><strong>".$banco." "."</strong></td>
					<td style='text-align: right;'><strong>"."$".number_format($importe,2,',','.')."</strong></td>
					<td style='width: 70px; text-align: right;'><strong>".$n_cheque."</strong></td>
				</tr>";

				$t++;
				$c++;
				$j+=5;
			}
			
		}
		$p.="</table>";
		$k.="</table>";
	}

	include('../conexion.php');
	
	require "../conversor.php";
	$aux = 0;
	$texto1 = '';
	$texto2 = '';
	$texto3 = '';
	$texto4 = '';
	$findme = "CERO";
	
	$fecha = date('Y-m-d'); 

	// Consigo el ingreso
	$query = "SELECT * from caja_gral 
			where numero = (SELECT (Max(numero) - 1) FROM `caja_gral` 
							where numero_caja = '$numero_caja' 
							and fecha = '$fecha')";    
	$result = mysqli_query($connection, $query);    
	$ing = mysqli_fetch_assoc($result);
	

	if($ing['operacion'] == 4)
	{
		$detalle_in = $ing['detalle'];

		$ingreso = '$'.number_format($ing['ingreso'],2,',','.');
		$aux = $ing['ingreso'];
			
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
			if ($pos === true)
			{
				$texto2 = str_replace($findme, "", $texto2);
			}
		}
	}
	
	/*------------------------------------------------------*/

	// Consigo el egreso
	$query = "SELECT * from caja_gral 
			where numero = (SELECT Max(numero) FROM `caja_gral` 
							where numero_caja = '$numero_caja' 
							and fecha = '$fecha')";    
	$result = mysqli_query($connection, $query);    
	$eg = mysqli_fetch_assoc($result);

	if($eg['operacion'] == 1)
	{
		$egreso = '$'.number_format($eg['egreso'],2,',','.');
			
		$aux = $eg['egreso'];
			
		if( parte_entera(strval($aux)) <> 0)
		{	
			$texto3 = convertir(parte_entera(strval($aux))).' '."PESOS";				
			$pos = strpos($texto3, $findme)."</br>";		
			if ($pos > 0)
			{
				$texto3 = str_replace($findme, "", $texto3);
			}
				
		}
		if( parte_decimal(strval($aux)) <> 0) 
		{	

			$texto3.= " CON ";		
			$texto4 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
			$pos = strpos($texto4, $findme);
			if ($pos === true)
			{
				$texto4 = str_replace($findme, "", $texto4);
			}
		}
	}
	

	$hoy = date('Y-m-d');
	$fecha = $eg['fecha']; 
	$caja = $eg['numero_caja'];
	$detalle_eg = $eg['detalle'];
	$hora = date('G').':'.date('i').':'.date('s');
	mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Comprobante</title>
    <link rel="stylesheet" href="style-pdf.css">
	<style>
		#left {
			width: 42%;
			float: left;
			margin-left: 4mm;
			margin-top: 2mm;
		}

		#right {
			
			width: 52%;
			float: right;
			
		}
		
	</style>
</head>
<body>

<div id="page_pdf">
	
	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/logo-baba.png" style="width: 150px; height: 80px;">
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Canje de cheque'); ?></span>
					<!--p><?php echo $configuracion['razon_social']; ?></p>
					<p><?php echo $configuracion['direccion']; ?></p>
					<p>NIT: <?php echo $configuracion['nit']; ?></p>
					<p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
					<p>Email: <?php echo $configuracion['email']; ?></p-->
				</div>
				
			</td>
			<td class="info_factura">
				<!--div class="round">
					<span class="h3">Datos de la empresa</span>
					<p>C.U.I.T.: <strong>30-70737896-0 </strong></p>
					<p>D.G.R.: <strong>30-70737896-0 </strong></p>
					
				</div-->
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<!--span class="h3">Detalle</span-->
						<div>
							<p style="float: left;">Fecha: 
								<strong><?php echo fecha_min($hoy); ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								
							</p>

							<p style="display: inline-block;  float:right;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>
						
						<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
						<!--p> Canje Nº: <strong><?php echo 1;?></strong></p-->
						<p> Ing: 
							<strong>
								<?php //echo "$ingreso"." "."($texto1 $texto2)"; //linea original
								$space = 0; // variable para texto largo
								
								if( strlen($texto1." ".$texto2) > 67 )
								{            
									echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$ingreso." (".strtolower($texto1)." ".strtolower($texto2).")"."</strong>";
									$space = 1;
								}
								else
								echo "<strong>".$ingreso." (".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong>";
								
								?>
							</strong>
						</p>
						
						<p> Egr: 
							<strong>
								<?php //echo "$egreso"." "."($texto3 $texto4)"; // texto original
									if($space == 1){
										echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$egreso." (".strtolower($texto3)." ".strtolower($texto4).")"."</strong>";
									}
									else{
										echo "<strong>".$egreso." (".strtoupper($texto3)." ".strtoupper($texto4).")"."</strong>";
									}
								?>
							</strong>
						</p>
											
						<p> Detalle : <strong><?php echo ' '.$detalle_in; ?></strong></p>
						<?php
							$div="";
							 
								$div="<div>
										<div id='left'>".$p."</div>";
								if($cant > 4){
									$div.= "<div id='right'>".$k."</div></div>";
								}
								else $div.="</div>";
								echo $div;
								if($cant == 1)
									echo "<br>";
								else 
									if($cant == 2)
										echo "<br><br><br>";
									else
										if($cant == 3)
											echo "<br><br><br>";
										else echo "<br><br><br><br><br>";
							 
												
						?>
						 
								
				</div>
			</td>

		</tr>
	</table>

	<table id="factura_detalle">
			<thead>
				
			</thead>
			<tbody id="detalle_productos">

			</tbody>
			<tfoot id="detalle_totales">
		
			</tfoot>
	</table>
	<br><br>
	<div class="nota-op">
			
			<div style="display: inline-block; width: 30%; height: 3%; ">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong style="margin: 0px auto; text-align: center; ">Confeccionó</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; text-align: center;">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong>Recibió (Firma y Aclaración)</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; margin-left: 10%;">
				<!--hr style="width: 100%; color: black; margin: 0px auto;"-->
				<strong style="float: right;">Autorizó</strong>
			</div>
	</div>

	<!--br><br><br><br><br><br><br><br><br><br-->
	<?php
            if($cant >=1 && $cant <4)
            {
                switch($cant)
                {
                    case 1: 
						if(strlen($texto1." ".$texto2) > 67 )
							echo "<br><br><br><br><br><br><br><br><br>";
						else {
							echo "<br><br><br><br><br><br><br><br><br><br><br>";
						}
                    break;
                    case 2: echo "<br><br><br><br><br><br>";
                    break;
                    case 3: echo "<br><br><br>";
                    break;
                }
                
            }
            else{
				if($cant >= 4)
                {
                    if($space == 1)
                    {
                        echo "<br><br><br><br>";
                    }
                    else
                        echo "<br><br><br><br><br></br>";
                }
                	
				else echo "<br><br><br><br><br><br><br><br><br>";
            }
        ?>
	<!---------------------------------------------------->

	<table id="factura_cliente">
		<tr>
			<td >
				<div class="logo_factura" >
					<img src="img/logo-baba.png" style="width: 150px; height: 80px;">
				</div>
			</td>
			<td class="info_empresa">
			
				<div>
					<span class="h2"><?php echo strtoupper('Canje de cheque'); ?></span>
					<!--p><?php echo $configuracion['razon_social']; ?></p>
					<p><?php echo $configuracion['direccion']; ?></p>
					<p>NIT: <?php echo $configuracion['nit']; ?></p>
					<p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
					<p>Email: <?php echo $configuracion['email']; ?></p-->
				</div>
				
			</td>
			<td class="info_factura">
				<!--div class="round">
					<span class="h3">Datos de la empresa</span>
					<p>C.U.I.T.: <strong>30-70737896-0 </strong></p>
					<p>D.G.R.: <strong>30-70737896-0 </strong></p>
					
				</div-->
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<!--span class="h3">Detalle</span-->
						<div>
							<p style="float: left;">Fecha: 
								<strong><?php echo fecha_min($hoy); ?></strong> 
							</p>

							<p style="display: inline-block; margin-left:140px;">
								
							</p>

							<p style="display: inline-block;  float:right;">
							Hora: <strong><?php echo $hora; ?></strong>
							</p>
						</div>
						
						<hr>

						<p> Emitido por : <strong><?php echo ' '.$rol." (caja $numero_caja)"; ?></strong></p>
						<!--p> Cange Nº: <strong><?php echo 1;?></strong></p-->
						<p> Ing: 
							<strong>
								<?php //echo "$ingreso"." "."($texto1 $texto2)";
								$space = 0; // variable para texto largo
								if( strlen($texto1." ".$texto2) > 67 )
								{            
									echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$ingreso." (".strtolower($texto1)." ".strtolower($texto2).")"."</strong>";
									$space = 1;
								}
								else
								echo "<strong>".$ingreso." (".strtoupper($texto1)." ".strtoupper($texto2).")"."</strong>";
								?>
							</strong>
						</p>
						
						<p> Egr: 
							<strong>
								<?php //echo "$egreso"." "."($texto3 $texto4)";
								if($space == 1){
									echo "<strong style='line-height:10px; font-family: 'BrixSansRegular'; font-size: 11pt;'>".$egreso." (".strtolower($texto3)." ".strtolower($texto4).")"."</strong>";
								}
								else{
									echo "<strong>".$egreso." (".strtoupper($texto3)." ".strtoupper($texto4).")"."</strong>";
								}
								?>
							</strong>
						</p>
											
						<p> Detalle : <strong><?php echo ' '.$detalle_eg; ?></strong></p>
						<?php
							$div="";
							 
								$div="<div>
										<div id='left'>".$p."</div>";
								if($cant > 4){
									$div.= "<div id='right'>".$k."</div></div>";
								}
								else $div.="</div>";
								echo $div;
								if($cant == 1)
									echo "<br>";
								else 
									if($cant == 2)
										echo "<br><br><br>";
									else
										if($cant == 3)
											echo "<br><br><br>";
										else echo "<br><br><br><br><br>";
							 
												
						?>
								
				</div>
			</td>

		</tr>
	</table>

	<table id="factura_detalle">
			<thead>
				
			</thead>
			<tbody id="detalle_productos">

			</tbody>
			<tfoot id="detalle_totales">
		
			</tfoot>
	</table>
	<br><br>
	<div class="nota-op">
			
			<div style="display: inline-block; width: 30%; height: 3%; ">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong style="margin: 0px auto; text-align: center; ">Confeccionó</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; text-align: center;">	
				<!--hr style="width:100%; color: black; margin: 0px auto;"-->
				<strong>Recibió (Firma y Aclaración)</strong>
			</div>

			<div style="display: inline-block; width: 30%; height: 3%; margin-left: 10%;">
				<!--hr style="width: 100%; color: black; margin: 0px auto;"-->
				<strong style="float: right;">Autorizó</strong>
			</div>
	</div>
  
</div>


