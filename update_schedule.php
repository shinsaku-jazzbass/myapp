<?php
// CORS設定: React（異なるポート）からのアクセスを許可
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// プリフライトリクエスト（OPTIONS）への対応
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// データベース接続情報
$host = 'localhost';
$dbname = 'my_app';
$user = 'root';
$pass = 'root'; // MAMPのデフォルトは 'root'

try {
    // データベース接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Reactから送られてきたJSONデータを取得
    $data = json_decode(file_get_contents("php://input"), true);

    // 必須データの存在チェック（IDがあるか）
    if (!isset($data['id'])) {
        echo json_encode(["status" => "error", "message" => "ID is required"]);
        exit;
    }

    // SQL文の準備: 指定されたIDのレコードを更新
    $sql = "UPDATE schedules 
            SET gig_date = :gig_date, 
                venue = :venue, 
                start_time = :start_time, 
                price = :price, 
                description = :description 
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    // 実行
    $stmt->execute([
        ':gig_date'    => $data['gig_date'],
        ':venue'       => $data['venue'],
        ':start_time'  => $data['start_time'],
        ':price'       => $data['price'],
        ':description' => $data['description'],
        ':id'          => $data['id']
    ]);

    // 成功レスポンス
    echo json_encode(["status" => "success", "message" => "Schedule updated successfully"]);

} catch (PDOException $e) {
    // エラーレスポンス
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>