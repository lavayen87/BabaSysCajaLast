
<?php 
date_default_timezone_set('America/Argentina/Salta');

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
            if($numero_caja == 34)
            {
              echo "<img class='img-responsive img-rounded' src='img/administrador1.jpg' alt='User picture'>";
            } 
            else
              echo "<img class='img-responsive img-rounded' src='https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg' alt='User picture'>";
          ?>
          <!--img class="img-responsive img-rounded" src="https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg" alt="User picture"-->
        </div>
        <div class="user-info">
          <span class="user-name"> 
            <strong>
              <?php 
                if($numero_caja == 34 || $numero_caja == 5 || $numero_caja == 6 || $numero_caja == 8)
                  echo $nombre_usuario;
                else echo $rol; 
              ?>
            </strong>
          </span>
          <span class="user-role"><?php if($_SESSION['rol'] =='Administrador2') echo  $_SESSION['rol'];?></span>
          <span class="user-status">
            <i class="fa fa-circle"></i>
            <span>Online</span>
          </span>
        </div>
      </div>
      
      <div class="sidebar-menu">
        <ul>
          <?php 
            if($numero_caja <> 22 && $numero_caja <> 2)
            {
              echo "<li class=sidebar-dropdown>
                <a href='#'>
                  <i class='fas fa-cash-register'></i>
                  <span>Fondos</span>
                  <span class='badge badge-pill badge-warning'></span>
                </a>
                <div class='sidebar-submenu'>
                  <ul>
                    <li>
                      <a href='file_ingresos.php'>Ingresos
                        <span class='badge badge-pill badge-success'></span>
                      </a>
                    </li>
                    <li>
                      <a href='file_egresos.php'>Egresos</a>
                    </li>
                  </ul>
                </div>
              </li>";
            }
          ?>
          
          <li class="sidebar-dropdown">
            <?php
            if($numero_caja <> 2)
            echo "<a href='#'>
              <i class='fas fa-sync'></i>
              <span>Operaciones</span>
            </a>";
            ?>
            <div class="sidebar-submenu">
              <ul>
                <li>
                  <?php 
                    /*if($numero_caja <> 22)
                    {
                      echo "<a href='file_orden_pago.php'>Órden de pago</a>";
                    }*/
                    if($numero_caja <> 2)
                      echo "<a href='file_orden_pago.php'>Órden de pago</a>";
                  ?>
                  
                </li>

                <!--li>
                  <?php 
                    if($numero_caja <> 22 && $numero_caja <> 2)
                    {
                      echo "<a href='file_orden_pago_cheque.php'>Órden de pago con cheque</a>";
                    }
                  ?>  
                </li>
                
                <li>
                  <?php 
                    if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                      echo "<a href='file_solicitud_op.php'>Solicitud de órden de pago</a>";
                  ?>
                </li>
                
                <li>
                  <?php 
                    if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 12 || $numero_caja == 9  || $numero_caja == 3) 
                      echo "<a href='file_autorizar_op.php'>Autorizar solicitud</a>";
                  ?>
                </li-->
                
                <li>
                  <?php 
                    if($numero_caja <> 22 && $numero_caja <> 2)
                    {
                      echo "<a href='file_retiros.php'>Retiros</a>";
                    }
                  ?>
                </li>
                
                <li>
                  <?php 
                      if($numero_caja <> 22 && $numero_caja <> 2)
                      {                       
                        echo "<a href='file_nueva_transferencia.php'>Transferencia</a>";
                      }
                  ?>                 
                </li>
                
                <li>
                  <?php 
                      if($numero_caja <> 22 && $numero_caja <> 2)
                      {                       
                        echo "<a href='file_compras.php'>Canjes</a>";
                      }
                  ?>
                </li>
                
                <li>
                  <?php 
                      if($numero_caja <> 22 && $numero_caja <> 2)
                      {     
                        echo "<a href='file_canje_cheque_cra.php'>Canje de cheques</a>";                  
                        //echo "<a href='file_canje_cheque.php'>Canje de cheques</a>";
                      }
                  ?>                  
                </li>
              
              </ul>
            </div>
          </li>
         
          <li class="sidebar-dropdown">
            <a href="#">
              <i class="far fa-list-alt"></i>
              <span>Listados</span>   
            </a>
            <div class="sidebar-submenu">
              <ul>
                <li>
                  <?php 
                      if($numero_caja <> 22 && $numero_caja <> 2)
                      {                       
                        echo "<a href='file_listado.php'>Caja</a>";
                      }
                  ?>                 
                </li>
                <li>
                  <?php 
                      if($numero_caja <> 22)
                      {                 
                        echo "<a href='file_listado_op.php'>Órdenes de pago</a>"; 
                      }
                      else
                      {  
                        echo "<a href='file_listado_op_bc.php'>Órdenes de pago</a>"; 
                      }
                  ?>                  
                </li>
                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 9 || $numero_caja == 7)
                      echo "<a href='file_listado_op_cta.php'>Órdenes por cuenta</a>";
                  ?>   
                </li>
                <li>
                  <?php  
                      if($numero_caja <> 2 && $numero_caja <> 8)
                        echo "<a href='file_cheques_cartera.php'>Chques en cartera</a>";
                  ?>   
                </li>
                <li>
                  <?php               
                      echo "<a href='file_listado_solicitudes.php'>Solicitudes</a>";                    
                  ?>
                </li>
                <li>
                  <?php  
                    if($numero_caja <> 22)
                    {
                      echo "<a href='file_listado_retiros.php'>Retiros</a>";
                    }
                  ?>       
                </li>
                <li>
                  <?php  
                    if($numero_caja <> 22 && $numero_caja <> 2)
                    {
                      echo "<a href='file_transferencias_recibidas.php'>Transferencias recibidas</a>";
                    }
                  ?>                  
                </li>
                <li>
                  <?php  
                    if($numero_caja <> 22 && $numero_caja <> 2)
                    {
                      echo "<a href='file_transferencias_realizadas.php'>Transferencias realizadas</a>";
                    }
                  ?>                  
                </li>
              </ul>
            </div>         
          </li>
          
          <li class="sidebar-dropdown">
            <a href="#">
              <i class="fas fa-tools"></i>
              <span>Administrar</span>   
            </a>
            <div class="sidebar-submenu">
              <ul>
                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 7)
                      echo "<a href='file_admin_usuarios.php'>Usuarios</a>";                  
                  ?>
                </li>
                <li>
                  <a href="file_password.php">Cambiar contraseña</a>
                </li>

                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 9 || $numero_caja == 7)
                      echo "<a href='file_agregar_cuenta.php'>Agregar Cuenta Contable</a>";
                  ?>
                </li>
                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 9 || $numero_caja == 7)
                      echo "<a href='file_agregar_empresa.php'>Agregar Empresa</a>";
                  ?>
                </li>
                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 9 || $numero_caja == 7)
                      echo "<a href='file_agregar_obra.php'>Agregar Obra</a>";
                  ?>   
                </li>
                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 9 || $numero_caja == 7)
                      echo "<a href='file_editar_cuenta.php'>Editar Cuenta</a>";
                  ?>
                </li>
                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 9 || $numero_caja == 7)
                      echo "<a href='file_editar_obra.php'>Editar Obra</a>";
                  ?>
                </li>
                <li>
                  <?php  
                    if($numero_caja == 34 || $numero_caja == 9 || $numero_caja == 7)
                      echo "<a href='file_editar_empresa.php'>Editar Empresa</a>";
                  ?>
                </li>
                <li>
                  <?php  
                    if($numero_caja == 34)
                      echo "<a href='cargar_lotes.php'>Cargar lotes</a>";
                  ?>
                </li>
              </ul>
            </div>
          </li>

          <li>
            <?php  
              if($numero_caja == 34 || $numero_caja == 1 || $numero_caja == 9 || $numero_caja == 10)
              {
                echo "<a href='file_cobranza.php'>
                        <i class='far fa-arrow-alt-circle-right'></i>
                        <span>Cargar cobranza</span>
                      </a>";
              }
            ?>  
          </li>
          <li class="sidebar-dropdown">
            <?php  
              if($rol == 'Admin1' || $rol == 'Daniel B.'|| $rol == 'Admin-Luis')
                echo "<a href='#'>
                        <i class='fas fa-file-alt'></i>
                        <span>Reimprimir</span>
                      </a>
                      <div class='sidebar-submenu'>
                        <ul>
                          <li>
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
                          </li>
                        </ul>
                      </div>";
            ?> 
          </li>

          <li>
            <?php 
              if($numero_caja == 34  || $numero_caja == 2 || $numero_caja == 5 || $numero_caja == 9 || $numero_caja == 12)
                echo "<a href='file_listar_caja.php'.php'>
                        <i class='far fa-arrow-alt-circle-right'></i>
                        <span>Listar Cajas</span>
                      </a>";
            ?>
          </li>
          <li class="sidebar-dropdown">
            <?php 
              if($numero_caja == 34  || $numero_caja == 2)
              {
                echo "<a href='#'>
                        <i class='fas fa-crosshairs'></i>
                        <span>Servicios</span>
                      </a>
                      <div class='sidebar-submenu'>
                        <ul>
                          
                          <li>
                            <a href='buscar_lotes_new.php'>Buscar lotes</a>
                          </li>
                          
                          <li>
                            <a href='file_listado_lotes.php'>Listado de lotes</a>
                          </li>
                        </ul>
                      </div>";
                      //-version original
                      /*<li>
                          <a href='file_load_service.php'>Cargar servicio</a>
                      </li>
                      <li>
                            <a href='file_update_service.php'>Buscar lotes</a>
                        </li>*/
                      //-
                      /*<li> agregar al listado de servicios
                          <a href='file_lotes_servicios.php'>Posesion</a>
                        </li>*/
              }
              else{
                if($numero_caja == 8)
                {
                  echo "<a href='#'>
                        <i class='fas fa-crosshairs'></i>
                        <span>Servicios</span>
                      </a>
                      <div class='sidebar-submenu'>
                        <ul>
                          <li>
                            <a href='file_listado_lotes.php'>Listado de lotes</a>
                          </li>
                        </ul>
                      </div>";
                }
                else
                {
                  if($numero_caja == 1)
                  {
                    /*echo "<a href='#'>
                          <i class='fas fa-crosshairs'></i>
                          <span>Servicios</span>
                        </a>
                        <div class='sidebar-submenu'>
                          <ul>
                            <li>
                              <a href='file_load_service.php'>Cargar servicio</a>
                            </li>
                          </ul>
                        </div>";
                    */
                    echo "<a href='#'>
                        <i class='fas fa-crosshairs'></i>
                        <span>Servicios</span>
                      </a>
                      <div class='sidebar-submenu'>
                          <ul>
                            <li>
                              <a href='buscar_lotes_new.php'>Buscar lotes</a>
                            </li>
                          </ul>
                      </div>";
                  } 
                }
              }
            ?>
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
            include('conexion.php');
            $cant = 0;
            $qry  = "SELECT count(*) as cantidad FROM solicitud_orden_pago";
            $res  = mysqli_query($connection, $qry);
            if($res->num_rows > 0){
              $dta = mysqli_fetch_array($res);
              $cant = $dta['cantidad'];
              echo $cant;
            }
            else echo 0;
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