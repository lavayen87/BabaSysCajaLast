<?php  

session_start();

include('../conexion.php');
include('../funciones.php');

if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
}

// Datos enviados por get
//$numero_caja = $_GET['num_caja']; 
//$f1 = $_GET['fecha1'];
//$f2 = $_GET['fecha2'];

$tabla = "<table>
<thead>  
<tr> 
<td><strong>N°</strong></td>
<td><strong>Fecha</strong></td>
<td><strong>Caja</strong></td>
<td><strong>Ceunta</strong></td>
<td><strong>Empresa</strong></td>
<td><strong>Obra</strong></td>
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
        <span style="display: inline-block; padding-bottom: 8px; padding-left: 18%;"> Todas las cajas </span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            
            $get_fechas = "SELECT fecha FROM orden_pago";
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
            $fecha1 = fecha_min($lista[0]);
            $fecha2 = fecha_min($lista[$n-1]);
            if($n > 1)
            {
                echo "Todas la órdenes de pago (".$fecha1." - ".$fecha2.")";
            }
            else echo "Todas las órdenes de pago (".$fecha1." - ".$fecha1.")";
            //echo "Órdenes de pago (".fecha_min($f1)." - ".fecha_min($f2).")";
        ?>
    </div>
</div>


<?php

    $p="<p>";
    $p.="<label style='float: left;'>Fecha: ".fecha_min(date('Y-m-d'))." - Hora: ".date('G').':'.date('i').':'.date('s')."</label>";
    $p.="<label style='float: right;'>Emitido por: ".$_SESSION['rol']." (caja ".$_SESSION['numero_caja'].")</label></p>"; 
    echo $p."<br></br><br>";


    // $qry = "SELECT sum(importe) as total from orden_pago";

    // $res_qry = mysqli_query($connection, $qry);

    // $get = mysqli_fetch_array($res_qry);

    // $total = $get['total'];

    // $select = "SELECT * from orden_pago
    //           order by numero_orden";
                        
    // $res = mysqli_query($connection, $select);

    $qry = "SELECT * FROM orden_pago op inner join caja_gral cj
          on op.numero_orden = cj.numero
          where op.numero_orden
          order by op.fecha"; // agregar limit 110  o 500 para mostara y evitar el time out

    //$qry = "SELECT * FROM orden_pago WHERE numero_caja = 1 order by numero_orden limit 110";

    $res = mysqli_query($connection, $qry);

               
        //$q = "SELECT sum(importe) as total FROM orden_pago"; 
                          
        while($d = mysqli_fetch_array($res))
        {
            $importe_op = $d['anulado'] == 1 ? "<s class='candeled'>"."$".number_format($d['importe'],2,',','.')."</s>"  : "$".number_format($d['importe'],2,',','.');

        	$tabla.= "<tr>
        	<td style='width:5%; text-align: center;'>".$d['numero_orden']."</td>
        	<td style='width:7%;'>".fecha_min($d['fecha'])."</td>
        	<td style='width:5%; text-align: center;'>".$d['numero_caja']."</td>
        	<td style='width:12%; text-align: center;'>".limitar_cadena($d['empresa'],20)."</td>
        	<td style='width:12%; text-align: center;'>".limitar_cadena($d['obra'],15)."</td>
        	<td style='width:17%;'>".limitar_cadena($d['cuenta'],18)."</td>
            <td style='width:30%;'>".limitar_cadena($d['detalle'],35)."</td>
        	<td style='text-align: right; width:17%;'>".$importe_op."</td>
        	</tr>";
        	
        }  

        $q = "SELECT  ((SELECT sum(importe) FROM orden_pago) - (SELECT sum(op.importe)
              FROM orden_pago op inner join caja_gral cj
                on op.numero_orden = cj.numero
                WHERE cj.anulado = 1)
                )as total";   
       
        $r = mysqli_query($connection, $q); 

        $get_total = mysqli_fetch_array($r);

        $total = $get_total['total'];
        $tabla.="<tr><td colspan = '8' height='5px;'></td></tr> ";
        $tabla.="<tr>
        		<td></td>
        		<td></td> 
        		<td></td>
        		<td></td>
        		<td></td>
        		<td></td>
        		<td style='text-align: center;'><strong>Total</strong></td>
        		<td style='text-align: right; width=17%'>"."$".number_format($total,2,',','.')."</td> 
        		</tr> ";

        echo $tabla;
    

?>
</body>
</html>

