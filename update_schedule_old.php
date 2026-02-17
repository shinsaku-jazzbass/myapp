<?php
// ...ヘッダー省略...
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $data = json_decode(file_get_contents("php://input"), true);

    $sql = "UPDATE schedules SET gig_date=:d, venue=:v, start_time=:t, price=:p, description=:desc WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':d' => $data['gig_date'], ':v' => $data['venue'], ':t' => $data['start_time'],
        ':p' => $data['price'], ':desc' => $data['description'], ':id' => $data['id']
    ]);
    echo json_encode(["status" => "success"]);
} catch (PDOException $e) { echo json_encode(["status" => "error"]); }