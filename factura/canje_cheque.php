<?php

	
	
require_once '../pdf/vendor/autoload.php';
use Dompdf\Dompdf;


ob_start();
include(dirname('__FILE__').'/plantilla_canje_cheque.php');
$html = ob_get_clean();
// instantiate and use the dompdf class
$dompdf = new Dompdf();

$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'portrait');
// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream('cange.pdf',array('Attachment'=>0));
//exit;
		
?>