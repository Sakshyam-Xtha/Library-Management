<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Visitor';
$userRole = $_SESSION['role'] ?? 'user';
$currentTheme = $_SESSION['theme'] ?? 'light';
$words = explode(' ', trim($userName)); 
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
$user_email = $_SESSION['email'];
?>