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

    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :u");
    $stmt->execute([':u' => $data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($data['password'], $user['password'])) {
        // パスワードは返さないように除外
        unset($user['password']);
        
        // 成功時：id, username, role が入った配列を返す
        echo json_encode([
            "status" => "success",
            "user" => $user
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}