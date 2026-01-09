<?php
require_once __DIR__ . '/../config/database.php';

class Auth {

    private function create_token($user_id) {
        $token = bin2hex(random_bytes(32)); // 64 символа
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $token, $expires]);

        return ['token' => $token, 'expires_at' => $expires];
    }

    public function check_token($token)
    {
        if (!$token) return -1;
        global $pdo;

        $stmt = $pdo->prepare("SELECT id, user_id, created_at, expires_at FROM tokens WHERE token = ?");
        $stmt->execute([$token]);
        $row = $stmt->fetch();

        if (!$row) return -1;

        $now = date('Y-m-d H:i:s');
        $expired = $row['expires_at'] < $now;
        $old = $row['created_at'] < date('Y-m-d H:i:s', strtotime('-30 days'));

        if ($expired || $old) {
            $stmt = $pdo->prepare("DELETE FROM tokens WHERE id = ?");
            $stmt->execute([$row['id']]);
            return -1;
        }

        $expires = date('Y-m-d H:i:s', strtotime('+7 days'));

        $stmt = $pdo->prepare("UPDATE tokens SET expires_at = ? WHERE id = ?");
        $stmt->execute([$expires, $row['id']]);

        return $row['user_id'];
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
            return;
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
            echo json_encode(['success' => true, 'message' => 'User logged in', 'token' => $token['token'], 'expires_at' => $token['expires_at']]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'wrong username or password']);
        }
    }
}


