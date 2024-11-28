
<?php  

function check_moneda(int $num)
{
	$moneda = "";
	include('conexion.php');
	$qry = "SELECT * FROM orden_pago
			WHERE numero_orden = '$num'";
	$res = mysqli_query($connection, $qry);

	if($res->num_rows > 0)
	{
		$datos = mysqli_fetch_array($res);
		$moneda = $datos['moneda'];

	}
	
	return $moneda;
}

function get_pde( string $moneda, int $op, int $num_caja, $fecha)
{
	include('conexion.php');
	// consigo  pesos/dolares/euros
	if($moneda == 'pesos'){
		$set_moneda = 'pesos';
		$qry = "SELECT pesos FROM caja_gral 
				WHERE fecha = '$fecha'
				and numero_caja = '$num_caja'
				and operacion = '$op'
				order by numero desc limit 1";
	}
	else
		if($moneda == 'dolares'){
			$set_moneda = 'dolares';
			$qry = "SELECT dolares FROM caja_gral 
					WHERE fecha = '$fecha'
					and numero_caja = '$num_caja'
					and operacion = '$op'
					order by numero desc limit 1";
		}
		else{
			if($moneda == 'euros'){
				$set_moneda = 'euros';
				$qry = "SELECT euros FROM caja_gral 
					WHERE fecha = '$fecha'
					and numero_caja = '$num_caja'
					and operacion = '$op'
					order by numero desc limit 1";
			}
			else{
				$set_moneda = 'cheques';
				$qry = "SELECT cheques FROM caja_gral 
					WHERE fecha = '$fecha'
					and numero_caja = '$num_caja'
					and operacion = '$op'
					order by numero desc limit 1";
			}
		}
	
	$res = mysqli_query($connection, $qry);
	$datos = mysqli_fetch_array($res);
	$pde = $datos[$moneda];
	return $pde;
}

function Update_caja(int $numero_caja, int $op, $fecha,$fecha1,$fecha2)
{

	$monto = 0;
	include('conexion.php');

	if($op == 1)
	{
		$moneda = 'pesos';
		$set_moneda = 'pesos';
		// Consigo datos de cobranza diaria
		$cob = "SELECT importe from cobranza
				WHERE fecha = '$fecha' 
				AND numero_caja = '$numero_caja'";
		$res_cob = mysqli_query($connection, $cob);
		$datos_cob = mysqli_fetch_array($res_cob);

		if($datos_cob['importe'] <> [])
			$monto = $datos_cob['importe'];
		else $monto = 0;
	}
	else
	{ 
		if($op == 2){
			$moneda = 'dolares';
			$set_moneda = 'dolares';
		}
		else
			if($op == 3){
				$moneda = 'euros';
				$set_moneda = 'euros';
			}
			else{
				$moneda = 'cheques';
				$set_moneda = 'cheques';
			}
		 
	}
	// vacio la columna con la moneda indicada
	$query_empty = "UPDATE lista_temp SET $set_moneda = 0 
				   where numero_caja = '$numero_caja' 
				   AND operacion = '$op'
				   AND fecha = '$fecha'";
	$result_empty = mysqli_query($connection, $query_empty);

	$qr = "SELECT numero FROM lista_temp 
		  where numero_caja = '$numero_caja' 
		  AND operacion = '$op'
		  AND fecha = '$fecha'";
	$res = mysqli_query($connection, $qr); // busqueda de numeros
	$cantidad = $res->num_rows; // cantidad de numeros obtenidos

	if($cantidad > 0)
	{
		$k = 0;
		$lista = array();
		while ($r = mysqli_fetch_array($res))
		{
			
			$lista[$k] = $r['numero']; // obtengo una lista con los numeros
			$k++;	
		}

		$inicial = $lista[0];
	}
	$query_get_data = "SELECT * FROM lista_temp 
					   where numero_caja = '$numero_caja'
					   and operacion = '$op' 
					   AND fecha = '$fecha'
					   ORDER BY numero LIMIT 1"; // datos para actualizar columna con la moneda indicada (primer fila)
	$result_get_data = mysqli_query($connection, $query_get_data);
	$data = mysqli_fetch_array($result_get_data);

	// actualizamos los campos con cobranza diaria
	if($monto > 0.00)
	{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE lista_temp SET $set_moneda = 'monto' + '$pde'  
					   WHERE numero = '$inicial'";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE lista_temp SET $set_moneda = 'monto' - '$pde'  
						   WHERE numero = '$inicial'";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
			}
	}
	else{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE lista_temp SET $set_moneda = (0 + '$pde') 
					   WHERE numero = '$inicial'";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE lista_temp SET $set_moneda = (0 - '$pde')  
						   WHERE numero = '$inicial'";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
			}
	}

	 	for($i=0; $i <= $cantidad; $i++)
	 	{
			if(isset($lista[$i+1]))
			{

				$n = $lista[$i+1]; // fila inferior
				$m = $lista[$i]; // fila suoerior
				$qry = "SELECT * FROM lista_temp
						WHERE numero = '$n'
						and numero_caja = '$numero_caja'";
				$res = mysqli_query($connection,$qry);
				$dta = mysqli_fetch_array($res);
				$ingreso = $dta['ingreso'];
				$egreso = $dta['egreso'];
					
				if($ingreso > 0)
				{
					$qry = "SELECT * FROM lista_temp
							WHERE numero = '$m'
							and numero_caja = '$numero_caja'";
					$res = mysqli_query($connection,$qry);
					$dta = mysqli_fetch_array($res);
					$pde = $dta[$moneda];
						
					$update = "UPDATE lista_temp SET $set_moneda = '$pde' + '$ingreso' 
								WHERE numero = '$n' 
								AND operacion = '$op'
								AND fecha = '$fecha'";
					$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
				}
				else
					if($egreso > 0)
					{
						$qry = "SELECT * FROM lista_temp 
						WHERE numero = '$m'
						and numero_caja = '$numero_caja'";
						$res = mysqli_query($connection,$qry);
						$dta = mysqli_fetch_array($res);
						$pde = $dta[$moneda];
							
						$update = "UPDATE lista_temp SET $set_moneda = '$pde' - '$egreso'
									WHERE numero = '$n'
									AND operacion = '$op'
									AND fecha = '$fecha'";
						$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
					}
			
			}		
			
	 	}
	
}
	
