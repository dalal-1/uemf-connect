<?php

$host = "localhost";
$username = "root";
$password = ""; 
$database = "students_manager"; 

$connexion = new mysqli($host, $username, $password, $database);

if ($connexion->connect_error) {
    die("Échec de la connexion à la base de données : " . $connexion->connect_error);
}



function getModuleNameById($moduleId, $connexion) {
    $moduleNameQuery = "SELECT module_name FROM modules WHERE id_module = ?";
    $moduleNameStmt = $connexion->prepare($moduleNameQuery);
    $moduleNameStmt->bind_param("i", $moduleId);
    $moduleNameStmt->execute();
    $moduleNameResult = $moduleNameStmt->get_result();
    $moduleName = $moduleNameResult->fetch_assoc()['module_name'];
    $moduleNameStmt->close();
    return $moduleName;
}




function getTeacherDetailsById($teacherId, $connexion) {
    $teacherDetailsQuery = "SELECT * FROM teachers WHERE id_teacher = ?";
    $teacherDetailsStmt = $connexion->prepare($teacherDetailsQuery);
    $teacherDetailsStmt->bind_param("i", $teacherId);
    $teacherDetailsStmt->execute();
    $teacherDetailsResult = $teacherDetailsStmt->get_result();
    $teacherDetails = $teacherDetailsResult->fetch_assoc();
    $teacherDetailsStmt->close();
    return $teacherDetails;
}
function getTeacherFullNameById($teacherId, $connexion) {
    $teacherDetailsQuery = "SELECT first_name, last_name FROM teachers WHERE id_teacher = ?";
    $teacherDetailsStmt = $connexion->prepare($teacherDetailsQuery);
    $teacherDetailsStmt->bind_param("i", $teacherId);
    $teacherDetailsStmt->execute();
    $teacherDetailsResult = $teacherDetailsStmt->get_result();
    $teacherDetails = $teacherDetailsResult->fetch_assoc();
    $teacherDetailsStmt->close();

    
    return $teacherDetails['first_name'] . ' ' . $teacherDetails['last_name'];
}




?>
