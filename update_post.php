<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$host = 'localhost'; $dbname = 'my_app'; $user = 'root'; $pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    // YouTube URLを取得
    $youtube_url = isset($_POST['youtube_url']) ? $_POST['youtube_url'] : '';

    // 新しい画像がアップロードされた場合
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], './uploads/' . $image_name);
        
        // 画像パスとYouTube URLの両方を更新
        $sql = "UPDATE posts SET title = :t, content = :c, image_path = :i, youtube_url = :y WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':t' => $title, 
            ':c' => $content, 
            ':i' => $image_name, 
            ':y' => $youtube_url, 
            ':id' => $id
        ]);
    } else {
        // 画像はそのままで、テキストとYouTube URLを更新
        $sql = "UPDATE posts SET title = :t, content = :c, youtube_url = :y WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':t' => $title, 
            ':c' => $content, 
            ':y' => $youtube_url, 
            ':id' => $id
        ]);
    }

    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}