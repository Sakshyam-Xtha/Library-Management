<?php
require_once 'env_loader.php';

loadEnv(__DIR__ . '/.env');

$dbname = "Library";
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

$con = mysqli_connect($host, $user, $pass);
if (!$con){
    die ("Error detected: ".mysqli_connect_error());
}else{
    $db_selected = mysqli_select_db($con,$dbname);
    if (!$db_selected) {
        try{

            mysqli_query($con, "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4");
            mysqli_select_db($con, $dbname);

            $sql_file = 'Library.sql';
            if (!file_exists($sql_file)) {
                die("Error: SQL file not found at $sql_file");
            }
            $query = file_get_contents($sql_file);

            if (!mysqli_multi_query($con, $query)) {
                throw new Exception("Schema import failed: " . mysqli_error($con));
            }

            while (mysqli_next_result($con));

            // inserting the super admin
            $admin_name = "Super Admin";
            $admin_pwd = "3838473266@admin";
            $hashed_pwd = password_hash($admin_pwd,PASSWORD_DEFAULT);
            $admin_email = "admin@lms.com";
            $admin_role = "admin";
            $insert = $con->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
            $insert->bind_param("ssss",$admin_name,$admin_email,$hashed_pwd,$admin_role);
            $insert->execute();
            $insert->close();
        }catch (Exception $e){
            error_log("Setup error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "error",
                "message" => "Database setup failed. Check server logs."
            ]);
            exit;
        }
    }
}

?>