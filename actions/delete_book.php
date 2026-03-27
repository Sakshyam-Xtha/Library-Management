<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"),true);

include '../config/dbconnecter.php';

if (isset($data['id'])) {
    // Corrected bind_param: only one "i" for one ID
    $stmt = $con->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param("i", $data['id']);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $con->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "No ID provided"]);
}

mysqli_close($con);
?>