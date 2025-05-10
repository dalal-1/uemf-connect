<?php
session_start();
include 'config.php';

$teacher_id = $_SESSION['user_id'] ?? null;


function getTeacherName($teacherId) {
    global $connexion;
    $query = "SELECT first_name, last_name FROM teachers WHERE id_teacher = $teacherId";
    $result = $connexion->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            echo "No user found!";
        }
    } else {
        echo "Error executing query: " . $connexion->error;
    }

    return [];
}

$teacherData = getTeacherName($teacher_id);

$teacherFirstName = $teacherData['first_name'] ?? '';
$teacherLastName = $teacherData['last_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Enseignant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
    margin: 0;
    font-family: 'Quicksand', sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background:white; /* Lright gray background color */
}

header {
    background-color:rebeccapurple; /* Pink color */
    color: white;
    padding: 5px;
    text-align: center;
    width: 500px;
    height: 380px;
}

header h1 {
    margin: 0;
    font-size: 32px;
}

header img {
    max-height: calc(50vh - 40px);
    margin-top: 8px;
    margin-right: 20px;
}

main {
    flex: 1;
    padding: 30px;
    background-color:white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    box-sizing: border-box;
    text-align: center;
}

        h2 {
            color: #333;
        }

        table {
            border: 1px solid #ccc;
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            display: none;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        form {
            margin-top: 20px;
            background-color: bisque;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: bisque;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        img {
            margin-top: 20px;
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="submit"] {
            background-color: rebeccapurple; /* Pink color */
            color: white;
            padding: 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
        }

        input[type="submit"]:hover {
            background-color:black; 
        }

        i {
            margin-right: 10px;
        }

        aside {
            width: 350px;
            background-color:blanchedalmond;
            color: black;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            overflow-y: auto;
            cursor: pointer;
        }

        nav {
            margin-top: 20px;
            width: 100%;
        }

        nav a {
            color: wheat;
            text-decoration: none;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 30px;
            background-color:rebeccapurple; 
            display: block;
            text-align: center;
        }

        nav a:hover {
            background-color: black; 
        }

       

nav a.nav-button {
    text-decoration: none;
    padding: 15px 20px;
    margin-bottom: 10px;
    border-radius: 30px;
    background-color: rebeccapurple; /* Green color */
    display: block;
    text-align: center;
    margin-top: 20px;
}

nav a.nav-button:hover {
    background-color: black; 
}


button.profile-button {
    width: 120px;
    background-color: rebeccapurple;
    color: wheat;
    padding: 10px;
    border: black;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    margin-left: auto;
    margin-right: -210px; 
    margin-top: -40px; 
}

button.profile-button:hover {
    background-color: black;
}

#profile-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px; 
    position: relative;
}

#profile-image-container {
    position: relative;
}

#profile-image {
    width: 150px;
    height: 150px; 
    object-fit: cover; 
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
}

#profile-container button.profile-button {
    position: absolute;
    top: 0;
}



        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover {
            color: black;
            cursor: pointer;
        }

        #activitiesForm {
            margin-top: 20px;
            background-color: bisque;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #activitiesForm label {
            display: block;
            margin-bottom: 10px;
            color: rebeccapurple;
        }

        #activitiesForm input[type="text"],
        #activitiesForm textarea,
        #activitiesForm input[type="date"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        #activitiesForm input[type="submit"] {
            background-color: rebeccapurple; /* Pink color */
            color: white;
            padding: 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            width: 100%;
        }

        #activitiesForm input[type="submit"]:hover {
            background-color: black; /* Darker pink color on hover */
        }

        #activitiesForm textarea[name="activity_description"],
        #activitiesForm input[type="date"] {
            height: 50px;
        }
    </style>
</head>
<body>
    <header>
        <img src="mylogo.png" alt="Logo de Votre École">
    </header>

    <main>
        <h2>Bienvenue dans votre espace Enseignant</h2>
        <p>Explorez les fonctionnalités et suivez votre progression académique dans l'université ueuromed de fes.</p>
        <p>Si vous avez des questions, n'hésitez pas à contacter le support @dooogha.</p>
    </main>
    <div id="profile-container">
            <div id="profile-image-container">
                <?php if (!empty($profileImage)) : ?>
                    <img id="profile-image" src="<?php echo $profileImage; ?>" alt="<?php echo $altText; ?>">
                <?php else : ?>
                    <img id="profile-image" src="default-profile-image.jpg" alt="Default Profile Image">
                <?php endif; ?>
            </div>
            <button class="profile-button">
                <?php echo "$teacherFirstName $teacherLastName"; ?>
            </button>
        </div>

        </div>


       
    <nav>
    <a href="planning_teacher.php" class=nav-button><i class="fas fa-calendar-alt"></i> Planning</a>
    <a href="absences_teacher.php" class=nav-button><i class="fas fa-times-circle"></i> Gérer Absences</a>
    <a href="grades_teacher.php" class=nav-button><i class="fas fa-clipboard"></i> Gérer Notes</a>
    <a href="schedule_management.php" class=nav-button><i class="fas fa-edit"></i> Modifier Planning Étudiants</a>
    <a href="projet.php" class=nav-button><i class="fas fa-tasks"></i> Encadrer Projets</a>
    <a href="view_activities.php" class=nav-button><i class="fas fa-eye"></i> Voir Activités Parascolaires Étudiants</a>
    <a href="profile.php" class=nav-button><i class="fas fa-user"></i> Profil</a>
    <a href="login.php"><i class="fas fa-sign-out-alt" class=nav-button></i> Se Déconnecter</a>
</nav>





    </aside>

    <main>
    <div>
           


        <section id="activitiesSection" style="display: none;">
            <h2 id="activitiesTitle" style="display: none;">Vos Activités Parascolaires</h2>
            <table border="1" id="activitiesTable">
                <thead>
                    <tr>
                        <th>Activité ID</th>
                        <th>Nom de l'activité</th>
                        <th>Description</th>
                        <th>Date d'ajout</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($teacherActivitiesData as $row) {
                        echo "<tr>
                                  <td>{$row['id_activity']}</td>
                                  <td>{$row['activity_name']}</td>
                                  <td>{$row['description']}</td>
                                  <td>{$row['timestamp']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <form id="activitiesForm" action="teacher.php" method="post" style="display: none;">
                <label for="activity_name">Nom de l'activité:</label>
                <input type="text" name="activity_name" required><br>

                <label for="activity_description">Description:</label>
                <textarea name="activity_description" required></textarea><br>

                <label for="activity_date">Date d'ajout:</label>
                <input type="date" name="activity_date" required><br>

                <input type="submit" value="Ajouter une activité">
            </form>
        </section>

        <div id="chatModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeChatModal()">&times;</span>
                <h2>Chat here--></h2>
                <div id="chatMessages"></div>
                <input type="text" id="messageInput" placeholder="Type your message...">
                <select id="recipientSelect">
                    <option value="student">Lina</option>
                    <option value="teacher">Teacher</option>
                </select>
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>
    </main>

    <script>
        // Reste du code JavaScript...
    </script>
</body>
</html>
