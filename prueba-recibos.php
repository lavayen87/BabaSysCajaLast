<?php
date_default_timezone_set('America/Argentina/Salta');
include('conexion.php');
include('funciones.php');
$fecha = date('Y-m-d');
$lote = 'TE0015';

//echo print_r(num_recibos($lote,$fecha,$fecha));

$sql = "SELECT numero FROM det_recibo 
			WHERE lote = '$lote'
			AND fecha BETWEEN '$fecha' AND '$fecha'";
	
$res = mysqli_query($connection, $sql);

	if($res->num_rows > 0)
		 
    
        while($d=mysqli_fetch_array($res))
        {
            echo "Numero de recibo: ".$d['numero']."</br>";
        }



echo get_code_recibo($lote,$fecha,$fecha)." <br>";
?>