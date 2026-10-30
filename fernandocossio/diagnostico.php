<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

$resultado = array();
$resultado['PHP'] = PHP_VERSION;
$resultado['Plantillas'] = is_dir(__DIR__ . '/templates') && is_file(__DIR__ . '/templates/inicio.html') ? 'Disponible' : 'No encontrada';
$temporal = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'smarty_agenda_grupo13sc_fernando';
if (!is_dir($temporal)) @mkdir($temporal, 0777, true);
$prueba = @tempnam($temporal, 'prueba_');
$resultado['Escritura temporal'] = $prueba ? 'Disponible' : 'Sin permisos';
if ($prueba) @unlink($prueba);
@require_once('/usr/share/php/Smarty/Smarty.class.php');
$resultado['Smarty'] = class_exists('Smarty') ? 'Disponible' : 'No disponible';
?><!doctype html><html lang="es"><meta charset="utf-8"><title>Diagnóstico Agenda</title><body><h1>Diagnóstico Agenda 3C</h1><ul><?php foreach ($resultado as $clave => $valor) { echo '<li><strong>' . htmlspecialchars($clave) . ':</strong> ' . htmlspecialchars($valor) . '</li>'; } ?></ul><p>Elimine este archivo después de terminar la prueba.</p></body></html>
