<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$host = 'localhost'; $dbname = 'my_app'; $user = 'root'; $pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['title']) && !empty($data['content']) && !empty($data['user_id'])) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (:u, :t, :c)");
        $stmt->execute([
            ':u' => $data['user_id'],
            ':t' => $data['title'],
            ':c' => $data['content']
        ]);
        echo json_encode(["status" => "success", "message" => "投稿しました！"]);
    } else {
        echo json_encode(["status" => "error", "message" => "入力が不足しています"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}