
<?php 
date_default_timezone_set('America/Argentina/Salta');
//session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $nombre_usuario = $_SESSION['nombre'];
  $numero_caja = $_SESSION['numero_caja'];
  $rol = $_SESSION['rol'];
}
else{
  
  echo "<script>
          if(confirm('Expiró la sesión...'))
          {
            window.location = 'index.php';
          }
        </script>";
  //header("Location: index.php");
}

function tiene_permiso(int $numero_caja, int $id_permiso)
{
	include('conexion.php');

	$qry = "SELECT btn_accion from det_permisos
			WHERE numero_caja = '$numero_caja'
			AND id_permiso = '$id_permiso'
			GROUP BY numero_caja";

	$res = mysqli_query($connection, $qry);

	if($res->num_rows)
  {
		return true;
	}
	else 
        return false;
	
}

?>
<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
      <div class="sidebar-brand">
        <a href="#">Ocultar</a>
        <div id="close-sidebar">
          <i class="fas fa-times"></i>
        </div>
      </div>
      <div class="sidebar-header">
        <div class="user-pic">
          <?php 
            
              echo "<img class='img-responsive img-rounded' src='img/user.png' alt='User picture'>";
            
              //echo "<img class='img-responsive img-rounded' src='https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg' alt='User picture'>";
          ?>
          <!--img class="img-responsive img-rounded" src="https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg" alt="User picture"-->
        </div>
        <div class="user-info">
          <span class="user-name"> 
            <strong>
              <?php 
                if($numero_caja == 0 || $numero_caja == 5 || $numero_caja == 6 || $numero_caja == 8)
                  echo $nombre_usuario;
                else echo $rol; 
              ?>
            </strong>
          </span>
          <span class="user-role"><?php if($_SESSION['rol'] =='Admin-Luis') echo  $_SESSION['rol'];?></span>
          <span class="user-status">
            <i class="fa fa-circle"></i>
            <span>Online</span>
          </span>
        </div>
      </div>
      
      <div class="sidebar-menu">
        <ul>
           
          <li class=sidebar-dropdown>
            <a href='#'>
              <i class='fas fa-cash-register'></i>
              <span>Fondos</span>
              <span class='badge badge-pill badge-warning'></span>
            </a>
            <div class='sidebar-submenu'>
              <ul>
                <?php 
                  if($numero_caja == 0 || $numero_caja == 12)
                  {
                    echo "<li>
                            <a href='file_ingresos.php'>Ingresos
                              <span class='badge badge-pill badge-success'></span>
                            </a>
                          </li>
                          <li>
                            <a href='file_egresos.php'>Egresos</a>
                          </li>";
                  }
                  if(tiene_permiso($numero_caja,1))
                    echo "<li>
                            <a href='file_ingresos.php'>Ingresos
                              <span class='badge badge-pill badge-success'></span>
                            </a>
                          </li>";
                  if(tiene_permiso($numero_caja,2))
                  echo "<li>
                          <a href='file_egresos.php'>Egresos</a>
                        </li>";
                ?>  
              </ul>
            </div>
          </li>
            
          <li class="sidebar-dropdown">
            <a href='#'>
              <i class='fas fa-sync'></i>
              <span>Operaciones</span>
            </a>
            <div class="sidebar-submenu">
              <ul>
                <?php 
                    if($numero_caja == 0 || $numero_caja == 12)
                    {
                      echo "<li>
                            <a href='file_orden_pago.php'>Operaciones de caja</a>
                            </li>
                            <li>
                            <a href='file_limitar_orden_pago.php'>Limitar órden de pago</a>
                            </li>
                            <li>
                            <a href='file_retiros.php'>Retiros</a>
                            </li>
                            <li>
                            <a href='file_nueva_transferencia.php'>Transferencia</a>
                            </li>
                            <li>
                            <a href='file_canjes.php'>Canjes</a>
                            </li>
                            <li>
                            <a href='file_canje_cheque_cra.php'>Canje de cheques</a>
                            </li>";
                    }
                    if(tiene_permiso($numero_caja,49)) // 3
                      echo "<li>
                            <a href='file_orden_pago.php'>Operaciones de caja</a>
                            </li>";

                    if(tiene_permiso($numero_caja,11))
                      echo "<li>
                            <a href='file_retiros.php'>Retiros</a>
                            </li>";

                    if(tiene_permiso($numero_caja,12))
                      echo "<li>
                            <a href='file_nueva_transferencia.php'>Transferencia</a>
                            </li>";

                    if(tiene_permiso($numero_caja,13))
                            echo "<li>
                                  <a href='file_canjes.php'>Canjes</a>
                                  </li>";
                    if(tiene_permiso($numero_caja,14))
                            echo "<li>
                                  <a href='file_canje_cheque_cra.php'>Canje de cheques</a>
                                  </li>";
                ?>             
              </ul>
            </div>
          </li>
          
          <!-- LISTADOS -->
          <li class="sidebar-dropdown">
            <a href="#" class='indice-listados'>
              <i class="far fa-list-alt"></i>
              <span>Listados</span>   
            </a>
            <div class="sidebar-submenu">
              <ul>
                  <?php 
                    if($numero_caja == 0 || $numero_caja == 12)
                    {
                      echo "<li>
                            <a href='file_listado.php'>Caja</a>
                            </li>

                            <li>
                            <a href='file_listar_caja.php'>Todas las Cajas</a>
                            </li>

                            <li>
                            <a href='file_listado_recibos.php'>Listado de recibos</a>
                            </li>

                            <li>
                            <a href='file_listado_posesion.php'>Posesión</a>
                            </li>

                            <li>
                            <a href='file_listado_red.php'>Red de Cloacas</a>
                            </li>

                            <li>
                            <a href='file_listado_op.php'>Órdenes de pago</a>
                            </li>

                            <li>
                            <a href='file_listado_op_cta.php'>Órdenes por cuenta</a>
                            </li>

                            <li>
                            <a href='file_cheques_cartera.php'>Chques en cartera</a>
                            </li>

                            <li>
                            <a href='file_listado_solicitudes.php'>Solicitudes</a>
                            </li>
                            <li>
                            <a href='file_listado_retiros.php'>Retiros</a>
                            </li>
                            <li>
                            <a href='file_transferencias_recibidas.php'>Transferencias recibidas</a>
                            </li>
                            <li>
                            <a href='file_transferencias_realizadas.php'>Transferencias realizadas</a>
                            </li>
                            <li>
                            <a href='file_listado_lotes.php'>Conexiones</a>
                            </li>";
                    }
                    if(tiene_permiso($numero_caja,15))                     
                      echo "<li>
                            <a href='file_listado.php'>Caja</a>
                            </li>";
                    if(tiene_permiso($numero_caja,50)){    // permiso "listar caja servicios"              
                      echo "<li>
                            <a href='file_listado_recibos.php'>Listado caja servicios</a>
                            </li>";
                    }
                    if(tiene_permiso($numero_caja,44)){
                      echo "<li>
                            <a href='file_listado_posesion.php'>Posesión</a>
                            </li>";
                    }
                    if(tiene_permiso($numero_caja,45)){
                      echo "<li>
                            <a href='file_listado_red.php'>Red de Cloacas</a>
                            </li>";
                    }
                    if(tiene_permiso($numero_caja,16))                     
                      echo "<li>
                            <a href='file_listar_caja.php'.php'>Todas las Cajas</a>
                            </li>";
                    if($rol == 'AdminBC')
                    { 
                      if(tiene_permiso($numero_caja,17))                     
                        echo "<li>
                              <a href='file_listado_op_bc.php'>Órdenes de pago</a>
                              </li>";
                    }
                    else {
                      if(tiene_permiso($numero_caja,17))  
                        echo "<li>
                              <a href='file_listado_op.php'>Órdenes de pago</a>
                              </li>";
                    }
                    if(tiene_permiso($numero_caja,18)) 
                        echo "<li>
                              <a href='file_listado_op_cta.php'>Órdenes por cuenta</a>
                              </li>";
                    if(tiene_permiso($numero_caja,19)) 
                        echo "<li>
                              <a href='file_cheques_cartera.php'>Chques en cartera</a>
                              </li>";
                    if(tiene_permiso($numero_caja,20)) 
                        echo "<li>
                              <a href='file_listado_solicitudes.php'>Solicitudes</a>
                              </li>";
                    if(tiene_permiso($numero_caja,21)) 
                        echo "<li>
                              <a href='file_listado_retiros.php'>Retiros</a>
                              </li>";
                    if(tiene_permiso($numero_caja,22)) 
                        echo "<li>
                              <a href='file_transferencias_recibidas.php'>Transferencias recibidas</a>
                              </li>";
                    if(tiene_permiso($numero_caja,23)) 
                        echo "<li>
                              <a href='file_transferencias_realizadas.php'>Transferencias realizadas</a>
                              </li>";
                    if(tiene_permiso($numero_caja,24)) 
                        echo "<li>
                              <a href='file_listado_lotes.php'>Conexiones</a>
                              </li>";
                  ?>                  
              </ul>
            </div>         
          </li>
          
          <!-- AUTORIZAR -->
          <?php  
            if($numero_caja == 0 || $numero_caja == 12 || $numero_caja == 13)
            {
              echo " <li>
                      <a href='file_autorizar_op.php'>
                        <i class='far fa-arrow-alt-circle-right'></i>
                        <span>Autorizar</span>
                      </a>
                    </li>";
            }
            
          ?> 

          <!-- COBRANZA -->
          <?php  
            if($numero_caja == 0 || $numero_caja == 12)
            {
              echo " <li>
                      <a href='file_cobranza.php'>
                        <i class='far fa-arrow-alt-circle-right'></i>
                        <span>Cargar cobranza</span>
                      </a>
                    </li>";
            }
            if(tiene_permiso($numero_caja,25))
            {
              echo " <li>
                      <a href='file_cobranza.php'>
                        <i class='far fa-arrow-alt-circle-right'></i>
                        <span>Cargar cobranza</span>
                      </a>
                    </li>";
            }
          ?> 

          <!-- REIMPRIMIR-->
          <li class="sidebar-dropdown">
               
            <a href='#'>
              <i class='fas fa-file-alt'></i>
              <span>Reimprimir</span>
            </a>
            <div class='sidebar-submenu'>
              <ul>
                <?php
                  if($numero_caja == 0 || $numero_caja == 12)
                  {
                    echo "<li>
                          <a href='file_reimprimir_ing.php'>Ingreso</a>
                          </li>
                          <li>
                            <a href='file_reimprimir_egr.php'>Egreso</a>
                          </li>
                          <li>
                            <a href='file_reimprimir_op.php'>Órden de pago</a>
                          </li>
                          <li>
                            <a href='file_reimprimir_tr.php'>Transferencia</a>
                          </li>
                          <li>
                            <a href='file_reimprimir_re.php'>Retiro</a>
                          </li>";
                  }
                  if(tiene_permiso($numero_caja,26))
                    echo "<li>
                          <a href='file_reimprimir_ing.php'>Ingreso</a>
                          </li>";
                  if(tiene_permiso($numero_caja,27))
                    echo "<li>
                          <a href='file_reimprimir_egr.php'>Egreso</a>
                          </li>";
                  if(tiene_permiso($numero_caja,28))
                    echo "<li>
                            <a href='file_reimprimir_op.php'>Órden de pago</a>
                          </li>";
                  if(tiene_permiso($numero_caja,29))
                    echo "<li>
                          <a href='file_reimprimir_tr.php'>Transferencia</a>
                          </li>";
                  if(tiene_permiso($numero_caja,30))
                    echo "<li>
                          <a href='file_reimprimir_re.php'>Retiro</a>
                          </li>";
                  
                ?>
              </ul>
            </div>
          </li>

          
          <li class="sidebar-dropdown">
            
            <a href='#' class="indice-servicios" >
              <i class='fas fa-crosshairs'></i>
              <span>Servicios</span>
            </a>
            <div class='sidebar-submenu'>
              <ul>
                  <?php
                    if($numero_caja == 0 || $numero_caja == 12)
                      echo "<li>
                            <a href='buscar_lotes_new.php'>Buscar lotes</a>
                            </li>";
                    if(tiene_permiso($numero_caja,31))
                      echo "<li>
                            <a href='buscar_lotes_new.php'>Buscar lotes</a>
                            </li>";
                  ?>     
              </ul>
            </div>  
                  
          </li>

          <!-- ADMINISTRAR-->
          <li class="sidebar-dropdown">
            <a href="#">
              <i class="fas fa-tools"></i>
              <span>Administrar</span>   
            </a>
            <div class="sidebar-submenu">
              <ul>
                <?php  
                    if($numero_caja == 0 || $numero_caja == 12)
                    {
                      echo "<li>
                            <a href='file_admin_usuarios.php'>Usuarios</a>
                            </li>
                            <li>
                            <a href='file_password.php'>Cambiar contraseña</a>
                            </li>
                            <li>
                            <a href='file_actualizar_precios.php'>Actualizar Precios</a>
                            </li>
                            <li>
                            <a href='file_agregar_cuenta.php'>Agregar Cuenta Contable</a>
                            </li>
                            <li>
                            <a href='file_agregar_empresa.php'>Agregar Empresa</a>
                            </li>
                            <li>
                            <a href='file_agregar_obra.php'>Agregar Obra</a>
                            </li>
                            <li>
                            <a href='file_editar_cuenta.php'>Editar Cuenta</a>
                            </li>
                            <li>
                            <a href='file_editar_empresa.php'>Editar Empresa</a>
                            </li>
                            <li>
                            <a href='file_editar_obra.php'>Editar Obra</a>
                            </li>
                            <li>
                            <a href='cargar_lotes.php'>Cargar lotes</a>
                            </li>
                            <li>
                            <a href='file_tasainteres.php'>Tasas de interes</a>
                            </li>"; 
                    } 
                    if(tiene_permiso($numero_caja,32)) 
                      echo "<li>
                            <a href='file_admin_usuarios.php'>Usuarios</a>
                            </li>"; 
                    if(tiene_permiso($numero_caja,33))  
                      echo "<li>
                            <a href='file_password.php'>Cambiar contraseña</a>
                            </li>"; 
                    if(tiene_permiso($numero_caja,34))
                      echo "<li>
                            <a href='file_agregar_cuenta.php'>Agregar Cuenta Contable</a>
                            </li>";   
                    if(tiene_permiso($numero_caja,35))
                      echo "<li>
                            <a href='file_editar_empresa.php'>Agregar Empresa</a>
                            </li>";  
                    if(tiene_permiso($numero_caja,36))
                      echo "<li>
                            <a href='file_agregar_obra.php'>Agregar Obra</a>
                            </li>";  
                    if(tiene_permiso($numero_caja,37))
                      echo "<li>
                            <a href='file_editar_cuenta.php'>Editar Cuenta</a>
                            </li>";
                    if(tiene_permiso($numero_caja,38))
                      echo "<li>
                            <a href='file_editar_empresa.php'>Editar Empresa</a>
                            </li>"; 
                    if(tiene_permiso($numero_caja,39))
                      echo "<li>
                            <a href='file_editar_obra.php'>Editar Obra</a>
                            </li>";  
                    if(tiene_permiso($numero_caja,40))
                      echo "<li>
                            <a href='cargar_lotes.php'>Cargar lotes</a>
                            </li>"; 
                    if(tiene_permiso($numero_caja,51)) 
                      echo "<li>
                            <a href='file_tasainteres.php'>Tasas de interes</a>
                            </li>";    

                ?>                
              </ul>
            </div>
          </li>

        </ul>
      </div>
      <!-- sidebar-menu  -->
    </div>

    <!-- sidebar-content  -->
    <div class="sidebar-footer">
      <a href="#">
        <i class="fa fa-bell"></i>
        <span class="badge badge-pill badge-warning notification">
          <?php  
            /*include('conexion.php');
            $cant = 0;
            $qry  = "SELECT count(*) as cantidad FROM solicitud_orden_pago";
            $res  = mysqli_query($connection, $qry);
            if($res->num_rows > 0){
              $dta = mysqli_fetch_array($res);
              $cant = $dta['cantidad'];
              echo $cant;
            }
            else echo 0;*/
          ?>
        </span>
      </a>
      <a href="#">
        <i class="fas fa-text-height"></i>
        <span class="badge badge-pill badge-success notification" id="notifi-tr">
          0
        </span>
      <a href="#">
        <i class="fa fa-cog"></i>
        <span class="badge-sonar"></span>
      </a>
      <a href="#" id="close-sesion">
        <i class="fa fa-power-off"></i>
      </a>
    </div>
  </nav>