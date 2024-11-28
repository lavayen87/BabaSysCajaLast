<?php 
 
if($_SESSION['active'])
{
    $micaja = $_SESSION['nombre_caja'];
    $numero_caja= $_SESSION['numero_caja'];
}
include('conexion.php');
$query = "SELECT * FROM transferencias
          WHERE numero_caja_destino = '$numero_caja' 
          AND   estado = 'Recibido'";
$result = mysqli_query($connection, $query);
                                
if($result->num_rows > 0)
{
    while($datos = mysqli_fetch_array($result))
    {
        echo "<tr>
              <td>".$datos['numero_tr']."</td>
              <td>".$datos['fecha']."</td>
              <td>".$datos['fecha_aceptacion']."</td>
              <td>".$datos['nombre_caja_origen']."</td>
              <td>".$datos['numero_caja_origen']."</td>
              <td>".$datos['nombre_caja_destino']."</td>
              <td>".$datos['observaciones']."</td>
              <td>".$datos['pesos']."</td>
              <td>".$datos['dolares']."</td>
              <td>".$datos['euros']."</td>
              <td>".$datos['estado']."</td>
             </tr>";                              
    }
}
/*$query = "SELECT * FROM transferencias
		WHERE numero_caja_destino = '$numero_caja' AND estado = 'Recibido'";
$result = mysqli_query($connection, $query);

if($result->num_rows > 0)
{
	$lista = array();
    while($row = mysqli_fetch_array($result)) 
    {
      $lista[] = array(
        'numero_tr' => $row['numero_tr'],
        'fecha' => $row['fecha'],
        'fecha_aceptacion' => $row['fecha_aceptacion'],
        'nombre_caja_origen' => $row['nombre_caja_origen'],
        'numero_caja_origen' => $row['numero_caja_origen'],
        'numero_caja_destino' => $row['numero_caja_destino'],
        'detalle'  => $row['observaciones'],  
        'pesos'   => $row['pesos'],
        'dolares' => $row['dolares'],
        'euros'   => $row['euros'],
        'estado'  => $row['estado']
      );
    }
    $datos = json_encode($lista);
    echo $datos;
}
else echo "Error";*/


?>