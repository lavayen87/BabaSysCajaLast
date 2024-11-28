<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>prueba select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet"  href="fontawesome-free-5.15.2-web/css/all.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <link rel="stylesheet" href="css/styles-update.css">

    <!--link rel="stylesheet" href="chosen/chosen.css"-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Bootstrap core CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="js/jquery-3.5.1.min.js"></script> 
    <link rel="stylesheet" href="chosen_v1.8.5/chosen.css">
    <script src="chosen_v1.8.5/chosen.jquery.js" type="text/javascript"></script>
     
    <script>
        $(window).ready(function(){
            $(".chosen").chosen();
            $(".chosen3").chosen({max_selected_options: 3});
        })
    </script>
    <style>
            @import url("//harvesthq.github.io/chosen/chosen.css");
        body {
        font-family: sans-serif;
        font-size: 16px;
        line-height: 1.5;
        background: #eee;
        }


    </style>
</head>
<body>
<p>Con Chosen, desactivada la opción de búsqueda y activada la opción de deselección</p>
<div class="container">
    <select name="chosen-nosearch" class="form-select chosen" data-placeholder="Elige un color">
        <option value=""></option>
        <option value="azul">Azul</option>
        <option value="amarillo">Amarillo</option>
        <option value="blanco">Blanco</option>
        <option value="gris">Gris</option>
        <option value="marron">Marrón</option>
        <option value="naranja">Naranja</option>
        <option value="negro">Negro</option>
        <option value="rojo">Rojo</option>
        <option value="verde">Verde</option>
        <option value="violeta">Violeta</option>
    </select>

    <br>
    <br>
    <!-- selección múltiple -->
 
    <select name="miselect[]" class="form-select chosen3" data-placeholder="Elige tus colores favoritos" multiple>
        <option value="azul">Azul</option>
        <option value="amarillo">Amarillo</option>
        <option value="blanco">Blanco</option>
        <option value="gris">Gris</option>
        <option value="marron">Marrón</option>
        <option value="naranja">Naranja</option>
        <option value="negro">Negro</option>
        <option value="rojo">Rojo</option>
        <option value="verde">Verde</option>
        <option value="violeta">Violeta</option>
    </select>

</div>
    
</body>
</html>