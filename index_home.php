<?php 
  date_default_timezone_set('America/Argentina/Salta');
	session_start();
 	$alerta ="";
 	if(!empty($_SESSION['active']))
 	{
 		header("Location: sidebar.php"); 
 	}
 	else{
	    if( isset($_POST['btn-login']) )
	    {
	    	if( isset($_POST['usuario']) && isset($_POST['pass']) 
            && $_POST['usuario'] !="" && $_POST['pass'] !="")
		    {   
		    	
		        $usuario = $_POST['usuario'];
		        $pass = $_POST['pass'];
		        include('conexion.php');
		        $query = "SELECT * FROM usuarios  
		                WHERE usuario = '$usuario'  AND pass = '$pass'";

		        $result = mysqli_query($connection,$query);
		       
		        if($result->num_rows > 0)
		        {
		            $row = mysqli_fetch_array($result);
		            	$_SESSION['active'] = true;
		                $_SESSION['nombre'] = $row['nombre'];
		                $_SESSION['rol'] = $row['rol'];
		                $_SESSION['usuario'] = $row['usuario'];
                    $_SESSION['nombre_caja'] = $row['nombre_caja'];
		                $_SESSION['numero_caja'] = $row['numero_caja'];
		                if($_SESSION['numero_caja'] == 13)
                    {
                      header("Location: file_autorizar_op.php");
                    }
                    else{
                      header("Location: sidebar.php"); 
                    }
		                         
		            
		        }
		        else  $alerta = "<strong>No se encontró el usuario.</strong>";
		    }
		    else  $alerta = "<strong>Debe ingresar el usuario y contraseña.</strong>";
		}   	
    
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Inicio</title>
  <link href="css/login-styles.css" rel="stylesheet" /> 
</head>

<body class="">
  <div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
      <main>
        <div class="container">
          <div class="row justify-content-center" >
            <div class="col-lg-5">
              <div class="card shadow-lg border-0 rounded-lg mt-5" style="width: 80%;">

                <div class="alert alert-success" >
                  <h3 style="text-align: center;">
                    <strong style="font-family: 'Consolas', monospace; color: green;">
                      Sistema de  cajas
                    </strong>
                  </h3>
                </div>

                <div class="card-body" >
                  <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                      <label class="small mb-1" for="inputEmailAddress"><strong>Usuario</strong></label>
                      <input class="form-control py-4" id="inputEmailAddress" type="text" name="usuario"
                        placeholder="Ingresa tu usuario" />
                    </div>
                    <div class="form-group">
                      <label class="small mb-1" for="inputPassword"><strong>Contraseña</strong></label>
                      <input class="form-control py-4" id="inputPassword" type="password" name="pass"
                        placeholder="contraseña" />
                    </div>
                    
                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                      
                      <!--button class="btn btn-primary" type="submit">Login</button-->
                      <div style="margin:0px auto;">
                        <button   type="submit" name="btn-login" id="btn-login" class="btn btn-success">Acceder</button>
                      </div>
                      <!--div class=""><a href="registro_usuario.php">Crear cuenta</a></div-->	
                    </div>
                  </form>
                </div>

                <div class="card-footer text-center" style="height: 35px;">
                  <div class="small"><?php echo $alerta; ?></div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <div id="layoutAuthentication_footer">
      <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid">
          <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">
              <?php
                echo "Copyright &copy; Your Website " .date('Y');
              ?>
              
            </div>
            <div>
              
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
  </script>
  <!--script src="js/scripts.js"></script-->
</body>

</html>