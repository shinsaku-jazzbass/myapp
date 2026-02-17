<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$host = 'localhost';
$dbname = 'my_app';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!empty($data['username']) && !empty($data['password'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $data['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ユーザーが存在し、パスワードが一致するか確認
        if ($user && password_verify($data['password'], $user['password'])) {
            // ログイン成功（本来はここでトークンを発行しますが、まずは簡易的にユーザー情報を返します）
            echo json_encode([
                "status" => "success", 
                "message" => "ログインしました",
                "user" => ["id" => $user['id'], "username" => $user['username']]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "ユーザー名またはパスワードが違います"]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}