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
        $deleteScheduleStmt->execute();

        // Check if any rows were affected
        if ($deleteScheduleStmt->affected_rows > 0) {
            // Deletion successful
            header("Location: schedule_management.php");
            exit();
        } else {
            
            echo "Error: No schedule entry deleted.";
        }
    }
}


$connexion->close();
?>









