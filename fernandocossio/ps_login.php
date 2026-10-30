<?php
include('env.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: pc_login.php'); exit; }
$usuario = trim(isset($_POST['usuario']) ? $_POST['usuario'] : '');
$clave = isset($_POST['clave']) ? $_POST['clave'] : '';
$db = ADONewConnection($dbdriver);
$db->Connect($dbserver, $dbuser, $dbpassword, $dbname);
$sql = 'SELECT p.per_cod, p.per_nom, p.per_appm, g.grup_cod FROM permiso pm '
     . 'INNER JOIN persona p ON p.per_cod = pm.per_cod '
     . 'INNER JOIN grupo g ON g.grup_cod = pm.grup_cod '
     . 'WHERE pm.per_cod = ' . $db->qstr($usuario) . ' AND pm.perm_pass = ' . $db->qstr($clave) . ' AND pm.perm_est = true';
$datos = $db->GetRow($sql);
$db->Close();
if (!$datos) { header('Location: pc_login.php?error=1'); exit; }
session_start();
session_regenerate_id(true);
$_SESSION['nivel'] = (int) $datos['grup_cod'];
$_SESSION['ci'] = trim($datos['per_cod']);
$_SESSION['usuario'] = trim($datos['per_nom']) . ' ' . trim($datos['per_appm']);
header('Location: pc_menu.php');
