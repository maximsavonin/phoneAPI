<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/listUsers.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');

$segments = explode('/', $path);

switch ($segments[0]) {
    case 'login':
        (new Auth())->login();
        exit;
    case 'register':
        (new Auth())->register();
        exit;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = null;

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

$user_id = (new Auth())->check_token($token);
if ($user_id === -1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

switch ($segments[0]) {
    case 'users':
        switch ($segments[1]) {
            case 'list':
                (new ListUsers())->getUsers($user_id);
                exit;
            case 'access':
                (new ListUsers())->giveAccess($user_id);
                exit;
            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Invalid endpoint']);
        }
        exit;

    case 'getBooks':
        switch ($segments[1]) {
            case 'user':
                (new Book())->getBooksUser($user_id);
                exit;
            case 'otherUser':
                (new Book())->getBooksOtherUser($user_id);
                exit;
            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Invalid endpoint']);
        }
        exit;

    case 'book':
        switch ($segments[1]) {
            case 'create':
                (new Book())->createBook($user_id);
                exit;
            case 'get':
                (new Book())->getBook();
                exit;
            case 'update':
                (new Book())->updateBook();
                exit;
            case 'delete':
                (new Book())->deleteBook();
                exit;

            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Invalid endpoint']);
        }
        exit;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Invalid endpoint']);
}