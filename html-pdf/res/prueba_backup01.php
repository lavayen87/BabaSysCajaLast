<style type="text/css">
<!--
    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}
    div.note {border: solid 1mm #DDDDDD;background-color: #EEEEEE; padding: 2mm; border-radius: 2mm; width: 100%; }
    ul.main { width: 95%; list-style-type: square; }
    ul.main li { padding-bottom: 2mm; }
    h1 { text-align: center; font-size: 20mm}
    h3 { text-align: center; font-size: 14mm}
-->
</style>
<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
    <page_header>
        <table class="page_header">
            <tr>
                <td style="width: 50%; text-align: left">
                    T&iacute;tulo...
                </td>
                <td style="width: 50%; text-align: right">
                    Html2Pdf v<?php echo $html2pdf->getVersion(); ?>
                </td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 50%; text-align: left;">
                    &copy; Baba S.R.L - <?php echo date('Y'); ?>
                </td>
                <td style="width: 50%; text-align: right">
                    p&aacute;gina [[page_cu]]/[[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>
    <h1>Error</h1>
    <div style="text-align: center; width: 100%;">
        <img src="./res/logo.png" alt="Logo Html2Pdf" style="width: 30mm">
    </div>
</page>
<page pageset="old">
    <div class="note">
        Hola mundo<br>
    </div>
</page>