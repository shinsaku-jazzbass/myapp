<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// OPTIONSリクエスト（プリフライト）への対応
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$host = 'localhost';
$dbname = 'my_app';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Reactから送られてきたJSONを取得
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!empty($data['username']) && !empty($data['password'])) {
        $username = $data['username'];
        // パスワードを安全にハッシュ化
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([':username' => $username, ':password' => $password])) {
            echo json_encode(["status" => "success", "message" => "ユーザー登録が完了しました"]);
        } else {
            echo json_encode(["status" => "error", "message" => "登録に失敗しました（既に存在するユーザー名など）"]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}