<?php 
	session_start();
 	$alerta ="";
 	if(!empty($_SESSION['active']))
 	{
 		header("Location: sidebar.php"); 
 	}
 	else{
	    if( isset($_POST['btn-register']) )
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
		                $_SESSION['numero_caja'] = $row['numero_caja'];
		                $_SESSION['nombre_caja'] = $row['nombre_caja'];
		                header("Location: sidebar.php");          
		            
		        }
		        else  $alerta = "<strong style='color: red;'>No se encontró el usuario.</strong>";
		    }
		    else  $alerta = "<strong style='color: red;'>Debe ingresar el usuario y contraseña.</strong>";
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

<body class="bg-primary">
  <div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
      <main>
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-5">
              <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header">
                  <h3 class="text-center font-weight-light my-4">Nuevo Usuario</h3>
                </div>
                <div class="card-body">
                  <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                      <label class="small mb-1" for="inputEmailAddress">Nombre/s</label>
                      <input class="form-control py-4" id="inputEmailAddress" type="text" name="nombre"
                        placeholder="Ingresa tu/s nombre/s" />
                    </div>
                    <div class="form-group">
                      <label class="small mb-1" for="inputEmailAddress">Apellido</label>
                      <input class="form-control py-4" id="inputEmailAddress" type="text" name="apellido"
                        placeholder="Ingresa tu apellido" />
                    </div>
                    <div class="form-group">
                      <label class="small mb-1" for="inputEmailAddress">Numero de caja</label>
                      <input class="form-control py-4" id="inputEmailAddress" type="number" name="caja"
                        placeholder="Ingresa un numero para tu caja" />
                    </div>
                    <div class="form-group">
                      <label class="small mb-1" for="inputPassword">Contraseña</label>
                      <input class="form-control py-4" id="inputPassword" type="password" name="pass1"
                        placeholder="contraseña" />
                    </div>

                    <div class="form-group">
                      <label class="small mb-1" for="inputPassword">Confirma Contraseña</label>
                      <input class="form-control py-4" id="inputPassword" type="password" name="pass2"
                        placeholder="contraseña" />
                    </div>
                    
                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                      
                      <!--button class="btn btn-primary" type="submit">Login</button-->
                      <button  type="submit" name="btn-register" id="btn-register" class="btn btn-primary">Registrar</button>
                      <div class=""></div>	
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center">
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
            <div class="text-muted">Copyright &copy; Your Website 2021</div>
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
  <script src="js/scripts.js"></script>
</body>

</html>