<?php
session_start();
include 'config.php';


function validateFormData($data)
{
    
    return $data;
}


function addScheduleToDatabase($data, $connexion)
{
    

    $insertScheduleQuery = "INSERT INTO student_schedule (class_id, day_of_week, module_id, teacher_id, start_time, end_time, classroom)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $insertScheduleStmt = $connexion->prepare($insertScheduleQuery);

    if (!$insertScheduleStmt) {
        echo "Prepare failed: (" . $connexion->errno . ") " . $connexion->error;
        exit();
    }

    $insertScheduleStmt->bind_param("sssssss", $data['class_id'], $data['day_of_week'], $data['module_id'], $data['teacher_id'], $data['start_time'], $data['end_time'], $data['classroom']);

    if (!$insertScheduleStmt->execute()) {
        echo "Execute failed: (" . $insertScheduleStmt->errno . ") " . $insertScheduleStmt->error;
        exit();
    }

    $insertScheduleStmt->close();
}


$formData = $_POST;


$formData = validateFormData($formData);


addScheduleToDatabase($formData, $connexion);


header("Location: schedule_management.php");
exit();
?>
