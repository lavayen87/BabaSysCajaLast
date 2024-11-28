<?php  


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
			$set_moneda = 'euros';
			$qry = "SELECT euros FROM caja_gral 
					WHERE fecha = '$fecha'
					and numero_caja = '$num_caja'
					and operacion = '$op'
					order by numero desc limit 1";
		}
	
	$res = mysqli_query($connection, $qry);
	$datos = mysqli_fetch_array($res);
	$pde = $datos[$moneda];

	return $pde;
}


$moneda = 'euros';

$fecha = '2021-07-15';

$op = 3;

$numero_caja = 34;

echo "Pesos en dia 15-07-2021: ". get_pde($moneda,$op,$numero_caja,$fecha);

?>