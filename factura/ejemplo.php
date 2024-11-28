
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Demo Recibo</title>
<style>
    
    @page { 
        margin-left: 4px;
        margin-right: 4px; 
        margin-top: 2px; 
        box-sizing: border-box;
    } 
    p, label, span{
        font-family: 'BrixSansRegular';
        font-size: 11pt;
        line-height:6px;
    }
    #page {
        width: 100%; 
    }
    #header {
        height: 85px;
        background:white;
        
    }
    #left {
        width: 44%;
        float: left;
        
    }

    #right {
        width: 55%;
        float: right;
        
    }
    #content-datos {
        padding: 4px;
        clear: both;
        border-radius: 10px;
        border: 1px solid #049776;
    }
    #titulo{
        display: inline-block; 
        height: 45px; 
        padding-top: 35px;
        margin-left: 30%;
    }
    #content-cheques{
        width: 100%; 
        height: 90px;
        overflow: hidden; 
        
    }
    #content-firma{
        width: 100%; 
        height: 30px;
        padding-top: 50px;
        padding-left: 4px;
        overflow: hidden; 
        
    }
    
}
</style>
<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>

<body>
    
    <div id="page">
        
        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong>ÓRDEN DE PAGO CON CHEQUES</strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;">N°</strong>
                    <strong style="display: inline-block; margin-left: 35%;">Fecha:</strong>
                    <strong style="float: right;">Hora:</strong>
                    </p>
                </div>
                <hr>
                <p><strong>Solicitante: Luis Lavayén (caja 34)</strong></p>
                <p><strong>Emitió: Cajero1 (caja 1)</strong></p>
                <p><strong>Recibe: Corralón El amigo</strong></p>
                <p><strong>Empresa: Baba S.R.L. - Obra: Terranova</strong></p>
                <p><strong>Cuenta: Mat. Construcción</strong></p>
                <p><strong>Detalle: Pago materiales Teranova</strong></p>
                <p><strong>Son: $99.999.999,00 (noventa y nueve millones novescientos noventa y nueve mil novecientos noventa y nueve pesos)</strong></p>
                
                <div id="content-cheques" >
                    <div id="left">
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                    </div>
                    <div id="right">
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                        <p><strong>25/10/21 - Hipotecario - $99.999.999,00 - 45210078</strong></p>
                    </div>
                </div>
            </div>
            
            <div id="content-firma">
                
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <strong style="float: right;">Autorizó</strong>
                </p>
            </div>
        </div>
        
        <div style="height: 38px;"></div>
        <!--div style="height: 35px; border:1px solid red;"> <br><br> </div-->
        <img class="anulada" src="img/anulado.png" alt="Anulada">
        <div class="bloque">
            <div id="header">
                <img src="img/logo1-baba.png" style="float: left; width: 150px; height: 80px;">
                <div id="titulo"> 
                    <strong>ÓRDEN DE PAGO CON CHEQUES</strong>
                </div>
            </div>
            <div id="content-datos">

                <div style="width: 100%; height: 25px; border:1px solid transparent;">
                    <p>
                    <strong style="float: left;">N°</strong>
                    <strong style="display: inline-block; margin-left: 35%;">Fecha:</strong>
                    <strong style="float: right;">Hora:</strong>
                    </p>
                </div>
                <hr>
                <p><strong>Solicitante: Luis Lavayén (caja 34)</strong></p>
                <p><strong>Emitió: Cajero1 (caja 1)</strong></p>
                <p><strong>Recibe: Corralón El amigo</strong></p>
                <p><strong>Empresa: Baba S.R.L. - Obra: Terranova</strong></p>
                <p><strong>Cuenta: Mat. Construcción</strong></p>
                <p><strong>Detalle: Pago materiales Teranova</strong></p>
                <p><strong>Son: $99.999.999,00 (noventa y nueve millones novescientos noventa y nueve mil novecientos noventa y nueve pesos)</strong></p>
                
                
                <div id="content-cheques" >
                    <div id="left">
                        <table>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Hipotecario</td><td style="width: 80px; text-align: right;">$99.999.999,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Hipotecario</td><td style="width: 80px; text-align: right;">$99.999.999,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Hipotecario</td><td style="width: 80px; text-align: right;">$55.990,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Credicoop</td><td style="width: 80px; text-align: right;">$237.000,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        </table>
                        
                    </div>
                    <div id="right">
                        <table>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Hipotecario</td><td style="width: 80px; text-align: right;">$99.999.999,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Hipotecario</td><td style="width: 80px; text-align: right;">$99.999.999,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Hipotecario</td><td style="width: 80px; text-align: right;">$55.990,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        <tr><td style="width: 65px; text-align: left;">25/10/21</td><td style="width: 85px; text-align: left;">Credicoop</td><td style="width: 80px; text-align: right;">$237.000,00</td><td style="width: 70px; text-align: right;">45210078</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div id="content-firma">
                <p>
                <strong style="float: left;">Confeccionó</strong>
                <strong style="display: inline-block; margin-left: 35%;">Recibió (firma y aclaración)</strong>
                <strong style="float: right;">Autorizó</strong>
                </p>
            </div>
        </div>
    </div>

    
</body>
</html>
