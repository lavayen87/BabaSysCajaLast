<?php 
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
  $micaja = $_SESSION['nombre_caja'];
  $nombre_usuario = $_SESSION['nombre'];
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
<script>
  $(document).ready(function(){

    
        $('#btnBackup').on('click', function(){
           
            $.get('backup.php', (resp) => {
                // if(parseInt(resp) == 1)
                //     alert("Backup Realizado con exito.")
                // else
                //     alert("Error al realizar el Backup...")
            })
        })
  })
</script>
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
      <h2>Principal</h2>
      <hr>
      <!--div class="alert alert-success" role="alert"> 
        <?php  

        include('conexion.php');

        include('funciones.php'); 
        $hoy = date('Y-m-d');
        $saldo_pesos = 0;
        $saldo_dolares = 0;
        $saldo_euros = 0;
        $fecha_saldos = '';

        $saldo_temp = "SELECT * FROM caja_gral_temp
              WHERE fecha = date_add('$hoy', INTERVAL -1 DAY)
              and numero_caja = '$numero_caja'
              and operacion = 1
              order by numero desc limit 1";
        $res_temp = mysqli_query($connection, $saldo_temp);

        if($res_temp->num_rows > 0)
        {
          $datos_temp = mysqli_fetch_array($res_temp);
          $fecha_saldos = $datos_temp['fecha'];

        }
        else       
        {
          $saldo_temp = "SELECT * FROM caja_gral_temp
                  WHERE fecha < '$hoy'
                  and numero_caja = '$numero_caja'
                  and operacion = 1
                  order by numero desc limit 1";
          $res_temp = mysqli_query($connection, $saldo_temp);
          if($res_temp->num_rows > 0)
          {
            $datos_temp = mysqli_fetch_assoc($res_temp);
            $fecha_saldos = $datos_temp['fecha'];
            
          }
        }

        
        if($fecha_saldos <> '')
        {

          $saldo_pesos = saldo_ant('pesos',$numero_caja,$hoy);
          
          echo 'Saldo anterior en pesos: '.'<strong>'.'$'.number_format($saldo_pesos,2,',','.').'</strong>'.' '.'('.fecha_min($fecha_saldos).')';

          echo '<hr>'; 
          $saldo_dolares = saldo_ant('dolares',$numero_caja,$hoy);
          
          echo 'Saldo anterior en dolares: '.'<strong>'.'US'.round($saldo_dolares).'</strong>'.' '.'('.fecha_min($fecha_saldos).')';

          echo '<hr>'; 
          $saldo_euros = saldo_ant('euros',$numero_caja,$hoy);
          
          echo 'Saldo anterior en euros: '.'<strong>'.'€'.round($saldo_euros).'</strong>'.' '.'('.fecha_min($fecha_saldos).')';
        }
        else
        {
          echo 'Caja '.'<strong>'.$nombre_usuario.'</strong>'. ' aún no  tiene movimientos.';
        }
        
        


        ?>
      </div-->

      <?php 
        if($numero_caja == 0)
        {
          echo "<br>
                <button class='btn btn-primary' id='btnBackup'>BackUp</button>
                <br>";
        }
      ?>

    </div>

  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>