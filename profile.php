<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Function to fetch user profile based on role
function getUserProfile($userId, $role) {
    global $connexion;

    if ($role === 'teacher') {
        $table = 'teachers';
        $idField = 'teacher_id';
    } elseif ($role === 'student') {
        $table = 'students';
        $idField = 'student_id';
    } else {
        // Handle other roles if needed
        return [];
    }

    $query = "SELECT * FROM $table WHERE user_id = ?";
    $stmt = $connexion->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

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

$profileData = getUserProfile($user_id, $role);

$userName = $profileData['username'] ?? '';
$firstName = $profileData['first_name'] ?? '';
$lastName = $profileData['last_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = isset($_POST['username']) ? $_POST['username'] : $userName;
    $newPassword = isset($_POST['password']) ? $_POST['password'] : null;

    // Add other fields as needed

    // Check if a new image has been uploaded
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageName = basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        $imagePath = $uploadPath;

        // Set the session variable with the updated image path
        $_SESSION['updated_image_path'] = $imagePath;
    } else {
        $imagePath = $profileData['image_path'];
    }

    // Use prepared statement to avoid SQL injection
    $updateProfileQuery = "UPDATE profile SET username = ?, password = ?, image_path = ? WHERE user_id = ?";
    $stmt = $connexion->prepare($updateProfileQuery);
    $stmt->bind_param("sssi", $newUsername, $newPassword, $imagePath, $user_id);
    $result = $stmt->execute();

    if ($result) {
        echo "Profil mis à jour avec succès.";

        $profileData['username'] = $newUsername;
        $profileData['image_path'] = $imagePath;
    } else {
        echo "Erreur lors de la mise à jour du profil: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

$profileImage = !empty($profileData['image_path']) ? $profileData['image_path'] : 'default-profile-image.jpg';
$altText = "$firstName $lastName";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile</title>
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
<body>

    <div id="aside">
        <div id="welcome-section">
            <div id="welcome-text">
                <p>Bienvenue, <?php echo "$firstName $lastName"; ?>!</p>
            </div>
        </div>
    </div>

    <div id="profilSection">
        <h2 id="profilTitle">Modifier votre profil</h2>

        <form method="post" action="" enctype="multipart/form-data">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" name="username" id="username" value="<?php echo $userName; ?>" required>

            <label for="password">Mot de passe:</label>
            <input type="text" name="password" id="password">

            <label for="image">Image de profil:</label>
            <input type="file" name="image" id="image">

            <img src="<?php echo $profileImage; ?>" alt="<?php echo $altText; ?>">

            <input type="submit" value="Mettre à jour le profil">
        </form>
    </div>

</body>
</html>
