<?php
session_start();
include 'config.php';

$student_id = $_SESSION['user_id'] ?? null;






$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}



function getStudentProfile($studentId) {
    global $connexion;
    $query = "SELECT * FROM profile WHERE student_id = $studentId";
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

$profileData = getStudentProfile($student_id);


$studentName = $profileData['first_name'] ?? '';
$studentSurname = $profileData['last_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $matricul = isset($_POST['matricul']) ? $_POST['matricul'] : null;

    
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageName = basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        $image_path = $uploadPath;
    } else {
        $image_path = $profileData['image_path'];
    }

    $query = "UPDATE profile SET username = '$username', matricul = '$matricul', image_path = '$image_path' WHERE student_id = $student_id";
    $result = $connexion->query($query);

    if ($result) {
        echo "Profil mis à jour avec succès.";
        // Update profileData after successful update
        $profileData['username'] = $username;
        $profileData['matricul'] = $matricul;
        $profileData['image_path'] = $image_path;
    } else {
        echo "Erreur lors de la mise à jour du profil: " . $connexion->error;
    }
}


$profileImage = !empty($profileData['image_path']) ? $profileData['image_path'] : 'default-profile-image.jpg';
$altText = "$studentName $studentSurname";



function getStudentName($studentId) {
    global $connexion;
    $query = "SELECT first_name, last_name FROM students WHERE id_student = $studentId";
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
$userData = getStudentName($student_id);

$firstName = $userData['first_name'] ?? '';
$lastName = $userData['last_name'] ?? '';
$activitiesData = getStudentActivities($student_id);

function getStudentActivities($studentId) {
    global $connexion;
    $query = "SELECT * FROM extracurricular_activities WHERE student_id = $studentId";
    $result = $connexion->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            echo "No activities found!";
        }
    } else {
        echo "Error executing query: " . $connexion->error;
    }

    return [];
}
function sanitizeInput($input) {
    global $connexion;
    return mysqli_real_escape_string($connexion, $input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activity_name'], $_POST['activity_description'])) {
    $activityName = $_POST['activity_name'];
    $activityDescription = $_POST['activity_description'];

    $query = "INSERT INTO extracurricular_activities (student_id, activity_name, description) VALUES ('$student_id', '$activityName', '$activityDescription')";
    $result = $connexion->query($query);

    if ($result) {
        echo "Activité ajoutée avec succès.";
        
        $activitiesData = getStudentActivities($student_id);

        
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'activité : " . $connexion->error;
    }
}



function getActivityFields() {
    global $connexion;
    $tableName = 'extracurricular_activities'; 
    $query = "DESCRIBE $tableName";
    $result = $connexion->query($query);

    $fields = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row['Field'];
        }
    } else {
        echo "Erreur lors de la récupération des informations sur la table : " . $connexion->error;
    }

    return $fields;
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Étudiant</title>
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
            background-color:black; /* Darker pink color on hover */
        }

        i {
            margin-right: 10px;
        }

        aside {
            width: 300px;
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
            background-color:rebeccapurple; /* Green color */
            display: block;
            text-align: center;
        }

        nav a:hover {
            background-color: black; /* Darker green color on hover */
        }

        button {
            width: 120px;
            background-color:rebeccapurple; /* Green color */
            color: wheat;
            padding: 10px;
            border: black;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            margin-bottom: -40px;
            margin-left: 175px;
            margin-top: -15px;
        }

        button:hover {
            background-color: black; /* Darker green color on hover */
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
        
        <h2>Bienvenue dans votre espace Etudiant</h2>
        <p>Explorez les fonctionnalités et suivez votre progression académique dans l'université ueuromed de fes .</p>
        <p>Si vous avez des questions, n'hésitez pas à contacter le support @dooogha.</p>
        
    </main>

<aside>
<button>
        <?php echo "$firstName $lastName"; ?>
       
    </button>
        
   

<?php if (!empty($profileImage)) : ?>
        <p><?php echo "$studentName $studentSurname"; ?></p>
        <img src="<?php echo $profileImage; ?>" alt="<?php echo $altText; ?>" width="100">
    <?php else : ?>
        <p><?php echo "$studentName $studentSurname"; ?></p>
    <?php endif; ?>
    
    <nav>
    <a href="planning.php"><i class="fas fa-calendar-alt"></i> Planning</a>
    <a href="absences.php"><i class="fas fa-times-circle"></i> Absences</a>
    <a href="notes.php"><i class="fas fa-clipboard"></i> Notes</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="Activities.php"><i class="fas fa-star"></i> Activités Parascolaires</a>
    <a href="projet.php" class=nav-button><i class="fas fa-tasks"></i> Projets</a>
    <a href="javascript:void(0);" onclick="openChatModal()"><i class="fas fa-comment"></i> Chat</a>
    <a href="login.php"><i class="fas fa-sign-out-alt"></i> Se Déconnecter</a>
</nav>

</aside>

<main>
  



    

    <section id="profilSection" style="display: none;">
        <h2 id="profilTitle" style="display: none;">Votre Profil</h2>
        <form action="student.php" method="post" enctype="multipart/form-data">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" name="username" value="<?php echo $profileData['username'] ?? ''; ?>" required><br>

            <label for="matricul">Matricul:</label>
            <input type="text" name="matricul" value="<?php echo $profileData['matricul'] ?? ''; ?>" required><br>

            <label for="image">Image de profil:</label>
            <input type="file" name="image"><br>
            <?php if (!empty($profileImage)) : ?>
                <img src="<?php echo $profileImage; ?>" alt="<?php echo $altText; ?>" width="100">

            <?php endif; ?>

            <input type="submit" value="Mettre à jour le profil">
        </form>
    </section>
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
            foreach ($activitiesData as $row) {
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

    <form id="activitiesForm" action="student.php" method="post" style="display: none;">
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
            <option value="student"><?php echo $firstName  ?> </option>
            <option value="teacher">Teacher</option>
            
        </select>
        <button onclick="sendMessage()">Send</button>
    </div>
</div>
</main>

<script>

function toggleFormVisibility() {
        var form = document.getElementById('activitiesForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    function showSection(sectionId, tableId, titleId) {
    document.querySelectorAll('section, h2').forEach(item => item.style.display = 'none');

    document.getElementById(sectionId).style.display = 'block';
    document.getElementById(titleId).style.display = 'block';

    document.querySelectorAll('table').forEach(table => table.style.display = 'none');
    document.getElementById(tableId).style.display = 'table';

    // Ajout pour afficher le formulaire des activités parascolaires
    if (sectionId === 'activitiesSection') {
        document.getElementById('activitiesForm').style.display = 'block';
    } else {
        document.getElementById('activitiesForm').style.display = 'none';
    }
}

    function updateProfile() {
    var form = new FormData(document.getElementById('profileForm'));

   
    fetch('update_profile.php', {
        method: 'POST',
        body: form
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            
            document.getElementById('profileImage').src = data.imagePath;
            alert('Profil mis à jour avec succès.');
        } else {
            alert('Erreur lors de la mise à jour du profil.');
        }
    })
    .catch(error => console.error('Erreur AJAX:', error));
}


function openChatModal() {
        document.getElementById('chatModal').style.display = 'block';
    }

    function closeChatModal() {
        document.getElementById('chatModal').style.display = 'none';
    }

    function sendMessage() {
        var messageInput = document.getElementById('messageInput');
        var recipientSelect = document.getElementById('recipientSelect');
        var chatMessages = document.getElementById('chatMessages');

       
        var recipient = recipientSelect.value;
        var messageText = messageInput.value;

        
        var messageElement = document.createElement('div');
        messageElement.innerHTML = `<strong>${recipient}:</strong> ${messageText}`;

        
        chatMessages.appendChild(messageElement);

       
        messageInput.value = '';
    }


</script>
</body>
</html>


