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
	
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<style>
  table tr td a{
    text-decoration: none;
    color: green;
  }
</style>
</head>
<body>
<div class="page-wrapper chiller-theme toggled">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  <?php include('menu_main.php');?>
  <!-- sidebar-wrapper  -->

  <!-- page-content" -->
  <main class="page-content">
    <div class="container">
      <h2>Lotes y servicios</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert">
            <!--h4 class="alert-heading">Listado de caja</h4-->       
            <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
               

               <strong>Loteo</strong>
                <select name="loteo" id="select-loteo">
                   <option value="">Todos</option>
                   <?php  
                      include("conexion.php");
                      $consulta = "SELECT DISTINCT * FROM loteos";
                      $resultado = mysqli_query($connection , $consulta);

                      while($misdatos = mysqli_fetch_assoc($resultado))
                      { 
                        echo "<option value='".$misdatos['nombre']."' id='".$misdatos['id']."'>".$misdatos['nombre']."</option>"; 
                      }
                    ?>
               </select> 

               <strong>Servicios</strong>
                <select name="servicio" id="servicio-lote">
                   <option value="">Todos</option>
                   <option value="Agua">Agua</option>
                   <option value="Agrimensor">Agrimensor</option>
                   <option value="Cloacas">Cloacas</option>
               </select> 

               <strong>Estado</strong>
                   <select name="estado" id="estado-lote">
                   <option value="">Todos</option>
                   <option value="Pendiente">Pendiente</option>
                   <option value="Solicitado">Solicitado</option>
                   <option value="Realizado">Realizado</option>
               </select>
               
               <button id="btn-lotes" name="btn-lotes" class="btn btn-success">listar</button>
                
                <?php

                    $serv="";
                    $loteo ="";
                    $est ="Solicitado";

                    if(isset($_POST['servicio']))
                    {
                        $serv = $_POST['servicio'];
                    }

                    if(isset($_POST['loteo']))
                    {
                        $loteo = $_POST['loteo'];
                    }

                    if(isset($_POST['estado']))
                    {
                        $est = $_POST['estado'];
                    }

                    echo "<a href='factura/listado_lotes.php?loteo=$loteo&serv=$serv&est=$est' 
                          type='submit' name='listado-lotes' 
                          id='listado-lotes'  target='_blank' 
                          style='float: right; display: none;' 
                          class='btn btn-primary' title='Imprimir'>
                          <i class='fas fa-print'></i>
                          </a>";

                    echo "</br></br><hr>";
                ?>
                                                             
            </form>
            <?php
                    $cabecera = "<table class='table table-striped table-hover'>
                    <thead>  
                    <tr>                   
                    <td><strong>Loteo</strong></td>  
                    <td><strong>Lote</strong></td>
                    <td><strong>Servicio</strong></td>                                  
                    <td><strong>Solicitado</strong></td>
                    <td><strong>Realizado</strong></td>
                    <td><strong>Estado</strong></td>
                    <td><strong>Acción</strong></td>
                    </tr>
                    </thead>
                    <tbody id='tbody-datos'>";

                    include('conexion.php');
                    include('funciones.php');  

                    // lista de servicios
                    $qry_nom_servicios = "SELECT nombre FROM servicios";
                    $res_nom_servicios = mysqli_query($connection, $qry_nom_servicios);
                    $servicios = [];
                    $i = 0;
                    $n = 0;
                    $case = 0;
                    while($row = mysqli_fetch_assoc($res_nom_servicios)){
                        $servicios[$i] = $row['nombre'];
                        $i++;
                    }

                    // filtro 

                    if(isset($_POST['btn-lotes']))
                    {
                      $tabla = $cabecera;
                      // caso 1
                      if($_POST['loteo']=="" && $_POST['servicio']=="" && $_POST['estado']=="")
                      {
                        $qry = "SELECT * FROM det_lotes
                                WHERE pago_agrimensor!='0000-00-00'
                                or forma_pago!=''";
                        $n = 4;
                      }         
                      else
                      {
                        // caso 2
                        if($_POST['loteo']!="" && $_POST['servicio']!="" && $_POST['estado']!="")
                        {
                          $loteo    = $_POST['loteo'];
                          $servicio = $_POST['servicio'];
                          $estado   = $_POST['estado'];
                          //echo $loteo." ".$servicio." ".$estado; 
                          if($servicio == 'Agua')
                          {
                             
                            $qry = "SELECT * FROM det_lotes
                                WHERE loteo = '$loteo'
                                AND pago_agua != '0000-00-00'
                                AND estado_agua = '$estado'
                                ORDER BY id";
                            $n =4;
                          }
                          else
                          {
                            if($servicio == 'Agrimensor')
                            {
                              $qry = "SELECT * FROM det_lotes
                                  WHERE loteo = '$loteo'
                                  AND pago_agrimensor != '0000-00-00'
                                  AND estado_agr = '$estado'
                                  ORDER BY id";
                            }
                            else{
                              if($servicio == 'Cloacas')
                              {
                                $qry = "SELECT * FROM det_lotes
                                    WHERE loteo = '$loteo'
                                    AND pago_cloacas != '0000-00-00'
                                    AND estado_clo = '$estado'
                                    ORDER BY id";
                                
                              }
                            }
                          }        
                
                          /*$qry = "SELECT * FROM det_loteo
                                  WHERE loteo = '$loteo'
                                  AND servicio = '$servicio'
                                  AND estado = '$estado'
                                  AND servicio <> 'Red Cloacas'
                                  ORDER BY id";*/
                        }
                        else
                        {
                          // caso 3
                          if($_POST['loteo']!="" && $_POST['servicio']=="" && $_POST['estado']=="")
                          {
                            $loteo    = $_POST['loteo'];

                            $qry = "SELECT * FROM det_servicio
                                    WHERE loteo = '$loteo'
                                    AND servicio <> 'Red Cloacas'
                                    ORDER BY id";
                          }
                          else
                          {
                            // caso 4
                            if($_POST['loteo']=="" && $_POST['servicio']!="" && $_POST['estado']=="")
                            {
                              $servicio = $_POST['servicio'];

                              $qry = "SELECT * FROM det_servicio
                                      WHERE servicio = '$servicio'
                                      AND servicio <> 'Red Cloacas'
                                      ORDER BY id";
                            }
                            else
                            {
                              // caso 5
                              if($_POST['loteo']=="" && $_POST['servicio']=="" && $_POST['estado']!="")
                              {
                                $estado   = $_POST['estado'];

                                $qry = "SELECT * FROM det_servicio
                                        WHERE estado = '$estado'
                                        AND servicio <> 'Red Cloacas'
                                        ORDER BY id";
                              }
                              else
                              {
                                // caso 6
                                if($_POST['loteo']=="" && $_POST['servicio']!="" && $_POST['estado']!="")
                                {
                                  $servicio   = $_POST['servicio'];
                                  $estado   = $_POST['estado'];

                                  $qry = "SELECT * FROM det_servicio
                                          WHERE servicio = '$servicio'
                                          AND estado = '$estado'
                                          AND servicio <> 'Red Cloacas'
                                          ORDER BY id";
                                }
                                else
                                {
                                  //caso 7
                                  if($_POST['loteo']!="" && $_POST['servicio']!="" && $_POST['estado']=="")
                                  {
                                    $loteo   = $_POST['loteo'];
                                    $servicio   = $_POST['servicio'];

                                    $qry = "SELECT * FROM det_servicio
                                            WHERE loteo = '$loteo'
                                            AND servicio = '$servicio'
                                            AND servicio <> 'Red Cloacas'
                                            ORDER BY id";
                                  }
                                  else{
                                    //caso 8
                                    if($_POST['loteo']!="" && $_POST['servicio']=="" && $_POST['estado']!="")
                                    {
                                      $loteo   = $_POST['loteo'];
                                      $estado   = $_POST['estado'];

                                      $qry = "SELECT * FROM det_servicio
                                              WHERE loteo = '$loteo'
                                              AND estado = '$estado'
                                              AND servicio <> 'Red Cloacas'
                                              ORDER BY id";
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                      

                      $res = mysqli_query($connection, $qry);
                         
                      if($res->num_rows > 0)
                      {
                        $tabla.=$cabecera;
                        while($datos = mysqli_fetch_array($res))
                        {
                          for($j=0; $j < 4; $j++)
                          {
                            switch($j)
                            {
                              case 0:
                                if($numero_caja<>8)
                                {
                                $tabla.="<tr id='".($datos['id']+4)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Agrimensor"."</td>               
                                <td>".fecha_min($datos['pago_agrimensor'])."</td>
                                <td fr_id='".($datos['id']+4)."'>".fecha_min($datos['realizado_agr'])."</td>";
                                if($datos['estado_agr'] == 'Realizado'){ 
                                  $tabla.="<td style='background: #91EC7F;' id='".($datos['id']+4)."'>".$datos['estado_agr']."</td>
                                  <td></td>";
                                }
                                else
                                {
                                  if($datos['estado_agr'] == 'Solicitado')
                                  {
                                    $tabla.="<td style='background: #E3D822;' id='".($datos['id']+4)."'>".$datos['estado_agr']."</td>
                                    <td><a href='#' class='link-realizar' id='".($datos['id']+4)."' servicio='1'><strong>Realizar</strong></a></td>
                                    </tr>";
                                  }
                                  else
                                    $tabla.="<td></td><td></td></tr>";
                                }
                                
                                }
                              break;
                              case 1:
                                
                                $tabla.="<tr id='".($datos['id']+8)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Agua"."</td>               
                                <td>".fecha_min($datos['pago_agua'])."</td>
                                <td fr_id='".($datos['id']+8)."'>".fecha_min($datos['realizado_agua'])."</td>";
                                if($datos['estado_agr'] == 'Realizado'){ 
                                  $tabla.="<td style='background: #91EC7F;' id='".($datos['id']+8)."'>".$datos['estado_agr']."</td>
                                  <td></td>";
                                }
                                else
                                {
                                  if($datos['estado_agr'] == 'Solicitado')
                                  {
                                    $tabla.="<td style='background: #E3D822;' id='".($datos['id']+8)."'>".$datos['estado_agr']."</td>
                                    <td><a href='#' class='link-realizar' id='".($datos['id']+8)."' servicio='1'><strong>Realizar</strong></a></td>
                                    </tr>";
                                  }
                                  else
                                    $tabla.="<td></td><td></td></tr>";
                                }
                                
                                break;
                              case 2:
                                
                                $tabla.="<tr id='".($datos['id']+12)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Cloacas"."</td>               
                                <td>".fecha_min($datos['pago_cloacas'])."</td>
                                <td fr_id='".($datos['id']+12)."'>".fecha_min($datos['realizado_clo'])."</td>";
                                if($datos['estado_agr'] == 'Realizado'){ 
                                  $tabla.="<td style='background: #91EC7F;' id='".($datos['id']+12)."'>".$datos['estado_agr']."</td>
                                  <td></td>";
                                }
                                else
                                {
                                  if($datos['estado_agr'] == 'Solicitado')
                                  {
                                    $tabla.="<td style='background: #E3D822;' id='".($datos['id']+12)."'>".$datos['estado_agr']."</td>
                                    <td><a href='#' class='link-realizar' id='".($datos['id']+12)."' servicio='1'><strong>Realizar</strong></a></td>
                                    </tr>";
                                  }
                                  else
                                    $tabla.="<td></td><td></td></tr>";
                                }
                                break;
                              case 3:                               
                                $tabla.="<tr id='".($datos['id']+16)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Red Cloacas"."</td>               
                                <td></td>
                                <td></td>
                                <td style='background: #91EC7F;' id='".$datos['id']."'>".$datos['forma_pago']."</td>
                                
                                </tr>";
                                break;
                            } 
                          }
                        }
                        $tabla.="</tbody></table>";

                        echo"<script>
                            $('#listado-lotes').show();
                            </script>";

                        echo $tabla;
                        
                      }
                      else
                      {
                        echo "<strong>No se encontraron lotes.</strong>";
                      }
                        
                      
                      
                    }
                    else // Listado de solicitados
                    {
                      
                      $qry = "SELECT * FROM det_lotes
                      WHERE (estado_agr = 'Solicitado' AND estado_agua = 'Solicitado')";
                      
                      $res = mysqli_query($connection, $qry);

                      if($res->num_rows > 0)
                      {

                        $tabla=$cabecera;
                
                        while($datos = mysqli_fetch_array($res))
                        {
                          for($j=0; $j < 4; $j++)
                          {
                            switch($j)
                            {
                              case 0:
                                if($numero_caja<>8)
                                {
                                $tabla.="<tr id='".($datos['id']+4)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Agrimensor"."</td>               
                                <td>".fecha_min($datos['pago_agrimensor'])."</td>
                                <td fr_id='".($datos['id']+4)."'>".fecha_min($datos['realizado_agr'])."</td>";
                                if($datos['estado_agr'] == 'Realizado'){ 
                                  $tabla.="<td style='background: #91EC7F;' id='".($datos['id']+4)."'>".$datos['estado_agr']."</td>";
                                }
                                else{
                                  $tabla.="<td style='background: #E3D822;' id='".($datos['id']+4)."'>".$datos['estado_agr']."</td>";
                                }
                                
                                $tabla.="<td><a href='#' class='link-realizar' id='".($datos['id']+4)."' servicio='1'><strong>Realizar</strong></a></td>
                                </tr>";
                                }
                              break;
                              case 1:
                                
                                $tabla.="<tr id='".($datos['id']+8)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Agua"."</td>               
                                <td>".fecha_min($datos['pago_agua'])."</td>
                                <td fr_id='".($datos['id']+8)."'>".fecha_min($datos['realizado_agua'])."</td>";
                                if($datos['estado_agr'] == 'Realizado'){ 
                                  $tabla.="<td style='background: #91EC7F;' id='".($datos['id']+8)."'>".$datos['estado_agr']."</td>";
                                }
                                else{
                                  $tabla.="<td style='background: #E3D822;' id='".($datos['id']+8)."'>".$datos['estado_agr']."</td>";
                                }
                                
                                $tabla.="<td><a href='#' class='link-realizar' id='".($datos['id']+8)."' servicio='2'><strong>Realizar</strong></a></td>
                                </tr>";
                                
                                break;
                              case 2:
                                
                                $tabla.="<tr id='".($datos['id']+12)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Cloacas"."</td>               
                                <td>".fecha_min($datos['pago_cloacas'])."</td>
                                <td fr_id='".($datos['id']+12)."'>".fecha_min($datos['realizado_clo'])."</td>";
                                if($datos['estado_agr'] == 'Realizado'){ 
                                  $tabla.="<td style='background: #91EC7F;' id='".($datos['id']+12)."'>".$datos['estado_agr']."</td>";
                                }
                                else{
                                  $tabla.="<td style='background: #E3D822;' id='".($datos['id']+12)."'>".$datos['estado_agr']."</td>";
                                }
                                
                                $tabla.="<td><a href='#' class='link-realizar' id='".($datos['id']+12)."' servicio='3'><strong>Realizar</strong></a></td>
                                </tr>";
                                break;
                              case 3:                               
                                $tabla.="<tr id='".($datos['id']+16)."'>
                                <td>".$datos['loteo']."</td>                      
                                <td>".$datos['lote']."</td>
                                <td>"."Red Cloacas"."</td>               
                                <td>".fecha_min($datos['pago_red'])."</td>
                                <td>".fecha_min($datos['realizado_red'])."</td>
                                <td style='background: #E3D822;' id='".$datos['id']."'>".$datos['forma_pago']."</td>
                                
                                </tr>";
                                break;
                            } 
                          }
                        }
                        $tabla.="</tbody></table>";

                        echo"<script>
                            $('#listado-lotes').show();
                            </script>";

                        echo $tabla;
                      }
                      else{
                        echo "<strong>No hay lotes solicitados.</strong>";
                      }
                    }
                ?> 
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