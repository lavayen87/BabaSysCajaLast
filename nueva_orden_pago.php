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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="js/main-style.js"></script>


<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-single').select2();
  });
</script>
<style>
  .opciones-op ul li{
    list-style: none;
    padding: 5px;
  }

  .opciones-op ul li a{
    text-decoration: none;
    color: white;
    font-family: verdana;
    margin-left: 5px;
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
      <h2>Órden de pago</h2>
      <hr>
      <div class="row">  
        <div class="form-group col-md-12">  

          <div class="alert alert-success" role="alert">              
              <!--menu horizontal-->
            <nav class="navbar navbar-expand-lg navbar-light " style="background-color: #22A49D; border-radius: 6px;">
              <div class="container-fluid">                    
                          
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav">
                      <?php
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
                          //echo "<a href='file_solicitud_op.php' class='nav-item nav-link' style='color: white;'>Solicitud</a>";
                          if($numero_caja == 3 || $numero_caja == 9)
                          {
                          echo "<div class='nav-item dropdown'>
                                    <a href='#' class='nav-link dropdown-toggle' data-bs-toggle='dropdown' style='color: white;'>Solicitud con</a>
                                    <div class='dropdown-menu'>
                                        <a href='file_solicitud_banco.php' class='dropdown-item item0'>Banco</a>
                                        <a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>
                                        <a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>
                                        <a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>
                                    </div>
                                </div>";
                          }
                          else{
                            echo "<div class='nav-item dropdown'>
                                    <a href='#' class='nav-link dropdown-toggle' data-bs-toggle='dropdown' style='color: white;'>Solicitud con</a>
                                    <div class='dropdown-menu'>
                                        <a href='file_solicitud_cash.php' class='dropdown-item item1'>Efectivo</a>
                                        <a href='file_solicitud_my_check.php' class='dropdown-item item2'>Mis cheques</a>
                                        <a href='file_solicitud_check_list.php' class='dropdown-item item3'>Cheques en cartera</a>
                                    </div>
                                </div>";
                          }
                        }
                        if($numero_caja == 34 || $numero_caja == 22 || $numero_caja == 7 || $numero_caja == 9 || $numero_caja == 10 || $numero_caja == 12 || $numero_caja == 3) 
                        {
                          echo "<a href='file_autorizar_op.php' class='nav-item nav-link' style='color: white;'>Autorizar</a>";
                        }
                                  
                        if($numero_caja == 1 || $numero_caja == 3 || $numero_caja == 9 || $numero_caja == 12)
                        {
                          echo "<a href='file_emitir_orden.php' class='nav-item nav-link' style='color: white;'>Emitir órden de pago</a>";
                        }
                        
                      ?>
                    </div>
                    <?php 
                      echo "<div class='navbar-nav ms-auto'>
                                <div  class='nav-item nav-link info_opcion_opt'></div>
                            </div>";
                    ?>
                </div>
              </div>
            </nav>
            <!--fin menu horizontal-->
            <br>

            <div style="100%; overflow: hidden;">
                <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                    <div class="form-group col-md-6" style="width: 40%; float: left;"> 
                      <strong>Empresa</strong>
                      <div class="row-fluid">
                          <select name="op-empresa" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" id="select-empresa">
                            <option value=""><?php echo ""; ?></option> 
                            <?php  
                            include("conexion.php");
                            $consulta = "SELECT DISTINCT * FROM empresas";
                            $resultado = mysqli_query($connection , $consulta);

                            while($misdatos = mysqli_fetch_assoc($resultado))
                            { 
                              echo "<option value='".$misdatos['nombre_empresa']."' id='".$misdatos['id_empresa']."'>".$misdatos['nombre_empresa']."</option>"; 
                            }
                            ?>
                          </select>
                      </div>
                      
                      <strong>Obra</strong>
                      <div class="row-fluid">
                          <select name="op-obra" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" class="" id="select-obra"> 
                            <option value=""><?php echo ""; ?></option>
                            <?php  
                            include("conexion.php");
                            $consulta = "SELECT DISTINCT * FROM obras";
                            $resultado = mysqli_query($connection , $consulta);

                            while($misdatos = mysqli_fetch_assoc($resultado))
                            { 
                              echo "<option value='".$misdatos['nombre_obra']."' id='".$misdatos['id_obra']."'>".$misdatos['nombre_obra']."</option>"; 
                            }
                            ?>
                          </select>
                      </div>
                      
                      <strong>Cuenta Contable</strong>             
                      <div class="row-fluid">
                        <select name="op-cuenta" style="width: 100%;" class="js-example-basic-single form-control" id="select-cuenta">
                          <option value=""><?php echo ""; ?></option> 
                          <!-- js-example-basic-multiple name="states[]" multiple="multiple" para select multiple-->
                          <?php
                          include("conexion.php");
                          $consulta = "SELECT DISTINCT * FROM cuentas ORDER BY descripcion";
                          $resultado = mysqli_query($connection , $consulta);

                          while($misdatos = mysqli_fetch_assoc($resultado))
                          { 
                            echo "<option value='".$misdatos['descripcion']."'>".$misdatos['descripcion']."</option>"; 
                          }

                          ?>          
                        </select>
                      </div>
                      <strong>Recibe</strong>
                      <input name="op-recibe" id="importe-op" type="text" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" maxlength="30">
                      <br>                            
                      <strong>Importe</strong>
                      <input name="op-importe" id="importe-op" type="number" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;">
                      <br>
                      <strong>Detalle</strong>
                      <input name="op-detalle" id="detalle-op" type="text" style="width: 100%; height: 29px; border-radius: 5px 5px 5px 5px;" maxlength="30">                    
                    
                      <br><br>
                    
                      <!--seccion de botones Aceptar  - cancelar  - nueva orden d epago-->
                        <button name="op-aceptar" id="aceptar-op" class="btn btn-primary" style="display: inline-block;">Aceptar</button>
                        <button name="op-cancelar" id="cancelar-op" class="btn btn-secondary" style="display: inline-block;">Cancelar</button>
                                  
                        <button name="op-nueva" id="nueva-op" class="btn btn-success" style="float: right;"><i class="fas fa-plus-circle"></i>Nueva</button>
                    </div>  
                                  
                </form>  
            
            </div> <!--div contenedor del formulario y opciones de operacion-->

            <br>
            <?php
                include('conexion.php');
                include('funciones.php');
                $resp = "";
                $band = 0;
                if(isset($_POST['op-aceptar']))
                {
                    if($_POST['op-empresa']!="" && $_POST['op-obra'] && $_POST['op-cuenta'] && $_POST['op-importe'] && $_POST['op-detalle'])
                    {
                        $empresa = $_POST['op-empresa'];
                        $obra = $_POST['op-obra'];
                        $cuenta = $_POST['op-cuenta'];
                        $importe = $_POST['op-importe'];
                        $detalle = $_POST['op-detalle'];

                        $fecha   = date('Y-m-d');

                        $saldo_anterior = 0.00;
                        $saldo_anterior_dolares = 0;
                        $saldo_anterior_euros = 0;
                        $saldo_anterior_cheques = 0;
                        $monto = 0;
                        $total_gral = 0.00;

                        // Consigo pesos desde mi caja
                        if($numero_caja == 3)
                        {
                            $query = "SELECT pesos
                                    from caja_gral
                                    where numero_caja = '$numero_caja' 
                                    AND operacion = 1
                                    order by numero desc limit 1"; 
                        }
                        else
                        {
                            $query = "SELECT pesos
                                    from caja_gral
                                    where numero_caja = '$numero_caja' 
                                    AND operacion = 1
                                    AND fecha = '$fecha'
                                    order by numero desc limit 1"; 
                        }
                        
                        $result = mysqli_query($connection, $query);
                        $datos = mysqli_fetch_array($result);
                        $pesos = $datos['pesos'];

                        //Consigo dolares desde mi caja
                        $query = "SELECT sum(dolares) as total_dolares from caja_gral
                                    where numero_caja = '$numero_caja'
                                    and operacion = 2
                                    and fecha = '$fecha'
                                    order by numero desc limit 1";    
                        $result = mysqli_query($connection, $query);
                        $datos = mysqli_fetch_array($result);
                        $dolares_hoy = $datos['total_dolares'];

                        //Consigo euros desde mi caja
                        $query = "SELECT sum(euros) as total_euros from caja_gral
                                    where numero_caja = '$numero_caja'
                                    and operacion = 3
                                    and fecha = '$fecha'
                                    order by numero desc limit 1";    
                        $result = mysqli_query($connection, $query);
                        $datos = mysqli_fetch_array($result);
                        $euros_hoy = $datos['total_euros'];

                        //Consigo cheques desde mi caja
                        $query = "SELECT sum(cheques) as total_cheques from caja_gral
                                where numero_caja = '$numero_caja'
                                and operacion = 4
                                and fecha = '$fecha'
                                order by numero desc limit 1";   
                        $result = mysqli_query($connection, $query);
                        $datos = mysqli_fetch_array($result);
                        $cheques_hoy = $datos['total_cheques'];

                        ////////////////////////////////////////////

                        // consigo el ultimo cobro en caja cobranza
                        $qry = "SELECT  importe from cobranza
                                WHERE fecha = '$fecha' 
                                AND numero_caja = '$numero_caja'
                                order by numero limit 1";
                        $res = mysqli_query($connection, $qry);
                        $datos = mysqli_fetch_array($res);
                        $ultimo_cobro = $datos['importe']; 

                        // Realizo la operacion  

                        if($ultimo_cobro > 0.00)
                        {
                            if($pesos == [])
                            {
                                $pesos_a_restar = $ultimo_cobro - $importe;
                            }
                            else
                            {
                                $pesos_a_restar = $pesos - $importe;
                            }
                        }
                        else
                        {
                            if($pesos == [])
                            {
                                $pesos_a_restar = (-1)*$importe;
                            }
                            else
                            {
                                $pesos_a_restar = $pesos - $importe;
                            }
                        }

                        // cargo la orden en mi caja
                        $insert1 = "INSERT IGNORE INTO caja_gral
                        VALUES ('','$numero_caja','$fecha','$fecha','$detalle',0,'$importe','$pesos_a_restar',0,0,0,1)";
                        //$insert_result1 = mysqli_query($connection, $insert1);
                        mysqli_query($connection, $insert1);
                        // consigo numero de movimiento 
                        $qry = "SELECT numero FROM caja_gral
                                    WHERE numero_caja = '$numero_caja'
                                    AND operacion = 1
                                    AND fecha = '$fecha'
                                    order by numero desc limit 1";
                        $res_qry = mysqli_query($connection, $qry);
                        $get_datos = mysqli_fetch_array($res_qry);
                        $num = $get_datos['numero'];

                        // cargo la orden en tabla orden_pago
                        $insert2 = "INSERT IGNORE INTO orden_pago VALUES ('$num','$fecha','$numero_caja','$cuenta','$detalle','$importe',0,'$empresa','$obra','pesos','Dueño del local')";
                        //$result_insert2 = mysqli_query($connection, $insert2);
                        mysqli_query($connection, $insert2);

                        //Buscamos Saldo anterior 
                        $saldo_anterior = saldo_ant('pesos',$numero_caja,$fecha);
                        $saldo_anterior_dolares = saldo_ant('dolares',$numero_caja,$fecha);
                        $saldo_anterior_euros = saldo_ant('euros',$numero_caja,$fecha);
                        $saldo_anterior_cheques = saldo_ant('cheques',$numero_caja,$fecha);


                        //Consigo total del dia en pesos desde mi caja

                        $pesos_hoy = get_total(1,$numero_caja,$fecha);

                        //Consigo total del dia en dolares desde mi caja

                        $dolares_hoy = get_total(2,$numero_caja,$fecha);

                        //Consigo total del dia en euros desde mi caja

                        $euros_hoy = get_total(3,$numero_caja,$fecha);

                        //Consigo total del dia en cheques desde mi caja

                        $cheques_hoy = get_total(4,$numero_caja,$fecha);

                        // consigo cobranza
                        $cob = "SELECT importe from cobranza
                                    WHERE fecha = '$fecha'
                                    AND numero_caja = '$numero_caja'
                                    order by numero limit 1";
                        $res_cob = mysqli_query($connection, $cob);
                        $datos_cob = mysqli_fetch_array($res_cob);

                        // cargo la tabla de totales generales

                        if($datos_cob['importe']<>[]){
                            $monto = $datos_cob['importe'];
                        }

                        if( ($pesos_hoy<>[]) && ($monto>=0) )
                        {
                            $total_gral_pesos = ($saldo_anterior + $pesos_hoy + $monto);
                        }
                        else
                            if( ($pesos_hoy==[]) && ($monto>=0) )
                            {
                                $total_gral_pesos = ($saldo_anterior + $monto);
                            }
                            else{
                                
                                $total_gral_pesos = $saldo_anterior;
                            }
                            


                        $total_gral_dolares = ($saldo_anterior_dolares + $dolares_hoy);
                        $total_gral_euros = ($saldo_anterior_euros + $euros_hoy);
                        $total_gral_cheques = ($saldo_anterior_cheques + $cheques_hoy);

                        $qry = "SELECT * from caja_gral_temp
                                where operacion = 1
                                and numero_caja = '$numero_caja'
                                and fecha = '$fecha'	
                                order by numero desc limit 1";    
                        $res = mysqli_query($connection, $qry);

                        if($res->num_rows>0)
                        {
                            $set = "UPDATE caja_gral_temp
                                    SET pesos = '$total_gral_pesos',
                                    dolares = '$total_gral_dolares',
                                    euros = '$total_gral_euros',
                                    cheques = '$total_gral_cheques'
                                    WHERE numero_caja = '$numero_caja'
                                    and fecha = '$fecha'
                                    and operacion = 1";
                            $res = mysqli_query($connection, $set);
                        }
                        else{
                            $insert = "INSERT IGNORE INTO caja_gral_temp VALUES 
                            ('','$numero_caja','$fecha','$fecha','Total gral.','$total_gral_pesos','$total_gral_dolares','$total_gral_euros','$total_gral_cheques',1)";

                            $result_insert = mysqli_query($connection, $insert);
                        }

                        $resp = 'Datos Procesados.';
                        $band = 1;
                    }
                    else{
                        $resp = 'Debe llenar todos los campos.';
                    }
                }
                else{
                    $resp = "";
                    $band = 0;
                }
                      
            ?>
            <div class="form-group col-md-6">

                    <div class="row">
                      <div id="content" class="col-lg-12">
                          <?php
                            if($band == 1)
                            {
                              echo "<strong>".$resp."</strong>";
                              echo "<div class='' role='alert' id='exito-op'>                 
                                      <strong>Órden de pago realizada con exito !</strong>                
                                      <br><br>
                                      <div class='button-close'>
                                        <a href='factura/orden_pago_pdf.php' id='show-op-pdf' class='btn btn-primary' target='_blank'>
                                            Imprimir 
                                            <i class='fas fa-print'></i>
                                        </a>
                                        <button id='cerrar-content-op' class='btn btn-secondary' style='display: inline-block;'>Cancelar</button>
                                      </div>
                                    </div>";
                            }
                            else{
                              echo $resp;
                            }
                            

                          ?>
                      </div>
                    </div>

                                                
            </div> <!--div resultado-->   
                         
          </div> <!--div alert--> 
        </div>       
      </div> <!--div row-->
    </div>  <!--div container-->                
  </main>
  <!-- page-content" -->
</div>
<!-- page-wrapper -->
</body>
</html>