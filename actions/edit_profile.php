<?php
require_once '../includes/auth.php';
include '../config/dbconnecter.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"),true);

if ($data) {
    // 1. Collect and sanitize input
    $id    = $data['id'];
    $fname = $data['fname'];
    $lname = $data['lname'];
    $email = $data['email'];

    // 2. Basic Validation
    if ($id <= 0 || empty($fname) || empty($email)) {
        echo json_encode(['success' => 'failed', 'message' => 'Missing required fields.']);
        exit;
    }

    // Combine names for the database 'name' column
    $fullName = $fname . ($lname ? ' ' . $lname : '');

    try {
        // 3. Prepare Update Query
        // Note: We check user_id to ensure a user can only update their own profile
        $stmt = $con->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        
        // Security: Ensure logged-in user can only update their own ID
        if (!$_SESSION['role'] && $id !== $_SESSION['user_id']) {
            echo json_encode(['success' => 'failed', 'message' => 'Unauthorized action.']);
            exit;
        }

        $stmt->bind_param("ssi", $fullName, $email, $id);

        if ($stmt->execute()) {
            // 4. Update Session variables so the UI reflects changes immediately
            $_SESSION['user_name'] = $fullName;
            $_SESSION['user_email'] = $email;

            echo json_encode([
                'success' => 'success', 
                'message' => 'Profile updated successfully!',
                'newName' => $fullName
            ]);
        } else {
            echo json_encode(['success' => 'failed', 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$con->close();

?>