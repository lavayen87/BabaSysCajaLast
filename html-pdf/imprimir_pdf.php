<?php header("Content-Type: text/html;charset=utf-8"); ?>
<?php

include "../php/conexion.php";

// Control de acceso adaptado para la impresion de PDF

// Comprueba que no esté activo el Bloqueo general y trae duracion de la sesión
$sql11 = "SELECT cof_duracion_sesion_segundos, cof_sistema_bloqueado FROM cof_configuracion";
$sql11 .= " WHERE cof_id=1"; // Único registro
$query11 = $con->query($sql11);
while ($r11=$query11->fetch_array()) {
    $cof_sistema_bloqueado = $r11['cof_sistema_bloqueado'];
    $cof_duracion_sesion_segundos = $r11['cof_duracion_sesion_segundos'];
    break;
}
if($cof_sistema_bloqueado) {
    session_start();
	$_SESSION[$g_nombre_ussuario_id_session] = null;
    print "<script>alert(\"Sistema bloqueado momentaneamente.\");window.location='../index.php';</script>";
    exit();
}

ini_set("session.cookie_lifetime",$cof_duracion_sesion_segundos);
ini_set("session.gc_maxlifetime",$cof_duracion_sesion_segundos); 
session_cache_expire($cof_duracion_sesion_segundos);
session_start();
session_regenerate_id(true);

ob_start();

$permisosDeAccion_array = array();
$paginaActual = $_POST['entrada'];

if (!isset($_SESSION[$g_nombre_ussuario_id_session]) OR $_SESSION[$g_nombre_ussuario_id_session]==null) {
	print "<script>alert(\"Acceso invalido! Su sesión expiró.\");window.location='../index.php';</script>";
    exit();
} else {
    $tienePermiso = false;
    $sql3 = "SELECT * FROM tpu_tipos_por_usuario";
    $sql3 .= " WHERE usu_id = " . $_SESSION[$g_nombre_ussuario_id_session];
    $query3 = $con->query($sql3);
    while ($r3=$query3->fetch_array()) {
        $usuario_tipo = $r3["tus_id"];
        // Control de acceso a página
        $sql2 = "SELECT * FROM per_permisos";
        $sql2 .= " LEFT JOIN pag_paginas ON per_permisos.pag_id = pag_paginas.pag_id";
        $sql2 .= " WHERE per_permisos.tus_id = " . $usuario_tipo;
        $sql2 .= " AND pag_paginas.pag_url = '" . $paginaActual . "'";
        $query2 = $con->query($sql2);
        while ($r2=$query2->fetch_array()) {
            $tienePermiso = true;
            break;
        }
        if ($tienePermiso) {
            break;
        }
    }

    if (!$tienePermiso) {
        print "<script>alert(\"Acceso invalido! No tiene permiso.\");window.location='../inicio.php';</script>";
        exit();
    } else {

        $usuario_id = null;
        $sql1 = "SELECT * FROM usu_usuarios";
        $sql1 .= " WHERE usu_id = " . $_SESSION[$g_nombre_ussuario_id_session];
        $sql1 .= " AND usu_inhabilitado = false";
        $query = $con->query($sql1);
        while ($r=$query->fetch_array()) {
            $usuario_id = $r["usu_id"];
            $g_usuario_nombre = $r["usu_nombre_completo"];
            $g_usuario_loteo = $r["los_id"];
            $g_usuario_vendedor = $r["ven_id"];
            $g_usuario_caja = $r["caj_id"];
            break;
        }


        if ($usuario_id==null) {
            $_SESSION[$g_nombre_ussuario_id_session] = null;
            print "<script>alert(\"Acceso invalido! Usuario inhabilitado.\");window.location='../index.php';</script>";
            exit();
        }
    }
}
//-------------------

// VALORES POR DEFECTO Y PARAMETROS POR POST
$orientacion = 'P';
$tamano = 'A4';
$archivo_entrada = '/res/error.php';
$archivo_salida = 'archivo.pdf';

// nombre de archivo html - Requerido
if (isset($_POST['entrada']) AND $_POST['entrada']<>'') {
    $archivo_entrada = $_POST['entrada'];
}
// nombre de archivo pdf
if (isset($_POST['salida']) AND $_POST['salida']<>'') {
    $archivo_salida = $_POST['salida'];
}
if (isset($_POST['orientacion']) AND $_POST['orientacion']<>'') {
    $orientacion = $_POST['orientacion'];
}
if (isset($_POST['tamano']) AND $_POST['tamano']<>'') {
    $tamano = $_POST['tamano'];
}
?>
<?php
/**
 * Html2Pdf Library - example
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */
require_once dirname(__FILE__).'/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
    $html2pdf = new Html2Pdf($orientacion, $tamano, 'es', true, 'UTF-8', array(0, 0, 0, 0));
    $html2pdf->pdf->SetDisplayMode('fullpage');

    $url = realpath('./../') . '/' . $archivo_entrada;
    include $url;
    $content = ob_get_clean();

    // Elimina el BOM
    $bom = pack("CCC", 0xEF, 0xBB, 0xBF);
	$content = str_replace($bom, '', $content);

    $html2pdf->writeHTML($content);
    $html2pdf->output($archivo_salida);
} catch (Html2PdfException $e) {
    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
}
