<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$host = 'localhost'; $dbname = 'my_app'; $user = 'root'; $pass = 'root';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $data = json_decode(file_get_contents("php://input"), true);
    
    // セキュリティのためuser_idもチェック条件に含めるとより安全です
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => $data['id']]);
    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}