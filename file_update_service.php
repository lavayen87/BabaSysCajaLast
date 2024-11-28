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
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<link rel="stylesheet" href="css/styles-update.css">

<!--link rel="stylesheet" href="chosen/chosen.css"-->
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Bootstrap core CSS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('input').on('blur', function() {
        var field = $(this);
        var validationField = field.parent().find('.validation');
        var dataString = 'id_cliente='+field.attr('id_cliente')+'&id='+field.attr('id');
        dataString+= '&value='+field.val()+'&field='+field.attr('name');
		$.ajax({
            type: "POST",
            url: "update_service.php",
            data: dataString,
            success: function(data) {
				field.val(data);
                validationField.hide().empty();

                setTimeout(function() {
                    validationField.append('<i class="fa fa-check"></i>');
				    validationField.show();
                }, 500); 
            }
        });
	});

});
</script>

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
      <h2>Actualizar servicios</h2>
      <hr>
       
      <div class="alert alert-success" role="alert"> 
    
        <div class="row">
            <div id="content" class="col-lg-12">
                
                <form  method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                    Busqueda
                    <div style="width: 18rem;">
                        <input type="text" class="form-control" name="codigo-lote" style="width: 30%; float: left;" maxlength='6' placeholder="Lote">
                        <button class="btn btn-primary" name="buscar-codigo" style="display: inline-block; margin-left: 5px;">Buscar</button>
                    </div>
                    <hr>

                    <?php
                    $codigo = "";
                    include('conexion.php');

                    if(isset($_POST['buscar-codigo']))
                    {
                        $codigo = $_POST['codigo-lote'];

                        if($codigo != "")
                        {
                            $qry = "SELECT cl.id_cliente, cl.nombre, cl.telefono, cl.lote, ds.id, ds.servicio, ds.recibo,
                                    ds.fecha_pago, ds.fecha_solicitud, ds.fecha_realizado, ds.fecha_abonado, ds.estado 
                                    FROM clientes as cl INNER JOIN det_servicio as ds
                                    on cl.id_cliente = ds.id_cliente
                                    where cl.lote = '$codigo'";
                            
                            $result = $connection->query($qry);

                            if ($result->num_rows > 0) 
                            {
                                $row = $result->fetch_array();
                            ?>
                               

                                <div class="input-group">
                                    <div style='width: 20%;'>Cliente</div>
                                    
                                        <div class="input-group-prepend">
                                            <span class="input-group-text validation"></span>
                                        </div>
                                        <input type="text" style="display: inline-block;" class="form-control" name="nombre" id_cliente="<?=$row['id_cliente']?>" value="<?=$row['nombre']?>" >
                                   
                                </div>
                                
                                <div class="input-group">
                                    <div style='width: 20%;'>Teléfono</div>
                                     
                                        <div class="input-group-prepend">
                                            <span class="input-group-text validation"></span>
                                        </div>
                                        <input type="text" style="display: inline-block;" class="form-control" name="telefono" id_cliente="<?=$row['id_cliente']?>"  value="<?=$row['telefono']?>" >
                                     
                                </div>
                                
                                <div class="input-group">
                                    <div style='width: 20%;'>lote</div>
                                     
                                        <!--div class="input-group-prepend">
                                            <span class="input-group-text validation" ></span>
                                        </div-->
                                        <input type="text" style="display: inline-block;" class="form-control" name="lote" id="<?=$row['id_cliente']?>" value="<?=$row['lote']?>" readonly maxlength="6">
                                    
                                </div>
                            

                                <?php
                                $qry_servicios = "SELECT cl.id_cliente, cl.nombre, cl.telefono, cl.lote, ds.id, ds.servicio, ds.recibo,
                                        ds.fecha_pago, ds.fecha_solicitud, ds.fecha_realizado, ds.fecha_abonado, ds.estado, ds.forma_pago 
                                        FROM clientes as cl INNER JOIN det_servicio as ds
                                        on cl.id_cliente = ds.id_cliente
                                        where cl.lote = '$codigo'";
                                
                                $res_servicios = mysqli_query($connection,$qry_servicios);

                                $ficha = "";

                                while($serv = mysqli_fetch_array($res_servicios))
                                {
                                    $ficha.= "<hr>
                                            <div class='form-group'>
                                            <strong>".$serv['servicio']."</strong>
                                            </br></br>

                                            <div class='input-group'>

                                                <div style='width: 20%;'>Nº de recibo</div>
                                                <div class='input-group-prepend'>
                                                    <span class='input-group-text validation' style='background: #AAF139;'></span>
                                                </div>
                                                <input type='text' class='form-control' name='recibo' value='".$serv['recibo']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."' maxlength='10'>
                                                
                                            </div>

                                            <div class='input-group'>

                                                <div style='width: 20%;'>fecha de pago</div>
                                                <div class='input-group-prepend'>
                                                    <span class='input-group-text validation' style='background: #AAF139;'></span>
                                                </div>
                                                <input type='date' class='form-control' name='fecha_pago' value='".$serv['fecha_pago']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                                
                                            </div>

                                            <div class='input-group'>

                                                <div style='width: 20%;'>fecha de solicitud</div>
                                                <div class='input-group-prepend'>
                                                    <span class='input-group-text validation' style='background: #AAF139;'></span>
                                                </div>
                                                <input type='date' class='form-control' name='fecha_solicitud'  value='".$serv['fecha_solicitud']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                                
                                            </div>

                                            <div class='input-group'>

                                                <div style='width: 20%;'>fecha de realizado</div>
                                                <div class='input-group-prepend'>
                                                    <span class='input-group-text validation' style='background: #AAF139;'></span>
                                                </div>
                                                <input type='date' class='form-control' name='fecha_realizado'  value='".$serv['fecha_realizado']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                                
                                            </div>

                                            <div class='input-group'>

                                                <div style='width: 20%;'>fecha de abonado</div>
                                                <div class='input-group-prepend'>
                                                    <span class='input-group-text validation' style='background: #AAF139;'></span>
                                                </div>
                                                <input type='date' class='form-control' name='fecha_abonado'  value='".$serv['fecha_abonado']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."'>
                                                
                                            </div>

                                            <div class='input-group'>

                                                <div style='width: 20%;'>Estado</div>
                                                <div class='input-group-prepend'>
                                                    <span class='input-group-text validation' style='background: #AAF139;'></span>
                                                </div>
                                                <input type='text' class='form-control' name='estado'  value='".$serv['estado']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."' maxlength='12'>
                                                
                                            </div>";

                                        /*</div>*/
                                        

                                            if($serv['servicio'] == 'Red Cloacas')
                                            {
                                                $ficha.="<div class='input-group'>

                                                        <div style='width: 20%;'>Forma de pago</div>
                                                        <div class='input-group-prepend'>
                                                            <span class='input-group-text validation' style='background: #AAF139;'></span>
                                                        </div>
                                                        <input type='text' class='form-control' name='forma_pago'  value='".$serv['forma_pago']."' id='".$serv['id']."' id_cliente='".$serv['id_cliente']."' maxlength='15'>
                                                        
                                                        </div>"; 

                                            }

                                            
                                }

                                $ficha.="</div>";
                                echo $ficha;

                                ?>
                            
                            
                            <?php
                            }
                            else{
                                echo "No se encontró el codigo: ".$codigo;   
                            }
                        }
                        else{
                            echo "Debe ingrese el codigo del lote";
                        }
                    }

                    
                    ?>

                    

                </form>
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