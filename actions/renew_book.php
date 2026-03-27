<?php
include_once '../config/dbconnecter.php';

header("Content-Type: application/json");
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['borrowing_id'])) {
    echo json_encode(["success" => false, "message" => "Missing borrowing_id"]);
    exit;
}

if ($con) {
    try {
        $con->begin_transaction();

        // Get current due date
        $stmt = $con->prepare("SELECT due_date FROM issued_books WHERE id = ?");
        $stmt->bind_param("i", $data['borrowing_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) {
            echo json_encode(["success" => false, "message" => "Borrowing record not found"]);
            exit;
        }

        // Calculate new due date (extend by 14 days)
        $currentDueDate = new DateTime($row['due_date']);
        $newDueDate = $currentDueDate->modify('+14 days')->format('Y-m-d');

        // Update the due date
        $update = $con->prepare("UPDATE issued_books SET due_date = ? WHERE id = ?");
        $update->bind_param("si", $newDueDate, $data['borrowing_id']);
        $update->execute();
        $update->close();

        $con->commit();

        echo json_encode(["success" => true, "message" => "Book renewed successfully", "new_due_date" => $newDueDate]);
    } catch (Exception $e) {
        $con->rollback();
        echo json_encode(["success" => false, "message" => "Error occurred: " . $e->getMessage()]);
    }
    $con->close();
} else {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
}
?>