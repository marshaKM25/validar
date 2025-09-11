<?php
session_start();
include_once 'funcs/funcs.php';

// Configuración de seguridad
$maxIntentos = 3;             // Intentos por captcha
$tiempoBloqueoSeg = 300;      // Bloqueo temporal (5 min)
$tiempoExpiracionCaptcha = 120; // Expiración 2 min

// Captura datos del formulario
$nombre = $_POST['nombre'] ?? '';
$codigo = $_POST['codigo'] ?? '';
$formID = $_POST['form_id'] ?? 'default';

// Validar campos vacíos
if (empty($nombre) || empty($codigo)) {
    setFlashData('error','Debe llenar todos los datos');
    redirect('index.php');
}

// Generar fingerprint del navegador
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . session_id());

// Inicializa intentos por fingerprint si no existe
if(!isset($_SESSION['intentos_por_fingerprint'])){
    $_SESSION['intentos_por_fingerprint'] = [];
}
                    //todos los usuarios               //ya especifico //que quiero guardar
$intentosUsuario = $_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] ?? 0;
$ultimoIntento = $_SESSION['intentos_por_fingerprint'][$fingerprint]['last_attempt'] ?? 0;

// Verificar bloqueo temporal
if($intentosUsuario >= $maxIntentos){
    $tiempoRestante = $tiempoBloqueoSeg - (time() - $ultimoIntento);
    if($tiempoRestante > 0){
        setFlashData('error','Has excedido el número máximo de intentos. Intenta de nuevo en ' . ceil($tiempoRestante) . ' segundos.');
        redirect('index.php');
    } else {
        $_SESSION['intentos_por_fingerprint'][$fingerprint] = ['count' => 0, 'last_attempt' => 0];
        $intentosUsuario = 0;
    }
}

// Recuperar captcha de la sesión
$captchaData = $_SESSION['captcha'][$formID] ?? null;

if(!$captchaData){
    setFlashData('error','Captcha no encontrado o expirado.');
    redirect('index.php');
}

// Verificar expiración
if(time() - $captchaData['time'] > $tiempoExpiracionCaptcha){
    //se borrra todo lo de captcha
    unset($_SESSION['captcha'][$formID]);
    //se restablece todo a 0
    $_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] = 0;
    //mensaje de error 
    setFlashData('error','El captcha ha expirado. Recarga la página.');
    redirect('index.php');
}

// Comparación segura
if(!hash_equals($captchaData['hash'], hash('sha256', $codigo))){
    $_SESSION['intentos_por_fingerprint'][$fingerprint] = [
        'count' => $intentosUsuario + 1,
        'last_attempt' => time()
    ];
    unset($_SESSION['captcha'][$formID]); // Un solo uso
    sleep(2); // Retardo anti-bots
    setFlashData('error','Código de verificación incorrecto. Intento #' . ($_SESSION['intentos_por_fingerprint'][$fingerprint]['count']));
    redirect('index.php');
}

// Correcto
unset($_SESSION['captcha'][$formID]); // Un solo uso
$_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] = 0;

echo "Bienvenido, $nombre";
?>

