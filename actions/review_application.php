<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/dbconnecter.php";
header("Content-Type: application/json");

$response = ['status' => 'failed', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = 'Invalid JSON input: ' . json_last_error_msg();
    } elseif ($data) {
        $app_id = $data['id'] ?? 0;
        $status = $data['status'] ?? "";
        $assigned_role = $data['role'] ?? null; // New: get assigned role
        $assigned_color = $data['color'] ?? null; // New: get assigned color

        if ($app_id === 0) {
            $response['message'] = 'Application ID is missing.';
        } elseif (empty($status)) {
            $response['message'] = 'Status is missing.';
        } else {
            try {
                // Start a transaction for atomicity
                $con->begin_transaction();

                if ($status === 'approved') {
                    if (empty($assigned_role) || empty($assigned_color)) {
                        $response['message'] = 'Assigned role or color is missing for approved application.';
                        $con->rollback();
                    } else {
                        // 1. Get the user_id from the application
                        $stmt = $con->prepare("SELECT user_id FROM member_applications WHERE id = ?");
                        if (!$stmt) throw new Exception("Prepare failed for selecting user_id: " . $con->error);
                        $stmt->bind_param("i", $app_id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $app = $res->fetch_assoc();
                        $stmt->close();

                        if ($app && $app['user_id']) {
                            // 2. Update user role and color
                            $stmt = $con->prepare("UPDATE users SET role = ?, color = ? WHERE id = ?");
                            if (!$stmt) throw new Exception("Prepare failed for updating user role/color: " . $con->error);
                            $stmt->bind_param("ssi", $assigned_role, $assigned_color, $app['user_id']);
                            $stmt->execute();
                            if ($stmt->affected_rows === 0) {
                                // This could mean the user already has this role/color or user_id is bad
                                error_log("No rows updated for user_id {$app['user_id']} with role {$assigned_role} and color {$assigned_color}");
                            }
                            $stmt->close();

                            // 3. Update application status
                            $stmt = $con->prepare("UPDATE member_applications SET status = ? WHERE id = ?");
                            if (!$stmt) throw new Exception("Prepare failed for updating application status: " . $con->error);
                            $stmt->bind_param("si", $status, $app_id);
                            $stmt->execute();
                            $stmt->close();

                            $con->commit();
                            $response = ['status' => 'success', 'message' => 'Application approved and user updated successfully.'];
                        } else {
                            $con->rollback();
                            $response['message'] = 'Application not found or user ID missing.';
                        }
                    }
                } elseif ($status === 'rejected') {
                    $stmt = $con->prepare("UPDATE member_applications SET status = ? WHERE id = ?");
                    if (!$stmt) throw new Exception("Prepare failed for rejecting application: " . $con->error);
                    $stmt->bind_param("si", $status, $app_id);
                    $stmt->execute();
                    $stmt->close();

                    $con->commit();
                    $response = ['status' => 'success', 'message' => 'Application rejected successfully.'];
                } else {
                    $con->rollback();
                    $response['message'] = 'Invalid application status provided.';
                }
            } catch (Exception $e) {
                $con->rollback(); // Rollback on any error
                error_log("review_application.php error: " . $e->getMessage());
                $response['message'] = 'Server error: ' . $e->getMessage();
            }
        }
    }
}

echo json_encode($response);

if (isset($con) && $con instanceof mysqli) {
    $con->close();
}
?>
