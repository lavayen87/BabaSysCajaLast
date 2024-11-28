<?php

if($_SESSION['active'])
{
    $micaja = $_SESSION['nombre_caja'];
    $numero_caja= $_SESSION['numero_caja'];
}
include('conexion.php');
$query = "SELECT * FROM transferencias
          WHERE numero_caja_origen = '$numero_caja'";
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
?>