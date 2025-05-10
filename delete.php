<?php
session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule'])) {
    $scheduleId = $_POST['schedule_id'] ?? null;
    if ($scheduleId) {
        $deleteScheduleQuery = "DELETE FROM student_schedule WHERE id_schedule = ? AND teacher_id = ?";
        $deleteScheduleStmt = $connexion->prepare($deleteScheduleQuery);
        $deleteScheduleStmt->bind_param("si", $scheduleId, $teacherId);
        
        // Execute the prepared statement
        if ($deleteScheduleStmt->execute()) {
            // Deletion successful
            header("Location: schedule_management.php");
            exit();
        } else {
            // Handle error
            echo "Error deleting schedule entry: " . $deleteScheduleStmt->error;
        }
    } else {
        // Handle the case where $scheduleId is not set
        echo "Schedule ID is not set.";
    }
}


$connexion->close();
?>


