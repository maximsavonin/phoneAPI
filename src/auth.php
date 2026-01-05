<?php
require_once '../config/database.php'; // $pdo подключение к БД

class Auth {

    private function create_token($user_id) {
        $token = bin2hex(random_bytes(32)); // 64 символа
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $token, $expires]);

        return ['token' => $token, 'expires_at' => $expires];
    }

    public function register() {
        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Получаем данные
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (!$username || !$password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username and password required']);
            return;
        }


        if ($password !== $password_confirm) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        }

        global $pdo;

        // Проверяем, есть ли уже такой пользователь
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            return;
        }

        // Хэшируем пароль
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Сохраняем пользователя
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);

        $id = $pdo->lastInsertId();
        $token = $this->create_token($id);

        echo json_encode(['success' => true, 'message' => 'User registered', 'token' => $token['token'], 'expires_at' => $token['expires_at']]);
    }

    public function login() {
        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Получаем данные
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$username || !$password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username and password required']);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        global $pdo;
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $token = $this->create_token($user['id']);
            echo json_encode(['success' => true, 'message' => 'User logined', 'token' => $token['token'], 'expires_at' => $token['expires_at']]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'wrong username or password']);
        }
    }
}


