<?php 
include "../config/dbconnecter.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"),true);

if($data){
    $app_id = $data['id'] ?? 0;
    $status = $data['status'] ?? "";

    if ($status == 'approved') {
        // 1. Get the application details
        $stmt = $con->prepare("SELECT user_id, type FROM member_applications WHERE id = ?");
        $stmt->bind_param("i",$app_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $app = $res->fetch_assoc();
        $stmt->close();

        // 2. Update user role
        $stmt = $con->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si",$app['type'],$app['user_id']);
        $stmt->execute();
        $stmt->close();

        // 3. Update application status
        $stmt = $con->prepare("UPDATE member_applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si",$status,$app_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success']);
    }
    else if ($status === 'rejected'){
        $stmt = $con->prepare("UPDATE member_applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si",$status,$app_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success']);
    }
    
}
else{
    echo json_encode(['status'=>'failed']);
}
$con->close();
?>