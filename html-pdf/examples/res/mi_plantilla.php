<?php
    $num = 'CMD01-'.date('ymd');
    $nom = 'DUPONT Alphonse';
    $date = '31/12/'.date('Y');
?>
<style type="text/css">
    
/*page[size="A4"] {  
  width: 21cm;
  height: 29.7cm; 
}
page[size="A4"][layout="portrait"] {
  width: 29.7cm;
  height: 21cm;  
}*/

    div.zone { 
        border: none; 
        /*border-radius: 6mm;*/ 
        background: #FFFFFF; 
        padding-left:3mm;
        padding-right:3mm; 
        margin-left: 5px;
        font-size: 2.7mm;
        width: 95%;
    }
    h1 { padding: 0; margin: 0;  font-size: 5mm; }
     
    p, label, span strong{     
        font-size: 11pt;
        line-height:1em;
    }
    #content-firma{
        width: 100%; 
        height: 40px;
        padding-top: 45px;
        padding-left: 4px;
        overflow: hidden; 
        margin-bottom: 20px;
        border: 1px solid red;
    }
</style>


<page width:="21cm" height="29.7cm"  style="font: arial;">
    
    <div style="width: 100%; border: none;" cellspacing="4mm" cellpadding="0">
        
        <div style="height: 50%; margin-top:10px;">
            
            <table class="default">
                 
                <tr>
                    <td><img src="./res/logo1-baba.png" alt="logo"></td>
                    <td style="width: 370px; text-align: center; padding-top: 40px;">
                        <strong style="font-size: 18px;">ORDEN DE PAGO</strong>
                    </td>
                </tr>
            </table>
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green;">
                <table class="default">
                    
                    <tr>
                        <td><label for="">N° orden: </label></td>
                        <td style="width: 370px; text-align: center;">
                            <label style="">Fecha: </label>
                        </td>
                        <td><label for="">Hora: </label></td>
                    </tr>
                </table>
                <hr>
                <p>Solicitante:</p>
                <p>Emitida por:</p>
                <p>Empresa: - Obra:</p>
                <p>Cuenta:</p> 
                <p>Recibe:</p>
                <p>Detalle:</p>
                <p>Son: $49.999.999,99 (Cuarenta y nueve millones novecientos noventa y nueve mil novecientos noventa y nueve)</p>
                <div style="">
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                </div>
                
            </div>
            <div id="content-firma">
                <table>
                    
                    <tr>
                        <td style="width:270px;">
                            <label>Confeccionó</label>
                        </td>
                        <td style="width:200px;">
                            <label>Recibió (firma y aclaración)</label>
                        </td>
                        <td style="width:280px; text-align: right;">
                            <label>Autorizó</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="height: 50%; margin-top:10px;">
            
            <table class="default">
                 
                <tr>
                    <td><img src="./res/logo1-baba.png" alt="logo"></td>
                    <td style="width: 370px; text-align: center; padding-top: 40px;">
                        <strong style="font-size: 18px;">ORDEN DE PAGO</strong>
                    </td>
                </tr>
            </table>
            <div class="zone" style=" vertical-align: middle; text-align: justify; border:1px solid green;">
                
                <table class="default">
                    
                    <tr>
                        <td><label for="">N° orden: </label></td>
                        <td style="width: 370px; text-align: center;">
                            <label style="">Fecha: </label>
                        </td>
                        <td><label for="">Hora: </label></td>
                    </tr>
                </table>
                <hr>
                <p>Solicitante:</p>
                <p>Emitida por:</p>
                <p>Empresa: - Obra:</p>
                <p>Cuenta:</p> 
                <p>Recibe:</p>
                <p>Detalle:</p>
                <p>Son:</p>
                <div style="">
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                    <p>22/10/21 - Hipotecario - $99.999.999,99 - 45628107</p>
                </div>
                
            </div>
            <div id="content-firma">
                <table>
                    
                    <tr>
                        <td style="width:270px;">
                            <label>Confeccionó</label>
                        </td>
                        <td style="width:200px;">
                            <label>Recibió (firma y aclaración)</label>
                        </td>
                        <td style="width:280px; text-align: right;">
                            <label>Autorizó</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>    
    
</page>
