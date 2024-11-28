<?php

	//print_r($_REQUEST);
	//exit;
	//echo base64_encode('2');
	//exit;
	/*session_start();
	if(empty($_SESSION['active']))
	{
		header('location: ../');
	}*/
	
	require_once '../pdf/vendor/autoload.php';
	use Dompdf\Dompdf;


ob_start();
include(dirname('__FILE__').'/plantilla_rec_cloacas.php'); //plantilla_tr
$html = ob_get_clean();
// instantiate and use the dompdf class
$dompdf = new Dompdf();

$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'portrait');
// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream('Reconocimiento.pdf',array('Attachment'=>0));
//exit;
		
?>