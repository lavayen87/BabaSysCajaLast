<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}

$saldo_anterior = 0.00;
$saldo_anterior_dolares = 0;
$saldo_anterior_euros = 0;
$saldo_anterior_cheques = 0;
$total_gral = 0.00;
$monto = 0;
$monto_serv = 0;
$fecha = date('Y-m-d');
$moneda = $_POST['moneda'];

if(isset($_POST['detalle']))
{ 
	$detalle = $_POST['detalle'];
}

if(is_numeric($_POST['ingreso']))
{
	$ingreso = $_POST['ingreso'];  

	include('conexion.php');
	include('funciones.php');
	
	/*$query1 = "SELECT pesos from caja_gral
			  where operacion = 1 
			  and numero_caja = '$numero_caja'
			  and fecha = '$fecha'	  
			  order by numero desc limit 1";    
	$result1 = mysqli_query($connection, $query1);
	$datos = mysqli_fetch_array($result1);
    $datos_pesos = $datos['pesos'];*/

    $cobranza = 0.00;
	$ing_servicio = 0.00;

	$query2 = "SELECT dolares from caja_gral
			  where operacion = 2 
			  and numero_caja = '$numero_caja'
			  and fecha = '$fecha'		  
			  order by numero desc limit 1";    
	$result2 = mysqli_query($connection, $query2);
	$datos = mysqli_fetch_array($result2);
    $datos_dolares = $datos['dolares'];

	$query3 = "SELECT euros from caja_gral
			  where operacion = 3 
			  and numero_caja = '$numero_caja'
			  and fecha = '$fecha'	
			  order by numero desc limit 1";    
	$result3 = mysqli_query($connection, $query3);
	$datos = mysqli_fetch_array($result3);
    $datos_euros = $datos['euros'];

	$query4 = "SELECT cheques from caja_gral
			  where operacion = 4 
			  and numero_caja = '$numero_caja'
			  and fecha = '$fecha'	
			  order by numero desc limit 1";    
	$result4 = mysqli_query($connection, $query4);
	$datos = mysqli_fetch_array($result4);
    $datos_cheques = $datos['cheques'];


	/*-------------------------------------------------------*/
	//if($result1)
	//{ 
	   	switch ($moneda) 
	   	{
	   		case 'pesos':

				if($numero_caja == 3) // caja banco
				{
					$query1 = "SELECT pesos from caja_gral
							where operacion = 1 
							and anulado = 0
							and numero_caja = '$numero_caja'	  
							order by numero desc limit 1";    					
				}
				else{
					$query1 = "SELECT pesos from caja_gral
							where operacion = 1 
							and numero_caja = '$numero_caja'
							and anulado = 0
							and fecha = '$fecha'	  
							order by numero desc limit 1";    					
				}

				$result1 = mysqli_query($connection, $query1);
				$datos = mysqli_fetch_array($result1);
				$datos_pesos = $datos['pesos'];

				/*-------------------------------------------- */

				// consigo ingreso por servicios
				$qry_serv = "SELECT  importe from ingresos_servicios
							WHERE fecha = '$fecha' 
							AND numero_caja = '$numero_caja'
							order by id limit 1";
				$res_serv = mysqli_query($connection, $qry_serv);
				$datos_serv = mysqli_fetch_array($res_serv);
				
				if($datos_serv<>[])
				{
					$ing_servicio = $datos_serv['importe'];
				}

				// consigo cobranza
				$qry = "SELECT  importe from cobranza
							WHERE fecha = '$fecha' 
							AND numero_caja = '$numero_caja'
							order by numero limit 1";
				$res = mysqli_query($connection, $qry);
				$datos = mysqli_fetch_array($res);
				
				$ultimo_cobro = $datos['importe']; // consigo el ultimo cobro en caja cobranza
				
				if($ultimo_cobro<>[]){
					$monto = $ultimo_cobro;
				}
				 
				
				/*if($ultimo_cobro > 0.00)
				{
					if($datos_pesos == [])
					{
						$pesos_a_sumar = ($monto + $ing_servicio + $ingreso);
					}
					else
					{
						$pesos_a_sumar = ($datos_pesos + $ing_servicio + $ingreso);
					}
				}
				else
				{
					if($datos_pesos == [])
					{
						$pesos_a_sumar = (0 + $ing_servicio + $ingreso);
					}
					else
					{
						$pesos_a_sumar = ($datos_pesos + $ingreso);
					}
				}*/
				if($ultimo_cobro > 0.00)
				{
					if($datos_pesos == [])
					{
						$pesos_a_sumar = ($monto + $ing_servicio + $ingreso);
					}
					else
					{
						$pesos_a_sumar = ($datos_pesos + $ingreso);
					}
				}
				else
				{
					if($ing_servicio > 0.00)
					{
						if($datos_pesos == [])
						{
							$pesos_a_sumar = ($ing_servicio + $ingreso);
						}
						else
						{
							$pesos_a_sumar = ($datos_pesos + $ingreso);
						}
					}
					else{
						if($datos_pesos == [])
						{
							$pesos_a_sumar = (0 + $ingreso);
						}
						else
						{
							$pesos_a_sumar = ($datos_pesos + $ingreso);
						}
					}
				}


				$insert = "INSERT IGNORE INTO caja_gral VALUES 
				('','$numero_caja','$fecha','$fecha','$detalle',0,'$ingreso',0,'$pesos_a_sumar',0,0,0,1,0)";

				$result_insert = mysqli_query($connection, $insert);

				// consigo saldo anterior en pesos, dolares, euros y cheques
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

				//cargo la tabla de totales generales:

				// ultima cobranza
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

				// ultimo ingreso por servicios
				$ultimo_ingreso = 0;
				$qry2 = "SELECT  importe from ingresos_servicios
						 WHERE fecha = '$fecha' 
						 AND numero_caja = '$numero_caja'
						 order by id DESC limit 1";
				$res2 = mysqli_query($connection, $qry2);
				$datos_ingresos = mysqli_fetch_array($res2);
				$ultimo_ingreso = $datos_ingresos['importe'];

				if( $ultimo_ingreso<>[])
				{ 
					$monto_serv = $ultimo_ingreso;
				}

				if( ($pesos_hoy<>[]) && ($monto>=0) && ($monto_serv)>=0)
				{
					$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto + $monto_serv);
				}
				else
					if( ($pesos_hoy==[]) && ($monto>=0) && ($monto_serv)>=0)
					{
						$total_gral_pesos = ($saldo_anterior + $monto + $monto_serv);
					}
					else $total_gral_pesos = $saldo_anterior;

				$total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
				$total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
				$total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);
			
				$qry = "SELECT * from caja_gral_temp
						  where operacion = 1 
						  and numero_caja = '$numero_caja'
						  and fecha = '$fecha'	
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
			    else{
			    	$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				   	('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

					$result_insert = mysqli_query($connection, $insert);
			    }
				echo 'ok';
	   			
			    break;

	   		////////////////////////////////////////////////////////////
	   		case 'dolares':
	   			if($datos_dolares == []) 
	   			{ 
		   			$dolares_asumar = $ingreso;
				}
				else $dolares_asumar = ($datos_dolares + $ingreso);   
				   	
				$insert = "INSERT IGNORE INTO caja_gral VALUES 
				   	('','$numero_caja','$fecha','$fecha','$detalle',0,'$ingreso',0,0,'$dolares_asumar',0,0,2,0)";
				$result_insert = mysqli_query($connection, $insert);

				// consigo saldo anterior en pesos, dolares, euros y cheques
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

	            // consigo cobranza
			    $cob = "SELECT importe from cobranza
							WHERE fecha = '$fecha'
							AND numero_caja = '$numero_caja'
							order by numero limit 1";
				$res_cob = mysqli_query($connection, $cob);
				$datos_cob = mysqli_fetch_array($res_cob);

				if($datos_cob['importe']<>[]){
					$monto = $datos_cob['importe'];
				}

				// ultimo ingreso por servicios
				$ultimo_ingreso = 0;
				$qry2 = "SELECT  importe from ingresos_servicios
						WHERE fecha = '$fecha' 
						AND numero_caja = '$numero_caja'
						order by id DESC limit 1";
				$res2 = mysqli_query($connection, $qry2);
				$datos_ingresos = mysqli_fetch_array($res2);
				$ultimo_ingreso = $datos_ingresos['importe'];

				if( $ultimo_ingreso<>[])
				{ 
					$monto_serv = $ultimo_ingreso;
				}

				if( ($pesos_hoy<>[]) && ($monto>=0) && ($monto_serv>=0) )
				{
					$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto + $monto_serv);
				}
				else
					if( ($pesos_hoy==[]) && ($monto>=0) && ($monto_serv>=0))
					{
						$total_gral_pesos = ($saldo_anterior + $monto + $monto_serv);
					}
					else $total_gral_pesos = $saldo_anterior;

				$total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
				$total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
				$total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);


				$qry = "SELECT * from caja_gral_temp
						  where operacion = 1 
						  and numero_caja = '$numero_caja'
						  and fecha = '$fecha'	
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
			    else{
			    	$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				   	('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

					$result_insert = mysqli_query($connection, $insert);
			    }

				echo 'ok';
				
			    break;

	   	 	case 'euros':

	   	 		if($datos_euros == [])
	   	 		{		   			 
		   			$euros_asumar = $ingreso;
				}
				else $euros_asumar = ($datos_euros + $ingreso); 
				   	
				$insert = "INSERT IGNORE INTO caja_gral VALUES 
				   	('','$numero_caja','$fecha','$fecha','$detalle',0,'$ingreso',0,0,0,'$euros_asumar',0,3,0)";
				$result_insert = mysqli_query($connection, $insert);

				// consigo saldo anterior en pesos, dolares, euros y cheques
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

				$cob = "SELECT importe from cobranza
							WHERE fecha = '$fecha'
							AND numero_caja = '$numero_caja'
							order by numero limit 1";
				$res_cob = mysqli_query($connection, $cob);
				$datos_cob = mysqli_fetch_array($res_cob);

				if($datos_cob['importe']<>[]){
					$monto = $datos_cob['importe'];
				}

				// ultimo ingreso por servicios
				$ultimo_ingreso = 0;
				$qry2 = "SELECT  importe from ingresos_servicios
						WHERE fecha = '$fecha' 
						AND numero_caja = '$numero_caja'
						order by id DESC limit 1";
				$res2 = mysqli_query($connection, $qry2);
				$datos_ingresos = mysqli_fetch_array($res2);
				$ultimo_ingreso = $datos_ingresos['importe'];

				if( $ultimo_ingreso<>[])
				{ 
					$monto_serv = $ultimo_ingreso;
				}

				if( ($pesos_hoy<>[]) && ($monto>=0) && ($monto_serv>=0) )
				{
					$total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto + $monto_serv);
				}
				else
					if( ($pesos_hoy==[]) && ($monto>=0) && ($monto_serv>=0))
					{
						$total_gral_pesos = ($saldo_anterior + $monto + $monto_serv);
					}
					else $total_gral_pesos = $saldo_anterior;

				$total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
				$total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
				$total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);
				
				$qry = "SELECT * from caja_gral_temp
						  where operacion = 1 
						  and numero_caja = '$numero_caja'
						  and fecha = '$fecha'	
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
			    else{
			    	$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				   	('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

					$result_insert = mysqli_query($connection, $insert);
			    }

				    echo 'ok';
				
			    break; 
			
			case 'cheques':

	   	 		if($datos_cheques == [])
	   	 		{		   			 
		   			$cheques_asumar = $ingreso;
				}
				else $cheques_asumar = ($datos_cheques + $ingreso); 
				   	
				$insert = "INSERT IGNORE INTO caja_gral VALUES 
				   	('','$numero_caja','$fecha','$fecha','$detalle',0,'$ingreso',0,0,0,0,'$cheques_asumar',4 )";
				$result_insert = mysqli_query($connection, $insert);

				// consigo saldo anterior en pesos, dolares, euros y cheques
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

				$cob = "SELECT importe from cobranza
						WHERE fecha = '$fecha'
						AND numero_caja = '$numero_caja'
						order by numero limit 1";
				$res_cob = mysqli_query($connection, $cob);
				$datos_cob = mysqli_fetch_array($res_cob);

				if($datos_cob['importe']<>[]){
					$monto = $datos_cob['importe'];
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
				
				$qry = "SELECT * from caja_gral_temp
						where operacion = 1 
						and numero_caja = '$numero_caja'
						and fecha = '$fecha'	
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
			    else{
			    	$insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
				   	('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

					$result_insert = mysqli_query($connection, $insert);
			    }

				    echo 'ok';
				
			    break; 
	   	}
	   	  
	//}
	//else    
	    //echo "Error de conexión";
}
else "Error... formato numerico invalido.";
?>