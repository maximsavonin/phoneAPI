<?php
require_once __DIR__ . '/../config/database.php';

class Book
{
    public function getBooksUser($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        global $pdo;

        $stmt = $pdo->prepare("SELECT id, title FROM books WHERE owner_id = ? AND deleted = null");
        $stmt->execute([$user_id]);
        $books = $stmt->fetchAll();
        echo json_encode(['success' => true, 'message' => '', 'books' => $books]);
    }

    public function createBook($user_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $book_title = $_POST['book_title'] ?? '';
        $text = $_POST['text'] ?? '';
        $file = $_FILES['file'] ?? null;

        if (!$book_title) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Book title is required']);
            return;
        }

        if (!$text) {
            if ($file && isset($file['tmp_name'])) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'error uploading file']);
                    return;
                }

                if ($file['size'] > 30 * 1024 * 1024) {
                    http_response_code(413);
                    echo json_encode(['success' => false, 'message' => 'file is too large (max 30 MB)']);
                    return;
                }

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($ext !== 'txt') {
                    http_response_code(415);
                    echo json_encode(['success' => false, 'message' => 'file extension not allowed']);
                    return;
                }

                $content = file_get_contents($file['tmp_name']);
                if ($content === false) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'error reading file']);
                    return;
                }

                // Проверяем кодировку
                if (!mb_check_encoding($content, 'UTF-8')) {
                    http_response_code(415);
                    echo json_encode(['success' => false, 'message' => 'file must be in UTF-8']);
                    return;
                }

                $text = $content;
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'text or file not transferred']);
                return;
            }
        }

        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO books (title, text, owner_id) VALUES (?, ?, ?)");
        $stmt->execute([$book_title, $text, $user_id]);

        echo json_encode(['success' => true, 'message' => 'book created']);
    }

    public function getBook() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $book_id = $_GET['book_id'] ?? '';

        if (!$book_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'book id is required']);
            return;
        }

        global $pdo;
        $stmt = $pdo->prepare("SELECT title, text FROM books WHERE id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();
        if (!$book) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'book not found']);
            return;
        }
        echo json_encode(['success' => true, 'message' => '', 'book' => $book]);
    }

    public function updateBook() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $book_id = $_POST['book_id'] ?? '';
        $book_title = $_POST['book_title'] ?? '';
        $text = $_POST['text'] ?? '';

        if (!$book_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'book id is required']);
            return;
        }
        if (!$book_title) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'book title is required']);
            return;
        }
        if (!$text) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'text is required']);
            return;
        }

        global $pdo;

        $stmt = $pdo->prepare("UPDATE books SET title = ?, text = ? WHERE id = ?");
        $stmt->execute([$book_title, $text, $book_id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'book not found']);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'book updated']);
    }

    public function deleteBook() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $book_id = $_POST['book_id'] ?? '';

        if (!$book_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'book id is required']);
            return;
        }

        global $pdo;

        $stmt = $pdo->prepare("UPDATE books SET deleted_at = ? WHERE id = ?");
        $stmt->execute([date('Y-m-d H:i:s'), $book_id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'book not found']);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'book deleted']);
    }

    public function getBooksOtherUser($user_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $owner_id = $_GET['owner_id'] ?? '';

        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM accesss WHERE owner_id = ? AND user_id = ?");
        $stmt->execute([$owner_id, $user_id]);
        $access = $stmt->fetchAll();

        if (!$access) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'access denied']);
            return;
        }

        $stmt = $pdo->prepare("SELECT id, title FROM books WHERE owner_id = ? AND deleted = null");
        $stmt->execute([$owner_id]);
        $books = $stmt->fetchAll();
        echo json_encode(['success' => true, 'message' => '', 'books' => $books]);
    }
}