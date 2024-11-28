<?php
date_default_timezone_set('America/Argentina/Salta');
session_start();
if($_SESSION['active'])
{
	$micaja = $_SESSION['nombre_caja'];
	$rol = $_SESSION['rol'];
	$numero_caja = $_SESSION['numero_caja'];
   
}


//echo file_get_contents('loteos.txt');

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Fetch</title>
</head>
<body>
  <button id="btn">Hacer una conexión con Ajax</button>
  <br><br>
  <div id='resultado'></div>
  <script>
  document.addEventListener('DOMContentLoaded', configureAjaxCalls);

  function configureAjaxCalls() 
  {
    document.getElementById('btn').addEventListener('click', function() {
      fetch('loteos.txt')
        .then(ajaxPositive)
        .catch(showError);
    });

    function ajaxPositive(response) {
      console.log('response.ok: ', response.ok);
      if(response.ok) {
        response.text().then(showResult);
      } else {
        showError('status code: ' + response.status);
      }
    }

    function showResult(txt) {
      console.log('muestro respuesta:'+'\n', txt);
      const div = document.getElementById('resultado');
      div.textContent = txt+" // FIN DEL TXT."; // <div>text</div>
      //div.textContent;  // "text"
    }

    function showError(err) { 
      console.log('muestor error', err);
    }
  }
  </script>
</body>
</html>

