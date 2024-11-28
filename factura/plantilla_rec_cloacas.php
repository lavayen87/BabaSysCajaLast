<?php
date_default_timezone_set("America/Argentina/Salta");
//$cadena = "85.52999";
// (float) --> convierte cadena a numero
//$numero = (float)$cadena; 


//strval($num); // convierte un numero a cedena

    
/*function convert_two_digit(string $cadena)
{	
    $numero = " ";
	$i = 0;
	if(strlen($cadena) == 1 && $cadena <>',' && $cadena !='.')
		return (int)$cadena;
	else
	{
		while( $i <= strlen($cadena) )
        if(substr($cadena, $i,1) != '.')
        {
            $numero.=substr($cadena, $i,1);
            $i++;	
        }
        else
        {
            //$i = strlen($cadena) + 100;
            $pos = $i; // posision donde esta el '.'
            $numero.=substr($cadena, $pos,3);
            $i = strlen($cadena) + 100;	
        }	
		return $numero;
	}
}*/
//convert_two_digit(strval($float))
include('../conversor.php');
include('../funciones.php');
$fecha = date('Y-m-d');
$datos = json_decode($_GET['datos']); 

/*$precio = convert_two_digit(strval($datos->precio),3); //precio
$anticipo = convert_two_digit(strval($datos->anticipo),3);  // anticipo
$monto_afn = $datos->monto_afn; // monto_afn
$diario_fn = $datos->diario_fn; // diario_fn
$diario = convert_two_digit(strval($datos->diario),3); // diario
$n_cuotas = $datos->n_cuotas; // n_cuotas
$dias_fn = $datos->dias_fn; // dias_fn
$interes_total = round(convert_two_digit(strval($datos->interes_total),3)); // interes total
$monto_fdo = round(convert_two_digit(strval($datos->monto_fdo),3)); // monto_fdo*/
$valor_cuota = round(convert_two_digit(strval($datos[0]),3)); // valor_cuota
$total_op = round(convert_two_digit(strval($datos[1]),3)); // total operacion
$lote = $datos[2]; // opcion

//$op = $datos->op; // opcion

//echo print_r($datos);exit;

//echo " Total operacion: ".$diario_fn; 
 
$aux = 0;
$texto1 = '';
$texto2 = '';
$aux2 = 0;
$texto3 = '';
$texto4 = '';
$findme = "CERO";

// Texto total operacion
if($total_op > 0)
{
    $cantidad = '$'.number_format($total_op,2,',','.');
    $aux = $total_op;
    
    if( parte_entera(strval($aux)) <> 0)
    {	
        $texto1 = convertir(parte_entera(strval($aux))).' '."PESOS";				
        $pos = strpos($texto1, $findme);		
        if ($pos > 0)
        {
            $texto1 = str_replace($findme, "", $texto1);
        }
        
    }
    if( parte_decimal(strval($aux)) <> 0) 
    {	

        $texto1.= " CON ";		
        $texto2 = convertir(parte_decimal(strval($aux)))." CENTAVOS";
        $pos = strpos($texto2, $findme);
        if ($pos === true){
            $texto2 = str_replace($findme, "", $texto2);
        }
    }
    
}	 

// texto valor de la cuota
if($valor_cuota > 0)
{
    $cantidad2 = '$'.number_format($valor_cuota,2,',','.');
    $aux2 = $valor_cuota;
    
    if( parte_entera(strval($aux2)) <> 0)
    {	
        $texto3 = convertir(parte_entera(strval($aux2))).' '."PESOS";				
        $pos = strpos($texto3, $findme);		
        if ($pos > 0)
        {
            $texto3 = str_replace($findme, "", $texto3);
        }
        
    }
    if( parte_decimal(strval($aux2)) <> 0) 
    {	

        $texto3.= " CON ";		
        $texto4 = convertir(parte_decimal(strval($aux2)))." CENTAVOS";
        $pos = strpos($texto2, $findme);
        if ($pos === true){
            $texto4 = str_replace($findme, "", $texto4);
        }
    }
    
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div>
        <p align="justify">
        Por la presente, el/la Señor/a GOMEZ INES, coprador/a por boleto
        de compra-venta del lote Nº <strong><?php echo $lote;?></strong>, del loteo Buen Clima, reconoce
        adeudar a Buen Clima S.R.L., la suma de <strong>PESOS <?php echo "$texto1 $texto2 ($cantidad)";?></strong>
        en concepto de costo proporcional a las obras de red cloacal efecutadas en el loteo.
        Dicha suma será abomada en doce (12) cuotas mensuales, iguales y consecutivas de 
        <strong>PESOS <?php echo "$texto3 $texto4 ($cantidad2)";?></strong> cada una actualizable con el índice de la construcción (ICC), 
        con vencimiento la primera de ellas en fecha 10/01/2022 y así sucesivamente las posteriores.-- 
        </p>
    </div>
</body>
</html>

