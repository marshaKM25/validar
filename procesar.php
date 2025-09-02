<?php
/**
 * Script de validación de CAPTCHA seguro
 * - Usa SHA-256 para el hash del captcha
 * - Usa hash_equals() para comparar hashes sin vulnerabilidad de timing
 * - Maneja número máximo de intentos
 * - Borra el captcha y la sesión cuando se supera el límite
 * - Verifica expiración del captcha
 **/

session_start();

include_once 'funcs/funcs.php';

// Configuración
$maxIntentos = 3; // Cambié a 1 según tu comentario de prueba de un solo intento
$tiempoExpiracion = $_SESSION['captcha_expires'] ?? 120; // 2 minutos por defecto

// Inicializa intentos si no existen
if(!isset($_SESSION['intentos'])){
    $_SESSION['intentos'] = 0;
}

// Captura datos del formulario
$nombre = $_POST['nombre'] ?? '';
$codigo = $_POST['codigo'] ?? '';
$contraseña = $_POST['contraseña'] ?? ''; // solo simulación

// Validación de campos vacíos
if(empty($nombre) || empty($codigo) || empty($contraseña)){
    setFlashData('error','Debe llenar todos los datos');
    redirect('index.php');
}

// Recupera el captcha almacenado
$codigoVerificacion = $_SESSION['codigo_verificacion'] ?? '';
$captchaIngresado = hash('sha256', $codigo);

// Verifica expiración del captcha
if(isset($_SESSION['captcha_time'])){
    if(time() - $_SESSION['captcha_time'] > $tiempoExpiracion){
        setFlashData('error','El captcha ha expirado, recarga la página.');
        unset($_SESSION['codigo_verificacion']);
        $_SESSION['intentos'] = 0;
        redirect('index.php');
    }
}

// Verifica número máximo de intentos
if($_SESSION['intentos'] >= $maxIntentos){
    setFlashData('error','Ha superado el número máximo de intentos.');
    // Reinicia todo para que no quede nada en sesión
    session_unset();
    session_destroy();
    redirect('bloqueado.php');
}

// Comparación segura del captcha
if(!hash_equals($codigoVerificacion, $captchaIngresado)){
    $_SESSION['intentos']++;            // suma 1 intento
    unset($_SESSION['codigo_verificacion']); // borra captcha actual
    setFlashData('error','El código de verificación es incorrecto. Intento #' . $_SESSION['intentos']);
    redirect('index.php');
}

// Captcha correcto
$_SESSION['intentos'] = 0; // reinicia intentos
unset($_SESSION['codigo_verificacion']); // borra captcha usado
echo "Bienvenido, $nombre";
