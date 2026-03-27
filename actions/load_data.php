<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to access $_SESSION['user_id']
require_once('../includes/auth.php');
include '../config/dbconnecter.php';

header('Content-Type: application/json');

$members = [];
$books = [];
$borrowings = [];
$history = [];
$all_history = [];
$user_borrowed_ids = [];
$applications = [];
$app_status = null; // Initialize to null

try {
    if (!$con) {
        // This check should ideally be in dbconnecter.php, but added here for robustness
        throw new Exception("Database connection failed during include of dbconnecter.php. Check dbconnecter.php for errors.");
    }

    // 1. Fetch Members (Existing logic)
    $stmt = $con->prepare("SELECT id, name, email, role FROM users WHERE role != 'admin'");
    if (!$stmt) throw new Exception("Prepare failed for members: " . $con->error);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $names = explode(' ', $row['name']);
        $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
        $row['initials'] = $initials;
        $row['color']    = "#1a4a8a,#2563eb";
        $row['joined']   = "Mar 2026";
        $row['status']   = "active";
        $row['borrowedBooks'] = [];
        $members[] = $row;
    }
    $stmt->close();

    // 2. Fetch Books (Updated to include copies_available)
    $stmt = $con->prepare("SELECT id, title, author, category AS genre, isbn, quantity AS copies, copies_available, cover FROM books");
    if (!$stmt) throw new Exception("Prepare failed for books: " . $con->error);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();

    // 3. Fetch active borrowings
    $stmt = $con->prepare("SELECT id, user_id AS member_id, book_id, issue_date, due_date, return_date AS returned_date,fine FROM issued_books WHERE return_date IS NULL");
    if (!$stmt) throw new Exception("Prepare failed for active borrowings: " . $con->error);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $borrowings[] = $row;
    }
    $stmt->close();

    // 4. Fetch user history (returned books)
    if (isset($_SESSION['user_id'])) {
        $u_id = $_SESSION['user_id'];
        $stmt = $con->prepare("SELECT id, user_id AS member_id, book_id, issue_date, due_date, return_date AS returned_date, fine FROM issued_books WHERE user_id = ? AND return_date IS NOT NULL ORDER BY return_date DESC");
        if (!$stmt) throw new Exception("Prepare failed for user history: " . $con->error);
        $stmt->bind_param("i", $u_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $history[] = $row;
        }
        $stmt->close();
    }

    // 4.5. Fetch all history for admin
    $all_history = [];
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $stmt = $con->prepare("SELECT id, user_id AS member_id, book_id, issue_date, due_date, return_date AS returned_date, fine FROM issued_books WHERE return_date IS NOT NULL ORDER BY return_date DESC");
        if (!$stmt) throw new Exception("Prepare failed for all history: " . $con->error);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $all_history[] = $row;
        }
        $stmt->close();
    }

    // 5. Fetch IDs of books borrowed by the CURRENT logged-in user
    // This allows the frontend to show "You have this" correctly
    if (isset($_SESSION['user_id'])) {
        $u_id = $_SESSION['user_id'];
        // We only select books that haven't been returned yet
        $stmt = $con->prepare("SELECT book_id FROM issued_books WHERE user_id = ? AND return_date IS NULL");
        if (!$stmt) throw new Exception("Prepare failed for user borrowed IDs: " . $con->error);
        $stmt->bind_param("i", $u_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()) {
            $user_borrowed_ids[] = (int)$row['book_id'];
        }
        $stmt->close();
    }

    if (isset($_SESSION['user_id'])) {
        $u_id = $_SESSION['user_id'];
        $stmt = $con->prepare("SELECT status FROM member_applications WHERE user_id = ? ORDER BY applied_at DESC LIMIT 1");
        if (!$stmt) throw new Exception("Prepare failed for app status: " . $con->error);
        $stmt->bind_param("i", $u_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $app_status = $row['status'];
        }
        $stmt->close();
    }

    //Fetch all pending membership applications (for Admin view)
    // Only fetch 'pending' ones to keep the admin dashboard clean
    $app_query = "SELECT a.*, u.name as applicant_name, u.email as applicant_email
                FROM member_applications a
                JOIN users u ON a.user_id = u.id
                WHERE a.status = 'pending'
                ORDER BY a.applied_at DESC";

    $app_res = $con->query($app_query);
    if ($app_res === false) { // Check for query execution failure
        throw new Exception("Application query failed: " . $con->error);
    }
    while ($row = $app_res->fetch_assoc()) {
        $applications[] = $row;
    }

} catch (Exception $e) {
    error_log("load_data.php error: " . $e->getMessage());
    // Send a 500 status code and a JSON error message to the client
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    // Terminate script execution after sending error
    exit();
} finally {
    if (isset($con) && $con instanceof mysqli) {
        $con->close();
    }
}

echo json_encode([
    "books" => $books,
    "members" => $members,
    "borrowings" => $borrowings,
    "history" => $history,
    "all_history" => $all_history,
    "user_borrowed_ids" => $user_borrowed_ids,
    "app_status" => $app_status,
    "applications"=>$applications// Send this to the frontend
],JSON_UNESCAPED_UNICODE);
?>
