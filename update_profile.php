<?php


session_start();
include 'config.php';

$student_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $matricul = $_POST['matricul'];

    $uploadDir = 'uploads/';
    $imageName = basename($_FILES['image']['name']);
    $uploadPath = $uploadDir . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
    $image_path = $uploadPath;

    
    $query = "UPDATE profile SET username = '$username', matricul = '$matricul', image_path = '$image_path' WHERE student_id = $student_id";
    $result = $connexion->query($query);

    if ($result) {
       
        echo json_encode(['success' => true, 'imagePath' => $image_path]);
    } else {
       
        echo json_encode(['success' => false]);
    }
}
?>

