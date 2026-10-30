# Reporte: Caso de uso “Modificar amigos”

**Estudiante:** Fernando Cossio  
**Práctica 4:** Aplicación Agenda en tres capas

## Introducción

La separación de una aplicación en presentación, negocio y datos permite que cada parte tenga una responsabilidad definida. En esta práctica se desarrolló el caso de uso **Modificar amigos** para una agenda. La solución permite listar contactos con su fotografía, abrir un formulario con los datos existentes, actualizarlos y reemplazar la fotografía de manera controlada.

## Flujo de acceso y estructura de tres capas

La Agenda se ajusta al diagrama de casos de uso proporcionado. Primero, `index.php` dirige al ingreso; `pc_login.php` muestra el formulario y `ps_login.php` verifica las credenciales en las tablas `permiso`, `persona` y `grupo`. Al autenticarse se crea una sesión, se asigna el nivel y se muestra `pc_menu.php`. El propietario (nivel 2) puede listar y modificar todos los contactos; el amigo (nivel 1) solo puede consultar y modificar su propio registro. `ps_logout.php` destruye la sesión.

Las credenciales corresponden a los registros cargados previamente en la base de datos de la Agenda 3C del grupo.

## Estructura de tres capas

| Capa | Archivos principales | Responsabilidad |
|---|---|---|
| Presentación | `templates/`, `pc_login.php`, `pc_menu.php` | Muestra ingreso, menú, listado, formulario, mensajes y fotografías. |
| Negocio | `personas.php`, `ps_login.php` | Controla acceso, permisos, datos, imagen y coordina la actualización. |
| Datos | `env.php`, ADOdb y PostgreSQL | Conecta y consulta las tablas `persona`, `permiso` y `grupo`. |

La página `personas.php` recibe las opciones de listado, formulario y actualización, y delega el trabajo al servicio; no contiene SQL ni lógica de carga de archivos.

## Plantilla de modificación

La vista `vistas/modificar.php` presenta los campos solicitados: nombre, teléfono, celular, correo electrónico y foto. El formulario usa `method="post"` y `enctype="multipart/form-data"`, condición necesaria para que PHP reciba el archivo. Los valores existentes se muestran previamente para que el usuario solo cambie la información necesaria.

Los controles HTML5 agregan una primera validación con `required`, `type="email"`, `pattern` e `inputmode="tel"`. También se declara `accept` en el selector de fotografía para orientar la selección hacia JPG, PNG o WEBP.

## Lógica de actualización y fotografía

`AgendaServicio` verifica que el nombre tenga una longitud válida, que teléfono y celular tengan caracteres permitidos y que el correo sea válido. Si existe un archivo, se controla lo siguiente:

1. La carga debe terminar sin errores.
2. El tamaño máximo es 3 MB.
3. El tipo MIME, obtenido con `finfo`, debe ser JPEG, PNG o WEBP.
4. `getimagesize` debe reconocer el contenido como una imagen real.
5. El archivo recibe un nombre generado con el identificador del amigo y bytes aleatorios; no se conserva el nombre proporcionado por el navegador.

Solo después de validar la información se mueve la imagen a `fotos/` y se actualiza el registro. Si la operación se completa, se elimina la foto anterior. Las consultas del repositorio usan sentencias preparadas de PDO, evitando concatenar valores de formulario dentro de SQL.

## Listado de amigos

`vistas/listar.php` obtiene los datos mediante el servicio y muestra cada contacto en una tabla. Cuando un amigo tiene fotografía se carga desde `fotos/`; en caso contrario se muestra un ícono de perfil. Cada fila incluye un enlace a su formulario de modificación.

## Ejecución y pruebas recomendadas

Desde la carpeta `fernandocossio`, se puede iniciar el servidor de desarrollo con:

```bash
php -S localhost:8000
```

Después se abre `http://localhost:8000`. Se recomienda probar datos inválidos, correos incorrectos, imágenes mayores a 3 MB, archivos con extensiones falsas y una imagen válida. Para una entrega completa, se debe repetir la revisión visual en Chrome, Firefox, Opera y Safari, y validar el HTML con W3C.

## Conclusión

La práctica evidencia que la arquitectura de tres capas facilita ampliar una Agenda sin mezclar interfaz, reglas y persistencia. La vista se enfoca en orientar al usuario, el servicio concentra la validación y el repositorio aísla el acceso a datos. El manejo de fotografías requiere una validación adicional porque el atributo `accept` no es una medida de seguridad. Al revisar tipo, contenido, tamaño y nombre de destino se reduce el riesgo de recibir archivos no deseados. En un sistema publicado, estas validaciones deben mantenerse y complementarse con permisos de directorio, copias de seguridad y controles del servidor.
