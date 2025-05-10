<?php
session_start();
include 'config.php';

$teacher_id = $_SESSION['user_id'] ?? null;

$uploadDir = 'uploads/teachers/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function getTeacherProfile($teacherId) {
    global $connexion;
    $query = "SELECT * FROM profile_teacher WHERE teacher_id = $teacherId";
    $result = $connexion->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            echo "No profile found!";
        }
    } else {
        echo "Error executing query: " . $connexion->error;
    }

    return [];
}

$teacherProfileData = getTeacherProfile($teacher_id);

$teacherName = $teacherProfileData['first_name'] ?? '';
$teacherProfileImage = $uploadDir . ($teacherProfileData['profile_image'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $matricul = isset($_POST['matricul']) ? $_POST['matricul'] : null;

    
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageName = basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        $imagePath = $uploadPath;
    } else {
        $imagePath = $teacherProfileData['profile_image'];
    }

    $query = "UPDATE profile_teacher SET username = '$username', matricul = '$matricul', profile_image = '$imagePath' WHERE teacher_id = $teacher_id";
    $result = $connexion->query($query);

    if ($result) {
        echo "Profil mis à jour avec succès.";
       
        $teacherProfileData['username'] = $username;
        $teacherProfileData['matricul'] = $matricul;
        $teacherProfileData['profile_image'] = $imagePath;
    } else {
        echo "Erreur lors de la mise à jour du profil: " . $connexion->error;
    }
}

$teacherProfileImage = !empty($teacherProfileData['profile_image']) ? $uploadDir . $teacherProfileData['profile_image'] : 'default-profile-image.jpg';
$teacherAltText = "$teacherName";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>teacher_profile</title>
    <style>
       body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f0f0f0;
    display: flex;
    flex-direction: row; /* Changed from column to row */
    height: 100vh;
}

#aside {
    width: 300px;
    background-color: #4e2a8e; /* Purple color */
    color: white;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

#welcome-section {
    text-align: center;
    margin-bottom: 20px;
}

#welcome-text {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px;
}

#profilSection {
    flex: 1;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#profilTitle {
    color: #4e2a8e; /* Purple color */
}

form {
    margin-top: 20px;
}

label {
    display: block;
    margin-bottom: 10px;
    color: #333;
}

input[type="text"],
input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
}

img {
    max-width: 100%; /* Changed from 100% to fixed width */
    height: auto;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

input[type="submit"] {
    background-color: #4e2a8e; /* Purple color */
    color: white;
    padding: 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
}

input[type="submit"]:hover {
    background-color: #452671; /* Darker purple color on hover */
}

    </style>
</head>
<header>
    <?php include 'home.php'; ?>
</header>
<body>

    <div id="aside">
        <?php if (!empty($teacherProfileImage)) : ?>
            <img id="profile-image" src="<?php echo $teacherProfileImage; ?>" alt="<?php echo $teacherAltText; ?>" width="200">
        <?php endif; ?>
    </div>

    <section id="profilSection">
        

        <h2 id="profilTitle">Votre Profil</h2>
        <form action="teacher.php" method="post" enctype="multipart/form-data">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" name="username" value="<?php echo $teacherProfileData['username'] ?? ''; ?>" required><br>

            

            <label for="image">Image de profil:</label>
            <input type="file" name="image"><br>
            <?php if (!empty($teacherProfileImage)) : ?>
                <img id="profile-image" src="<?php echo $teacherProfileImage; ?>" alt="<?php echo $teacherAltText; ?>" width="200">
            <?php endif; ?>

            <input type="submit" value="Mettre à jour le profil">
        </form>
    </section>
</body>
</html>

