<!DOCTYPE html>
<html>
 <head>
 <title>Formulario con Ajax</title>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

 </head>
 <body>
    <div id="formulario">
        <form name="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
            <label>Numero: </label>
            <input type="number" name="num" id="nombre">
            <input type="submit" name="btn-num" value="Enviar">
        </form>
        <br>
        <?php

            function genera_num(int $num)
            {

                switch($num)
                {
                    case ($num<10):
                        $num_recibo = '0000000'.$num;
                        break;
                    case ($num>=10 && $num<100):
                        $num_recibo = '000000'.$num;
                        break;
                    case ($num>=100 && $num<1000):
                        $num_recibo = '00000'.$num;
                        break;
                    case ($num>=1000 && $num<10000):
                        $num_recibo = '0000'.$num;
                        break;
                    case ($num>=10000 && $num<100000):
                        $num_recibo = '000'.$num;
                        break;
                    case ($num>=100000 && $num<1000000):
                        $num_recibo = '00'.$num;
                        break;
                }               

                return $num_recibo;
            }

            $num=0;
            if(isset($_POST['btn-num']))
            {
                if( ($_POST['num'] > 0) && ($_POST['num']<> "") )
                {    
                    $num = $_POST['num'];
                    
                    echo "Numero generado: ".genera_num($num);
                }
                else
                {
                    echo "Debe ingresa un numero mayor a cero.";
                }
            }              
        ?>
    </div>
 </body>
</html>