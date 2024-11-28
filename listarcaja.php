<?php 
  session_start();
  if($_SESSION['active'])
  {
    $micaja = $_SESSION['nombre_caja'];
    $numero_caja = $_SESSION['numero_caja'];
  }

                    include('conexion.php');

                    $fecha_inicial = "";
                    $fecha_finala  = "";

                    //if( isset($_POST['fecha_inicial']) && isset($_POST['fecha_final']) ){
                      $fecha_inicial = $_POST['fecha_inicial'];
                      $fecha_final = $_POST['fecha_final'];
                   // }

                    $hoy = date('Y-m-d');
                    $cant ="";
                    $tabla = "";
                    $tabla_final = "";
                    $saldo_anterior = 0.00;
                    $saldo_final = 0.00;
                    
                    $cabecera = "<table class='table table-striped'>
                                  <thead>  
                                    <tr> 
                                        <td><strong>N°</strong></td>
                                        <td><strong>Fecha</strong></td>
                                        <td><strong>N° caja</strong></td>
                                        <td><strong>Detalle</strong></td>
                                        <td><strong>Ingresos</strong></td>
                                        <td><strong>Egresos</strong></td>
                                        <td><strong>Pesos</strong></td>
                                        <td><strong>Dolares</strong></td>
                                        <td><strong>Euros</strong></td>
                                        <td><strong>Acción</strong></td>
                                    </tr>
                                  </thead>
                                  <tbody id='tbody-datos'>";
                    //if(isset($_POST['listar']))
                    //{
                      /*if( isset($_POST['fecha_inicial']) && $_POST['fecha_inicial'] !="" 
                         && isset($_POST['fecha_final']) && $_POST['fecha_final'] !="" )*/
                      //{
                        $fecha_inicial = $_POST['fecha_inicial'];
                        $fecha_final   = $_POST['fecha_final'];

                        if($fecha_inicial == $fecha_final) 
                        //si las fechas coinciden, mostramos el resultado con saldo anterior y totales.
                        {

                          // buscamos datos por las fechas ingresadas
                          $query = "SELECT * from caja_gral
                                    where fecha >= '$fecha_inicial' AND fecha <= '$fecha_final'
                                    AND numero_caja = '$numero_caja'
                                    order by numero";    
                          $result = mysqli_query($connection, $query);

                          // numero de filas encontradas en la busqueda anterior
                          $cant = $result->num_rows; 

                          // validamos el numero de filas encontradas y buscamos el saldo anterior
                          if($cant >= 1)
                          {
                            $query_saldo_anterior = "SELECT pesos from caja_gral 
                                          where fecha = date_add('$fecha_inicial', INTERVAL -1 DAY)
                                          AND numero_caja = '$numero_caja' 
                                          order by numero desc limit 1";  
                            $result_saldo = mysqli_query($connection, $query_saldo_anterior);
                            $saldo_inicial = mysqli_fetch_array($result_saldo);
                            // verificamos si existe un saldo anterior
                            if($saldo_inicial['pesos'] <> 0  &&  $saldo_inicial['pesos'] <>"" )
                            {
                              $saldo_anterior = $saldo_inicial['pesos'];
                              //echo "saldo anterior encontrado HOY: $".$saldo_anterior."</br>";
                            }
                            else {
                              $query_saldo_anterior = "SELECT * from caja_gral
                                        where pesos <> 0 AND fecha < '$fecha_inicial'
                                        AND numero_caja = '$numero_caja'
                                        order by numero desc limit 1 ";
                              $result_saldo = mysqli_query($connection, $query_saldo_anterior);
                              $saldo_inicial = mysqli_fetch_array($result_saldo);
                              
                              if($saldo_inicial['pesos'] <> 0  &&  $saldo_inicial['pesos'] <>"" )
                                $saldo_anterior = $saldo_inicial['pesos'];
                              else $saldo_anterior = 0.00;
                              //$saldo_anterior = 0.00; // <-- IMPORTANTE
                              //echo "saldo anterior enontrado hoy: $".$saldo_anterior."</br>";
                            }
                          }
                          else // si $cant = 0
                          {
                            $query_saldo_anterior = "SELECT * from caja_gral
                                        where pesos <> 0 AND fecha < '$fecha_inicial'
                                        AND numero_caja = '$numero_caja'
                                        order by numero desc limit 1 ";
                            $result_saldo = mysqli_query($connection, $query_saldo_anterior);
                            $saldo_inicial = mysqli_fetch_array($result_saldo);
                            $saldo_anterior = $saldo_inicial['pesos'];
                            //$saldo_anterior = 0.00;
                          }

                          // calculamos el total del dia
                          $query_total = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                          where (fecha = '$fecha_inicial') AND (dolares = 0) AND (euros = 0)
                                          AND numero_caja = '$numero_caja'";         
                          $result_total = mysqli_query($connection, $query_total);
                          $total = mysqli_fetch_array($result_total);

                          //if($total['total'] - $saldo_anterior > 0)
                          if($total['total'] <> 0){
                            $saldo_final = $total['total'];
                          }

                          ////// mostramos los datos ///////
                          $tabla.=$cabecera;
                          $tabla.= "<tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Saldo anterior: $".$saldo_anterior."</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                  </tr>";
                        
                          while($datos = mysqli_fetch_array($result))
                          {
                            $tabla.= "<tr>
                                    <td>".$datos['numero']."</td>
                                    <td>".$datos['fecha']."</td>
                                    <td>".$datos['numero_caja']."</td>
                                    <td>".$datos['detalle']."</td>
                                    <td>".$datos['ingreso']."</td>
                                    <td>".$datos['egreso']."</td>
                                    <td>".$datos['pesos']."</td>
                                    <td>".$datos['dolares']."</td>
                                    <td>".$datos['euros']."</td>
                                    <td><input type='button' class='borrar'  id='".$datos['numero']."' value='Eliminar' /></td>
                                  </tr>";
                          }

                            $tabla.= "<tr>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td>"."Saldo anterior: "."$".$saldo_anterior."</td>
                                    <td></td><td></td><td></td><td></td>
                                  </tr>
                                  <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td>".'Total del dia: '.'$'.$saldo_final."</td>
                                    <td></td><td></td><td></td><td></td>
                                  </tr>
                                  <tr>
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                    <td>".'Total General: '.'$'.($saldo_anterior + $saldo_final)."</td>
                                    <td></td><td></td><td></td><td></td>
                                  </tr>";
                            $tabla.="</tbody>";
                            echo $tabla;
                        } 
                        else //caso en que las fechas son distintas
                        {
                          if($fecha_inicial < $fecha_final)
                          {
                              $query_saldo_anterior = "SELECT * from caja_gral
                                        where fecha = date_add('$fecha_inicial', INTERVAL -1 DAY)
                                        AND pesos <> 0 AND numero_caja = '$numero_caja'
                                        order by numero desc limit 1 ";
                              $result_saldo = mysqli_query($connection, $query_saldo_anterior);
                              $saldo_inicial = mysqli_fetch_array($result_saldo);
                                // verificamos si existe un saldo anterior
                                if($saldo_inicial['pesos'] <> 0)
                                {
                                  $saldo_anterior = $saldo_inicial['pesos'];
                                  //echo "saldo anterior enontrado: $".$saldo_anterior."</br>";
                                }
                                else {
                                  $query_saldo_anterior = "SELECT * from caja_gral
                                        where pesos <> 0 AND fecha < '$fecha_inicial'
                                        AND numero_caja = '$numero_caja'
                                        order by numero desc limit 1 ";
                                  $result_saldo = mysqli_query($connection, $query_saldo_anterior);
                                  $saldo_inicial = mysqli_fetch_array($result_saldo);
                                  if($saldo_inicial['pesos'] <> 0)
                                    $saldo_anterior = $saldo_inicial['pesos'];
                                  else $saldo_anterior = 0.00;
                                  //echo "saldo anterior enontrado: $".$saldo_anterior."</br>";
                                }
                              // calculamos el total del dia
                              $query_total = "SELECT sum(ingreso) - sum(egreso) as total from caja_gral
                                              where (fecha >= '$fecha_inicial') 
                                              AND (fecha <= '$fecha_final') AND (dolares = 0) AND (euros = 0)
                                              AND numero_caja = '$numero_caja'";                
                              $result_total = mysqli_query($connection, $query_total);
                              $total = mysqli_fetch_array($result_total);

                              if($total['total'] <> 0){
                                $saldo_final = $total['total'];                              
                              }

                              // RESULTADO TABLA GENERAL
                              $query = "SELECT * from caja_gral
                                        where fecha >= '$fecha_inicial' AND fecha <= '$fecha_final'
                                        AND numero_caja = '$numero_caja'
                                        order by numero";    
                              $result = mysqli_query($connection, $query);
                              $tabla.=$cabecera;
                              $tabla.= "<tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Saldo anterior: $".$saldo_anterior."</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                      </tr>";
                              //$tabla.=$cabecera;
                              while($datos = mysqli_fetch_array($result))
                              {
                                $tabla.= "<tr>
                                        <td>".$datos['numero']."</td>
                                        <td>".$datos['fecha']."</td>
                                        <td>".$datos['numero_caja']."</td>
                                        <td>".$datos['detalle']."</td>
                                        <td>".$datos['ingreso']."</td>
                                        <td>".$datos['egreso']."</td>
                                        <td>".$datos['pesos']."</td>
                                        <td>".$datos['dolares']."</td>
                                        <td>".$datos['euros']."</td>
                                        <td><input type='button' class='borrar'  id='".$datos['numero']."' value='Eliminar' /></td>
                                      </tr>";
                              }
                              $tabla.= "<tr>
                                        <td></td><td></td><td></td><td></td><td></td><td></td>
                                        <td>"."Saldo anterior: "."$".$saldo_anterior."</td>
                                        <td></td><td></td><td></td>
                                      </tr>
                                      <tr>
                                        <td></td><td></td><td></td><td></td><td></td><td></td>
                                        <td>".'Total del dia: '.'$'.$saldo_final."</td>
                                        <td></td><td></td><td></td>
                                      </tr>
                                      <tr>
                                        <td></td><td></td><td></td><td></td><td></td><td></td>
                                        <td>".'Total General: '.'$'.($saldo_anterior + $saldo_final)."</td>
                                        <td></td><td></td><td></td>
                                      </tr>";
                                $tabla.="</tbody>";
                                echo $tabla;
                          }
                          else echo "<strong style='color: red;'>Fechas incorrectas</strong>";
                        }// end else tabla general

                      /*}
                      else echo "<strong style='color: red;'>Fechas incorrectas</strong>";
                    }
                    else echo "<strong style='color: blue;'>Ingrese fechas para listar</strong>";*/

                  ?>  