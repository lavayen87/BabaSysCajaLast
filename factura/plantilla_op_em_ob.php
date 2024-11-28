<?php  

session_start();
include('../conexion.php');
include('../funciones.php');
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
}
	
$numero_caja = $_GET['num_caja']; // Dato enviado por post
if(isset($_GET['fecha1']) && $_GET['fecha2'])
{
    $f1 = $_GET['fecha1'];
    $f2 = $_GET['fecha2'];
}
else{
    $f1 ="";
    $f2 ="";
}

if(isset($_GET['emp']) && isset($_GET['ob']))
{
    $empresa = $_GET['emp'];
    $obra = $_GET['ob'];
}
else{
    $empresa = "";
    $obra = "";
}

$tabla = "<table>
<thead>  
<tr> 
<td><strong>N°</strong></td>
<td><strong>Fecha</strong></td>
<td><strong>Caja</strong></td>
<td><strong>Empresa</strong></td>
<td><strong>Obra</strong></td>
<td><strong>Cuenta</strong></td>
<td><strong>Detalle</strong></td>
<td><strong>Importe</strong></td>
</tr>
</thead>
<tbody id='tbody-datos'>";

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
        <span style="display: inline-block; padding-bottom: 8px; padding-left: 18%;"> Caja <?php echo $numero_caja; ?></span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            if($f1 =="" && $f2=="")
            {
                $get_fechas = "SELECT fecha FROM orden_pago_temp 
                            WHERE empresa = 'Buen Clima' 
                            AND obra = 'Buen clima'";
                $res = mysqli_query($connection, $get_fechas);
                $n = $res->num_rows;
                if($n > 0)
                {
                    $k = 0;
                    $lista = array();
                    while ($fechas = mysqli_fetch_array($res))
                    {
                        $lista[$k] = $fechas['fecha'];
                        $k++; // obtengo una lista de fechas
                    }
                }
                $f1 = $lista[0];
                $f2 = $lista[$n-1];
                if($n > 1)
                {
                    echo "Órdenes de pago (".fecha_min($f1)." - ".fecha_min($f2).")";
                }
                else echo "Órdenes de pago (".fecha_min($f1)." - ".fecha_min($f1).")";
            }
            else
                echo "Órdenes de pago (".fecha_min($f1)." - ".fecha_min($f2).")";
        ?>
    </div>
</div>

<br>

<?php

$p="<p>";
$p.="<label style='float: left;'>Fecha: ".fecha_min(date('Y-m-d'))." - Hora: ".date('G').':'.date('i').':'.date('s')."</label>";
$p.="<label style='float: right;'>Emitido por: ".$_SESSION['rol']." (caja ".$_SESSION['numero_caja'].")</label></p>"; 
echo $p."<br></br><br>";

