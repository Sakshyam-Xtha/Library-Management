<?php
session_start();
include '../config/dbconnecter.php';
include '../includes/auth.php';
header("Content-Type: application/json");

//data from the front end
$data = json_decode(file_get_contents("php://input"),true);
$book_id = $data['book_id'] ?? null;



// Define authorized roles
$authorized_roles = ["member", "student"];


if (in_array(strtolower($userRole), $authorized_roles)){
    if ($con && $book_id && $userId){
        //Start a Transaction (Crucial for data integrity)
        $con->begin_transaction();
        try{
            //checking if the book is available or not
            $stmt = $con->prepare("SELECT copies_available FROM books WHERE id=?");
            $stmt->bind_param("i",$book_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $book = $res ? $res->fetch_assoc() : null;
            $stmt->close();

            //actual borrowing logic
            if ($book && $book['copies_available'] > 0){
                //subtracting the copies_available
                $update = $con->prepare("UPDATE books SET copies_available = copies_available - 1 WHERE id = ?");
                $update->bind_param("i", $book_id);
                $update->execute();
                $update->close();

                $insert = $con->prepare("INSERT INTO issued_books(user_id, book_id) VALUES (?,?)");
                $insert->bind_param("ii",$userId,$book_id);
                $insert->execute();
                $insert->close();

                //committing the sql queries
                $con->commit();
                echo json_encode(["status" => "success", "message" => "Book borrowed successfully"]);
            }
            else{
                echo json_encode(["status" => "error", "message" => "Book not available"]);
            }
        }catch (Exception $e){
            $con->rollback();
            echo json_encode(["status" => "error", "message" => "Transaction failed: " . $e->getMessage()]);
        }
        
        mysqli_close($con);
    }else{
        die(json_encode(["status" => "error", "message" => "Book ID is required"]));
    }
}else{
    die(json_encode(["status"=>"error","message"=>"Unauthorized user.Apply for a membership"]));
}
?>