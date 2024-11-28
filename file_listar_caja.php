
<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

?>
 
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="img/logo-sistema.png"> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<style>

table thead tr td{
  padding-bottom: 5px; 
}
table thead{
  border-bottom: 1px solid black; 
}
tr:nth-child(odd){
    background:;
}
  tr:nth-child(even){
    background: #B6CCB2;
}

td{
  font color: #60655F;
}
</style>
</head>
<body>
<div class="page-wrapper chiller-theme toggled">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  <?php include('menu_lateral.php');?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">
    <div class="container">
      <h2>Listado de Caja</h2>
      <hr>
      <div class="row">
        
         
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->
            <div class="table-responsive">
              <p>
                <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                  <strong>Desde:</strong> 
                  <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>" style="width: 132px;">

                  <strong>Hasta:</strong> 
                  <input type="date" name="fecha_final" id="fecha_final" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>" style="width: 132px;">

                  <strong>Caja:</strong> 
                  <select name="num_caja" id="select-num-caja">
                  <option value=""></option>
                    <!--option value="1" id='Cajero1'>Caja Cajero</option> 
                    <option value="3" id='Banco'>Banco</option>
                    <option value="4" id='Tesoro'>Caja Tesoro</option>
                    <option value="5" id='Luis B.'>Caja Luis B.</option>
                    <option value="6" id='Sergio B.'>Caja Sergio B.</option>
                    <option value="7" id='Daniel B.'>Caja Daniel B.</option>
                    <option value="8" id='Ariel M.'>Caja Ariel M.</option>
                    <option value="9" id='Admin1'>Caja Admin1</option>
                    <option value="10" id='Legales'>Caja Legales</option-->
                    <?php 
                      /*if($numero_caja == 0){
                       
                        echo "<option value='1' id='Cajero1'>Caja Cajero</option> 
                              <option value='3' id='Banco'>Banco</option>
                              <option value='4' id='Tesoro'>Caja Tesoro</option>
                              <option value='5' id='Luis B.'>Caja Luis B.</option>
                              <option value='6' id='Sergio B.'>Caja Sergio B.</option>
                              <option value='7' id='Daniel B.'>Caja Daniel B.</option>
                              <option value='8' id='Ariel M.'>Caja Ariel M.</option>
                              <option value='9' id='Admin1'>Caja Admin1</option>
                              <option value='10' id='Legales'>Caja Legales</option>";
                      }*/
                      include('conexion.php');
                      $qry = "Select * from usuarios where numero_caja <> 0 and block_caja = 1";
                      $res = mysqli_query($connection, $qry);
                      if($res->num_rows > 0){
                        while($datos = mysqli_fetch_array($res))
                        {
                            echo "<option value='".$datos["numero_caja"]."'>"."Caja ".$datos["rol"]."</option>";
                                      
                        }
                      }
                    ?>
                  </select>
                  <!--input type="number" name="num_caja" style="width: 60px;"-->
                  
                  <input type="submit" name="listar" value="Listar" id="btn-listar" class="btn btn-success">
                  
                  <br> 
                  
                </form>
                
              </p>
                  <!--div id="content-listado"></div-->
              <?php 
                    date_default_timezone_set('America/Argentina/Salta');
                    $caja = 0;
                    $fecha_inicial = "";
                    $fecha_final  = "";                   
                    $hoy = date('Y-m-d');
                    $cant ="";
                    $tabla = "";
                    $saldo_anterior = 0.00;
                    $saldo_anterior_dolares = 0.00;
                    $saldo_anterior_euros = 0.00;
                    $saldo_anterior_cheques = 0.00;
                    $total_dia = 0.00;
                    $total_dolares = 0.00;
                    $total_euros = 0.00;
                    $moneda_dolares = "dolares";
                    $moneda_euros = "euros";
                    $monto = 0.00;
                    $tg = 0.00;
                    //class='table table-striped table-hover'>
                    
                    
                    if(isset($_POST['listar']))
                    {
                  
                      if( isset($_POST['fecha_inicial']) && $_POST['fecha_inicial'] !="" 
                         && isset($_POST['fecha_final']) && $_POST['fecha_final'] !="" )
                      {
                        if($_POST['num_caja'] !="")
                        {
                          $caja = $_POST['num_caja'];
                          $cabecera = "<table style='width: 100%;'>
                                        <thead>  
                                        <tr> 
                                        <td><strong>N°</strong></td>
                                        <td><strong>Fecha</strong></td>
                                        <td><strong>Detalle</strong></td>
                                        <td><strong>Ingresos</strong></td>
                                        <td><strong>Egresos</strong></td>
                                        <td><strong>Pesos</strong></td>";
                                        if($caja !=3)
                                        {
                                          $cabecera.="<td><strong>Dolares</strong></td>
                                                      <td><strong>Euros</strong></td>
                                                      <td><strong>Cheque</strong></td>";
                                        }
                                        
                                        $cabecera.="</tr>
                                                    </thead>
                                                    <tbody id='tbody-datos'>";
                          $f1 = "";
                          $f2 = "";
                          if(isset($_POST['fecha_inicial']))
                          {
                            $f1 = $_POST['fecha_inicial'];
                          }
                        
                          if(isset($_POST['fecha_final']))
                          {
                            $f2 = $_POST['fecha_final'];
                          }
                          echo "<a href='factura/listado_pdf.php?caja=$caja&f1=$f1&f2=$f2' type='submit' name='print-listado'  id='listado-caja' target='_blank' style='' class='btn btn-primary'>Imprimir</a>";
                  
                          $fecha_inicial = $_POST['fecha_inicial'];
                          $fecha_final   = $_POST['fecha_final'];

                          include('conexion.php');
                          include('funciones.php');

                          // verificamos si existe la caja
                          $check_user = "SELECT * from usuarios
                                        where numero_caja = '$caja'";
                                      
                          $res_user = mysqli_query($connection, $check_user);

                          if($res_user->num_rows > 0)
                          {
                            // consigo nombre de cajero 
                            $dta = mysqli_fetch_array($res_user);
                            $nombre_usuario = $dta['nombre'];

                            // CARGO LISTA TEMPORARL
                            $delete = "DELETE FROM lista_temp WHERE numero_caja = '$caja'";
                            $res_delete = mysqli_query($connection, $delete);

                            $select_gral = "SELECT * from caja_gral
                                          where numero_caja = '$caja'
                                          AND fecha >= '$fecha_inicial' AND fecha <= '$fecha_final'";
                            $res_gral = mysqli_query($connection, $select_gral);
                          
                            while($array = mysqli_fetch_array($res_gral))
                            {
                              $num      = $array['numero'];
                              $num_caja = $array['numero_caja'];
                              $fecha    = $array['fecha'];
                              $detalle  = $array['detalle'];
                              $ingreso  = $array['ingreso'];
                              $egreso   = $array['egreso'];
                              $pesos    = $array['pesos'];
                              $dolares  = $array['dolares'];
                              $euros    = $array['euros'];
                              $cheques    = $array['cheques'];
                              $operacion= $array['operacion'];
                              $insert_temp = "INSERT INTO lista_temp 
                                              VALUES 
                                              ('$num',
                                               '$num_caja',
                                               '$fecha',
                                               '$fecha',
                                               '$detalle',
                                               '$ingreso',
                                               '$egreso',
                                               '$pesos',
                                               '$dolares',
                                               '$euros',
                                               '$cheques',
                                               '$operacion')";
                              $res_insert_temp = mysqli_query($connection, $insert_temp);
                            }
                          
                            ///////////////////////////////////////////////

                            if($fecha_inicial == $fecha_final) 
                            //si las fechas coinciden, mostramos el resultado con saldo anterior y totales.
                            {  

                              //Saldo anterior en pesos, dolares y euros
                              $saldo_temp = "SELECT * FROM caja_gral_temp
                                              WHERE fecha = date_add('$fecha_inicial', INTERVAL -1 DAY)
                                              and numero_caja = '$caja'
                                              and operacion = 1
                                              order by numero desc limit 1";
                              $res_temp = mysqli_query($connection, $saldo_temp);
                              
                              if($res_temp->num_rows > 0)
                              {
                                $datos_temp = mysqli_fetch_assoc($res_temp);
                                $saldo_anterior = $datos_temp['pesos'];
                                $saldo_anterior_dolares = $datos_temp['dolares'];
                                $saldo_anterior_euros = $datos_temp['euros'];
                                $saldo_anterior_cheques = $datos_temp['cheques'];
                              }
                              else       
                              {
                                $saldo_temp = "SELECT * FROM caja_gral_temp
                                              WHERE fecha < '$fecha_inicial'
                                              and numero_caja = '$caja'
                                              and operacion = 1
                                              order by numero desc limit 1";
                                $res_temp = mysqli_query($connection, $saldo_temp);
                                if($res_temp->num_rows > 0)
                                {
                                  $datos_temp = mysqli_fetch_assoc($res_temp);
                                  $saldo_anterior = $datos_temp['pesos'];
                                  $saldo_anterior_dolares = $datos_temp['dolares'];
                                  $saldo_anterior_euros = $datos_temp['euros'];
                                  $saldo_anterior_cheques = $datos_temp['cheques'];
                                }
                              } 
                              
                              // Consigo datos de cobranza diaria
                              $cob = "SELECT importe from cobranza
                                      WHERE fecha = '$fecha_inicial'
                                      and numero_caja = '$caja'
                                      order by numero limit 1";
                             $res_cob = mysqli_query($connection, $cob);
                              $datos_cob = mysqli_fetch_array($res_cob);

                              if($datos_cob['importe'] <> [])
                              {
                                $monto = $datos_cob['importe'];
                              }                     

                              // Consigo total servicios
                              $sql = "SELECT sum(importe) as total_serv FROM ingresos_servicios
                                      WHERE numero_caja = '$caja'
                                      AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'";
                              $sql_res = mysqli_query($connection, $sql);

                              if($sql_res->num_rows > 0){
                                  $datos_servicios = mysqli_fetch_array($sql_res);
                                  $monto_serv = $datos_servicios['total_serv'];
                              }
                              else $monto_serv = 0;

                              // calculamos el total del dia
                              $query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                            where (fecha >= '$fecha_inicial') 
                                            AND (fecha <= '$fecha_final') 
                                            AND (operacion = 1) 
                                            AND numero_caja = '$caja'";         
                              $result_total = mysqli_query($connection, $query_total);
                              $total = mysqli_fetch_array($result_total);
                                
                              $total_dia = $total['total'];    

                              // total de dolares
                              $query_dolares = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                          WHERE (fecha >= '$fecha_inicial') 
                                          AND (fecha <= '$fecha_final')
                                          AND numero_caja = '$caja'
                                          AND operacion = 2";                
                              $result_total = mysqli_query($connection, $query_dolares);
                              $total = mysqli_fetch_array($result_total);
                              $total_dolares = round($total['total']);

                              //total de euros 
                              $query_euros = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                          WHERE (fecha >= '$fecha_inicial') 
                                          AND (fecha <= '$fecha_final')
                                          AND numero_caja = '$caja'
                                          AND operacion = 3";                
                              $result_total = mysqli_query($connection, $query_euros);
                              $total = mysqli_fetch_array($result_total);
                              $total_euros = round($total['total']);
                
                              //total de cheques 
                              $query_euros = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                          WHERE (fecha >= '$fecha_inicial') 
                                          AND (fecha <= '$fecha_final')
                                          AND numero_caja = '$caja'
                                          AND operacion = 4";                
                              $result_total = mysqli_query($connection, $query_euros);
                              $total = mysqli_fetch_array($result_total);
                              $total_cheques = $total['total'];

                              ////// mostramos los datos ///////
                              $tabla.=$cabecera;
                               
                              $qry = "SELECT * from caja_gral 
                                      where fecha >= '$fecha_inicial' AND fecha <= '$fecha_final'
                                      AND numero_caja = '$caja'
                                      order by numero";
                              $res = mysqli_query($connection,$qry);
                              
                              if($caja == 1 || $caja == 9 || $caja == 10)
                              {
                                $total_cobranza = total_cobranza($caja,$fecha_inicial,$fecha_final);
                                $tabla.= "<tr style='border-bottom: 1px solid black;'>                                     
                                          <td colspan='8'><strong>Total cobrado: "."$".number_format($total_cobranza,2,',','.')."</strong></td>                                    
                                          </tr>";
                              }
                              else
                                  $total_cobranza = total_cobranza($caja,$fecha_inicial,$fecha_final);

                              while($datos = mysqli_fetch_array($res))
                              {
                                $tabla.= "<tr>
                                      <td>".$datos['numero']."</td>
                                      <td style='width: 5%;'>".fecha_min($datos['fecha'])."</td>
                                      <td style='width: 27%;'>".limitar_cadena($datos['detalle'],30)."</td>
                                      <td>".number_format($datos['ingreso'],2,',','.')."</td>
                                      <td>".number_format($datos['egreso'],2,',','.')."</td>
                                      <td>".number_format($datos['pesos'],2,',','.')."</td>";
                                      if($caja <> 3)
                                      $tabla.="<td>".number_format($datos['dolares'],2,',','.')."</td>
                                      <td>".number_format($datos['euros'],2,',','.')."</td>
                                      <td>".number_format($datos['cheques'],2,',','.')."</td>
                                      </tr>";
                                
                              }
                              
                              if($caja <> 3)
                              { 
                                if($caja==1 || $caja==9 || $caja==10)
                                {
                                    $cobranza = "<strong>Cobranza:</strong>";
                                    $monto_cobranza = "<strong> $".number_format($monto,2,',','.')."</strong>";
                                    $servicios = "<strong>Servicios</strong>";
                                    $monto_servicios = "<strong> $".number_format($monto_serv,2,',','.')."</strong>";
                                }
                                else{
                                    $cobranza = "";
                                    $monto_cobranza = "";
                                    $servicios = "";
                                    $monto_servicios = "";
                                }
                                $tabla.= "<tr>
                                        <td></td>
                                        <td>".$cobranza."</td>
                                        <td>".$monto_cobranza."</td><td>
                                        </td><td>"."<strong>Saldo anterior</strong>"."</td>
                                        <td>"."$".number_format($saldo_anterior,2,',','.')."</td>
                                        <td>"."US".number_format($saldo_anterior_dolares,2,',','.')."</td>
                                        <td>"."€".number_format($saldo_anterior_euros,2,',','.')."</td>
                                        <td>"."$".number_format($saldo_anterior_cheques,2,',','.')."</td>
                                        </tr>
                                    
                                        <tr>
                                        <td></td>
                                        <td>".$servicios."</td>
                                        <td>".$monto_servicios."</td>
                                        <td></td>
                                        <td>"."<strong>Total del dia</strong>"."</td>
                                        <td>".'$'.number_format(($monto+$monto_serv+$total_dia),2,',','.')."</td>
                                        <td>".'US'.number_format($total_dolares,2,',','.')."</td>
                                        <td>"."€".number_format($total_euros,2,',','.')."</td>
                                        <td>"."$".number_format($total_cheques,2,',','.')."</td>
                                        </tr>
                                    
                                        <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>"."<strong>Total General</strong>"."</td>
                                        <td>".'$'.number_format(($saldo_anterior + $monto + $monto_serv + $total_dia),2,',','.')."</td>
                                        <td>".'US'.number_format(($saldo_anterior_dolares + $total_dolares),2,',','.')."</td>
                                        <td>".'€'.number_format(($saldo_anterior_euros + $total_euros),2,',','.')."</td>
                                        <td>".'$'.number_format(($saldo_anterior_cheques + $total_cheques),2,',','.')."</td>
                                        </tr>";
                              }
                              $tabla.="</tbody>";

                              echo "<hr>";

                              echo "<strong>Caja</strong> "."<strong style='font: oblique bold 100% cursive;'>".$nombre_usuario." ($caja)"."</strong></br>";

                              echo "<hr>";
                              echo $tabla;

                              $qry = "SELECT COUNT(*) as cantidad FROM lista_temp WHERE numero_caja = '$caja'";
                              $res = mysqli_query($connection, $qry);
                              $num_countidad = mysqli_fetch_array($res);
                              
                              if($num_countidad['cantidad'] > 0)
                              {
                                echo "<script>$('#listado-caja').show();</script>";
                              }

                            } 
                            else //caso en que las fechas son distintas
                            {
                              if($fecha_inicial < $fecha_final)
                              {
                                // consigo total de cobranza para esas fechas
                                $qry_cob = "SELECT sum(importe) as total_cob from cobranza
                                                where (fecha >= '$fecha_inicial') 
                                                AND (fecha <= '$fecha_final') 
                                                AND numero_caja = '$caja'";
                                $res_cob = mysqli_query($connection,$qry_cob);
                                $datos_cob = mysqli_fetch_array($res_cob);
                                $monto = $datos_cob['total_cob'];

                                // Consigo total servicios
                                $sql = "SELECT sum(importe) as total_serv FROM ingresos_servicios
                                        WHERE numero_caja = '$caja'
                                        AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'";
                                $sql_res = mysqli_query($connection, $sql);

                                if($sql_res->num_rows > 0)
                                {
                                    $datos_servicios = mysqli_fetch_array($sql_res);
                                    $monto_serv = $datos_servicios['total_serv'];
                                }
                                else $monto_serv = 0;

                                //Saldo anterior en pesos, dolares y euros
                                $saldo_temp = "SELECT * FROM caja_gral_temp
                                                WHERE fecha = date_add('$fecha_inicial', INTERVAL -1 DAY)
                                                and numero_caja = '$caja'
                                                and operacion = 1
                                                order by numero desc limit 1";
                                $res_temp = mysqli_query($connection, $saldo_temp);
                                
                                if($res_temp->num_rows > 0)
                                {
                                  $datos_temp = mysqli_fetch_assoc($res_temp);
                                  $saldo_anterior = $datos_temp['pesos'];
                                  $saldo_anterior_dolares = $datos_temp['dolares'];
                                  $saldo_anterior_euros = $datos_temp['euros'];
                                  $saldo_anterior_cheques = $datos_temp['cheques'];
                                }
                                else       
                                {
                                  $saldo_temp = "SELECT * FROM caja_gral_temp
                                                WHERE fecha < '$fecha_inicial'
                                                and numero_caja = '$caja'
                                                and operacion = 1
                                                order by numero desc limit 1";
                                  $res_temp = mysqli_query($connection, $saldo_temp);
                                  if($res_temp->num_rows > 0)
                                  {
                                    $datos_temp = mysqli_fetch_assoc($res_temp);
                                    $saldo_anterior = $datos_temp['pesos'];
                                    $saldo_anterior_dolares = $datos_temp['dolares'];
                                    $saldo_anterior_euros = $datos_temp['euros'];
                                    $saldo_anterior_cheques = $datos_temp['cheques'];
                                  }
                                }

                                // calculamos el total del dia
                                $query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                                where (fecha >= '$fecha_inicial') 
                                                AND (fecha <= '$fecha_final') 
                                                AND (operacion = 1) AND numero_caja = '$caja'";                
                                $result_total = mysqli_query($connection, $query_total);
                                $total = mysqli_fetch_array($result_total);
                                
                                $total_dia = $total['total'];                              
                             

                                // total de dolares
                                $query_dolares = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                                WHERE fecha BETWEEN '$fecha_inicial' 
                                                AND '$fecha_final'
                                                AND numero_caja = '$caja'
                                                AND operacion = 2";                
                                $result_total = mysqli_query($connection, $query_dolares);
                                $total = mysqli_fetch_array($result_total);
                                $total_dolares = round($total['total']);

                                //total de euros 
                                $query_euros = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                                WHERE fecha BETWEEN '$fecha_inicial' 
                                                AND '$fecha_final'
                                                AND numero_caja = '$caja'
                                                AND operacion = 3";                
                                $result_total = mysqli_query($connection, $query_euros);
                                $total = mysqli_fetch_array($result_total);
                                $total_euros = round($total['total']);

                                //total de cheques 
                                $query_euros = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                                WHERE fecha BETWEEN '$fecha_inicial' 
                                                AND '$fecha_final'
                                                AND numero_caja = '$caja'
                                                AND operacion = 4";                
                                $result_total = mysqli_query($connection, $query_euros);
                                $total = mysqli_fetch_array($result_total);
                                $total_cheques = $total['total'];

                                // mostramos los datos
                                $query = "SELECT * from caja_gral
                                                where fecha >= '$fecha_inicial' AND fecha <= '$fecha_final'
                                                AND numero_caja = '$caja'
                                                order by numero";    
                                $result = mysqli_query($connection, $query);
                                $tabla.=$cabecera;

                                if($caja == 1 || $caja == 9 || $caja == 10)
                                {
                                  $total_cobranza = total_cobranza($caja,$fecha_inicial,$fecha_final);
                                  $tabla.= "<tr style='border-bottom: 1px solid black;'>                                     
                                            <td colspan='8'><strong>Total cobrado: "."$".number_format($total_cobranza,2,',','.')."</strong></td>                                    
                                            </tr>";
                                }
                                else
                                    $total_cobranza = total_cobranza($caja,$fecha_inicial,$fecha_final);

                                while($datos = mysqli_fetch_array($result))
                                {
                                  $tabla.= "<tr>
                                  <td>".$datos['numero']."</td>
                                  <td style='width: 5%;'>".fecha_min($datos['fecha'])."</td>
                                  <td style='width: 27%;'>".limitar_cadena($datos['detalle'],30)."</td>
                                  <td>".number_format($datos['ingreso'],2,',','.')."</td>
                                  <td>".number_format($datos['egreso'],2,',','.')."</td>
                                  <td>".number_format($datos['pesos'],2,',','.')."</td>";
                                  if($caja <> 3)
                                  $tabla.="<td>".number_format($datos['dolares'],2,',','.')."</td>
                                  <td>".number_format($datos['euros'],2,',','.')."</td>
                                  <td>".number_format($datos['cheques'],2,',','.')."</td>
                                  </tr>";                                 
                                }

                                if($caja <> 3)
                                {
                                  if($caja==1 || $caja==9 || $caja==10)
                                  {
                                      $cobranza = "<strong>Cobranza:</strong>";
                                      $monto_cobranza = "<strong> $".number_format($monto,2,',','.')."</strong>";
                                      $servicios = "<strong>Servicios</strong>";
                                      $monto_servicios = "<strong> $".number_format($monto_serv,2,',','.')."</strong>";
                                  }
                                  else{
                                      $cobranza = "";
                                      $monto_cobranza = "";
                                      $servicios = "";
                                      $monto_servicios = "";
                                  }
                                $tabla.= "<tr>
                                          <td></td>
                                          <td>".$cobranza."</td>
                                          <td>".$monto_cobranza."</td>
                                          <td></td>
                                          <td>"."<strong>Saldo anterior</strong>"."</td>
                                          <td>"."$".number_format($saldo_anterior,2,',','.')."</td>
                                          <td>"."US".number_format($saldo_anterior_dolares,2,',','.')."</td>
                                          <td>"."€".number_format($saldo_anterior_euros,2,',','.')."</td>
                                          <td>"."$".number_format($saldo_anterior_cheques,2,',','.')."</td>
                                          </tr>
                                    
                                          <tr>
                                          <td></td>
                                          <td>".$servicios."</td>
                                          <td>".$monto_servicios."</td>
                                          <td></td>
                                          <td>"."<strong>Total del dia</strong>"."</td>
                                          <td>".'$'.number_format(($monto+$monto_serv+$total_dia),2,',','.')."</td>
                                          <td>".'US'.number_format($total_dolares,2,',','.')."</td>
                                          <td>"."€".number_format($total_euros,2,',','.')."</td>
                                          <td>"."€".number_format($total_cheques,2,',','.')."</td>
                                          </tr>
                                    
                                          <tr>
                                          <td></td>
                                          <td></td>
                                          <td></td>
                                          <td></td>
                                          <td>"."<strong>Total General</strong>"."</td>
                                          <td>".'$'.number_format(($saldo_anterior + $monto + $monto_serv + $total_dia),2,',','.')."</td>
                                          <td>".'US'.number_format(($saldo_anterior_dolares + $total_dolares),2,',','.')."</td>
                                          <td>".'€'.number_format(($saldo_anterior_euros + $total_euros),2,',','.')."</td>
                                          <td>".'$'.number_format(($saldo_anterior_cheques + $total_cheques),2,',','.')."</td>
                                          </tr>";
                                }
                                $tabla.="</tbody>";

                                echo "<hr>";
                                echo "<strong>Caja</strong> "."<strong style='font: oblique bold 100% cursive;'>".$nombre_usuario." ($caja)"."</strong></br>";
                                echo "<hr>";
                                echo $tabla;
                                  
                                $qry = "SELECT COUNT(*) as cantidad FROM lista_temp WHERE numero_caja = '$caja'";
                                $res = mysqli_query($connection, $qry);
                                $num_countidad = mysqli_fetch_array($res);
                                  
                                if($num_countidad['cantidad'] > 0)
                                {
                                  echo "<script>$('#listado-caja').show();</script>";
                                }
                              }
                              else echo "<strong style='color: #AD0A35;'>Fechas incorrectas !</strong>";
                            }// end else tabla general
                          }
                          else
                          {
                            echo "<script>$('#listado-caja').hide();</script>";
                            echo "<strong>"."No existe la caja Nº $caja"."</strong>";
                          }
                          
                        }
                        else{
                          echo "<strong>Debe seleccionar una caja.</strong>";
                        }
                      
                      }
                      else echo "<strong style='color: #AD0A35;'>Fechas incorrectas !</strong>";
                    }
                    else echo "<strong style='color: #217DB1;'>Ingrese fechas y la caja para listar</strong>";
                   
              ?>     
          
            </div>
          </div>

      </div>

         
    </div>
      
      <footer class="text-center">
        <div class="mb-2">
          <small>
          
          </small>
        </div>
      </footer>

    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>