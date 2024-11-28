
<?php 

session_start();
if($_SESSION['active'])
{
	$numero_caja = $_SESSION['numero_caja'];
	/*switch ($numero_caja) 
	{
		case 0: echo '0'; break;
		case 1: echo '1'; break;				
		case 2: echo '2'; break;
		case 3: echo '3'; break;
		case 4: echo '4'; break;
		case 5: echo '5'; break;
		case 6: echo '6'; break;
		case 7: echo '7'; break;
		case 8: echo '8'; break;
		case 9: echo '9'; break;	
		case 10: echo '10'; break;
		case 11: echo '11'; break;
		case 12: echo '12'; break;	
	}*/		
	
	echo "$numero_caja";
}
else echo 'usuario inactivo';

?>
