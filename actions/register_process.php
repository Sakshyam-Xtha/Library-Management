<?php
include '../config/dbconnecter.php';

$data = json_decode(file_get_contents('php://input'),true);

header('Content-Type: application/json');

if (!empty($data)){
    $fname = $data['fname'];
    $lname = $data['lname'];
    $email = $data['email'];
    $role = "user";
    $password = $data['pw'];
    //mysqli_real_escape_string is used to handle if the user inserts sql query in input

    $name = $fname." ".$lname;

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);//hashes the password for data security

    $check = $con->prepare("SELECT email FROM users WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();
    $check->close();

    if ($res->fetch_assoc()){
        echo json_encode(["status"=>"failed","message"=>"There is already an account linked with this email."]);
    }
    else{
        $stmt = $con->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss",$name,$email,$hashed_password,$role);
        if ($stmt->execute()){
            echo json_encode(["status"=>"success","message"=>"Welcome, $fname! Your account has been created. Redirecting to login…"]);
        }else {
            echo json_encode(["status"=>"failed","message"=>"An error occurred: ".$stmt->error()]);
        }

        $stmt->close();
    }
    
    mysqli_close($con);
}
?>