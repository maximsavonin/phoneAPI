<?php
require_once __DIR__ . '/../config/database.php';

class ListUsers
{

    public function getUsers($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        global $pdo;

        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
        $stmt->execute([$user_id]);
        $users = $stmt->fetchAll();
        echo json_encode(['success' => true, 'message' => '', 'users' => $users]);
    }

    public function giveAccess($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $other_user_id = $_POST['user_id'] ?? '';

        global $pdo;

        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");

        $stmt->execute([$other_user_id]);
        $users = $stmt->fetch();
        if (!in_array($users, $other_user_id)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User does not exist']);
            return;
        }

        // я бы добавил условие, если существует такой доступ то он удаляется или дополнительный метод, но это не выяснили при просмотре тз
        $stmt = $pdo->prepare("SELECT id FROM access WHERE owner_id = ? and user_id = ?");
        $stmt->execute([$user_id, $other_user_id]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'access already exists']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO access (owner_id, user_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $other_user_id]);
            echo json_encode(['success' => true, 'message' => 'access has been granted']);
        }
    }
}