<?php
include('env.php'); session_start();
if (!isset($_SESSION['nivel'])) { header('Location: pc_login.php'); exit; }

function mostrar_mensaje($titulo, $texto)
{
    $smarty = agenda_smarty($titulo, agenda_menu($_SESSION['nivel'] == 2));
    $smarty->assign('usuario', $_SESSION['usuario']);
    $smarty->assign('mensaje', $texto);
    $smarty->display('mensaje.html'); exit;
}
function puede_editar($codigo) { return $_SESSION['nivel'] == 2 || trim($_SESSION['ci']) === trim($codigo); }
function obtener_persona($db, $codigo) { return $db->GetRow('SELECT * FROM persona WHERE per_cod = ' . $db->qstr($codigo)); }

$opcion = isset($_REQUEST['opcion']) ? (int) $_REQUEST['opcion'] : 0;
$db = ADONewConnection($dbdriver);
$db->Connect($dbserver, $dbuser, $dbpassword, $dbname);

if ($opcion === 3) {
    $codigo = isset($_GET['per_cod']) ? trim($_GET['per_cod']) : '';
    if (!puede_editar($codigo)) { $db->Close(); mostrar_mensaje('Acceso denegado', 'No tiene permiso para modificar este amigo.'); }
    $persona = obtener_persona($db, $codigo); $db->Close();
    if (!$persona) mostrar_mensaje('Amigo no encontrado', 'El registro solicitado no existe.');
    $smarty = agenda_smarty('Modificar amigo', agenda_menu($_SESSION['nivel'] == 2));
    $smarty->assign('usuario', $_SESSION['usuario']); $smarty->assign('persona', $persona);
    $smarty->assign('foto', ruta_foto($persona['per_cod'])); $smarty->assign('errores', array());
    $smarty->display('personas-modificar.html'); exit;
}

if ($opcion === 30 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim(isset($_POST['cod']) ? $_POST['cod'] : '');
    if (!puede_editar($codigo)) { $db->Close(); mostrar_mensaje('Acceso denegado', 'No tiene permiso para actualizar este amigo.'); }
    $persona = obtener_persona($db, $codigo);
    $nombre = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
    $telefono = trim(isset($_POST['tel']) ? $_POST['tel'] : '');
    $celular = trim(isset($_POST['cel']) ? $_POST['cel'] : '');
    $correo = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $errores = array();
    if (!$persona) $errores[] = 'El amigo solicitado no existe.';
    if (strlen($nombre) < 3 || strlen($nombre) > 150) $errores[] = 'El nombre debe tener entre 3 y 150 caracteres.';
    if (!preg_match('/^[0-9 +()\-]{7,17}$/', $telefono)) $errores[] = 'El teléfono no tiene un formato válido.';
    if (!preg_match('/^[0-9 +()\-]{7,17}$/', $celular)) $errores[] = 'El celular no tiene un formato válido.';
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo electrónico no es válido.';
    $subirFoto = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
    $extension = null;
    if ($subirFoto) {
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) $errores[] = 'La carga de la fotografía no se completó.';
        elseif ($_FILES['foto']['size'] > 3 * 1024 * 1024) $errores[] = 'La fotografía no debe superar 3 MB.';
        else {
            $imagen = @getimagesize($_FILES['foto']['tmp_name']);
            $permitidos = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png');
            if (!$imagen || !isset($permitidos[$imagen[2]])) $errores[] = 'La fotografía debe ser una imagen JPG o PNG válida.';
            else $extension = $permitidos[$imagen[2]];
        }
    }
    if ($errores) {
        $db->Close(); $persona['per_nom'] = $nombre; $persona['per_telf'] = $telefono; $persona['per_cel'] = $celular; $persona['per_email'] = $correo;
        $smarty = agenda_smarty('Modificar amigo', agenda_menu($_SESSION['nivel'] == 2));
        $smarty->assign('usuario', $_SESSION['usuario']); $smarty->assign('persona', $persona); $smarty->assign('foto', ruta_foto($codigo)); $smarty->assign('errores', $errores);
        $smarty->display('personas-modificar.html'); exit;
    }
    $sql = 'UPDATE persona SET per_nom = ' . $db->qstr($nombre) . ', per_telf = ' . $db->qstr($telefono)
         . ', per_cel = ' . $db->qstr($celular) . ', per_email = ' . $db->qstr($correo) . ', per_update = now() WHERE per_cod = ' . $db->qstr($codigo);
    $ok = $db->Execute($sql);
    if ($ok && $subirFoto) {
        $carpeta = __DIR__ . '/fotos'; if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
        foreach (array('jpg', 'jpeg', 'png') as $vieja) { $archivo = $carpeta . '/' . preg_replace('/[^0-9A-Za-z_-]/', '', $codigo) . '.' . $vieja; if (is_file($archivo)) unlink($archivo); }
        $destino = $carpeta . '/' . preg_replace('/[^0-9A-Za-z_-]/', '', $codigo) . '.' . $extension;
        $ok = move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
    }
    $db->Close();
    if (!$ok) mostrar_mensaje('Error de actualización', 'No fue posible guardar los cambios.');
    header('Location: personas.php?mensaje=actualizado'); exit;
}

$sql = $_SESSION['nivel'] == 2 ? 'SELECT * FROM persona ORDER BY per_appm, per_nom'
    : 'SELECT * FROM persona WHERE per_cod = ' . $db->qstr($_SESSION['ci']);
$amigos = $db->GetArray($sql); $db->Close();
foreach ($amigos as $posicion => $amigo) $amigos[$posicion]['foto'] = ruta_foto($amigo['per_cod']);
$smarty = agenda_smarty('Listado de amigos', agenda_menu($_SESSION['nivel'] == 2));
$smarty->assign('usuario', $_SESSION['usuario']); $smarty->assign('amigos', $amigos);
$smarty->assign('propietario', $_SESSION['nivel'] == 2); $smarty->assign('mensaje_ok', isset($_GET['mensaje']));
$smarty->display('personas-listar.html');
