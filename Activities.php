<?php
session_start();
include 'config.php';

$studentId = $_SESSION['user_id'] ?? null;

// Fetch activities data for the logged-in teacher
$sql = "SELECT * FROM extracurricular_activities WHERE student_id = $studentId";
$result = $connexion->query($sql);

// Fetch the activities data into an array
$activitiesData = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $activitiesData[] = $row;
    }
}

// Process form submission to add a new activity or delete an existing activity

// Process form submission to add a new activity
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize form data
    $activityName = $_POST['activity_name'] ?? '';
    $activityDescription = $_POST['activity_description'] ?? '';
    $activityDate = $_POST['activity_date'] ?? '';

    // Perform database insert
    $insertActivitySql = "INSERT INTO extracurricular_activities (student_id, activity_name, description, date_activite)
                          VALUES ($studentId, '$activityName', '$activityDescription', '$activityDate')";

    if ($connexion->query($insertActivitySql) === TRUE) {
        // Activity added successfully
        header("Location: Activities.php");
        exit();
    } else {
        // Handle error
        echo "Error: " . $connexion->error;
    }
}

// Process form submission to delete an activity
if (isset($_GET['delete_activity_id'])) {
    $deleteActivityId = $_GET['delete_activity_id'];
    $deleteActivitySql = "DELETE FROM extracurricular_activities WHERE id_activity = $deleteActivityId AND student_id = $studentId";

    if ($connexion->query($deleteActivitySql) === TRUE) {
        // Activity deleted successfully
        header("Location:Activities.php");
        exit();
    } else {
        // Handle error
        echo "Error: " . $connexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Activités Parascolaires</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        main {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center; 

        h2 {
            color: #4e2a8e; /* Purple color */
            margin-bottom: 20px;
        }

        table {
            width: 100%; 
            margin: 20px auto; 
            border-collapse: collapse;
        }

        th, td {

            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4e2a8e; /* Purple color */
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        form {
            margin-top: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4e2a8e; /* Purple color */
            font-weight: bold;
        }

        input, textarea, select {
            padding: 10px;
            margin-bottom: 12px;
            width: calc(100% - 24px);
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4e2a8e; /* Purple color */
            color: #fff;
            cursor: pointer;
            border: none;
            padding: 12px;
            border-radius: 4px;
            width: auto;
        }

        input[type="submit"]:hover {
            background-color: #301966; /* Darker shade of purple */
        }

        .delete-btn {
            background-color:brown; /* Blue color */
            color: #fff;
            cursor: pointer;
            border: none;
            padding: 8px;
            border-radius: 4px;
        }

        .delete-btn:hover {
            background-color: indigo; /* Darker shade of blue on hover */
        }

        
        td:nth-child(2) {
            max-width: 300px; 
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<header>
    <?php include 'home.php'; ?>
</header>
<body>
    <main>
        <h2>Vos Activités Parascolaires :</h2>

        <!-- Display Activities -->
        <?php if (!empty($activitiesData)) : ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Nom de l'activité</th>
                        <th>Description</th>
                        <th>Date de l'activité</th>
                        <th>Supprimer activité</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activitiesData as $activity) : ?>
                        <tr>
                            <td><?php echo $activity['activity_name']; ?></td>
                            <td><?php echo $activity['description']; ?></td>
                            <td><?php echo $activity['date_activite']; ?></td>
                            <td>
                                <form method="get" action="Activities.php">
                                    <input type="hidden" name="delete_activity_id" value="<?php echo $activity['id_activity']; ?>">
                                    <button type="submit" class="delete-btn">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucune activité enregistrée pour le moment.</p>
        <?php endif; ?>

        <!-- Form to Add Activity -->
        <form method="post" action="Activities.php">
            <label for="activity_name">Nom de l'activité:</label>
            <input type="text" name="activity_name" placeholder="Entrez le nom de l'activité" required>

            <label for="activity_description">Description:</label>
            <textarea name="activity_description" placeholder="Entrez la description de l'activité" required></textarea>

            <label for="activity_date">Date de l'activité:</label>
            <input type="date" name="activity_date" placeholder="Sélectionnez la date de l'activité" required>

            <input type="submit" name="add_activity_submit" value="Ajouter une Activité">
        </form>
    </main>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$connexion->close();
?>



