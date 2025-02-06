<?php
session_start();
require 'config.php';

// Authentication
function login($username, $password, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            return true;
        }
    }
    return false;
}

// Course Enrollment
function enrollCourse($user_id, $course_id, $conn) {
    $stmt = $conn->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $course_id);
    return $stmt->execute();
}

// Attendance Marking
function markAttendance($user_id, $course_id, $status, $conn) {
    $stmt = $conn->prepare("INSERT INTO attendance (user_id, course_id, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $course_id, $status);
    return $stmt->execute();
}

// Fetch Courses
function getCourses($conn) {
    $result = $conn->query("SELECT * FROM courses");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch Student Enrollments
function getStudentCourses($user_id, $conn) {
    $stmt = $conn->prepare("SELECT c.* FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch Attendance
function getAttendance($user_id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
