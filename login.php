
<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($connexion, $_POST['username']);
    $password = mysqli_real_escape_string($connexion, $_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $connexion->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];

        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin.php");
                exit();
            case 'student':
                header("Location: student.php");
                exit();
            case 'teacher':
                header("Location: teacher.php");
                exit();
           
        }
    } else {
        $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Connexion</title>
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

<h2>Connexion</h2>

<?php
if (isset($error_message)) {
    echo '<p style="color: red;">' . $error_message . '</p>';
}
?>

<form method="post" action="">
    <input type="text" name="username" required placeholder="Entrez votre nom d'utilisateur">
    <br>
    <input type="password" name="password" required placeholder="Entrez votre mot de passe">
    <br>
    <select name="role" required>
        <option value="student">Étudiant</option>
        <option value="teacher">Enseignant</option>
        <option value="admin">Admin</option>

    </select>
    <br>
    <input type="submit" value="Se Connecter">
</form>

<a href="inscription.php">Pas encore inscrit? Inscrivez-vous ici.</a>

</body>
</html>
