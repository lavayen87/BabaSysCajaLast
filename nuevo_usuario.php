<?php

    include('conexion.php');
     //$block = $_POST['block'];
    $nombre  = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $pass = $_POST['pass'];
    $num_caja = $_POST['num_caja'];

    /**/

    
        // en caso de que se envie un numero de caja para reutilizar:

    $qry_chec = "SELECT * FROM usuarios WHERE numero_caja = '$num_caja'";

    $res_chec = mysqli_query($connection, $qry_chec);

    $cant = $res_chec->num_rows;

    if($cant > 0)
    {            

            $rol = 'Usuario_'.$num_caja;

            $arr = explode(' ',trim($nombre));

            if(count($arr) == 1)
                $nombre_caja = 'caja_'.$arr[0];
            else 
                $nombre_caja = 'caja_'.$arr[0].'_'.substr($arr[1], 0, 1);

            $update = "UPDATE usuarios 
                            SET nombre  = '$nombre',
                                usuario = '$usuario',
                                pass    = '$pass',
                                rol     = '$rol',
                            nombre_caja = '$nombre_caja',
                                block   = 0,
                            block_caja = 1
                            WHERE numero_caja = $num_caja";

            mysqli_query($connection, $update);
            mysqli_close($connection);
            echo 1;
    }
    /**/
    else{
        $qry = "SELECT * FROM usuarios";

        $res = mysqli_query($connection, $qry);

        $filas = $res->num_rows;

        if($filas == 0)
        {
            $num_caja = 1;

            $rol = 'Usuario_'.$num_caja;

            $arr = explode(' ',trim($nombre));

            if(count($arr) == 1)
                $nombre_caja = 'caja_'.$arr[0];
            else 
                $nombre_caja = 'caja_'.$arr[0].'_'.substr($arr[1], 0, 1);

            

            $insert = "INSERT IGNORE INTO usuarios VALUES 
            (1,
            1,
            '$nombre',
            '$pass',
            '$rol',
            '$usuario',
            '$nombre_caja',
            0,
            1
            )";

            mysqli_query($connection, $insert);
            mysqli_close($connection);
            echo 1;
        }
        else
        {
            if($filas >= 1)

                $num_caja = $filas+1;

                $qry_chec = "SELECT * FROM usuarios 
                             WHERE usuario = '$usuario'
                             OR pass = '$pass'";
                $res_chec = mysqli_query($connection, $qry_chec);

                $cant = $res_chec->num_rows;

                if($cant > 0)
                {
                    echo 400; 
                }
                else{
                    $rol = 'Usuario_'.$num_caja;

                    $arr = explode(' ',trim($nombre));
                    if(count($arr) == 1)
                        $nombre_caja = 'caja_'.$arr[0];
                    else 
                        $nombre_caja = 'caja_'.$arr[0].'_'.substr($arr[1], 0, 1);
                    
                    // agregado : max numero de operacion para asingar en caja_gral
					$qry_num = "SELECT max(id_usuario)+1 as numero FROM usuarios";
					$qry_res = mysqli_query($connection,$qry_num);
					$qry_datos = mysqli_fetch_array($qry_res);
					$id_usuario = $qry_datos['numero'];

                    $insert = "INSERT IGNORE INTO usuarios VALUES 
                    ('$id_usuario',
                    '$num_caja',
                    '$nombre',
                    '$pass',
                    '$rol',
                    '$usuario',
                    '$nombre_caja',
                    0,
                    1
                    )";

                    mysqli_query($connection, $insert);
                    mysqli_close($connection);
                    echo 1;
                }
        }
    }

?>