function fecha_min(string $fecha)
{
 
	$aux = substr($fecha,2,8);   
	$d = substr($aux,6,2);
	$m = substr($aux,3,2);
	$a = substr($aux,0,2);
	$nueva_fecha = $d.'/'.$m.'/'.$a;
	return $nueva_fecha;
}
	
function limitar_cadena($cadena, $limite)
{
	// Si la longitud es mayor que el límite...
	if(strlen($cadena) > $limite)
	{
		// Entonces limita la cadena a los primeros N caracteres. 
		return substr($cadena, 0, $limite).'.';
	}                           
	// Si no, entonces devuelve la cadena normal
	return $cadena;
}

function saldo_ant(string $moneda, int $num_caja, $fecha)
{
	$saldo_anterior = 0;

	include('conexion.php');

	$saldo_temp = "SELECT * FROM caja_gral_temp
					WHERE fecha = date_add('$fecha', INTERVAL -1 DAY)
					and numero_caja = '$num_caja'
					and operacion = 1
					order by numero desc limit 1";
	$res_temp = mysqli_query($connection, $saldo_temp);

	if($res_temp->num_rows > 0)
	{
		$datos_temp = mysqli_fetch_array($res_temp);
		$saldo_anterior = $datos_temp[$moneda];

	}
	else       
	{
		$saldo_temp = "SELECT * FROM caja_gral_temp
						WHERE fecha < '$fecha'
						and numero_caja = '$num_caja'
						and operacion = 1
						order by numero desc limit 1";
		$res_temp = mysqli_query($connection, $saldo_temp);
		if($res_temp->num_rows > 0)
		{
			$datos_temp = mysqli_fetch_assoc($res_temp);
			$saldo_anterior = $datos_temp[$moneda];
				
		}
	}
	return $saldo_anterior;
}


function get_fecha(int $num_caja, $fecha)
{
	$fecha_sa = "";
	include('conexion.php');
	$saldo_temp = "SELECT * FROM caja_gral_temp
			WHERE fecha = date_add('$fecha', INTERVAL -1 DAY)
			and numero_caja = '$num_caja'
			and operacion = 1
			order by numero desc limit 1";
	$res_temp = mysqli_query($connection, $saldo_temp);

	if($res_temp->num_rows > 0)
	{
		$datos_temp = mysqli_fetch_array($res_temp);
		$fecha_sa = $datos_temp['fecha'];

	}
	else       
	{
		$saldo_temp = "SELECT * FROM caja_gral_temp
						WHERE fecha < '$fecha'
						and numero_caja = '$num_caja'
						and operacion = 1
						order by numero desc limit 1";
		$res_temp = mysqli_query($connection, $saldo_temp);
		if($res_temp->num_rows > 0)
		{
			$datos_temp = mysqli_fetch_assoc($res_temp);
			$fecha_sa = $datos_temp['fecha'];
				
		}
	}
	return $fecha_sa;
}

