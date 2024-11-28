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
<html lang="en">
<link rel="shortcut icon" href="img/logo-sistema.png">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheques en cartera</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
<link rel="stylesheet" href="css/sidebar-style.css">
<script src="js/jquery-3.5.1.min.js"></script>  
<script src="js/main-style.js"></script>
<script src="js/main.js"></script>
<script>
  
  var elem;
  $(document).ready(function(){

    $(".delete-chek").on('click', function(){
      elem = $(this);
      var id = $(this).attr('id');
      var nchec = $(this).attr('nchec');
      var info = "<strong>Va a eliminar el cheque N° "+nchec+"?</strong>";
      $('#modal-info').html(info);
      $('#miModal').slideDown();

      $('.ok-modal-delete').on('click', function(){
        $('#miModal').slideUp();
       
          $.post('eliminar_cheques.php',{'id_cheque':id}, resp =>{
            console.log('resp:'+resp);
            if(resp == parseInt(1))
            {
              elem.closest('tr').remove();
            }
          });
      })

      $('.close-modal-delete').on('click', function(){
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
    width:350px;
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
                <button class="btn btn-success ok-modal-delete">Aceptar</button>
                <button class="btn btn-secondary close-modal-delete">Canelar</button>
            </div>
        </div>  
    </div>

    <div class="container">
      <h2>Cheques en cartera</h2>
      <hr>
      <div class="row">
        <div class="form-group col-md-12">
          <div class="alert alert-success" role="alert" style="padding-left: 2px; width:100%;">        
            
            <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
              <input type="number" placeholder='Nº cheque' name='num_cheque'>
              <button class="btn btn-primary" name='buscar-cheque'>Buscar</button>
              <button class="btn btn-success" name='ver-todos'>Ver todos</button>
              <?php
                echo "<a href='factura/cheques_cra.php?num_caja=$numero_caja' class='btn btn-primary' id='print-cheq-list' style='float: right; display: none;' title='Imprimir' target='_blanck'>
                        <i class='fas fa-print'></i>
                      </a>";
              ?>
              
            
              <hr>
              <?php
              include('conexion.php');
              include('funciones.php');
              $resp = "";
              $cabecera = "<table class='table table-hover'>        
              <thead>  
              <tr> 
              <td><strong>Vence</strong></td>
              <td><strong>Nº</strong></td>
              <td><strong>Banco</strong></td>
              <td><strong>Entregó</strong></td>
              <td><strong>Recibió</strong></td>
              <td><strong>Entregado</strong></td>
              <td><strong>Importe</strong></td>
              <td><strong>Estado</strong></td>";
              if($numero_caja == 12)
                $cabecera.="<td><i class='fas fa-arrow-down'></i></td>
                </tr>
                </thead>
                <tbody id='tbody-datos'>";
              $tabla = "";

              // filtro de busqueda por Nº cheque
              if(isset($_POST['buscar-cheque']))
              {
                if($_POST['num_cheque']>0)
                {
                  $num_cheque = $_POST['num_cheque'];
                  if($numero_caja == 4 || $numero_caja == 9 )
                  {                   
                    $qry = "SELECT * FROM cheques_cartera
                            WHERE num_cheque = '$num_cheque'";
                  }
                  else{
                    $qry = "SELECT * FROM cheques_cartera 
                            WHERE num_cheque = '$num_cheque'
                            and (num_caja_origen = '$numero_caja'
                            or num_caja_destino = '$numero_caja')";
                            //and activo = 1";
                  }
                  $res = mysqli_query($connection,$qry);
                  if($res->num_rows > 0)
                  {
                    $tabla.=$cabecera;
                    while($datos = mysqli_fetch_array($res))
                    {
                      $id_cheq = $datos['id_cheque'];
                      $tabla.="<tr style='background-color: transparent; border-bottom: 1px solid black;'>
                                <td>".fecha_min($datos['fecha_vto'])."</td>
                                <td>".$datos['num_cheque']."</td>
                                <td>".$datos['banco']."</td>
                                <td>".$datos['entrego']."</td>
                                <td>".$datos['persona_pago']."</td>
                                <td>".fecha_min($datos['fecha_entrega'])."</td>
                                <td>".number_format($datos['importe'],2,',','.')."</td>";
                                if($datos['estado']=="En cartera")
                                {
                                  if($numero_caja == 4 || $numero_caja == 9)
                                  {
                                    if($datos['caja_destino'] != "" )
                                    {
                                      $destino = $datos['caja_destino'];
                                    }
                                    else $destino = $datos['caja_origen'];

                                    $tabla.="<td>".$datos['estado']." - ".$destino."</td>                         
                                    ";
                                  }
                                  else $tabla.="<td>"."<strong style='color:green;'>".$datos['estado']."</strong>"."</td>                         
                                       ";
                                }
                                else
                                {
                                  if($datos['estado']=="Transferido"){
                                    if($numero_caja == 4 || $numero_caja = 9)
                                    {
                                      if($datos['caja_destino'] != "" )
                                      {
                                        $destino = $datos['caja_destino'];
                                      }
                                      else $destino = $datos['caja_origen'];

                                      $tabla.="<td>".$datos['estado']." (caja ".$destino.")"."</td>                         
                                      ";
                                    }
                                    else $tabla.="<td>"."<strong style='color:blue;'>".$datos['estado']."</strong>"."</td>                         
                                         ";
                                  }
                                  else{          
                                      $tabla.="<td>"."<strong>".$datos['estado']."</strong>"."</td>                         
                                      ";                                 
                                  }
                                }
                                if($numero_caja == 12){
                                  $tabla.="<td>"."<a href='#' id=".$id_cheq." class='delete-chek' style='color: red;' font-size:18px;>
                                         <i class='fas fa-trash-alt'></i>
                                         </a>
                                         </td></tr>";
                                }
                                else{
                                  $tabla.="<tr>";
                                }
                                
                                /*if($datos['estado']=="En cartera"){
                                  $tabla.="<td>"."<strong style='color:green;'>".$datos['estado']."</strong>"."</td>                         
                                  <tr>";
                                }
                                else{
                                  if($datos['estado']=="Transferido"){
                                    $tabla.="<td>"."<strong style='color:blue;'>".$datos['estado']."</strong>"."</td>                         
                                    <tr>";
                                  }
                                  else{          
                                      $tabla.="<td>"."<strong>".$datos['estado']."</strong>"."</td>                         
                                      <tr>";                                 
                                  }
                                }*/
                                
                    }
                    $tabla.="</tbody>";
                    echo $tabla;
                  }
                  else{
                    echo "<strong>No se encontró el cheque.</strong>";
                  }
                }
                else{
                  echo "<strong>Debe ingresar un Nº de cheque.</strong>";
                }
              }

              else
              {
                if(isset($_POST['ver-todos']) || !isset($_POST['ver-todos']))
                {
                  if($numero_caja == 4 || $numero_caja == 9 || $numero_caja == 12)
                  {
                    $qry = "SELECT * FROM cheques_cartera 
                            WHERE estado = 'En cartera'
                            ORDER BY fecha_vto";
                  }
                  else
                  {
                    $qry = "SELECT * FROM cheques_cartera 
                            WHERE (num_caja_origen = '$numero_caja'
                            or num_caja_destino = '$numero_caja')
                            and (activo < 3)
                            ORDER BY fecha_vto";
                  }
                                
                  $res = mysqli_query($connection, $qry);
                  
                  $tabla.=$cabecera;
                  if($res->num_rows > 0)
                  {
                    while($datos = mysqli_fetch_array($res))
                    {
                      $id_cheq = $datos['id_cheque'];
                      $tabla.="<tr style='background-color: transparent; border-bottom: 1px solid black;'>
                                <td style='width: 7%;'>".fecha_min($datos['fecha_vto'])."</td>
                                <td>".$datos['num_cheque']."</td>
                                <td>".$datos['banco']."</td>
                                <td>".$datos['entrego']."</td>
                                <td>".$datos['persona_pago']."</td>
                                <td>".fecha_min($datos['fecha_entrega'])."</td>
                                <td>".number_format($datos['importe'],2,',','.')."</td>";
                                
                                if($datos['estado']=="En cartera")
                                {
                                  if($numero_caja == 4 || $numero_caja == 9 || $numero_caja == 12)
                                  {
                                    if($datos['caja_destino'] != "" )
                                    {
                                      $destino = $datos['caja_destino'];
                                    }
                                    else $destino = $datos['caja_origen'];

                                    $tabla.="<td>".$datos['estado']." - ".$destino."</td>                         
                                    ";
                                  }
                                  else $tabla.="<td>"."<strong style='color:green;'>".$datos['estado']."</strong>"."</td>                         
                                       ";
                                }
                                else
                                {
                                  if($datos['estado']=="Transferido"){
                                    if($numero_caja == 4 || $numero_caja == 9 || $numero_caja == 12 )
                                    {
                                      if($datos['caja_destino'] != "" )
                                      {
                                        $destino = $datos['caja_destino'];
                                      }
                                      else $destino = $datos['caja_origen'];

                                      $tabla.="<td>".$datos['estado']." (caja ".$destino.")"."</td>                         
                                      ";
                                    }
                                    else $tabla.="<td>"."<strong style='color:blue;'>".$datos['estado']."</strong>"."</td>                         
                                         ";
                                  }
                                  else{          
                                      $tabla.="<td>"."<strong>".$datos['estado']."</strong>"."</td>                         
                                      ";                                 
                                  }
                                }
                                if($numero_caja == 12){
                                  $tabla.="<td>"."<a href='#' id=".$id_cheq." nchec='".$datos['num_cheque']."' class='delete-chek' style='color: red;' font-size:18px;>
                                         <i class='fas fa-trash-alt'></i>
                                         </a>
                                         </td></tr>";
                                }
                                else{
                                  $tabla.="<tr>";
                                }
                    }
                    echo "<script>$('#print-cheq-list').show();</script>";
                    $tabla.="</tbody>";
                    echo $tabla;
                  }
                  else
                  {
                    echo "<strong>No hay cheques para mostrar.</strong>";
                  }
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

</body>
</html>