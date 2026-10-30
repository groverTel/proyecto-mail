<?php
include('env.php');
session_start();
if (isset($_SESSION['nivel'])) { header('Location: pc_menu.php'); exit; }
$smarty = agenda_smarty('Ingreso a la Agenda', array(array('nombre' => 'Inicio', 'url' => 'index.php')));
$smarty->assign('error', isset($_GET['error']));
$smarty->display('login.html');
