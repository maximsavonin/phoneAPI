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
        break;
    case 'register':
        (new Auth())->register();
        break;
    case 'listUsers':
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            (new listUsers())->getUsers();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {// должен быть patch
            (new listUsers())->giveAccess();
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Invalid endpoint']);
}