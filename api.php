<?php
// Configuración básica
header('Content-Type: application/json');

// --- 0. Simulación de Usuarios (Requerido) ---
$usuarios = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

// Función simple para generar un token (usando base64_encode como se indica)
function generateToken($user) {
    // Genera un token simple con el nombre de usuario y un timestamp
    $payload = json_encode(['user' => $user['username'], 'iat' => time()]);
    return base64_encode($payload);
}

// Función simple para verificar y decodificar el token
function decodeToken($token) {
    // Intenta decodificar el payload
    $payload = base64_decode($token);
    $data = json_decode($payload, true);
    
    // Simplemente verificamos que el token tenga la estructura básica
    if ($data && isset($data['user']) && isset($data['iat'])) {
        return $data; // Retorna los datos del usuario dentro del token
    }
    return null;
}

// Obtiene la ruta de la solicitud
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// RUTA 1: POST /api/login - Autenticación
if ($method === 'POST' && preg_match('/\/api\/login$/', $request_uri)) {
    // Obtener las credenciales del cuerpo de la solicitud (JSON)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    $authenticated = false;
    $user_data = null;

    // Validar credenciales contra el array predefinido
    foreach ($usuarios as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $authenticated = true;
            $user_data = $user;
            break;
        }
    }

    if ($authenticated) {
        $token = generateToken($user_data);
        http_response_code(200); // OK
        echo json_encode([
            'message' => 'Autenticación exitosa. Token generado.',
            'token' => $token,
            'username' => $user_data['username']
        ]);
    } else {
        http_response_code(401); // Unauthorized (Requerido)
        echo json_encode(['error' => 'Credenciales incorrectas']);
    }
    exit;
}

// RUTA 2: GET /api/welcome - Endpoint Protegido

if ($method === 'GET' && preg_match('/\/api\/welcome$/', $request_uri)) {
    
    // Obtener el token de la cabecera (Authorization: Bearer <token>) (Requerido)
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    // Extraer el token de la cabecera
    if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
        $token = $matches[1];
        $decoded_token = decodeToken($token);

        if ($decoded_token) {
            // El token es válido, enviamos la información
            http_response_code(200); // OK
            echo json_encode([
                'message' => '¡Bienvenido al sistema!',
                'username' => $decoded_token['user'],
                'current_time' => date('H:i:s'), // Muestra la hora actual (Requerido)
                'additional_welcome' => 'Tus permisos han sido verificados.'
            ]);
            exit;
        }
    }
    
    // Si no hay token o es inválido, respondemos 403 Forbidden (Requerido)
    http_response_code(403); 
    echo json_encode(['error' => 'Acceso denegado. Token inválido o no proporcionado.']);
    exit;
}

// Si la ruta no coincide
http_response_code(404);
echo json_encode(['error' => 'Ruta no encontrada']);
?>
