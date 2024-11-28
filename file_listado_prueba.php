
<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $nombre_usuario = $_SESSION['nombre'];
  $micaja = $_SESSION['nombre_caja'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}

?>
 
<!DOCTYPE html>
<html>
<head>
  
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

<!-- cdn para alertas y confirmacion jquery -->

<!---->

<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
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
 
   
  <?php include('menu_black.php');?>
   
  <div class="container">
      <h2>Listado de Caja</h2>
      <hr>
       
      <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->
        <div class="table-responsive">

            <p>
              <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                <strong>Desde:</strong> 
                <input type="date" name="fecha_inicial" id="fecha_inicial" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>">

                <strong>Hasta:</strong> 
                <input type="date" name="fecha_final" id="fecha_final" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>">

                <!--strong>Nº de caja:</strong> <input type="number" name="num_caja" style="width: 60px;"-->
                <input type="submit" name="listar" value="Listar" id="btn-listar" class="btn btn-success" title='Listar caja'>
                    
                <?php  
                    echo "<a href='factura/listado_pdf.php?caja=$numero_caja' type='submit' name='print-listado' id='print-listado'  target='_blank' style='float: right; display: none;'' class='btn btn-primary' title='Imprimir'><i class='fas fa-print'></i></a>";
                ?>                     
              </form>
                  
            </p>
            <!--div id="content-listado"></div-->
            <?php 
            
            date_default_timezone_set('America/Argentina/Salta');
            $fecha_inicial = "";
            $fecha_final  = "";                   
            $hoy = date('Y-m-d');
            $cant ="";
            $tabla = "";
            $saldo_anterior = 0.00;
            $saldo_anterior_dolares = 0.00;
            $saldo_anterior_euros = 0.00;
            $total_dia = 0.00;
            $total_dolares = 0.00;
            $total_euros = 0.00;
            $moneda_dolares = "dolares";
            $moneda_euros = "euros";
            $monto = 0.00;
            $ing_pesos = 0;
            $ing_dolares = 0;
            $ing_euros = 0;
            $ing_cheques = 0;
            //class='table table-striped table-hover'>
            $cabecera = "<table style='width: 100%;' class='tabla-datos'>        
            <thead>  
            <tr> 
            <td><strong>N°</strong></td>
            <td><strong>Fecha</strong></td>
            <td><strong>Detalle</strong></td>
            <td><strong>Ingresos</strong></td>
            <td><strong>Egresos</strong></td>
            <td><strong>Pesos</strong></td>";
            if($numero_caja !=3){
              $cabecera.="<td><strong>Dolares</strong></td>
                          <td><strong>Euros</strong></td>
                          <td><strong>Cheque</strong></td>";
            }
            
            $cabecera.="<td><strong>Acción</strong></td>
            </tr>
            </thead>
            <tbody id='tbody-datos'>";
            
            if(isset($_POST['listar']))
            {
              
              if( isset($_POST['fecha_inicial']) && $_POST['fecha_inicial'] !="" 
               && isset($_POST['fecha_final']) && $_POST['fecha_final'] !="" )
              {
                $fecha_inicial = $_POST['fecha_inicial'];
                $fecha_final   = $_POST['fecha_final'];

                include('conexion.php');
                include('funciones.php');

                // CARGO LISTA TEMPORARL
                $delete = "DELETE FROM lista_temp 
                          WHERE numero_caja = '$numero_caja'";
                $res_delete = mysqli_query($connection, $delete);

                $select_gral = "SELECT * from caja_gral
                where numero_caja = '$numero_caja'
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
                  $cheque   = $array['cheques'];
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
                  '$cheque',
                  '$operacion')";
                  $res_insert_temp = mysqli_query($connection, $insert_temp);
                }
                
                ///////////////////////////////////////////////////////////////////

                //Buscamos Saldo anterior en pesos, dolares y euros caja de totales generales
                
                $saldo_anterior=saldo_ant('pesos',$numero_caja,$fecha_inicial);
                $saldo_anterior_dolares=saldo_ant('dolares',$numero_caja,$fecha_inicial);
                $saldo_anterior_euros=saldo_ant('euros',$numero_caja,$fecha_inicial);
                $saldo_anterior_cheques=saldo_ant('cheques',$numero_caja,$fecha_inicial);

                // Consigo datos de cobranza diaria
                $cob = "SELECT importe from cobranza
                WHERE fecha = '$fecha_inicial'
                and numero_caja = '$numero_caja'
                order by numero limit 1";
                $res_cob = mysqli_query($connection, $cob);
                $datos_cob = mysqli_fetch_array($res_cob);

                if($datos_cob['importe'] <> [])
                {
                  $monto = $datos_cob['importe'];
                }
                

                if($fecha_inicial == $fecha_final) 
                  //si las fechas coinciden, mostramos el resultado con saldo anterior y totales.
                {                        
                 
                  // calculamos el total del dia en pesos
                  $ing_pesos=get_total(1,$numero_caja, $fecha_final);

                  $query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                  where (fecha >= '$fecha_inicial') 
                  AND (fecha <= '$fecha_final') 
                  AND (operacion = 1) 
                  AND numero_caja = '$numero_caja'";         
                  $result_total = mysqli_query($connection, $query_total);
                  $total = mysqli_fetch_array($result_total);
                  
                  $total_dia = $total['total'];
                  // echo "total del dia: ".$saldo_final."</br>";    

                  // total de dolares
                  $ing_dolares=get_total(2,$numero_caja, $fecha_final);

                  $query_dolares = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                  WHERE (fecha >= '$fecha_inicial') 
                  AND (fecha <= '$fecha_final')
                  AND numero_caja = '$numero_caja'
                  AND operacion = 2";                
                  $result_total = mysqli_query($connection, $query_dolares);
                  $total = mysqli_fetch_array($result_total);
                  $total_dolares = round($total['total']);

                  //total de euros
                  $ing_euros=get_total(3,$numero_caja, $fecha_final); 

                  $query_euros = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                  WHERE (fecha >= '$fecha_inicial') 
                  AND (fecha <= '$fecha_final')
                  AND numero_caja = '$numero_caja'
                  AND operacion = 3";                
                  $result_total = mysqli_query($connection, $query_euros);
                  $total = mysqli_fetch_array($result_total);
                  $total_euros = round($total['total']);
                  

                  //total de cheques 
                  $ing_cheques=get_total(4,$numero_caja, $fecha_final);

                  $query_cheques = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                  WHERE (fecha >= '$fecha_inicial') 
                  AND (fecha <= '$fecha_final')
                  AND numero_caja = '$numero_caja'
                  AND operacion = 4";                
                  $result_total = mysqli_query($connection, $query_cheques);
                  $total = mysqli_fetch_array($result_total);
                  $total_cheques = $total['total'];


                  // total de ingresos y egresos
                  $total_ingresos = total_ingresos(1,$numero_caja,$fecha_final);
                  $total_egresos = total_egresos(1,$numero_caja,$fecha_final);

                  ////// mostramos los datos ///////
                  $tabla.=$cabecera;
                  
                  $qry = "SELECT * from caja_gral 
                  where fecha >= '$fecha_inicial' 
                  AND fecha <= '$fecha_final'
                  AND numero_caja = '$numero_caja'
                  order by numero";
                  $res = mysqli_query($connection,$qry);

                  

                  while($datos = mysqli_fetch_array($res))
                  {
                    $tabla.= "<tr>
                    <td style='width:4%;'>".$datos['numero']."</td>
                    <td style='width:5%;'>".fecha_min($datos['fecha'])."</td>
                    <td style='width:20%;'>".limitar_cadena($datos['detalle'],20)."</td>
                    <td>".number_format($datos['ingreso'],2,',','.')."</td>
                    <td>".number_format($datos['egreso'],2,',','.')."</td>
                    <td>".number_format($datos['pesos'],2,',','.')."</td>";
                    if($numero_caja != 3)
                    {
                      $tabla.="<td>".number_format($datos['dolares'],2,',','.')."</td>
                      <td>".number_format($datos['euros'],2,',','.')."</td>
                      <td>".number_format($datos['cheques'],2,',','.')."</td>";
                    }
                   

                    if($datos['fecha'] == date('Y-m-d'))
                    {
                      $tabla.= "<td>
                                  <button class='btn btn-secondary borrar'  id='".$datos['numero']."' title='Eliminar'>
                                  <i class='fas fa-trash-alt'></i>
                                  </button>
                                  </td>
                                  </tr>";
                      /*if(check_moneda($datos['numero'])!='cheques')
                      {
                        $tabla.= "<td>
                                  <button class='btn btn-secondary borrar'  id='".$datos['numero']."' title='Eliminar'>
                                  <i class='fas fa-trash-alt'></i>
                                  </button>
                                  </td>
                                  </tr>";
                      }
                      else
                      {
                        $tabla.= "<td></td></tr>";
                      }*/ 
                    }
                    else 
                      $tabla.= "<td></td></tr>";
                    
                  }
                  if($numero_caja != 3)
                  {
                  $tabla.="<tr style='background-color: transparent; border-bottom: 1px solid black;'>
                          <td></td>
                          <td></td>
                          <td>"."<strong>Totales</strong>"."</td>
                          <td>"."<strong>$".number_format($total_ingresos,2,',','.')."</strong>"."</td>
                          <td>"."<strong>$".number_format($total_egresos,2,',','.')."</strong>"."</td>
                          <td>"."<strong>$".number_format($ing_pesos,2,',','.')."</strong>"."</td>
                          <td>"."<strong>US".round($ing_dolares)."</strong>"."</td>
                          <td>"."<strong>€".round($ing_euros)."</strong>"."</td>
                          <td>"."<strong>$".number_format($ing_cheques,2,',','.')."</strong>"."</td>
                          <td></td>
                          <tr>";
                  
                  $tabla.= "<tr style='background-color: transparent;'>
                            <td></td>
                            <td id='id-cobranza'>"."<strong id='id-cobranza'>Cobranza:</strong>"."</td>
                            <td id='id-monto'>"."<strong id='id-monto'>"."$".number_format($monto,2,',','.')."</strong>"."</td>
                            <td></td>
                            <td>"."<strong>Sdo. anterior</strong>"."</td>
                            <td>"."$".number_format($saldo_anterior,2,',','.')."</td>
                            <td>"."US".$saldo_anterior_dolares."</td>
                            <td>"."€".$saldo_anterior_euros."</td>
                            <td>"."$".number_format($saldo_anterior_cheques,2,',','.')."</td>
                            <td></td>
                          </tr>

                          <tr style='background-color: transparent;'>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td>"."<strong>Total del dia</strong>"."</td>
                          <td>".'$'.number_format(($monto+$total_dia),2,',','.')."</td>
                          <td>".'US'.round($total_dolares)."</td>
                          <td>"."€".round($total_euros)."</td>
                          <td>"."$".number_format($total_cheques,2,',','.')."</td>
                          <td></td>
                          </tr>
                         
                          <tr style='background-color: transparent;'>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td>"."<strong>Total gral.</strong>"."</td>
                          <td>".'$'.number_format(($saldo_anterior + $monto + $total_dia),2,',','.')."</td>
                          <td>".'US'.($saldo_anterior_dolares + $total_dolares)."</td>
                          <td>".'€'.($saldo_anterior_euros + $total_euros)."</td>
                          <td>".'$'.number_format(($saldo_anterior_cheques + $total_cheques),2,',','.')."</td>
                          <td></td>
                          </tr>";
                    
                  $tabla.="</tbody>";
                  }
                  else{
                    $tabla.="</tbody>";

                  }
                  /*if($numero_caja <> 1 && $numero_caja <> 9 && $numero_caja <> 10)
                  {
                    $td_cobranza = 'id-cobranza';
                    $td_monto = 'id-monto';
                    echo "<script> 
                            $('strong[id='".$td_cobranza."']').html(''); 
                            $('strong[id='".$td_monto."']').html('');
                          </script>";
                    
                  }*/
                  echo $tabla;


                  $qry = "SELECT COUNT(*) as cantidad FROM lista_temp 
                  WHERE numero_caja = '$numero_caja'";
                  $res = mysqli_query($connection, $qry);
                  $num_countidad = mysqli_fetch_array($res);
                  
                  if($num_countidad['cantidad'] > 0)
                  {
                    echo "<script>$('#print-listado').show();</script>";
                  }

                } 
                else //caso en que las fechas son distintas
                {
                  if($fecha_inicial < $fecha_final)
                  {


                    $delete = "DELETE FROM lista_temp 
                            WHERE numero_caja = '$numero_caja'";
                    $res_delete = mysqli_query($connection, $delete);

                    $select_gral = "SELECT * from caja_gral
                    where numero_caja = '$numero_caja'
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
                      $cheque   = $array['cheques'];
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
                      '$cheque',
                      '$operacion')";
                      $res_insert_temp = mysqli_query($connection, $insert_temp);
                    }


                    // total de cobranza
                    $qry_cob = "SELECT sum(importe) as total_cob from cobranza
                            where (fecha >= '$fecha_inicial') 
                            AND (fecha <= '$fecha_final') 
                            AND numero_caja = '$numero_caja'";
                    $res_cob = mysqli_query($connection,$qry_cob);
                    $datos_cob = mysqli_fetch_array($res_cob);
                    $monto = $datos_cob['total_cob'];

                    if($numero_caja != 3)
                    {
                      $dia = 86400; # 24 horas * 60 minutos por hora * 60 segundos por minuto  (24*60*60)

                      for($i = strtotime($fecha_inicial); $i<= strtotime($fecha_final); $i+=$dia)
                      {
                          $fechaUno = date("Y-m-d", $i);
                          
                          //recalculamos la taba lista_temp_date
                          Update_caja($numero_caja,1,$fechaUno,$fecha_inicial,$fecha_final);
                          Update_caja($numero_caja,2,$fechaUno,$fecha_inicial,$fecha_final);
                          Update_caja($numero_caja,3,$fechaUno,$fecha_inicial,$fecha_final);
                          Update_caja($numero_caja,4,$fechaUno,$fecha_inicial,$fecha_final);
                      }
                    }


                    /*------------------------------------*/

                    // calculamos el total de los dias en pesos
                            $ing_pesos=get_total2(1,$numero_caja, $fecha_inicial, $fecha_final); 
                            $query_total = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from lista_temp
                                  where fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                                  AND (operacion = 1) 
                                  AND numero_caja = '$numero_caja'";                
                            $result_total = mysqli_query($connection, $query_total);
                            $total = mysqli_fetch_array($result_total);
                                    
                            $total_dia = $total['total'];                 

                            // total de dolares
                            $ing_dolares=get_total2(2,$numero_caja, $fecha_inicial, $fecha_final); 
                            $query_dolares = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from lista_temp
                                    where fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                                    AND numero_caja = '$numero_caja'
                                    AND operacion = 2";                
                            $result_total = mysqli_query($connection, $query_dolares);
                            $total = mysqli_fetch_array($result_total);
                            $total_dolares = round($total['total']);

                            //total de euros 
                            $ing_euros=get_total2(3,$numero_caja, $fecha_inicial, $fecha_final); 
                            $query_euros = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from lista_temp
                                      where fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                                      AND numero_caja = '$numero_caja'
                                      AND operacion = 3";                
                            $result_total = mysqli_query($connection, $query_euros);
                            $total = mysqli_fetch_array($result_total);
                            $total_euros = round($total['total']);

                            //total de cheques 
                            $ing_cheques=get_total2(4,$numero_caja, $fecha_inicial, $fecha_final); 
                            $query_euros = "SELECT sum(ingreso) + (-1)*sum(egreso) as total from caja_gral
                                          where fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                                          AND numero_caja = '$numero_caja'
                                          AND operacion = 4";                
                            $result_total = mysqli_query($connection, $query_euros);
                            $total = mysqli_fetch_array($result_total);
                            $total_cheques = $total['total'];

                            // total de ingresos y egresos
                            $total_ingresos = total_ingresos2(1,$numero_caja,$fecha_inicial, $fecha_final);
                            $total_egresos = total_egresos2(1,$numero_caja,$fecha_inicial, $fecha_final);

                            // mostramos los datos
                            $tabla.=$cabecera;

                            $query = "SELECT * from lista_temp
                                      where fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                                      AND numero_caja = '$numero_caja'
                                      order by numero";    
                            $result = mysqli_query($connection, $query);

                            while($datos = mysqli_fetch_array($result))
                            {
                              $tabla.= "<tr>
                              <td style='width:4%;'>".$datos['numero']."</td>
                              <td style='width:5%;'>".fecha_min($datos['fecha'])."</td>
                              <td style='width:20%;'>".limitar_cadena($datos['detalle'],20)."</td>
                              <td>".number_format($datos['ingreso'],2,',','.')."</td>
                              <td>".number_format($datos['egreso'],2,',','.')."</td>
                              <td>".number_format($datos['pesos'],2,',','.')."</td>";
                              if($numero_caja != 3)
                              {
                                $tabla.="<td>".number_format($datos['dolares'],2,',','.')."</td>
                                <td>".number_format($datos['euros'],2,',','.')."</td>
                                <td>".number_format($datos['cheques'],2,',','.')."</td>";
                              }
                              if($datos['fecha'] == date('Y-m-d'))
                              {
                                $tabla.= "<td>
                                  <button class='btn btn-secondary borrar'  id='".$datos['numero']."' title='Eliminar'>
                                  <i class='fas fa-trash-alt'></i>
                                  </button>
                                  </td>
                                  </tr>";
                                /*if(check_moneda($datos['numero'])!='cheques')
                                {
                                  $tabla.= "<td>
                                  <button class='btn btn-secondary borrar'  id='".$datos['numero']."' title='Eliminar'>
                                  <i class='fas fa-trash-alt'></i>
                                  </button>
                                  </td>
                                  </tr>";
                                }
                                else
                                {
                                  $tabla.= "<td></td></tr>";
                                }*/
                              }
                              else 
                                $tabla.= "<td></td></tr>";
                              
                            }

                            if($numero_caja !=3)
                            {
                            $tabla.="<tr style='background-color: transparent; border-bottom: 1px solid black;'>
                                    <td></td>
                                    <td></td>
                                    <td>"."<strong>Totales</strong>"."</td>
                                    <td>"."<strong>$".number_format($total_ingresos,2,',','.')."</strong>"."</td>
                                    <td>"."<strong>$".number_format($total_egresos,2,',','.')."</strong>"."</td>
                                    <td>"."<strong>$".number_format($ing_pesos,2,',','.')."</strong>"."</td>
                                    <td>"."<strong>US".round($ing_dolares)."</strong>"."</td>
                                    <td>"."<strong>€".round($ing_euros)."</strong>"."</td>
                                    <td>"."<strong>$".number_format($ing_cheques,2,',','.')."</strong>"."</td>
                                    <td></td>
                                    <tr>";
                            
                            $tabla.= "<tr style='background-color: transparent;'>
                                      <td></td>
                                      <td id='id-cobranza'>"."<strong id='id-cobranza'>Cobranza:</strong>"."</td>
                                      <td id='id-monto'>"."<strong id='id-monto'>"."$".number_format($monto,2,',','.')."</strong>"."</td>
                                      <td></td>
                                      <td>"."<strong>Sdo. anterior</strong>"."</td>
                                      <td>"."$".number_format($saldo_anterior,2,',','.')."</td>
                                      <td>"."US".number_format($saldo_anterior_dolares,2,',','.')."</td>
                                      <td>"."€".number_format($saldo_anterior_euros,2,',','.')."</td>
                                      <td>"."$".number_format($saldo_anterior_cheques,2,',','.')."</td>
                                      <td></td>
                                      </tr>
                            
                                      <tr style='background-color: transparent;'>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td>"."<strong>Total del dia</strong>"."</td><td>".'$'.number_format(($monto+$total_dia),2,',','.')."</td>
                                      <td>".'US'.number_format($total_dolares,2,',','.')."</td>
                                      <td>"."€".number_format($total_euros,2,',','.')."</td>
                                      <td>"."$".number_format($total_cheques,2,',','.')."</td>
                                      <td></td>
                                      </tr>
                            
                                      <tr style='background-color: transparent;'>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td>"."<strong>Total geral.</strong>"."</td>
                                      <td>".'$'.number_format(($saldo_anterior + $monto + $total_dia),2,',','.')."</td>
                                      <td>".'US'.number_format(($saldo_anterior_dolares + $total_dolares),2,',','.')."</td>
                                      <td>".'€'.number_format(($saldo_anterior_euros + $total_euros),2,',','.')."</td>
                                      <td>".'$'.number_format(($saldo_anterior_cheques + $total_cheques),2,',','.')."</td>
                                      <td></td>
                                      </tr>";

                            $tabla.="</tbody>";
                            }
                            else{
                              $tabla.="</tbody>"; 
                            }
                            echo $tabla;
                            
                            $qry = "SELECT COUNT(*) as cantidad FROM lista_temp WHERE numero_caja = '$numero_caja'";
                            $res = mysqli_query($connection, $qry);
                            $num_countidad = mysqli_fetch_array($res);
                            
                            if($num_countidad['cantidad'] > 0)
                            {
                              echo "<script>$('#print-listado').show();</script>";
                            }
                          }
                          else echo "<strong style='color: #AD0A35;'>Fechas incorrectas !</strong>";
                        }// end else tabla general

                      }
                      else echo "<strong style='color: #AD0A35;'>Fechas incorrectas !</strong>";
                    }
                    else echo "<strong style='color: #217DB1;'>Ingrese fechas para listar.</strong>";
                    
                    ?>     
             

          </div>

         </div>

    </div>
    

    

   
</body>
</html>