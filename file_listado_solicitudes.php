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
       
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       

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
                // (1,34,7,9,10,12,22,3)
                //if($numero_caja == 0 || $numero_caja == 1  || $numero_caja == 3 || $numero_caja == 4 || $numero_caja == 7  || $numero_caja == 9 || $numero_caja == 10  || $numero_caja == 11 )
                if($numero_caja == 0 || tiene_permiso($numero_caja,20))
                {  
                  /*$qry = "SELECT * from solicitud_orden_pago
                          where estado = 'Autorizada' 
                          or estado = 'Sin Autorizar'"; */
                          
                  if($numero_caja == 11) // caja buen clima
                  {
                    // Gerente BC
                    //  where fecha_orden = '$hoy'
                    $qry = "SELECT * from solicitud_orden_pago
                         
                          where (empresa = 'Buen clima'
                          or  obra = 'Buen clima')
                          and DATEDIFF ('$hoy', fecha_orden) <= 3";
                          /*and estado <> 'Realizada'
                          and estado <> 'Realizado'";
                          //and numero_caja = 22"; */
                  } 
                  else 
                  {  
                    // Usuarios que imprimen ordenes (1,3,9,12)
                    //if($numero_caja == 0 || $numero_caja == 1 || $numero_caja == 3 || $numero_caja == 9 || $numero_caja == 4)
                    if($numero_caja == 0 || tiene_permiso($numero_caja,20))
                    {
                      $qry = "SELECT * from solicitud_orden_pago
                              where estado <> 'Realizada'
                              and estado <> 'Realizado'
                              and numero_caja = '$numero_caja'";
                      /*$qry = "SELECT * from solicitud_orden_pago
                          where caja_pago = '$numero_caja'
                          and estado <> 'Realizada'"; */
                          
                          //originalmente
                          /*where fecha_orden = '$hoy'
                          and caja_pago = '$numero_caja'"; */
                    }
                    /*else{
                      // Usuarios que generan solicitudes
                      $qry = "SELECT * from solicitud_orden_pago
                              where estado <> 'Realizada'
                              and estado <> 'Realizado'
                              and caja_pago <> 0";
                         // where fecha_orden = '$hoy'"; originalmente
                          
                    } */    
                     
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

                      $qry_caja = "SELECT rol FROM usuarios
                                  where numero_caja = '$caja_pago'";
                      $res_caja = mysqli_query($connection,$qry_caja);

                      $datos_caja = mysqli_fetch_assoc($res_caja);

                      $nom_caja_pago = $datos_caja['rol'];

                      //primer campo originamlemte  :
                      //<td style='width:4%; padding-bottom: 8px;'>".$datos['numero_orden']."</td>
                      $tabla.= "<tr> 
                      
                      <td style='width:7%; padding-bottom: 8px;'>".fecha_min($datos['fecha_orden'])."</td>
                      <td style='width:15%; background: #B2D8C6; padding-bottom: 8px;'>".limitar_cadena($datos['solicitante'],20)."</td>
                      <td style='width:11%;padding-bottom: 8px;'>".limitar_cadena($datos['empresa'],20)."</td>
                      <td style='width:11%;padding-bottom: 8px;'>".limitar_cadena($datos['obra'],20)."</td>                                                           
                      <td style='width:26%;padding-bottom: 8px;'>".limitar_cadena($datos['cuenta'],20).'.'."</td>
                      <td style='width:10%;padding-bottom: 8px;'>"."$".number_format($datos['importe'],2,',','.')."</td>
                      <td style='width:8%; padding-bottom: 8px;'>".$nom_caja_pago."</td>";
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
                      

                      // Boton de Accion

                      // 1. Cajas que pagan ordenes (imprime la orden) 1,3,9,12
                      //if($numero_caja == 0 || $numero_caja == 1 || $numero_caja == 3 || $numero_caja == 4 || $numero_caja == 9 )
                      if($numero_caja == 0 || tiene_permiso($numero_caja,10))
                      {  
                        if( $numero_caja == $caja_pago  && $datos['estado']=='Sin autorizar' ) 
                        {
                          
                            $tabla.="<td></td></tr>"; 

                        }
                        else{
                          if($numero_caja == $datos['numero_caja'] && $datos['estado']!='Autorizada')
                          {
                            $tabla.="<td style='width: 20px;'>
                            <button class='btn btn-secondary btn-delete-sop' title='Eliminar' name='delete-solicitud' id='".$datos['numero_orden']."' style='float: left;'>
                            <i class='fas fa-trash-alt'></i>
                            </button>    
                            </td>
                            </tr>";
                          }
                          
                        }
                      }
                      else
                      // 2. Cajas que generan solicitudes tienen opcion de eliminar su solicitud (34,22,7,9,10)
                      {            
                        //if($numero_caja == 0  || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 11 )
                        if($numero_caja == 0  || tiene_permiso($numero_caja,20))
                        {
                          
                          if($datos['estado'] == 'Sin Autorizar' && $numero_caja == $datos['numero_caja'])
                          {
                                $tabla.="<td style='width: 20px;'>
                                <button class='btn btn-secondary btn-delete-sop' title='Eliminar' name='delete-solicitud' id='".$datos['numero_orden']."' style='float: left;'>
                                <i class='fas fa-trash-alt'></i>
                                </button>    
                                </td>
                                </tr>";
                          }
                          else $tabla.="<td style='width:20px;'></td></tr>";
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
                }  
                else echo "<strong>No hay solicitudes para ver.</strong>";  
              ?>
            <br>
          </div>
        
      </div>
    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>