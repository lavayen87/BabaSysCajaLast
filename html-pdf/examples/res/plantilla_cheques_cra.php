<?php
    
    
    date_default_timezone_set("America/Argentina/Salta");
    session_start();
    if($_SESSION['active'])
    {
        $micaja = $_SESSION['nombre_caja'];
        $nombre = $_SESSION['nombre'];
        $rol = $_SESSION['rol'];
        $numero_caja = $_SESSION['numero_caja'];
    }

    $numero_caja = $_GET['num_caja'];
    
?>


<style type="text/css">

    div.zone { 
        border: none; 
        background: #FFFFFF; 
        padding: 2mm;
        margin-left: 5px;
        font-size: 15px;
        width: 95%;
    }
    h1 { padding: 0; margin: 0;  font-size: 5mm; }
     
    p, label, span strong{     
        font-size: 11pt;
        line-height:1em;
    }
    #content-firma{
        width: 100%; 
        height: 40px;
        padding-top: 45px;
        padding-left: 4px;
        overflow: hidden; 
        margin-bottom: 20px;
        
    }
    .anulada{
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translateX(-50%) translateY(-50%);
}
    
</style>


<page width:="21cm" height="29.7cm"  style="font: arial;">
<div style="background-color: #d6e9c6; width: 100%; height: 4%; padding: 8px 8px;">
    <div style="float: left;">
        <img src="img/baba-img2.png" style="height: 40px; width: 40px; padding-top: 8px;">
        <span style="display: inline-block; padding-bottom: 8px;">Baba Urbanizaciones </span>
        <span style="display: inline-block; padding-bottom: 8px; padding-left: 18%;">
            <?php echo "Cheques en cartera - ".$rol; ?>
        </span>
    </div>
    <div style="float: right; padding-top: 15px;">
        <?php 
            
            include('../../conexion.php');
            include('../../funciones.php');
            
            //echo "Listado de caja (".fecha_min($fecha1)." - ".fecha_min($fecha2).")";
            $fecha = date('Y-m-d');
            $hora = date('G').':'.date('i').':'.date('s');
            echo fecha_min($fecha)." - "." Hora: ".$hora;
        ?>
    </div>
</div>

<br>

<main class="">
<div class="form-group col-md-12">
	<div class="alert alert-success" role="alert">
    	<div class="container">
                 
            <?php
                $cabecera = "<table>        
                <thead>  
                <tr> 
                <td><strong>Vence</strong></td>
                <td><strong>Número</strong></td>
                <td><strong>Banco</strong></td>
                <td><strong>Entregó</strong></td>
                <td><strong>Recibió</strong></td>
                <td><strong>Entregado</strong></td>
                <td><strong>Importe</strong></td>
                <td><strong>Estado</strong></td>
                </tr>
                </thead>
                <tbody>";
                $tabla = "";
                $destino = "";
                
                if($numero_caja == 4 || $numero_caja == 9)
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
                        and (activo = 1)
                        ORDER BY fecha_vto";
                }
                            
                $res = mysqli_query($connection, $qry);
                
                $tabla.=$cabecera;
                
                
                    while($datos = mysqli_fetch_array($res))
                    {
                        $tabla.="<tr>
                        <td style='width: 7%;'>".fecha_min($datos['fecha_vto'])."</td>
                        <td style='text-align: center ; width: 8%;'>".$datos['num_cheque']."</td>
                        <td style='text-align: center ;'>".$datos['banco']."</td>
                        <td style='text-align: center ;'>".limitar_cadena($datos['entrego'],16)."</td>
                        <td style='text-align: center ;'>".$datos['persona_pago']."</td>
                        <td style='text-align: center ;'>".fecha_min($datos['fecha_entrega'])."</td>
                        <td style='text-align: right;'>".number_format($datos['importe'],2,',','.')."</td>";
                        //<td style='text-align: center ;'>".$datos['estado']."</td>";
                                
                        if($datos['estado']=="En cartera")
                        {
                            if($numero_caja == 4 || $numero_caja == 9)
                            {
                                if($datos['caja_destino'] != "" ) 
                                {
                                    $destino = $datos['caja_destino'];
                                }
                                else{
                                    $destino = $datos['caja_origen'];
                                }
                                $tabla.="<td style='text-align: center ;'>".$datos['estado']." - ".$destino."</td>                         
                                <tr>";
                                
                            }
                            else 
                                $tabla.="<td style='text-align: center ;'>"."<strong style='color:green;'>".$datos['estado']."</strong>"."</td>                         
                                <tr>";
                        }
                        else
                        {
                            if($datos['estado']=="Transferido")
                            {
                                if($numero_caja == 4 || $numero_caja == 9)
                                {
                                    if($datos['caja_destino'] != "" )
                                    {
                                        $destino = $datos['caja_destino'];
                                    }
                                    else{ 
                                        $destino = $datos['caja_origen'];
                                    }
                                    $tabla.="<td>".$datos['estado']." (caja ".$destino.")"."</td>                         
                                    <tr>";
                                    
                                }
                                else 
                                    $tabla.="<td>"."<strong style='color:blue;'>".$datos['estado']."</strong>"."</td>                         
                                    <tr>";
                            }
                            else{          
                                $tabla.="<td>"."<strong>".$datos['estado']."</strong>"."</td>                         
                                <tr>";                                 
                            }
                        }
                                
                    }
                    $tabla.="</tbody></table>";
                    echo $tabla;
                
                  
                
                            
    		?>  			
             
    	</div>
    </div>
</div>
</div>
        
     
</page>
