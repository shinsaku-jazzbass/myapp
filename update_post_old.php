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

    // 新しい画像がアップロードされた場合
    if (isset($_FILES['image'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image_name);
        
        $sql = "UPDATE posts SET title = :t, content = :c, image_path = :i WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':t' => $title, ':c' => $content, ':i' => $image_name, ':id' => $id]);
    } else {
        // 画像はそのままでテキストのみ更新
        $sql = "UPDATE posts SET title = :t, content = :c WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':t' => $title, ':c' => $content, ':id' => $id]);
    }

    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}