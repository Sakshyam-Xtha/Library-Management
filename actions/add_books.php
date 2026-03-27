<?php 
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"),true);

include '../config/dbconnecter.php';

if (!empty($data['id'])) {
    // UPDATE existing book
    $stmt = $con->prepare("UPDATE books SET title=?, author=?, category=?, isbn=?, quantity=?, cover=? WHERE id=?");
    $stmt->bind_param("ssssssi", $data['title'], $data['author'], $data['genre'], $data['isbn'], $data['copies'], $data['cover'], $data['id']);
} else {
    // INSERT new book
    $stmt = $con->prepare("INSERT INTO books (title, author, category, isbn, quantity,cover,copies_available) VALUES (?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssisi", $data['title'], $data['author'], $data['genre'], $data['isbn'], $data['copies'], $data['cover'],$data['copies']);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "new_id" => $con->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => $con->error]);
}
$stmt->close();
mysqli_close($con);
?>