<?php
require_once('../includes/auth.php');
include '../config/dbconnecter.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'),true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No data received.']);
    exit;
}

// 1. Sanitize Inputs based on your structure
$u_id    = isset($data['user_id']) ? intval($data['user_id']) : 0;
if ($data['type'] === "General"){
    $type = "member";
}elseif ($data['type'] === "Faculty"){
    $type = "staff";
}else{
    $type = "student";
}
$phone   = isset($data['phone']) ? trim($data['phone']) : '';
$address = isset($data['address']) ? trim($data['address']) : '';
$reason  = isset($data['reason']) ? trim($data['reason']) : '';

// 2. Validation
if ($u_id <= 0 || empty($reason)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a reason for your application.']);
    exit;
}

// 3. Prevent Duplicate Pending Applications
$check = $con->prepare("SELECT id FROM member_applications WHERE user_id = ? AND status = 'pending'");
$check->bind_param("i", $u_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'You already have an application pending review.']);
    exit;
}
$check->close();

try {
    // 4. Insert Application using your specific column order
    $stmt = $con->prepare("INSERT INTO member_applications (user_id, type, phone, address, reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $u_id, $type, $phone, $address, $reason);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Application submitted successfully!']);
    } else {
        echo json_encode(['status' => 'failed', 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['status' => 'failed', 'message' => 'Server error: ' . $e->getMessage()]);
}

$con->close();
?>