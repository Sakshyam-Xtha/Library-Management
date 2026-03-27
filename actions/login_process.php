<?php
session_start();
include '../config/dbconnecter.php';

header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"),true);


if ($data){
    $email_inp = $data['email'] ?? null;
    $pass_inp = $data['pass'] ?? null;

    if ($email_inp && $pass_inp){
        $stmt = $con->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bind_param('s',$email_inp);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($user = $res->fetch_assoc()){
            if (password_verify($pass_inp,$user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['member_since'] = $user['created_at'];
                $_SESSION['email'] = $user['email'];

                echo json_encode(["status"=>"success","message"=>"Credentials verified. Securely signing in…","role"=>$user['role']]);
                exit();
            }else{
                echo json_encode(["status"=>"failed","message"=>"Credentials incorrect. Please enter the correct data.",]);
            }
        }else{
            echo json_encode(["status"=>"failed","message"=>"The provided email is not linked to an account. Please create an account first."]);
        }

        $stmt->close();
    }
    
    mysqli_close($con);
}
?>