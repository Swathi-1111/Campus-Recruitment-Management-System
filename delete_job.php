<?php
include 'db_connect.php'; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['job_id'])) {
    $job_id = $data['job_id'];

    $stmt = $conn->prepare("DELETE FROM jobs WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request. Missing job_id."]);
}
?>