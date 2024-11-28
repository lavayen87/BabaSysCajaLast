<?php
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}
include('conexion.php');
include('funciones.php');

$moneda1 = $_POST['moneda1'];

$moneda2 = $_POST['moneda2'];

$cantidad_compra = $_POST['cantidad_compra'];

$cotizacion = $_POST['cotizacion'];

$detalle_canje = $_POST['detalle_canje'];

$fecha = date('Y-m-d');

$ingreso = $cantidad_compra;

$egreso = $cantidad_compra * $cotizacion;

$op = 0;

switch ($moneda1) 
{
	case 'pesos':	//compra de pesos --> pago con dolares o euros
		if($moneda2 == 'dolares')
		{
			// consigo ultimo dolar mayor a cero para poder pagar
			$query2 = "SELECT dolares from caja_gral
						where numero_caja = '$numero_caja'
						AND operacion = 2
						AND fecha = '$fecha'
						order by numero desc limit 1";    
			$result2 = mysqli_query($connection, $query2); 
			$datos_dolares = mysqli_fetch_array($result2);
			$dolares = $datos_dolares['dolares'];
				 				
			// verifico si tengo dolares suficientes para pagar
			if($dolares >= ($cantidad_compra / $cotizacion)) 
			{	
				$egreso = $cantidad_compra / $cotizacion;
				$dolares_a_restar = $dolares - $egreso;
				$op = 1;
			}
			else{
				$sad = saldo_ant('dolares',$numero_caja,$fecha);
						
				if($sad >= ($cantidad_compra / $cotizacion))
				{
					$egreso = $cantidad_compra / $cotizacion;
					$dolares_a_restar = ($sad - $egreso);
					$op = 2;
				}
			}		 
			
			if($op > 0)
			{
					
				$query1 = "SELECT pesos from caja_gral
							where operacion = 1 
							AND numero_caja = '$numero_caja'
							AND fecha = '$fecha'
							order by numero desc limit 1";    
				$result1 = mysqli_query($connection, $query1);

				$datos_pesos = mysqli_fetch_array($result1);
				$pesos = $datos_pesos['pesos'];

				$ingreso = $cantidad_compra;				

				// consigo cobranza
				$qry = "SELECT  importe from cobranza
						WHERE fecha = '$fecha' 
						AND numero_caja = '$numero_caja'
						order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro = $datos['importe'];


				if($ultimo_cobro > 0){
					if($pesos == []){
						$pesos_a_sumar = ($ultimo_cobro + $ingreso);
					}
					else{
						$pesos_a_sumar = ($pesos + $ingreso);
					}
				}
				else{
					if($pesos == []){
						$pesos_a_sumar = (0 + $ingreso);
					}
					else{
						$pesos_a_sumar = ($pesos + $ingreso);
					}
				}

				$detalle = $detalle_canje." "."Cot. $".$cotizacion;
				
				$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Ing $ $detalle','$ingreso',0,'$pesos_a_sumar',0,0,0,1)";
				$result_insert = mysqli_query($connection, $insert);

				$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Egr US$ $detalle',0,'$egreso',0,'$dolares_a_restar',0,0,2)";
				$result_insert = mysqli_query($connection, $insert);

				/*-------------------------------------------------*/

				//Buscamos Saldo anterior en pesos, dolares y euros en tabla de totales generales
				$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
				$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
				$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
				$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);

				//Consigo total del dia en pesos  
								        
				$pesos_hoy = get_total(1,$numero_caja,$fecha);

				//Consigo total del dia en dolares  

				$dolares_hoy = get_total(2,$numero_caja,$fecha);

				//Consigo total del dia en euros  

				$euros_hoy = get_total(3,$numero_caja,$fecha);

				//Consigo total del dia en cheques  

				$cheques_hoy = get_total(4,$numero_caja,$fecha);

				//cargo  totales generales

				// consigo cobranza
				$qry = "SELECT  importe from cobranza
						WHERE fecha = '$fecha' 
						AND numero_caja = '$numero_caja'
						order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro = $datos['importe'];

				$monto = 0;
				if($ultimo_cobro > 0){
					$monto = $ultimo_cobro;
				}

				if( ($pesos_hoy<>[]) && ($monto>=0) )
				{
					$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
				}
				else
					if( ($pesos_hoy==[]) && ($monto>=0) )
					{
						$total_gral_pesos = ($saldo_anterior + $monto);
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

				echo 1;	
			}
			else{
				echo 2;
			}
		}
		else
		{
			
			if($moneda2 == 'euros')
			{
				$query3 = "SELECT euros from caja_gral
				where numero_caja = '$numero_caja'
				and operacion = 3
				AND fecha = '$fecha'
				order by numero desc limit 1";    
				$result3 = mysqli_query($connection, $query3);
				$datos_euros = mysqli_fetch_array($result3);
				$euros = $datos_euros['euros'];	
					
				if($euros >= ($cantidad_compra / $cotizacion))
				{	
					$egreso = $cantidad_compra / $cotizacion;
					$euros_a_restar = ($datos_euros['euros'] - $egreso);	 
					$op = 1;
				}
				else{
					$sae = saldo_ant('euros',$numero_caja,$fecha);
						
					if($sae >= ($cantidad_compra / $cotizacion))
					{
						$egreso = $cantidad_compra / $cotizacion;
						$euros_a_restar = ($sae - $egreso);
						$op = 2;
					}

				}

				if($op > 0)
				{
					
					$query1 = "SELECT pesos from caja_gral
							where operacion = 1 
							AND numero_caja = '$numero_caja'
							AND fecha = '$fecha'
							order by numero desc limit 1";    
					$result1 = mysqli_query($connection, $query1);

					$datos_pesos = mysqli_fetch_array($result1);
					$pesos = $datos_pesos['pesos'];

					$ingreso = $cantidad_compra;
						
					// consigo cobranza
					$qry = "SELECT  importe from cobranza
							WHERE fecha = '$fecha' 
							AND numero_caja = '$numero_caja'
							order by numero limit 1";
					$res = mysqli_query($connection, $qry);
					$datos = mysqli_fetch_array($res);
					$ultimo_cobro = $datos['importe'];


					if($ultimo_cobro > 0){
						if($pesos == []){
							$pesos_a_sumar = ($ultimo_cobro + $ingreso);
						}
						else{
							$pesos_a_sumar = ($pesos + $ingreso);
						}
					}
					else{
						if($pesos == []){
							$pesos_a_sumar = (0 + $ingreso);
						}
						else{
							$pesos_a_sumar = ($pesos + $ingreso);
						}
					}	


					$detalle = $detalle_canje." "."Cot. $".$cotizacion;

					$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Ing $ $detalle','$ingreso',0,'$pesos_a_sumar',0,0,0,1)";
					$result_insert = mysqli_query($connection, $insert);

					$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Egr € $detalle',0,'$egreso',0,0,'$euros_a_restar',0,3)";
					$result_insert = mysqli_query($connection, $insert);

					//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
					
					$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
					$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
					$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
					$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);

					//Consigo total del dia en pesos  
									        
					$pesos_hoy = get_total(1,$numero_caja,$fecha);

					//Consigo total del dia en dolares  

					$dolares_hoy = get_total(2,$numero_caja,$fecha);

					//Consigo total del dia en euros  

					$euros_hoy = get_total(3,$numero_caja,$fecha);

					//Consigo total del dia en cheques  

					$cheques_hoy = get_total(4,$numero_caja,$fecha);

					//cargo  totales generales

					// consigo cobranza
					$qry = "SELECT  importe from cobranza
							WHERE fecha = '$fecha' 
							AND numero_caja = '$numero_caja'
							order by numero limit 1";
					$res = mysqli_query($connection, $qry);
					$datos = mysqli_fetch_array($res);
					$ultimo_cobro = $datos['importe'];

					$monto = 0;
					if($ultimo_cobro > 0){
						$monto = $ultimo_cobro;
					}

					if( ($pesos_hoy<>[]) && ($monto>=0) )
					{
						$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
					}
					else
						if( ($pesos_hoy==[]) && ($monto>=0) )
						{
							$total_gral_pesos = ($saldo_anterior + $monto);
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

					echo 1;
					
				}
				else  echo 2;
			}
			else echo 0; // igualdad de monedas

		}

		break;

	case 'dolares':	//compra de dolares

		if($moneda2 == 'pesos')
		{
			$query1 = "SELECT pesos from caja_gral
						where numero_caja = '$numero_caja'
						AND operacion = 1
						AND fecha = '$fecha'
						order by numero desc limit 1";    
			$result1 = mysqli_query($connection, $query1);
			$datos_pesos = mysqli_fetch_array($result1);
			$pesos = $datos_pesos['pesos'];

			// consigo cobranza
			$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);
			$ultimo_cobro = $datos['importe'];

			//echo "cobranza: ".$ultimo_cobro;exit;


			if($ultimo_cobro > 0)
			{
				if($pesos == [] || $pesos == 0)
				{
					if($ultimo_cobro >= $egreso){
						$pesos_a_restar = $ultimo_cobro - $egreso;
						$op = 1;
					}
					else{
						// consigo saldo anterior:
						$sa = saldo_ant('pesos',$numero_caja,$fecha);

						if($sa >= $egreso)
						{
							$pesos_a_restar = ($sa - $egreso);
							$op = 2;	
						}
						else{ /* Nueva opcion */
							// consigo total gral
							$td = get_total(1,$numero_caja,$fecha);
							$sa = saldo_ant('pesos',$numero_caja,$fecha);
							$tg = ($td + $sa);
							if($tg >= $egreso)
							{
								$pesos_a_restar = ($tg - $egreso);
								$op = 20;	
							}
						}/* end  */
					}
				}
				else
				{
					if($pesos >= $egreso){
						$pesos_a_restar = $pesos - $egreso;
						$op = 3;
					}
					else{
						if($ultimo_cobro >= $egreso){
							$pesos_a_restar = $ultimo_cobro - $egreso;
							$op = 4;
						}
						else{
							// consigo saldo anterior:
							$sa = saldo_ant('pesos',$numero_caja,$fecha);

							if($sa >= $egreso)
							{
								$pesos_a_restar = ($sa - $egreso);
								$op = 5;	
							}
						}
					}
				}
			}
			else
			{
				if($pesos == [])
				{
					// consigo saldo anterior:
					$sa = saldo_ant('pesos',$numero_caja,$fecha);

					if($sa >= $egreso)
					{
						$pesos_a_restar = ($sa - $egreso);
						$op = 6;	
					}
					
				}
				else
				{
					if($pesos >= $egreso)
					{
						$pesos_a_restar = $pesos - $egreso;
						$op = 7;
					}
					else{

						// consigo saldo anterior:
						$sa = saldo_ant('pesos',$numero_caja,$fecha);
						if($sa >= $egreso)
						{
							$pesos_a_restar = ($sa - $egreso);
							$op = 8;	
						}
					
					}
				}
			}


			/*-------------------------------*/

			if($op > 0)
			{
				
				$query2 = "SELECT dolares from caja_gral
							WHERE operacion = 2 
							AND numero_caja = '$numero_caja'
							AND fecha = '$fecha'
							order by numero desc limit 1";    
				$result2 = mysqli_query($connection, $query2);

				$datos_dolares = mysqli_fetch_array($result2);

				if($datos_dolares['dolares'] == [])
				{
					$dolares_a_sumar = (0 + $ingreso); 
				}
				else
				{
					$dolares_a_sumar = $datos_dolares['dolares'] + $ingreso; 
				}

				
				$detalle = $detalle_canje." "."Cot. $".$cotizacion;
				

				$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Ing US$ $detalle','$ingreso',0,0,'$dolares_a_sumar',0,0,2)";
				$result_insert = mysqli_query($connection, $insert);

				$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Egr $ $detalle',0,'$egreso','$pesos_a_restar',0,0,0,1)";
				$result_insert = mysqli_query($connection, $insert);

				//Buscamos Saldo anterior en pesos, dolares, euros y cheques 
				$saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
				$saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
				$saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
				$saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);

				//Consigo total del dia en pesos  
											
				$pesos_hoy = get_total(1,$numero_caja,$fecha);

				//Consigo total del dia en dolares  

				$dolares_hoy = get_total(2,$numero_caja,$fecha);

				//Consigo total del dia en euros  

				$euros_hoy = get_total(3,$numero_caja,$fecha);

				//Consigo total del dia en cheques  

				$cheques_hoy = get_total(4,$numero_caja,$fecha);

				//cargo  totales generales

				// consigo cobranza
				$qry = "SELECT  importe from cobranza
							WHERE fecha = '$fecha' 
							AND numero_caja = '$numero_caja'
							order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro = $datos['importe'];

				$monto = 0;
				if($ultimo_cobro > 0){
					$monto = $ultimo_cobro;
				}

				if( ($pesos_hoy<>[]) && ($monto>=0) )
				{
					$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
				}
				else
					if( ($pesos_hoy==[]) && ($monto>=0) )
					{
						$total_gral_pesos = ($saldo_anterior + $monto);
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

				echo 1;
								
			}		
			else echo 2;
		}
		else echo 3;

		break;

	case 'euros': // compra de euros

		if($moneda2 == 'pesos')
		{
			$query1 = "SELECT pesos from caja_gral
						where numero_caja = '$numero_caja'
						and operacion = 1
						AND fecha = '$fecha'
						order by numero desc limit 1";    
			$result1 = mysqli_query($connection, $query1);
			$datos_pesos = mysqli_fetch_array($result1);
			$pesos = $datos_pesos['pesos'];

			// consigo cobranza
			$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
			$res = mysqli_query($connection, $qry);
			$datos = mysqli_fetch_array($res);
			$ultimo_cobro = $datos['importe']; 

			//echo "cobranza**: ".$ultimo_cobro;exit;

			if($ultimo_cobro > 0)
			{
				if($pesos == [] || $pesos == 0)
				{
					if($ultimo_cobro >= $egreso){
						$pesos_a_restar = $ultimo_cobro - $egreso;
						$op = 1;
					}
					else{
						// consigo saldo anterior:
						$sa = saldo_ant('pesos',$numero_caja,$fecha);

						if($sa >= $egreso)
						{
							$pesos_a_restar = ($sa - $egreso);
							$op = 2;	
						}
					}
				}
				else
				{
					if($pesos >= $egreso){
						$pesos_a_restar = $pesos - $egreso;
						$op = 3;
					}
					else{
						if($ultimo_cobro >= $egreso){
							$pesos_a_restar = $ultimo_cobro - $egreso;
							$op = 4;
						}
						else{
							// consigo saldo anterior:
							$sa = saldo_ant('pesos',$numero_caja,$fecha);

							if($sa >= $egreso)
							{
								$pesos_a_restar = ($sa - $egreso);
								$op = 5;	
							}
						}
					}
				}
			}
			else
			{
				if($pesos == [])
				{
					// consigo saldo anterior:
					$sa = saldo_ant('pesos',$numero_caja,$fecha);

					if($sa >= $egreso)
					{
						$pesos_a_restar = ($sa - $egreso);
						$op = 6;	
					}
					
				}
				else
				{
					if($pesos >= $egreso)
					{
						$pesos_a_restar = $pesos - $egreso;
						$op = 7;
					}
					else{

						// consigo saldo anterior:
						$sa = saldo_ant('pesos',$numero_caja,$fecha);
						if($sa >= $egreso)
						{
							$pesos_a_restar = ($sa - $egreso);
							$op = 8;	
						}
					
					}
				}
			}

			/*--------------------------------*/

			if($op > 0)
			{
				
				$query3 = "SELECT euros from caja_gral
						where numero_caja = '$numero_caja'
						AND operacion = 3
						AND fecha = '$fecha'
						order by numero desc limit 1";    
				$result3 = mysqli_query($connection, $query3);

				$datos_euros = mysqli_fetch_array($result3);

				if($datos_euros['euros'] == [])
				{
					$euros_a_sumar = (0 + $ingreso);
				}
				else{
					$euros_a_sumar = ($datos_euros['euros'] + $ingreso);
				}

				$detalle = $detalle_canje." "."Cot. $".$cotizacion;

				$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Ing € $detalle','$ingreso',0,0,0,'$euros_a_sumar',0,3)";
				$result_insert = mysqli_query($connection, $insert);

				$insert = "INSERT IGNORE INTO caja_gral VALUES ('','$numero_caja','$fecha','$fecha','Egr $ $detalle',0,'$egreso','$pesos_a_restar',0,0,0,1)";
				$result_insert = mysqli_query($connection, $insert);

				//Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
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

				$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				$ultimo_cobro = $datos['importe']; 

				$monto = 0;
				if($ultimo_cobro > 0){
					$monto = $ultimo_cobro;
				}

				//cargo  totales generales
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
						('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques'1)";

					$result_insert = mysqli_query($connection, $insert);
				}


				echo 1;
				
			}
			else echo 2;
		}
	else echo 3;

} // end switch
	

?>