<?php
session_start(); 
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = mysqli_real_escape_string($connexion, $_POST['username']);
    $password = mysqli_real_escape_string($connexion, $_POST['password']);
    $role = mysqli_real_escape_string($connexion, $_POST['role']);
    $first_name = mysqli_real_escape_string($connexion, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($connexion, $_POST['last_name']);
    $email = mysqli_real_escape_string($connexion, $_POST['email']);
    $date_of_birth = mysqli_real_escape_string($connexion, $_POST['date_of_birth']);

    

    
    $insert_user_query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";

    if ($connexion->query($insert_user_query) === TRUE) {
        $user_id = $connexion->insert_id; 

        if ($role === 'teacher') {
            
            $insert_teacher_query = "INSERT INTO teachers (user_id, first_name, last_name, email) 
                                     VALUES ($user_id, '$first_name', '$last_name', '$email')";
            $connexion->query($insert_teacher_query);
        } elseif ($role === 'student') {
            
            $insert_student_query = "INSERT INTO students (user_id, first_name, last_name, email, date_of_birth) 
                                     VALUES ($user_id, '$first_name', '$last_name', '$email', '$date_of_birth')";
            $connexion->query($insert_student_query);
        }

        $success_message = "Inscription réussie! Connectez-vous maintenant.";
    } else {
        $error_message = "Erreur lors de l'inscription : " . $connexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>

    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            margin: 20px;
            background-color: #f7f7f7;
            text-align: center;
           
        }

        h2 {
            color: #333;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
           
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="date"] {
            width: calc(100% - 20px); 
        }

        input[type="submit"] {
            background-color: blueviolet;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color:black;
        }

        p {
            margin-top: 15px;
            color: #333;
        }

        a {
            font-size: 20px;
            color:purple;
            text-decoration: none;
        }
        a:hover{
            color:black;
        }
    </style>
</head>
<body>

<h2>Inscription</h2>

<?php
if (isset($success_message)) {
    echo '<p style="color: green;">' . $success_message . '</p>';
} elseif (isset($error_message)) {
    echo '<p style="color: red;">' . $error_message . '</p>';
}
?>

<form method="post" action="">
    <label for="role"></label>
    <select name="role" id="role" onchange="showFields()">
        <option value="student">Étudiant</option>
        <option value="teacher">Enseignant</option>
    </select>
    <br>
    <br>

    <input type="text" name="last_name" id="last_name" placeholder="Nom" required>
    <br>
    <br>

    <input type="text" name="first_name" id="first_name" placeholder="Prénom" required>
    <br>
    <br>

    <div id="date_of_birth_container" style="display: none;">
        <input type="date" name="date_of_birth" placeholder="Date de naissance">
        <br>
    </div>

    <input type="email" name="email" placeholder="Email" required>
    <br>
    <br>

    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
    <br>
    <br>

    <input type="password" name="password" placeholder="Mot de passe" required>
    <br>



    

    <input type="submit" value="S'Inscrire">
</form>

<a href="login.php">Déjà inscrit? Connectez-vous ici.</a>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        showFields();
    });

    function showFields() {
        var role = document.getElementById("role").value;
        var dateOfBirthContainer = document.getElementById("date_of_birth_container");

        if (role === "student") {
            dateOfBirthContainer.style.display = "block";
        } else {
            dateOfBirthContainer.style.display = "none";
        
    }
}
</script>

</body>
</html>

