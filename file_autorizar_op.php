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
<link rel="stylesheet" href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script>  
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
   
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
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
      <h2>Autorizar solicitudes</h2>
      <hr>
      <div class="row">
        
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->  
            <!--menu horizontal-->
            <nav class="navbar navbar-expand-lg navbar-light " style="background-color: #22A49D; border-radius: 6px;">
              <div class="container-fluid">                    
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                  <span class="navbar-toggler-icon"></span>
                </button>          
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav">
                      <?php
                        /*
                          if($numero_caja <> 22 && $numero_caja <> 1)
                          {
                            echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>";
                          }                                    
                          if($numero_caja <> 22 && $numero_caja <> 1)
                          {
                            echo "<a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                          }                                           
                          if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                          {
                            echo "<a href='file_solicitud_cash.php' class='nav-item nav-link' style='color: white;'>Solicitud</a>";
                          }
                          if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                          {
                            echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>";
                          }
                          if($numero_caja == 1 || $numero_caja == 3 || $numero_caja == 9 || $numero_caja == 12)
                          {
                            echo "<a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                          }
                        */
                      ?>
                      <?php
                        if($numero_caja == 0 )
                        {
                          echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>
                                <a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                        }
                        if(tiene_permiso($numero_caja,3))
                        {
                          echo "<a href='file_orden_pago.php' class='nav-item nav-link' style='color: white;'>Efectivo</a>";
                        }         
                        if(tiene_permiso($numero_caja,4))
                        {
                          echo "<a href='file_orden_pago_cheque.php' class='nav-item nav-link' style='color: white;'>Cheque</a>";
                        }
                      ?> 
                                        
                      <div id="autorizar"> 
                        <div class='nav-item dropdown'>
                          <a href='#' class='nav-link dropdown-toggle' data-bs-toggle='dropdown' style='color: white;'>
                            Solicitud
                          </a>
                          <div class='dropdown-menu'>
                            <?php
                              if($numero_caja == 0)
                              {  
                                echo "<a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>
                                      <a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>
                                      <a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>
                                      <a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                              }
                              if(tiene_permiso($numero_caja,5))
                                echo "<a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>";
                              if(tiene_permiso($numero_caja,6))
                                echo "<a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>";
                              if(tiene_permiso($numero_caja,7))
                                echo "<a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>";
                              if(tiene_permiso($numero_caja,8))
                                echo "<a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>";
                            ?>
                              
                          </div>
                        </div>
                      </div>
                      <?php
                        if($numero_caja == 12 || $numero_caja == 13){
                          echo "<script>
                                  $('#autorizar').hide();
                                </script>";
                        }
                      ?>     
                      <?php
                        if($numero_caja == 0)
                          echo "<a href='file_solicitud_transferencia.php' class='nav-item nav-link' style='color: white;'>Solicitud de fondos</a>";
                        if(tiene_permiso($numero_caja,42))
                          echo "<a href='file_solicitud_transferencia.php' class='nav-item nav-link' style='color: white;'>Solicitud de fondos</a>";
                        if($numero_caja == 12)
                        {
                          echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>";
                                
                        }
                        if($numero_caja == 0)
                        {
                          echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>
                                <a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                        }
                        if(tiene_permiso($numero_caja,9)) 
                        {
                          echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>";
                        }
                                  
                        if(tiene_permiso($numero_caja,10))
                        {
                          echo "<a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                        }
                      ?>
                    </div>
                </div>
              </div>
            </nav>
            <!--fin menu horizontal-->
            <br>     
                            
              <?php 
                $total = 0.00;
                $hoy = date('Y-m-d');
                $tabla = "<table style='width: 100%;'>
                <thead style='border-bottom: 2px solid black;'> 
                <tr>  
                <td><strong>Fecha</strong></td>
                <td><strong>Solicita</strong></td>
                <td><strong>Empresa</strong></td>
                <td><strong>Obra</strong></td>
                <td><strong>Cuenta</strong></td>
                <td><strong>Importe</strong></td>
                <td><strong>Paga</strong></td>
                <td><strong>Estado</strong></td>
                <td><strong>Acción</strong></td>
                </tr>
                </thead>
                <tbody id='tbody-datos'>";
                              
                // Todas las solicitudes de orden de pago                 
                include('conexion.php');
                include('funciones.php');

                $caja_pago = "";
               
                //if($numero_caja == 0 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 4 || $numero_caja == 3 || $numero_caja == 12 || $numero_caja == 13)
                if($numero_caja == 0 || tiene_permiso($numero_caja,9))
                {
                  $qry = "SELECT * from solicitud_orden_pago 
                          where estado = 'Sin autorizar'"; 
                          //where numero_caja = '$numero_caja'
                    
                }
                else{
                  if($numero_caja == 11) // caja buen clima
                  {
                    $qry = "SELECT * from solicitud_orden_pago 
                            
                           where (estado = 'Sin autorizar' or
                           estado ='Autorizada')
                           and empresa = 'Buen Clima'
                           and obra = 'Buen clima'
                           and DATEDIFF ('$hoy', fecha_orden) <= 3";
                            /*where estado = 'Sin autorizar'
                            and numero_caja = 22";*/
                  }
                }
                
                $res = mysqli_query($connection, $qry);

                if($res->num_rows > 0)
                {  

                  $delete = "DELETE FROM solicitud_orden_pago 
                            WHERE TIMESTAMPDIFF(DAY, fecha_orden, '$hoy') >= 3
                            AND estado = 'Sin Autorizar'";  
                  $res_delete = mysqli_query($connection, $delete);

                  while($datos = mysqli_fetch_array($res))
                  {
                    $id = $datos['numero_orden'];
                    $detalle = $datos['detalle'];
                    $caja_pago = $datos['caja_pago'];
                    $moneda = $datos['moneda'];
                    $sim = "";
                    switch ($moneda) {
                      case 'pesos':
                        $sim = '$';
                        break;
                      
                      case 'dolares':
                        $sim = '$US';
                        break;

                      case 'euros':
                        $sim = '€';
                        break;
                    }
                    $qry_caja = "SELECT usuario FROM usuarios
                                where numero_caja = '$caja_pago'";
                    $res_caja = mysqli_query($connection,$qry_caja);

                    $datos_caja = mysqli_fetch_assoc($res_caja);

                    $nom_caja_pago = $datos_caja['usuario'];

                    $tabla.= "<tr> 
                    <td style='width:7%; padding-bottom: 8px;'>".fecha_min($datos['fecha_orden'])."</td>
                    <td style='width:15%; background: #B2D8C6; padding-bottom: 8px;'>".limitar_cadena($datos['solicitante'],20)."</td>
                    <td style='width:11%;padding-bottom: 8px;'>".limitar_cadena($datos['empresa'],20)."</td>
                    <td style='width:11%;padding-bottom: 8px;'>".limitar_cadena($datos['obra'],20)."</td>                                                           
                    <td style='width:26%;padding-bottom: 8px;'>".limitar_cadena($datos['cuenta'],20).'.'."</td>
                    <td style='width:10%;padding-bottom: 8px;'>".$sim.number_format($datos['importe'],2,',','.')."</td>
                    <td style='width:8%; padding-bottom: 8px;'>".$nom_caja_pago."</td>";

                    // Estado
                    if($datos['estado'] == 'Sin Autorizar')
                    {
                      $tabla.="<td style='text-align: center;'>"."<strong style='color: #AD3319; font-size: 13px' id='".$datos['numero_orden']."'>".$datos['estado']."</strong>"."</td>";
                    }
                    else $tabla.="<td style='text-align: center;'>"."<strong style='color: #107D23; font-size: 13px'>".$datos['estado']."</strong>"."</td>";
                    
                    /* caja pago */
                    
                    // Accion
                    //if($numero_caja == 0 || $numero_caja == 4 || $numero_caja == 11 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 12 || $numero_caja == 3 || $numero_caja == 13 )
                    if($numero_caja == 0 || tiene_permiso($numero_caja,9))
                    {
                      if($datos['estado'] == 'Sin Autorizar')
                      {
                        $tabla.="<td style='width: 20px;'>
                        <button class='btn btn-primary btn-autorizar-sop' title='Autorizar' name='autorizar-sop' id='".$datos['numero_orden']."' style='float: left;'>
                        <i class='far fa-check-circle'></i>
                        </button>    
                        </td>
                        </tr>";
                      }
                      else $tabla.="<td style='width:20px;'></td></tr>";
                    }
                    $tabla.="<tr style='border-bottom: 1px solid black;'>
                    <td colspan = '9'><strong>Detalle:</strong>"."  ".limitar_cadena($detalle,30)."</td>
                    </tr>";
                                         
                  }
                  mysqli_close ($connection);
                  $tabla.="</tbody></table>"; 
                  echo $tabla; 
                    
                  echo "<script>$('#fechas-listar').hide();</script>";

                }               
                else echo "<strong>No hay solicitudes para autorizar.</strong>";
                  
              ?>
            
          </div>
        
      </div>
    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>