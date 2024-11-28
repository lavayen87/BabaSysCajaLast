<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $nombre = $_SESSION['nombre'];
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
<div class="container">
  <div class="bs-example">

    <nav class="navbar navbar-expand-md navbar-dark bg-dark">

        <!--a href="#" class="navbar-brand">Baba</a-->

        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
            
            <div class="navbar-nav">
                <!--a href="#" class="nav-item nav-link active">Home</a-->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link active dropdown-toggle" data-toggle="dropdown" style="color:aqua;">Fondos</a>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">Ingresos</a>
                        <a href="#" class="dropdown-item">Egresos</a>
                    </div>
                </div>
                
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link active dropdown-toggle" data-toggle="dropdown" style="color:aqua;">
                        Operaciones
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">Órden de pago</a>
                        <a href="#" class="dropdown-item">Órden de pago con cheque</a>
                        <a href="#" class="dropdown-item">Solicitud de órden de pago</a>
                        <a href="#" class="dropdown-item">Solicitud de órden de pago</a>
                        <a href="#" class="dropdown-item">Autorizar solicitud</a>
                        <a href="#" class="dropdown-item">Imprimir solicitud</a>
                        <a href="#" class="dropdown-item">Retiros</a>
                        <a href="#" class="dropdown-item">Transferencia</a>
                        <a href="#" class="dropdown-item">Canjes</a>
                        <a href="#" class="dropdown-item">Canjes de cheques</a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="#" class="nav-link  active dropdown-toggle" data-toggle="dropdown" style="color:aqua;">
                        Listados
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">Mi caja</a>
                        <a href="#" class="dropdown-item">Listar Cajas</a>
                        <a href="#" class="dropdown-item">Órdenes de pago</a>
                        <a href="#" class="dropdown-item">Órdenes por cuenta</a>
                        <a href="#" class="dropdown-item">Solicitudes</a>
                        <a href="#" class="dropdown-item">Retiros</a>
                        <a href="#" class="dropdown-item">Transferencias recibidas</a>
                        <a href="#" class="dropdown-item">Transferencias realizadas</a>
                    </div>
                </div>

                <div class="navbar-nav">
                    <a href="#" class="nav-item nav-link active" style="color:aqua;">Cargar cobranza</a>
                    
                </div>

                <div class="nav-item active dropdown">
                    <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown" style="color:aqua;">
                        Reimprimir
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">Ingreso</a>
                        <a href="#" class="dropdown-item">Egreso</a>
                        <a href="#" class="dropdown-item">Órden de pago</a>
                        <a href="#" class="dropdown-item">Retiro</a>
                        <a href="#" class="dropdown-item">Transferencia</a>
                    </div>
                </div>

                <div class="nav-item active dropdown">
                    <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown" style="color:aqua;">
                        Administrar
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">Usuarios</a>
                        <a href="#" class="dropdown-item">cambiar Contraseña</a>
                        <a href="#" class="dropdown-item">Agregar / editar cuenta</a>
                        <a href="#" class="dropdown-item">Agregar / editar empresa</a>
                        <a href="#" class="dropdown-item">Agregar / editar obra</a>
                    </div>
                </div>

            </div>

            <!--Buscador-->
            <!--form class="form-inline">
                <div class="input-group">                    
                    <input type="text" class="form-control" placeholder="Search">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-secondary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form-->

            <div class="navbar-nav">
                <!--a href="#" class="nav-item nav-link">Login</a-->
                <div class="nav-item active dropdown">
                    <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown" style="color:aqua;">
                        <?php echo $nombre;?>
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item">cerrar Sesión</a>
                    </div>
                </div>
            </div>
      
        </div>

    </nav>

  </div>
</div>