// Suma de ingresos menos suma de egresos en el dia	
function get_total(int $op, int $num_caja, $fecha)
{

	include('conexion.php');
	$total = 0;
	if($op == 1){
		
		$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
					from caja_gral
					WHERE fecha = '$fecha' 
					AND operacion = '$op'
					AND anulado = 0
					AND numero_caja = '$num_caja'";         
		$result_total = mysqli_query($connection, $query_total);
		
	    
	}
	else{
		if($op == 2){
			$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND anulado = 0
                    AND operacion = '$op' 
                    AND numero_caja = '$num_caja'";         
	    	$result_total = mysqli_query($connection, $query_total);
		}
		else{
			if($op == 3){
				$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND anulado = 0
                    AND operacion = '$op' 
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
			else{
				$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND anulado = 0
                    AND operacion = '$op' 
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
		}
	}
	
    $dato = mysqli_fetch_array($result_total);              
    $total = $dato['total'];
    return $total;
}
///////////////
// Suma de ingresos menos suma de egresos en un rango de dias
function get_total2(int $op, int $num_caja, $fecha1, $fecha2)
{

	include('conexion.php');
	$total = 0;
	if($op == 1){
		$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
                    from caja_gral
                    WHERE fecha BETWEEN '$fecha1' and '$fecha2' 
                    AND anulado = 0
                    AND operacion = '$op' 
                    AND numero_caja = '$num_caja'";         
	    $result_total = mysqli_query($connection, $query_total);
	    
	}
	else{
		if($op == 2){
			$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
                    from caja_gral
					WHERE fecha BETWEEN '$fecha1' and '$fecha2'
					AND anulado = 0
                    AND operacion = '$op' 
                    AND numero_caja = '$num_caja'";         
	    	$result_total = mysqli_query($connection, $query_total);
		}
		else{
			if($op == 3){
				$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
                    from caja_gral
                    WHERE fecha BETWEEN '$fecha1' and '$fecha2'  
                    AND anulado = 0
                    AND operacion = '$op' 
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
			else{
				$query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total 
                    from caja_gral
                    WHERE fecha BETWEEN '$fecha1' and '$fecha2' 
                    AND anulado = 0
                    AND operacion = '$op' 
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
		}
	}
	
    $dato = mysqli_fetch_array($result_total);              
    $total = $dato['total'];
    return $total;
}
//////////////

// total de ingresos en el dia
function total_ingresos(int $op, int $num_caja, $fecha)
{

	include('conexion.php');
	$totla = 0;
	if($op == 1){
		$query_total = "SELECT sum(ingreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	    $result_total = mysqli_query($connection, $query_total);
	    
	}
	else{
		if($op == 2){
			$query_total = "SELECT sum(ingreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op'
                    AND anulado = 0 
                    AND numero_caja = '$num_caja'";         
	    	$result_total = mysqli_query($connection, $query_total);
		}
		else{
			if($op == 3){
				$query_total = "SELECT sum(ingreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
			else{
				$query_total = "SELECT sum(ingreso)  as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
		}
	}
	
    $dato = mysqli_fetch_array($result_total);              
    $total = $dato['total'];
    return $total;
}
// total de egresos en el dia
function total_egresos(int $op, int $num_caja, $fecha)
{

	include('conexion.php');
	$totla = 0;
	if($op == 1){
		$query_total = "SELECT sum(egreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	    $result_total = mysqli_query($connection, $query_total);
	    
	}
	else{
		if($op == 2){
			$query_total = "SELECT sum(egreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	    	$result_total = mysqli_query($connection, $query_total);
		}
		else{
			if($op == 3){
				$query_total = "SELECT sum(egreso) as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op'
                    AND anulado = 0 
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
			else{
				$query_total = "SELECT sum(egreso)  as total 
                    from caja_gral
                    WHERE fecha = '$fecha' 
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	    		$result_total = mysqli_query($connection, $query_total);
			}
		}
	}
	
    $dato = mysqli_fetch_array($result_total);              
    $total = $dato['total'];
    return $total;
}
// total de ingresos en un rango de dias
function total_ingresos2(int $op, int $num_caja, $fecha1, $fecha2)
{

	include('conexion.php');
	
	$total = 0;
	
	$query_total = "SELECT sum(ingreso) as total 
                    from caja_gral
                    WHERE fecha BETWEEN '$fecha1' and '$fecha2'
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	$result_total = mysqli_query($connection, $query_total);

	// if($op == 1){
	// 	$query_total = "SELECT sum(ingreso) as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2'
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     $result_total = mysqli_query($connection, $query_total);
	    
	// }
	// else{
	// 	if($op == 2){
	// 		$query_total = "SELECT sum(ingreso) as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2'
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     	$result_total = mysqli_query($connection, $query_total);
	// 	}
	// 	else{
	// 		if($op == 3){
	// 			$query_total = "SELECT sum(ingreso) as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2'
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     		$result_total = mysqli_query($connection, $query_total);
	// 		}
	// 		else{
	// 			$query_total = "SELECT sum(ingreso)  as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2' 
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     		$result_total = mysqli_query($connection, $query_total);
	// 		}
	// 	}
	// }
	
    $dato = mysqli_fetch_array($result_total);              
    $total = $dato['total'];
    return $total;
}
// total de egresos en un rango de dias
function total_egresos2(int $op, int $num_caja, $fecha1, $fecha2)
{

	include('conexion.php');

	$total = 0;

	$query_total = "SELECT sum(egreso) as total 
                    from caja_gral
                    WHERE fecha BETWEEN '$fecha1' and '$fecha2' 
                    AND operacion = '$op' 
                    AND anulado = 0
                    AND numero_caja = '$num_caja'";         
	$result_total = mysqli_query($connection, $query_total);

	// if($op == 1){
	// 	$query_total = "SELECT sum(egreso) as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2' 
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     $result_total = mysqli_query($connection, $query_total);
	    
	// }
	// else{
	// 	if($op == 2){
	// 		$query_total = "SELECT sum(egreso) as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2' 
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     	$result_total = mysqli_query($connection, $query_total);
	// 	}
	// 	else{
	// 		if($op == 3){
	// 			$query_total = "SELECT sum(egreso) as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2'
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     		$result_total = mysqli_query($connection, $query_total);
	// 		}
	// 		else{
	// 			$query_total = "SELECT sum(egreso)  as total 
 //                    from caja_gral
 //                    WHERE fecha BETWEEN '$fecha1' and '$fecha2' 
 //                    AND operacion = '$op' 
 //                    AND anulado = 0
 //                    AND numero_caja = '$num_caja'";         
	//     		$result_total = mysqli_query($connection, $query_total);
	// 		}
	// 	}
	// }
	
    $dato = mysqli_fetch_array($result_total);              
    $total = $dato['total'];
    return $total;
}

// Actualizar filas de una caja
function Update(int $numero_caja, int $op, $fecha, int $fila)
{
	$monto = 0;
	$ing_servicio = 0;
	include('conexion.php');

	if($op == 1)
	{
		$moneda = 'pesos';
		//$set_moneda = 'pesos';
		
		// Consigo datos de cobranza diaria
		$cob = "SELECT importe from cobranza
				WHERE fecha = '$fecha'
				AND numero_caja = '$numero_caja'
				order by numero limit 1";
		$res_cob = mysqli_query($connection, $cob);
		$datos_cob = mysqli_fetch_array($res_cob);

		if($res_cob->num_rows > 0)
			$monto = $datos_cob['importe'] > 0 ? $datos_cob['importe'] : 0;
		// if($datos_cob['importe'] <> [])
		// {
		// 	$monto = $datos_cob['importe'];
		// }


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

	}
	else
	{ 
		if($op == 2)
		{
			$moneda = 'dolares';			
		}
		else{
			if($op == 3)
				$moneda = 'euros';
			else $moneda = 'cheques';			
		} 
	}

	// vacio la columna con la moneda indicada
	$query_empty = "UPDATE caja_gral SET $moneda = 0 
				   where numero_caja = '$numero_caja' 
				   AND operacion = '$op'
				   AND fecha = '$fecha'";
	$result_empty = mysqli_query($connection, $query_empty);

	// Marco la fila en cuestion como  'anulada'
	$set = "UPDATE caja_gral SET anulado = 1
			WHERE numero = '$fila'
			AND numero_caja = '$numero_caja'
			AND fecha = '$fecha'
			AND operacion = '$op'";
	mysqli_query($connection,$set);


	// busqueda de numeros (movimientos)
	$qr = "SELECT numero FROM caja_gral 
		  where numero_caja = '$numero_caja' 
		  AND fecha = '$fecha'
		  AND operacion = '$op'
		  AND numero  != '$fila'
		  AND anulado = 0";
	

	$res = mysqli_query($connection, $qr);
	$cantidad = $res->num_rows; // cantidad de numeros obtenidos

	$k = 0; $t = 0;
	$lista = array();
	while ($r = mysqli_fetch_array($res))
	{
		
		$lista[$k] = $r['numero']; // obtengo una lista con los numeros
		$k++;	
	}

	$inicial = $lista[$t];
	//echo print_r($lista); exit;
	$query_get_data = "SELECT * FROM caja_gral 
					   where numero_caja = '$numero_caja'
					   and operacion = '$op' 
					   AND fecha = '$fecha'
					   AND anulado = 0
					   ORDER BY numero 
					   LIMIT 1"; // datos para actualizar columna con la moneda indicada (primer fila)
	$result_get_data = mysqli_query($connection, $query_get_data);
	$data = mysqli_fetch_array($result_get_data);

	// cactualizamos los campos con cobranza diaria
	if($monto > 0.00)
	{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE caja_gral SET $moneda = '$monto' + '$ing_servicio' + '$pde'  
					WHERE numero = '$inicial'
					AND numero_caja = '$numero_caja' ";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE caja_gral SET $moneda = '$monto' + '$ing_servicio' - '$pde' 
					WHERE numero = '$inicial'
				 	AND numero_caja = '$numero_caja'";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
			}
	}
	else{
		if($data['ingreso'] > 0)
		{
			$pde = $data['ingreso'];
			$update = "UPDATE caja_gral SET $moneda = 0 + '$pde' 
				WHERE numero = '$inicial'
				AND numero_caja = '$numero_caja' ";
			$result_update = mysqli_query($connection, $update); // actualizo la primer fila con la moneda indicada
			//echo 1; exit;
		}
		else
			if($data['egreso'] > 0)
			{
				$pde = $data['egreso'];
				$update = "UPDATE caja_gral SET $moneda = (-1) * '$pde'  
					WHERE numero = '$inicial'
					AND numero_caja = '$numero_caja' ";
				$result_update = mysqli_query($connection, $update);// actualizo la primer fila con la moneda indicada
				//echo 1; exit;
			}
	}

	for($i=0; $i <= $cantidad; $i++)
	{
		if(($i+1) <= $cantidad -1) //-1
		{
			
			$m = $lista[$i]; // fila superior
			$n = $lista[$i+1]; // fila inferior
			
			//echo "m = ".$lista[$i]." - n = ".$lista[$i+1];exit;

			$qry = "SELECT * FROM caja_gral
					WHERE numero = '$n'
					and numero_caja = '$numero_caja'";
			$res = mysqli_query($connection,$qry);
			$dta = mysqli_fetch_array($res);
			$ingreso = $dta['ingreso'];
			$egreso = $dta['egreso'];
				
			if($ingreso > 0)
			{
				$qry = "SELECT * FROM caja_gral
						WHERE numero = '$m'
						and numero_caja = '$numero_caja'";
				$res = mysqli_query($connection,$qry);
				$dta = mysqli_fetch_array($res);
				$pde = $dta[$moneda];
					
				$update = "UPDATE caja_gral SET $moneda = '$pde' + '$ingreso' 
							WHERE numero = '$n' 
							AND operacion = '$op'
							AND fecha = '$fecha'";
				$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
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
						
					$update = "UPDATE caja_gral SET $moneda = '$pde' - '$egreso'
								WHERE numero = '$n'
								AND operacion = '$op'
								AND fecha = '$fecha'";
					$result_update = mysqli_query($connection, $update); // actualizo todas las filas (con la moneda indicada)
				}
		}		
			
	}
}

function set_codigo($cadena)
{
	include('conexion.php');
	$let = "";
	$qry = "SELECT * FROM loteos WHERE nombre = '$cadena'";
	$res = mysqli_query($connection,$qry);
	$datos = mysqli_fetch_array($res);
	$let = $datos['codigo_loteo'];
	return $let;
}

function genera_num(int $num)
{

	switch($num)
	{
		case ($num<10):
			$num_recibo = '0000000'.$num;
			break;
		case ($num>=10 && $num<100):
			$num_recibo = '000000'.$num;
			break;
		case ($num>=100 && $num<1000):
			$num_recibo = '00000'.$num;
			break;
		case ($num>=1000 && $num<10000):
			$num_recibo = '0000'.$num;
			break;
		case ($num>=10000 && $num<100000):
			$num_recibo = '000'.$num;
			break;
		case ($num>=100000 && $num<1000000):
			$num_recibo = '00'.$num;
			break;
	}               

	return $num_recibo;
}

function get_code_recibo($lote, $numero, $fecha1, $fecha2){
	include('conexion.php');
	$codigos = -1;
	$t="";

	$sql = "SELECT  t1.codigo as codigo FROM 
			det_recibo as t1 inner join recibo as t2
			on t1.lote = t2.lote
			where t1.lote = '$lote' and t1.numero = '$numero'
			and t1.fecha BETWEEN '$fecha1' and '$fecha2'
			group by t1.codigo";
	
	$res = mysqli_query($connection, $sql);
	
	if($res->num_rows > 0)
	{
		while($codigos = mysqli_fetch_array($res))
		{
			switch($codigos['codigo']){
				case '001': $t.="COD".$codigos['codigo']." - "; break;
				case '002': $t.="COD".$codigos['codigo']." - "; break;
				case '003': $t.="COD".$codigos['codigo']." - "; break;
				case '004': $t.="COD".$codigos['codigo']." - "; break;
			}
			
		} 	
			
	}
	
	$t = substr($t,0,-2);
	
	return $t;
}

function get_code_concept(string $lote, $fecha_ini, $fecha_fin){
	include('conexion.php');
	$concepto = "";

	$sql1 = "SELECT numero FROM det_recibo 
			WHERE lote = '$lote'
			AND fecha BETWEEN '$fecha_ini' AND '$fecha_fin'";
	
	$res1 = mysqli_query($connection, $sql1);
	$numero = mysqli_fetch_array($res1);

	$sql = "SELECT codigo FROM det_recibo 
			WHERE lote = '$lote'
			AND fecha BETWEEN '$fecha_ini' AND '$fecha_fin'
			group by numero";
	$res = mysqli_query($connection, $sql);
	$cantidad = $res->num_rows;
	while($codigos = mysqli_fetch_array($res))
	{
		switch($codigos['codigo'])
		{
			case '001': $code = "COD1"; break;
			case '002': $code = "COD2";	break;
			case '003': $code = "COD3"; break;
			case '004': $code = "COD4"; break;
			case '005': $code = "COD5"; break; 		
		}
		$concepto.= $code." - ";		
	}
	
	return substr($concepto,0,-2);
}

function total_cobranza(int $numero_caja,$fecha_ini, $fecha_fin){
	include('conexion.php');
	
	$total = 0;
	if($fecha_ini == $fecha_fin)
	{
		$sql1 = "SELECT importe as importe FROM cobranza
				WHERE numero_caja = '$numero_caja'
				AND fecha BETWEEN '$fecha_ini' AND '$fecha_fin'";
		$res1 = mysqli_query($connection, $sql1);
		$dato_cobranza = mysqli_fetch_array($res1);

		$sql2 = "SELECT importe as importe FROM ingresos_servicios
				WHERE numero_caja = '$numero_caja'
				AND fecha BETWEEN '$fecha_ini' AND '$fecha_fin'";
		$res2 = mysqli_query($connection, $sql2);
		$dato_servicio = mysqli_fetch_array($res2);

	}
	else{
		if($fecha_ini < $fecha_fin)
		{
			$sql1 = "SELECT sum(importe) as importe FROM cobranza
				WHERE numero_caja = '$numero_caja'
				AND fecha BETWEEN '$fecha_ini' AND '$fecha_fin'";
			$res1 = mysqli_query($connection, $sql1);
			$dato_cobranza = mysqli_fetch_array($res1);

			$sql2 = "SELECT sum(importe) as importe FROM ingresos_servicios
					WHERE numero_caja = '$numero_caja'
					AND fecha BETWEEN '$fecha_ini' AND '$fecha_fin'";
			$res2 = mysqli_query($connection, $sql2);
			$dato_servicio = mysqli_fetch_array($res2);
		}
	}
	mysqli_close($connection);
	$total = ($dato_cobranza['importe'] + $dato_servicio['importe']);
	return $total;
	
}
?>
 
