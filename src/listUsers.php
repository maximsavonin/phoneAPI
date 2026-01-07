<?php
require_once '../config/database.php'; // $pdo подключение к БД

class listUsers
{

    public function getUsers()
    {
        global $pdo;

        $stmt = $pdo->prepare("SELECT id, username FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll();
        echo json_encode(['success' => true, 'message' => '', 'list' => $users]);
    }

    public function giveAccess()
    {
        $owner_id = $_POST['owner_id'] ?? '';
        $user_id = $_POST['user_id'] ?? '';

        global $pdo;

        $stmt = $pdo->prepare("SELECT id FROM users WHERE id ?");

        $stmt->execute([$owner_id]);
        $user = $stmt->fetch();
        if (!in_array($user, $owner_id)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User does not exist']);
            return;
        }

        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        if (!in_array($user, $user_id)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User does not exist']);
            return;
        }

        // я бы добавил условие, если существует такой доступ то он удаляется или дополнительный метод, но это не выяснили при просмотре тз
        $stmt = $pdo->prepare("SELECT id FROM access WHERE owner_id = ? and user_id = ?");
        $stmt->execute([$owner_id, $user_id]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'access already exists']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO access (owner_id, user_id) VALUES (?, ?)");
            $stmt->execute([$owner_id, $user_id]);
            echo json_encode(['success' => true, 'message' => 'access has been granted']);
        }
    }
}