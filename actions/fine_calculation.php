<?php
include '../config/dbconnecter.php';

// Query to get all issued/unreturned books
$sql = "SELECT id, due_date, fine, status FROM issued_books WHERE return_date IS NULL";
$result = $con->query($sql);

$today = new DateTime(); // Current date

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dueDate = new DateTime($row['due_date']);
        $id = $row['id'];
        
        // Check if the book is overdue
        if ($today > $dueDate) {
            // Calculate difference in days
            $interval = $today->diff($dueDate);
            $daysOverdue = $interval->days;

            // Logic: 10 per day
            $calculatedFine = $daysOverdue * 10;

            // Optional: Update the database so the "fine" and "status" stay current
            $updateSql = "UPDATE issued_books 
                          SET fine = $calculatedFine, status = 'overdue' 
                          WHERE id = $id";
            $con->query($updateSql);

        }
    }
}
?>