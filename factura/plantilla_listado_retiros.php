<?php 

session_start();
include('../conexion.php');
include('../funciones.php');
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$numero_caja = $_SESSION['numero_caja'];
}
	
$personal = $_GET['personal']; // Dato enviado por post
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<link rel="stylesheet" href="../css/table-style.css">
</head>
<body>

<div style="background-color: #d6e9c6; width: 100%; height: 4%; padding: 8px 8px;">
    <div style="float: left;">
        <img src="img/baba-img2.png" style="height: 40px; width: 40px; padding-top: 8px;">
        <span style="display: inline-block; padding-bottom: 8px;">Baba Urbanizaciones </span>
        <span style="display: inline-block; padding-bottom: 8px; padding-left: 18%;">  <?php //echo $numero_caja; ?></span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            
             echo "Retiros (".fecha_min($fecha1)." - ".fecha_min($fecha2).")";
        ?>
    </div>
</div>

<br>

<?php

$tabla = "<table>
<thead>  
<tr> 
<td><strong>Fecha</strong></td>
<td><strong>Nº caja</strong></td>
<td><strong>Personal</strong></td>
<td><strong>Concepto</strong></td>
<td><strong>Cuenta</strong></td>
<td><strong>Detalle</strong></td>
<td><strong>Importe</strong></td>
</tr>
</thead>
<tbody id='tbody-datos'>";
$p="<p>";
$p.="<label style='float: left;'>Fecha: ".fecha_min(date('Y-m-d'))." - Hora: ".date('G').':'.date('i').':'.date('s')."</label>";
$p.="<label style='float: right;'>Emitido por: ".$_SESSION['rol']." (caja ".$_SESSION['numero_caja'].")</label></p>"; 
echo $p."<br></br><br>";

$qry = "SELECT sum(importe) as total from retiros
		WHERE personal_habilitado = '$personal'
		and fecha_retiro BETWEEN '$fecha1' and '$fecha2' ";

$res_qry = mysqli_query($connection, $qry);

$get = mysqli_fetch_array($res_qry);

$total = $get['total'];

$select = "SELECT * from retiros
		WHERE personal_habilitado = '$personal'
		and fecha_retiro BETWEEN '$fecha1' and '$fecha2' ";
                    
$res = mysqli_query($connection, $select);
                          
while($d = mysqli_fetch_array($res))
{	

	$tabla.= "<tr>
	<td style='width:7%;'>".fecha_min($d['fecha_retiro'])."</td>

	<td style='width:5%; text-align: center;'>".$d['numero_caja']."</td>

	<td style='width:18%;'>".limitar_cadena($d['personal_habilitado'],15)."</td>

	<td style='width:18%; text-align: center;'>".limitar_cadena($d['concepto'],20)."</td>

	<td style='width:20%;'>".limitar_cadena($d['cuenta'],20)."</td>

	<td style='width:22%;'>".limitar_cadena($d['observaciones'],20)."</td>

	<td style='text-align: right; width:18%;'>".number_format($d['importe'],2,',','.')."</td>
	</tr>";

	
}

$tabla.="<tr><td colspan = '7' height='5px;'></td></tr> ";
$tabla.="<tr>
		<td></td> 
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style='text-align: center;'><strong>Total</strong></td>
		<td style='text-align: right;'>"."$".number_format($total,2,',','.')."</td> 
		</tr></tbody> ";
echo $tabla;

?>
</body>
</html>

