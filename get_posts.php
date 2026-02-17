<?php
// CORSの設定（開発用）
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$host = 'localhost';
$dbname = 'my_app';
$user = 'root';
$pass = 'root'; // MAMPのデフォルトはroot

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // 投稿一覧を取得
    $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($posts);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}