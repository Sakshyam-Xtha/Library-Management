<?php 
include '../config/dbconnecter.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"),true);
if ($data){
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i",$data['id']);
    if ($stmt->execute()){
        echo json_encode(["status"=>"success","message"=>"Successfully deleted account."]);
    }else{
        echo json_encode(["status"=>"failed","message"=>"Network Error."]);
    }
    $stmt->close();
}
?>