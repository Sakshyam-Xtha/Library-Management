<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
include '../config/dbconnecter.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'),true);
$cur_pwd = $data['current_pwd'] ?? null;
$new_pwd = $data['new_pwd'] ?? null;
$user_id = $data['id'] ?? null;

if (!$con){
    echo json_encode(["success"=>false,"message"=>"Database connection failed."]);
    exit;
}

if (!$cur_pwd || !$new_pwd || !$user_id){
    echo json_encode(["success"=>false,"message"=>"Invalid request. Missing required fields."]);
    exit;
}

try{
    //hash the new password
    $hash_new_pwd = password_hash($new_pwd,PASSWORD_DEFAULT);

    //fetch current password from database
    $select = $con->prepare("SELECT password FROM users WHERE id=?");
    if (!$select){
        throw new Exception($con->error);
    }
    
    $select->bind_param("i",$user_id);
    if (!$select->execute()){
        throw new Exception($select->error);
    }
    
    $result = $select->get_result();
    $row = $result->fetch_assoc();
    $select->close();

    if (!$row){
        echo json_encode(["success"=>false,"message"=>"User not found."]);
        exit;
    }

    //verify current password
    if (!password_verify($cur_pwd,$row['password'])){
        echo json_encode(["success"=>false,"message"=>"Current password is incorrect."]);
        exit;
    }

    //update password
    $update = $con->prepare("UPDATE users SET password=? WHERE id=?");
    if (!$update){
        throw new Exception($con->error);
    }
    
    $update->bind_param("si", $hash_new_pwd, $user_id);
    
    if (!$update->execute()){
        throw new Exception($update->error);
    }
    $update->close();

    echo json_encode(["success"=>true,"message"=>"Password updated successfully."]);
}
catch (Exception $e){
    echo json_encode(["success"=>false,"message"=>"Error: " . $e->getMessage()]);
}
finally{
    if($con) $con->close();
}
?>