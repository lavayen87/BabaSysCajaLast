
<?php
	
    session_start();
    include('conexion.php');

    if( isset($_POST['usuario']) && isset($_POST['pass']) )
    {   
        $usuario = $_POST['usuario'];
        $pass = $_POST['pass'];

        $query = "SELECT * FROM usuarios  
                WHERE usuario = '$usuario'  AND pass = '$pass'";

        $result = mysqli_query($connection,$query);
        //echo print_r($result);exit;
        //$datos = mysqli_fetch_array($result);
        //echo "<strong>".$datos['nombre']."</strong>";exit;
        if($result->num_rows > 0)
        {
            while($row = mysqli_fetch_array($result)){
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['rol'] = $row['rol'];
                $_SESSION['usuario'] = $row['usuario'];
                $_SESSION['numero_caja'] = $row['numero_caja'];
                header('home.php');          
            }
        }
        else echo "<script>
        			$('#content-aviso-login').html('Se produjo un error inesperado...intente nuyevamete.');
                    $('.aviso-login').slideDown();
                   </script>";
    }
    else echo "<script>
        			$('#content-aviso-login').html('No se encontro el usuario.');
                    $('.aviso-login').slideDown();
                   </script>";
   
?>