<?php 
date_default_timezone_set('America/Argentina/Salta'); 
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
}

$importe = $_POST['monto'];

$fecha = date('Y-m-d');

$total_actual = 0.00;
$saldo_anterior = 0.00;
$saldo_anterior_dolares = 0.00;
$saldo_anterior_euros = 0.00;
$total_gral_pesos = 0.00;
$total_gral_dolares = 0.00;
$total_gral_euros = 0.00;
$monto = 0.00;

include('conexion.php');
include('funciones.php');

// consigo ultimo importe de cobranza
$qry = "SELECT  importe from cobranza
		WHERE fecha = '$fecha' 
		AND numero_caja = '$numero_caja'
		order by numero desc limit 1";
$res = mysqli_query($connection, $qry);

$datos = mysqli_fetch_array($res);

$ultimo_cobro = $datos['importe'];

$num = $datos['numero'];

if($ultimo_cobro <> [])
{
	$qry = "UPDATE cobranza SET importe = '$importe' 
					WHERE fecha = '$fecha'
					and numero_caja = '$numero_caja'";
	$res = mysqli_query($connection, $qry);
}
else{
	//agregado  - codigo mariaDB
	$qry_num = "SELECT max(numero)+1 as numero FROM cobranza";
	$qry_res = mysqli_query($connection,$qry_num);
	$qry_datos = mysqli_fetch_array($qry_res);
	$numero = $qry_datos['numero']; 

	$qry = "INSERT INTO cobranza values('$numero','$numero_caja','$fecha','$importe')";
	$res = mysqli_query($connection, $qry);
}

// consigo el ultimo ingreso por servicios
$qry_serv = "SELECT  importe from ingresos_servicios
		WHERE fecha = '$fecha'
		AND numero_caja = '$numero_caja' 
		order by id DESC limit 1";
$res_serv = mysqli_query($connection, $qry_serv);
$datos_serv = mysqli_fetch_array($res_serv);

if($datos_serv['importe']<>[])
{
	$ultimo_ingreso = $datos_serv['importe']; 
}
else
	$ultimo_ingreso = 0; 
	

//Recalculamos la columna pesos
$query_empty = "UPDATE caja_gral SET pesos = 0 
						   where numero_caja = '$numero_caja' 
						   AND operacion = 1
						   AND fecha = '$fecha'";
$result_empty = mysqli_query($connection, $query_empty);// vacio la columna pesos

$qr = "SELECT numero FROM caja_gral 
		  where numero_caja = '$numero_caja' 
		  AND operacion = 1
		  AND fecha = '$fecha'";
$res = mysqli_query($connection, $qr); // busqueda de numeros
$cantidad = $res->num_rows; // cantidad de numeros obtenido

if($cantidad > 0)
{
	$k = 0;
	$lista = array();
	while ($r = mysqli_fetch_array($res))
	{
		$lista[$k] = $r['numero'];
		$k++;	// obtengo una lista con los numeros
	}

	$inicial = $lista[0];
}

// datos para actualizar columna pesos (la primer fila)
$query_get_data = "SELECT * FROM caja_gral 
					   		where numero_caja = '$numero_caja'
					   		and operacion = 1
					   		AND fecha = '$fecha'
					   		ORDER BY numero asc LIMIT 1"; 
$result_get_data = mysqli_query($connection, $query_get_data);
$data = mysqli_fetch_array($result_get_data);
if($data['ingreso'] > 0)
{
	$pesos = $data['ingreso'];
	$update = "UPDATE caja_gral SET pesos = '$importe' + '$ultimo_ingreso' + '$pesos'  
			   WHERE numero = '$inicial' and numero_caja = '$numero_caja'";
	$result_update = mysqli_query($connection, $update); // actualizo la primer fila en el campo pesos
}
else
	if($data['egreso'] > 0)
	{
		$pesos = $data['egreso'];
		$update = "UPDATE caja_gral SET pesos = '$importe' + '$ultimo_ingreso' - '$pesos'  
				  WHERE numero = '$inicial' and numero_caja = '$numero_caja'";
		$result_update = mysqli_query($connection, $update);// actualizo la primer fila en el campo pesos
	}

		// Actualizamos el resto de las filas
		for($i=0; $i <= $cantidad; $i++)
		{
			if(($i+1) <= $cantidad)
			{
				
				$n = $lista[$i+1]; // fila inferior
				$m = $lista[$i]; // fila superior
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
					$pesos = $dta['pesos'];
					
					$update = "UPDATE caja_gral SET pesos = '$pesos' + '$ingreso'  
							   WHERE numero = '$n' 
							   and numero_caja = '$numero_caja'
							   AND operacion = 1
							   AND fecha = '$fecha'";
					$result_update = mysqli_query($connection, $update); // actualizo las filas (campo pesos)
				}
				else
					if($egreso > 0)
					{
						$qry = "SELECT * FROM caja_gral
										WHERE numero = '$m'
										and numero_caja = '$numero_caja'"; 
						$res = mysqli_query($connection,$qry);
						$dta = mysqli_fetch_array($res);
						$pesos = $dta['pesos'];
						
						$update = "UPDATE caja_gral SET pesos = '$pesos' - '$egreso' 
								   WHERE numero = '$n'
								   and numero_caja = '$numero_caja'
								   AND operacion = 1
								   AND fecha = '$fecha'";
						$result_update = mysqli_query($connection, $update); // actualizo las filas (campo pesos)
					}
			}
			
		}

// Cargamos totales generales

$saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha);
$saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha);
$saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha);
$saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha);
			
// consigo total pesos, dolares, euros y cheques del dia
$pesos_hoy = get_total(1,$numero_caja,$fecha);
$dolares_hoy = get_total(2,$numero_caja,$fecha);
$euros_hoy = get_total(3,$numero_caja,$fecha);
$cheques_hoy = get_total(4,$numero_caja,$fecha);

// consigo ultima cobranza
$qry = "SELECT  importe from cobranza
					WHERE fecha = '$fecha' 
					AND numero_caja = '$numero_caja'
					order by numero limit 1";
$res = mysqli_query($connection, $qry);
$datos = mysqli_fetch_array($res);
$ultimo_cobro = $datos['importe'];

// ultimo ingreso por servicios
$ultimo_ingreso = 0;
$qry2 = "SELECT  importe from ingresos_servicios
		 WHERE fecha = '$fecha' 
		 AND numero_caja = '$numero_caja'
		 order by id DESC limit 1";
$res2 = mysqli_query($connection, $qry2);
$datos_ingresos = mysqli_fetch_array($res2);

if($datos_ingresos['importe']<>[])
	$monto_serv = $datos_ingresos['importe'];
else	
	$monto_serv = 0;

if( $ultimo_cobro<>[] )
{ 
	$monto = $ultimo_cobro;
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

echo "ok";

?>