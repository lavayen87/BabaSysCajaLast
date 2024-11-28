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
      <h2>Solicitudes de órden de pago</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       

              <?php 
                $total = 0.00;
                $hoy = date('Y-m-d');
                $tabla = "<table style='width: 100%;'>
                <thead style='border-bottom: 2px solid black;'>  
                <tr>  
                <td><strong>Nº</strong></td>                         
                <td><strong>Fecha</strong></td>
                <td><strong>Solicita</strong></td>
                <td><strong>Empresa</strong></td>
                <td><strong>Obra</strong></td>
                <td><strong>Cuenta</strong></td>
                <td><strong>Importe</strong></td>
                <td><strong>Estado</strong></td>
                <td><strong>Acción</strong></td>
                </tr>
                </thead>
                <tbody id='tbody-datos'>";
                              
                // Todas las solicitudes de orden de pago                 
                include('conexion.php');
                include('funciones.php');
                
                $qry = "SELECT * from solicitud_orden_pago
                        where numero_caja = 3"; 
                
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
                    $tabla.= "<tr> 
                    <td style='width:4%; padding-bottom: 8px;'>".$datos['numero_orden']."</td>
                    <td style='width:7%; padding-bottom: 8px;'>".fecha_min($datos['fecha_orden'])."</td>
                    <td style='width:15%;padding-bottom: 8px;'>".limitar_cadena($datos['solicitante'],20)."</td>
                    <td style='width:11%;padding-bottom: 8px;'>".limitar_cadena($datos['empresa'],20)."</td>
                    <td style='width:11%;padding-bottom: 8px;'>".limitar_cadena($datos['obra'],20)."</td>                                                           
                    <td style='width:26%;padding-bottom: 8px;'>".limitar_cadena($datos['cuenta'],20).'.'."</td>
                    <td style='width:10%;padding-bottom: 8px;'>"."$".number_format($datos['importe'],2,',','.')."</td>";

                    // Estado
                    if($datos['estado'] == 'Autorizada'){
                      $tabla.="<td style='width:13px;'>"."<strong style='color: #107D23; font-size: 13px'>".$datos['estado']."</strong>"."</td>";
                    }
                    else{
                      if($datos['estado'] == 'Sin Autorizar'){
                        $tabla.="<td style='text-align: center;'>"."<strong style='color: #AD3319; font-size: 13px'>".$datos['estado']."</strong>"."</td>";
                      }
                      else $tabla.="<td style='text-align: center;'>"."<strong style='color: blue; font-size: 13px'>".$datos['estado']."</strong>"."</td>";
                    }
                    
                    // Accion 
                  
                    if($datos['estado'] == 'Autorizada')
                    {
                      if($datos['caja_pago'] == 3)
                      {
                        $caja_pago = 3;

                        $tabla.="<td style='width: 20px;'>
                        <a href='factura/solicitud_op_pdf.php?id=$id&caja_pago=$caja_pago' title='Imprimir' target='_blank' class='btn btn-primary btn-solicitud-sop' name='print-solicitud' id='".$datos['numero_orden']."' style='float: left;'>
                        <i class='fas fa-print'></i>
                        </a>    
                        </td>
                        </tr>";
                      }
                      
                      
                      
                      /*$tabla.="<td style='width: 20px;'>
                      <a href='factura/solicitud_op_pdf.php?id=$id&caja_pago=$caja_pago' title='Imprimir' target='_blank' class='btn btn-primary btn-solicitud-sop' name='print-solicitud' id='".$datos['numero_orden']."' style='float: left;'>
                      <i class='fas fa-print'></i>
                      </a>    
                      </td>
                      </tr>";*/
                    }
                    else
                    {

                      if($datos['estado'] == 'Sin Autorizar')
                      {
                          $tabla.="<td></td></tr>";
                      }
                        
                        
                    }
                        
                    
                    $tabla.="<tr style='border-bottom: 1px solid black;' id='".($datos['numero_orden']+1)."'>
                    <td colspan = '9'><strong>Detalle:</strong>"."  ".limitar_cadena($detalle,30)."</td>
                    </tr>";
                                      
                  }
                  mysqli_close ($connection);
                  $tabla.="</tbody></table>";  
                  echo $tabla; 
                    
                  echo "<script>$('#fechas-listar').hide();</script>";

                }               
                else echo "<strong>No se realizaron solicitudes.</strong>";
                  
              ?>
            <br>
          </div>
        </div>
      </div>
    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>