if($numero_caja == 22)
{
    $select = "SELECT * from orden_pago_temp
              WHERE fecha BETWEEN '$f1' AND '$f2'
              AND empresa = 'Buen Clima' 
              AND obra = 'Buen clima' ";
    $res = mysqli_query($connection, $select);

    /////////////////////////////////////////////

    $qry = "SELECT sum(importe) as total from orden_pago_temp
		    where fecha BETWEEN '$f1' and '$f2'
            AND empresa = 'Buen Clima' 
            AND obra = 'Buen clima' ";

    $res_qry = mysqli_query($connection, $qry);

    $get = mysqli_fetch_array($res_qry);

    $total = $get['total'];
   
}
else{
    if($numero_caja == 0 || $numero_caja == 12 || $numero_caja == 9 || $numero_caja == 2)
    {
        // $select = "SELECT * from orden_pago_temp
        //         where fecha BETWEEN '$f1' and '$f2'";

        // $qry = "SELECT sum(importe) as total from orden_pago_temp
        //         where fecha BETWEEN '$f1' and '$f2'";

        $select = "SELECT 
                      cj.anulado anulado,
                      cj.numero_caja,
                      op.numero_orden,
                      op.fecha,
                      op.empresa,
                      op.obra,
                      op.cuenta,
                      op.detalle,
                      op.importe
                    FROM orden_pago op inner join caja_gral cj
                    on op.numero_orden = cj.numero
                    WHERE op.fecha between '$f1' and '$f2'
                    and (op.empresa = '$empresa' or '$empresa' = '') 
                    and (op.obra = '$obra' or '$obra' = '')                                   
                    order by op.numero_orden";
        $res = mysqli_query($connection, $select);        

        $qry = "SELECT sum(op.importe) as total 
                          FROM orden_pago op inner join caja_gral cj
                            on op.numero_orden = cj.numero
                          WHERE op.fecha BETWEEN '$f1' AND '$f2'
                            and (op.empresa = '$empresa' or '$empresa' = '') 
                            and (op.obra = '$obra' or '$obra' = '')
                            and cj.anulado = 0";
        $r = mysqli_query($connection, $qry);  
    
    }
    else
    {
        $select_gral = "SELECT 
                          cj.anulado anulado,
                          cj.numero_caja,
                          op.numero_orden,
                          op.fecha,
                          op.empresa,
                          op.obra,
                          op.cuenta,
                          op.detalle,
                          op.importe
                        FROM orden_pago op inner join caja_gral cj
                        on op.numero_orden = cj.numero
                        WHERE op.fecha between '$f1' and '$f2'
                        and (op.empresa = '$empresa' or '$empresa' = '') 
                        and (op.obra = '$obra' or '$obra' = '')                                   
                        and cj.numero_caja = '$numero_caja'
                        order by op.numero_orden";                    
                    
        $res = mysqli_query($connection, $select_gral);

        //calculo importe total 
        $qry = "SELECT sum(op.importe) as total 
              FROM orden_pago op inner join caja_gral cj
                on op.numero_orden = cj.numero
              WHERE cj.numero_caja = '$numero_caja'                        
                and op.fecha BETWEEN '$f1' AND '$f2'
                and (op.empresa = '$empresa' or '$empresa' = '') 
                and (op.obra = '$obra' or '$obra' = '')
                and cj.anulado = 0
                and cj.numero_caja = '$numero_caja'";
        $r = mysqli_query($connection, $qry);        
    } 

    if($r)
    {
        $get_total = mysqli_fetch_array($r);
        $total = $get_total['total'];
    }  
}
// volcado de datos

while($d = mysqli_fetch_array($res))
{
    $importe_op = $d['anulado'] == 1 ? "<s class='canceled'>"."$".number_format($d['importe'],2,',','.')."</s>"  : "$".number_format($d['importe'],2,',','.');
	$tabla.= "<tr>
	<td style='width:5%; text-align: center;'>".$d['numero_orden']."</td>
	<td style='width:7%;'>".fecha_min($d['fecha'])."</td>
	<td style='width:5%; text-align: center;'>".$d['numero_caja']."</td>
	<td style='width:12%; text-align: center;'>".limitar_cadena($d['empresa'],20)."</td>
	<td style='width:12%; text-align: center;'>".limitar_cadena($d['obra'],15)."</td>
	<td style='width:17%;'>".limitar_cadena($d['cuenta'],18)."</td>
    <td style='width:25%;'>".limitar_cadena($d['detalle'],35)."</td>
	<td style='width:20%; text-align: right;'>".$importe_op."</td>
	</tr>";
	
}

$tabla.="<tr><td colspan = '8' height='5px;'></td></tr> ";
$tabla.="<tr>
		<td></td>
		<td></td> 
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style='text-align: center; width=5%'><strong>Total</strong></td>
		<td style='width:20%; text-align: right;'>"."$".number_format($total,2,',','.')."</td> 
		</tr> ";
echo $tabla;

?>
</body>
</html>

