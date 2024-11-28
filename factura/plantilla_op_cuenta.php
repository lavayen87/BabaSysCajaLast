<?php  

session_start();
include('../conexion.php');
include('../funciones.php');
if($_SESSION['active'])
{
  $miaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
}
  
//$numero_caja = $_GET['num_caja']; // Dato enviado por post
$fecha_inicial = $_GET['fecha_inicial'];
$fecha_final = $_GET['fecha_final'];
$empresa = $_GET['emp'];
$cuenta = $_GET['cta'];

$tabla = "<table class='table table-striped'>
<thead>  
<tr> 
<td><strong>N°</strong></td>                               
<td><strong>Fecha</strong></td>
<td><strong>caja</strong></td>
<td><strong>Empresa</strong></td>
<td><strong>Obra</strong></td>
<td><strong>Cuenta contable</strong></td>
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
        <span style="display: inline-block; padding-bottom: 8px; padding-left: 18%;"></span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            
            /*$get_fechas = "SELECT fecha FROM orden_pago_temp
                           WHERE numero_caja = '$numero_caja'";
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
                echo "Órdenes por cuenta (".$fecha1." - ".$fecha2.")";
            }
            else echo "Órdenes por cuenta (".$fecha1." - ".$fecha1.")";*/
            
            echo "Órdenes por cuenta (".fecha_min($fecha_inicial)." - ".fecha_min($fecha_final).")";
        ?>
    </div>
</div>


<?php

$p="<p>";
$p.="<label style='float: left;'>Fecha: ".fecha_min(date('Y-m-d'))." - Hora: ".date('G').':'.date('i').':'.date('s')."</label>";
$p.="<label style='float: right;'>Emitido por: ".$_SESSION['rol']." (caja ".$_SESSION['numero_caja'].")</label></p>"; 
echo $p."<br></br><br>";

$total = 0.00;
// ORDENES DE PAGO POR FECHAS

       
/*$fecha_inicial = $lista[0];
$fecha_final   = $lista[$n-1];*/             


if($numero_caja == 0 || $numero_caja == 12 || $numero_caja == 9 || $numero_caja == 2)
{
  //Consulta de datos
  $qry = "SELECT
              c.anulado anulado,
              c.numero_caja,
              o.numero_orden,
              o.fecha,
              o.empresa,
              o.obra,
              o.cuenta,
              o.detalle,
              o.importe
            FROM orden_pago o inner join caja_gral c 
            on o.numero_orden = c.numero
            WHERE (o.cuenta = '$cuenta' or '$cuenta' = '')
            AND (o.empresa = '$empresa' or '$empresa' = '')
            AND o.fecha between '$fecha_inicial' and '$fecha_final'";

  $res = mysqli_query($connection, $qry);

  // Importe Total
  $q = "SELECT sum(o.importe) as total FROM orden_pago o inner join caja_gral c 
            on o.numero_orden = c.numero
            WHERE (o.cuenta = '$cuenta' or '$cuenta' = '')
            AND (o.empresa = '$empresa' or '$empresa' = '')
            AND o.fecha between '$fecha_inicial' and '$fecha_final'
            AND c.anulado = 0";

  $r = mysqli_query($connection, $q);
}
else
{

  //Consulta de datos
  $qry = "SELECT
              c.anulado anulado,
              c.numero_caja,
              o.numero_orden,
              o.fecha,
              o.empresa,
              o.obra,
              o.cuenta,
              o.detalle,
              o.importe
            FROM orden_pago o inner join caja_gral c 
            on o.numero_orden = c.numero
            WHERE (o.cuenta = '$cuenta' or '$cuenta' = '')
            AND (o.empresa = '$empresa' or '$empresa' = '')
            AND o.fecha between '$fecha_inicial' and '$fecha_final'
            AND c.numero_caja = '$numero_caja'";

  $res = mysqli_query($connection, $qry);

  // Importe Total
  $q = "SELECT sum(o.importe) as total FROM orden_pago o inner join caja_gral c 
            on o.numero_orden = c.numero
            WHERE (o.cuenta = '$cuenta' or '$cuenta' = '')
            AND (o.empresa = '$empresa' or '$empresa' = '')
            AND o.fecha between '$fecha_inicial' and '$fecha_final'
            AND c.anulado = 0
            AND c.numero_caja = '$numero_caja'";

  $r = mysqli_query($connection, $q);
}  

if($r)
{
  $get_total = mysqli_fetch_array($r);
  $total = $get_total['total']; 
}                          

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
      <td style='width:30%;'>".limitar_cadena($d['detalle'],35)."</td>
      <td style='text-align: right;'>".$importe_op."</td>
      </tr>";

    }
    mysqli_close ($connection); 
    $tabla.="<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style='text-align: center;'><strong>Total</strong></td>
    <td style='text-align: right;'>"."$".number_format($total,2,',','.')."</td>
    </tr>
    </tbody>";
    echo $tabla;               
         
?>
</body>
</html>






                


                