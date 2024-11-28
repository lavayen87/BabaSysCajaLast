<?php  

	
	 
	

	$fecha ='2021-07-02';
	$op = 1;
	$numero_caja = 10;
	$set_moneda = '[]';
	$monto = 0;

	$caja_origen  = 0;
	$caja_destino = 0;
	include('conexion.php');

	$qry_get = "SELECT * from transferencias 
				WHERE numero_tr = 474
				AND fecha = '$fecha'";
	$res_get = mysqli_query($connection, $qry_get);

	if($res_get->num_rows > 0)
	{
		$datos = mysqli_fetch_assoc($res_get);
		$caja_origen  = $datos['numero_caja_origen'];
		$caja_destino = $datos['numero_caja_destino'];
		
		echo 'Nº Caja origen: '.$caja_origen.'</br>';
		echo 'Nº Caja destino: '.$caja_destino.'</br>';
	}

	echo "</br>";

	// Elimino la fila indicada en caja_gral.
	$query = "DELETE  from caja_gral 
			WHERE numero = 458
			and fecha = '$fecha'";    
	$result = mysqli_query($connection, $query);  


	// elimino la operación de la tabla Transferencias
	$delete_tr = "DELETE from transferencias 
				  WHERE numero_tr = 458
				  AND fecha = '$fecha'";    
	$result_tr = mysqli_query($connection, $delete_tr);  

	if($op == 1)
	{
		$moneda = 'pesos';
		$set_moneda = 'pesos';
		// Consigo datos de cobranza diaria
		$cob = "SELECT importe from cobranza
				WHERE fecha = '$fecha'
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res_cob = mysqli_query($connection, $cob);
		$datos_cob = mysqli_fetch_array($res_cob);

		if($datos_cob['importe'] <> []){
			$monto = $datos_cob['importe'];
			echo 'monto: '.$monto.'</br>';
		}

	}
	else
	{ 
		if($op == 2){
			$moneda = 'dolares';
			$set_moneda = dolares;
		}
		else{
			$moneda = 'euros';
			$set_moneda = euros;
		} 
	}
	// vacio la columna con la moneda indicada
	$query_empty = "UPDATE caja_gral SET $set_moneda = 0 
				   where numero_caja = '$numero_caja' 
				   AND operacion = '$op'
				   AND fecha = '$fecha'";
	$result_empty = mysqli_query($connection, $query_empty);

	$qr = "SELECT numero FROM caja_gral 
		  where numero_caja = '$numero_caja' 
		  AND operacion = '$op'
		  AND fecha = '$fecha'";
	$res = mysqli_query($connection, $qr); // busqueda de numeros
	$cantidad = $res->num_rows; // cantidad de numeros obtenidos

	$k = 0;
	$lista = array();
	while ($r = mysqli_fetch_array($res))
	{
		
		$lista[$k] = $r['numero']; // obtengo una lista con los numeros
		$k++;	
	}

	$inicial = $lista[0];

	$query_get_data = "SELECT * FROM caja_gral 
					   where numero_caja = '$numero_caja'
					   and operacion = '$op' 
					   AND fecha = '$fecha'
					   ORDER BY numero LIMIT 1"; // datos para actualizar columna con la moneda indicada (primer fila)
	$result_get_data = mysqli_query($connection, $query_get_data);
	$data = mysqli_fetch_array($result_get_data);

	// cactualizamos los campos con cobranza diaria
	if($monto > 0.00)
	{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE caja_gral SET $set_moneda = '$monto' + '$pde'  WHERE numero = '$inicial'";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
			echo 'Primera fila: '.$monto. '+'. $pde.':'.($monto + $pde).'</br>';
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE caja_gral SET $set_moneda = $monto - '$pde'  WHERE numero = '$inicial'";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
				echo 'Primera fila: '.'$monto'. '-'. '$pde'.':'.($monto - $pde).'</br>';
			}
	}
	else{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE caja_gral SET $set_moneda = 0 + '$pde' 
				WHERE numero = '$inicial'";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
			echo 'Primera fila: '.$pde.':'.(0 + $pde).'</br>';
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE caja_gral SET $set_moneda = 0 - '$pde'  WHERE numero = '$inicial'";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
				echo 'Primera fila: '.$pde.':'.(0 - $pde).'</br>';
			}
	}

	echo ' ---- fin Primera fila ----- '.'</br>';

	for($i=0; $i <= $cantidad; $i++)
	{
		if(($i+1) <= $cantidad)
		{
			$m = $lista[$i]; // fila suoerior
			echo 'm = '.$m.' '.'('.'fila superior'.')'.'</br>';
			$n = $lista[$i+1]; // fila inferior
			echo 'n = '.$n.' '.'('.'fila inferior'.')'.'</br>';
			
			$qry = "SELECT * FROM caja_gral
					WHERE numero = '$n'
					and numero_caja = '$numero_caja'";
			$res = mysqli_query($connection,$qry);
			$dta = mysqli_fetch_array($res);
			$ingreso = $dta['ingreso'];
			echo 'Ingreso: '.$ingreso.'</br>';
			$egreso = $dta['egreso'];
			echo 'Egreso: '.$egreso.'</br>';
				
			if($ingreso > 0)
			{
				$qry = "SELECT * FROM caja_gral
						WHERE numero = '$m'
						and numero_caja = '$numero_caja'";
				$res = mysqli_query($connection,$qry);
				$dta = mysqli_fetch_array($res);
				$pde = $dta[$moneda];
				echo 'pesos fila m: '.$pde.'</br>';	
				$update = "UPDATE caja_gral SET $set_moneda = '$pde' + '$ingreso'  
							WHERE numero = '$n' 
							AND operacion = '$op'
							AND fecha = '$fecha'";
				$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
				echo 'operacion: '.$pde.'+'.$ingreso.': '.($pde+$ingreso).'</br>';
			}
			else
				if($egreso > 0)
				{
					$qry = "SELECT * FROM caja_gral 
							WHERE numero = '$m'
							and numero_caja = '$numero_caja'";
					$res = mysqli_query($connection,$qry);
					$dta = mysqli_fetch_array($res);
					$pde = $dta[$moneda];
					echo 'pesos fila m: '.$pde.'</br>';		
					$update = "UPDATE caja_gral SET $set_moneda = '$pde' - '$egreso' 
								WHERE numero = '$n'
								AND operacion = '$op'
								AND fecha = '$fecha'";
					$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
					echo 'operacion: '.$pde.'-'.$egreso.': '.($pde-$egreso).'</br>';
				}
		}
		echo '--------------------------------------------'.'</br>';		
			
	}
 


?>