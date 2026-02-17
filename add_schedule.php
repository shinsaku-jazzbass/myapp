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

    $sql = "INSERT INTO schedules (gig_date, venue, start_time, price, description) 
            VALUES (:d, :v, :t, :p, :desc)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':d' => $data['gig_date'],
        ':v' => $data['venue'],
        ':t' => $data['start_time'],
        ':p' => $data['price'],
        ':desc' => $data['description']
    ]);
    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}