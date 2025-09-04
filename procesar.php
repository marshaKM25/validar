<?php
/**
 * Procesar CAPTCHA seguro con fingerprint
 * - SHA-256 para hash
 * - hash_equals() para comparación segura
 * - Máximo de intentos por usuario
 * - Bloqueo temporal individual
 * - Captcha de un solo uso
 */

session_start();
include_once 'funcs/funcs.php';

// Configuración
$maxIntentos = 3;                  // Intentos por captcha
$tiempoBloqueoSeg = 300;           // Bloqueo temporal (5 min)
$tiempoExpiracionCaptcha = 120;    // Expiración del captcha

// Captura datos del formulario
$nombre = $_POST['nombre'] ?? '';
$codigo = $_POST['codigo'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

if(empty($nombre) || empty($codigo) || empty($contraseña)){
    setFlashData('error','Debe llenar todos los datos');
    redirect('index.php');
}

// Generar fingerprint del navegador
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . session_id());

// Inicializa intentos por fingerprint si no existe
if(!isset($_SESSION['intentos_por_fingerprint'])){
    $_SESSION['intentos_por_fingerprint'] = [];
}

$intentosUsuario = $_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] ?? 0;
$ultimoIntento = $_SESSION['intentos_por_fingerprint'][$fingerprint]['last_attempt'] ?? 0;

// Verificar bloqueo temporal
if($intentosUsuario >= $maxIntentos){
    $tiempoRestante = $tiempoBloqueoSeg - (time() - $ultimoIntento);
    if($tiempoRestante > 0){
        setFlashData('error','Has excedido el número máximo de intentos. Intenta de nuevo en ' . ceil($tiempoRestante) . ' segundos.');
        redirect('bloqueado.php');
    } else {
        // Reiniciar contador después del bloqueo
        $_SESSION['intentos_por_fingerprint'][$fingerprint] = ['count' => 0, 'last_attempt' => 0];
        $intentosUsuario = 0;
    }
}

// Recuperar captcha y verificar expiración
$captchaHash = $_SESSION['codigo_verificacion'] ?? '';
$captchaIngresado = hash('sha256', $codigo);

if(isset($_SESSION['captcha_time']) && time() - $_SESSION['captcha_time'] > $tiempoExpiracionCaptcha){
    //Reinicio de las variables
    unset($_SESSION['codigo_verificacion']);
    $_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] = 0;
    setFlashData('error','El captcha ha expirado. Recarga la página.');
    redirect('index.php');
}

// Comparación segura
if(!hash_equals($captchaHash, $captchaIngresado)){
    // Registrar intento
    $_SESSION['intentos_por_fingerprint'][$fingerprint] = [
        'count' => $intentosUsuario + 1,
        'last_attempt' => time()
    ];
    unset($_SESSION['codigo_verificacion']); // Captcha de un solo uso
    sleep(2); // Retardo para ataques automáticos
    setFlashData('error','Código de verificación incorrecto. Intento #' . ($_SESSION['intentos_por_fingerprint'][$fingerprint]['count']));
    redirect('index.php');
}

// Captcha correcto
unset($_SESSION['codigo_verificacion']);                  // Borra captcha usado
$_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] = 0; // Reinicia contador
echo "Bienvenido, $nombre";




//php -S localhost:8000
