<?php
include('env.php');
session_start();
if (isset($_SESSION['nivel'])) { header('Location: pc_menu.php'); exit; }
$smarty = agenda_smarty('Agenda personal · Tres capas', array(array('nombre' => 'Ingresar', 'url' => 'pc_login.php')));
$smarty->display('inicio.html');
