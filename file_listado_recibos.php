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
<html lang="en">
<head>
    <link rel="shortcut icon" href="img/logo-sistema.png">  
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <script src="js/jquery-3.5.1.min.js"></script> 
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->
    <!-- Bootstrap core CSS -->
    <script src="js/main-style.js"></script>
    <script src="js/main.js"></script>
    <script>
        var nombre = "";
        var usuario= "";
        var pass   = "";
        $(document).ready(function(){

            //Eliminar fila de caja
            var num_recibo = parseInt(0);
            var elem;
            $('.btn-eliminar-reibo').on('click', function(event){
                event.preventDefault();
                num_recibo = $(this).attr('id'); 
                elem = $(this);
                console.log('fila a eliminar: '+$(this).attr('id'))
                var info = "<strong>¿Realmente desea anular el recibo Nº "+num_recibo+"? </strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.ok-delete-recibo').on('click', function(){
                    
                    $('#miModal').slideUp();
                    $(".btn-eliminar-reibo[id='"+num_recibo+"']").hide();
                    //elem.closest('tr').remove();            
                   // window.open('html-pdf/examples/recibo_ejemplo.php' , '_blank');
                    $.post('anular_recibo.php', {'num_recibo': num_recibo,}, resp =>{
                        console.log(resp)
                        if(resp !='Error')
                        {
                            console.log('recibo '+num_recibo+' anulado');
                        }
                        else console.log('No se eliminó el recibo '+num_recibo+' en la BD');
                    
                    })
                })

                $('.close-delete-recibo').on('click', function(){
                    $('#miModal').slideUp();
                })


            })
        })
    </script>
    <style>
        /** modal */
        .modal-contenido{
            background-color: white;
            border: 4px solid #22A49D;
            border-radius: 8px;
            width:300px;
            padding: 10px 20px;
            margin: 20% auto;
            position: relative;
            /*box-shadow: 0 0 0 0.4rem rgba(40, 167, 69, 0.25);*/
            box-shadow: 0 0 0 2px rgb(255,255,255),
                        0.3em 0.3em 1em rgba(0,0,0,0.6);
        }
        .close-modal{
            text-decoration: none;
        }
        .modal{
            /*background-color: #CCC8;/*rgba(0,0,0,.8);
            background-color: transparent;/*rgba(0,0,0,.8);*/
            
            position:fixed;
            top:0;
            right:0;
            bottom:0;
            left:0;
            /*opacity:0.5;*/
            pointer-events:none;
            /*transition: all 1s;*/
            }
            #miModal{ /**target */
            opacity:1;
            pointer-events:auto;
            
            }
            #modal-info strong{
                font-size:17px;
        }
        /** end modal */
    </style>
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

    <!-- Modal -->
    <div id="miModal" class="modal">
        <div class="modal-contenido">
            
            
            <p id="modal-info"></p>
            <div style="width:100%; height: 35px; margin: 0 auto; text-align: center; ">
                <button class="btn btn-success ok-delete-recibo">Aceptar</button>
                <button class="btn btn-secondary close-delete-recibo">Canelar</button>
            </div>
        </div>  
    </div>

    <div class="container">
      <h2>Listado caja servicios</h2>
      <hr>
                     
        <div class="alert alert-success" role="alert"> 
            <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                <strong>Desde:</strong> 
                <input type="date" name="fecha_inicial" id="fecha_inicial" style="width: 132px;" value="<?php if(isset($_POST['fecha_inicial'])) echo $_POST['fecha_inicial']; else echo date('Y-m-d'); ?>">

                <strong>Hasta:</strong> 
                <input type="date" name="fecha_final" id="fecha_final" style="width: 132px;" value="<?php if(isset($_POST['fecha_final'])) echo $_POST['fecha_final']; else echo date('Y-m-d'); ?>">

                <!--strong>Nº de caja:</strong> <input type="number" name="num_caja" style="width: 60px;"-->
                <input type="submit" name="listar" value="Listar" id="btn-listar" class="btn btn-success" title='Listar caja'>  
                <?php
                    $f1 = "";
                    $f2 = "";
                    if(isset($_POST['fecha_inicial']))
                    {
                      $f1 = $_POST['fecha_inicial'];
                    }
                   
                    if(isset($_POST['fecha_final']))
                    {
                      $f2 = $_POST['fecha_final'];
                    }
                    
                    echo "<a href='factura/listado_recibos.php?caja=$numero_caja&f1=$f1&f2=$f2' type='submit' name='print-listado' id='print-listado-recibos'  target='_blank' style='float: right; display: none;' class='btn btn-primary' title='Imprimir'><i class='fas fa-print'></i></a>";
                ?>  
            </form>
            <div class="table-responsive">
                <?php
                    $fecha = date('Y-m-d'); 
                    $total = 0; 
                    $alerta = "";

                    if(isset($_POST['listar']))
                    {
                    
                        if( isset($_POST['fecha_inicial']) && $_POST['fecha_inicial'] !="" 
                        && isset($_POST['fecha_final']) && $_POST['fecha_final'] !="" )
                        {
                            $fecha_inicial = $_POST['fecha_inicial'];
                            $fecha_final   = $_POST['fecha_final'];

                            if($fecha_inicial <= $fecha_final)
                            {
                                include('conexion.php');
                                include('funciones.php');

                                $tabla = "<table class='table table-striped'>
                                            <thead>
                                            <tr>
                                                <th><strong>#</strong></th>
                                                <th><strong>Fecha</strong></th>
                                                <th><strong>Titular</strong></th>
                                                <th><strong>Loteo</strong></th>
                                                <th><strong>Lote</strong></th>
                                                <th style='text-align: center;'><strong>Concepto</strong></th>
                                                <th><strong>Importe</strong></th>
                                                <th><strong>Acción</strong></th>
                                            </tr>
                                            </thead>
                                            <tbody id='res-tr'>
                                            </tbody>
                                            ";
                                            
                                $qry = "SELECT * FROM recibo
                                        WHERE fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
                                        order by numero";
                                $res = mysqli_query($connection, $qry);
                            
                                if($res->num_rows > 0)
                                {
                                    while($datos = mysqli_fetch_array($res))
                                    {

                                        
                                        $lote = $datos['lote'];
                                        $num_recibo = $datos['numero'];
                                        $tabla.="<tr>
                                        <td>".$datos['numero']."</td>
                                        <td style='width: 6%;'>".fecha_min($datos['fecha'])."</td>
                                        <td>".$datos['titular']."</td>
                                        <td>".$datos['loteo']."</td>
                                        <td>".$datos['lote']."</td>
                                        <td style='text-align: center;'>".get_code_recibo($lote,$num_recibo, $fecha_inicial, $fecha_final)."</td>";
                                        if($datos['estado'] == 1)
                                        {
                                            $total+= $datos['importe'];
                                            $tabla.="<td>"."$".number_format($datos['importe'],2,',','.')."</td>";
                                        }
                                        else{
                                            $tabla.="<td><s style='color: #C70039;'>"."$".number_format($datos['importe'],2,',','.')."</s></td>";
                                        }
                                        if($datos['fecha'] == $fecha && $datos['estado'] == 1)
                                        {
                                            $tabla.="<td>".
                                                    "<button class='btn btn-secondary btn-eliminar-reibo' id='".$datos['numero']."'><i class='fas fa-trash-alt'></i></button> 
                                                    </td>";    
                                                    //"<a href='html-pdf/examples/recibo_ejemplo.php?num_recibo=$num_recibo' class='btn btn-primary' target='_blank'><i class='fas fa-print'></i></a>".
                            
                                        }
                                        /*else{
                                            $tabla.="<td><a href='html-pdf/examples/recibo_ejemplo.php?num_recibo=$num_recibo' class='btn btn-primary' target='_blank'><i class='fas fa-print'></i></a></td>";
                                        }*/
                                        
                                        $tabla.="</tr>";     

                                    }
                                    
                                    $tabla.="<tr>
                                            <td colspan='5'></td>
                                            <td style='text-align: right;'><strong>Total:</strong></td>
                                            <td>".'$'.number_format($total,2,',','.')."</td>
                                            <td></td>
                                            </tr>
                                            </tbody>
                                            <tsble>";
                                    
                                    echo $tabla;

                                    echo "<script>$('#print-listado-recibos').show();</script>";
                                    
                                }
                                else
                                {
                                    $alerta = "<strong>No se encontraron recibos.</strong>";
                                }

                            }
                            else
                                $alerta = "<strong>Fechas incorrectas.</strong>";
                            
                        }
                    
                    }
                ?>
            </div>
            <div style="display: none;" id="resultado-busqueda"></div>
               
            <div style="" id="alerta-resultado"><?php echo $alerta; ?></div>
              
        </div>
         
    </div>
</main>
</body>