<?php
include_once '../config/dbconnecter.php';

header("Content-Type: application/json");
$data = json_decode(file_get_contents('php://input'),true);

if (!$data || !isset($data['id']) || !isset($data['book_id'])) {
    echo json_encode(["success" => false, "message" => "Missing required data"]);
    exit;
}

if($con){
    try{
        $currentDate = date("Y-m-d");
        $con->begin_transaction();

        // Update the return_date in issued_books table
        $update = $con->prepare("UPDATE issued_books SET return_date = ?, status='returned' WHERE id = ?");
        $update->bind_param("si", $currentDate, $data['id']);
        $update->execute();
        $update->close();

        // Update copies_available in books table
        $update = $con->prepare("UPDATE books SET copies_available = copies_available + 1 WHERE id = ?");
        $update->bind_param("i", $data['book_id']);
        $update->execute();
        $update->close();

        $con->commit();

        echo json_encode(["success" => true, "message" => "Book returned successfully"]);
    }
    catch (Exception $e){
        $con->rollback();
        echo json_encode(["success" => false, "message" => "Error occurred: " . $e->getMessage()]);
    }
    $con->close();
} else {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
}
?>