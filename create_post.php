<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$host = 'localhost'; $dbname = 'my_app'; $user = 'root'; $pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    // 画像ファイルの処理
    $image_name = null;
    if (isset($_FILES['image'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], './uploads/' . $image_name);
    }

    // テキストデータの取得（ReactのFormDataから送られてくる）
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_POST['user_id'];
    // YouTube URLを取得（空の場合はnullまたは空文字）
    $youtube_url = isset($_POST['youtube_url']) ? $_POST['youtube_url'] : '';

    // SQL文に youtube_url を追加
    $sql = "INSERT INTO posts (user_id, title, content, image_path, youtube_url) 
            VALUES (:u, :t, :c, :i, :y)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':u' => $user_id,
        ':t' => $title,
        ':c' => $content,
        ':i' => $image_name,
        ':y' => $youtube_url
    ]);

    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}