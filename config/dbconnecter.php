<?php
require_once 'env_loader.php';
loadEnv(__DIR__ . '/.env');

$dbname = "if0_41492360_lms"; // Ensure this matches your vPanel exactly
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

// 1. Connect and select the database immediately
$con = mysqli_connect($host, $user, $pass, $dbname);

if (!$con){
    die ("Connection failed: " . mysqli_connect_error());
}

// 2. Check if the database is already set up to avoid duplicate errors
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    try {
        $sql_file = 'Library.sql';
        if (!file_exists($sql_file)) {
            die("Error: SQL file not found at $sql_file");
        }
        
        $query = file_get_contents($sql_file);

        // 3. Execute the schema import
        if (!mysqli_multi_query($con, $query)) {
            throw new Exception("Schema import failed: " . mysqli_error($con));
        }

        // Wait for multi_query to finish before proceeding
        while (mysqli_next_result($con));

        // 4. Insert the super admin
        $admin_name = "Super Admin";
        $admin_pwd = "3838473266@admin";
        $hashed_pwd = password_hash($admin_pwd, PASSWORD_DEFAULT);
        $admin_email = "admin@lms.com";
        $admin_role = "admin";

        $insert = $con->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
        $insert->bind_param("ssss", $admin_name, $admin_email, $hashed_pwd, $admin_role);
        $insert->execute();
        $insert->close();

    } catch (Exception $e) {
        error_log("Setup error: " . $e->getMessage());
        die("Database setup failed. Check server logs.");
    }
}
?>