<?php
/* Configuracion compatible con el servidor de la materia (Agenda 3C). */
include('/usr/share/php/adodb/adodb.inc.php');
require_once('/usr/share/php/Smarty/Smarty.class.php');

$dbdriver = 'postgres';
$dbserver = '127.0.0.1';
$dbuser = 'grupo13sc';
$dbpassword = 'grup013grup013*';
$dbname = 'db_' . $dbuser;
$path = 'https://mail.tecnoweb.org.bo/inf513/grupo13sc/practica4/fernandocossio';

function agenda_smarty($titulo, $menu)
{
    $smarty = new Smarty;
    $smarty->template_dir = __DIR__ . '/templates';
    $smarty->compile_dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR . 'smarty_agenda_grupo13sc_fernando';
    if (!is_dir($smarty->compile_dir)) @mkdir($smarty->compile_dir, 0777, true);
    $smarty->compile_check = true;
    $smarty->debugging = false;
    $smarty->assign('titulo', $titulo);
    $smarty->assign('menu', $menu);
    return $smarty;
}

function agenda_menu($propietario)
{
    $menu = array(array('nombre' => 'Personas', 'url' => 'personas.php'));
    if ($propietario) $menu[] = array('nombre' => 'Reporte', 'url' => 'reporte.html');
    $menu[] = array('nombre' => 'Salir', 'url' => 'ps_logout.php');
    return $menu;
}

function ruta_foto($codigo)
{
    $codigo = preg_replace('/[^0-9A-Za-z_-]/', '', trim($codigo));
    foreach (array('jpg', 'jpeg', 'png') as $extension) {
        if (is_file(__DIR__ . '/fotos/' . $codigo . '.' . $extension)) return 'fotos/' . $codigo . '.' . $extension;
    }
    return 'fotos/default.svg';
}
