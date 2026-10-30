<?php
include('env.php'); session_start();
if (!isset($_SESSION['nivel'])) { header('Location: pc_login.php'); exit; }
$propietario = $_SESSION['nivel'] == 2;
$smarty = agenda_smarty('Menú principal', agenda_menu($propietario));
$smarty->assign('usuario', $_SESSION['usuario']);
$smarty->assign('propietario', $propietario);
$smarty->display('menu.html');
