<?php
session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;

// Fetch absences data for the logged-in teacher
$sql = "SELECT * FROM absences WHERE teacher_id = $teacherId";
$result = $connexion->query($sql);

// Fetch the absence data into an array
$absencesData = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $absencesData[] = $row;
    }
}

// Process form submission to add a new absence

// Process form submission to add a new absence
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize form data
    $module_id=$_POST['module_id'] ??'';
    $studentId = $_POST['student_id'] ?? '';
    $dateAbsence = $_POST['absence_date'] ?? '';
    $startAbsence = $_POST['start_absence'] ?? '';
    $endAbsence = $_POST['end_absence'] ?? '';
    $reason = $_POST['reason'] ?? '';

    // Perform database insert
    $insertSql = "INSERT INTO absences (module_id,teacher_id, student_id, absence_date, start_time, end_time, reason)
                  VALUES ('$module_id',$teacherId, '$studentId', '$dateAbsence', '$startAbsence', '$endAbsence', '$reason')";
    
    if ($connexion->query($insertSql) === TRUE) {
        // Absence added successfully
        header("Location: absences_teacher.php");
        exit();
    } else {
        // Handle error
        echo "Error: " . $connexion->error;
    }
}
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteSql = "DELETE FROM absences WHERE id_absence = $deleteId AND teacher_id = $teacherId";
    if ($connexion->query($deleteSql) === TRUE) {
        // Absence deleted successfully
        header("Location: absences_teacher.php");
        exit();
    } else {
        // Handle error
        echo "Error: " . $connexion->error;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
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
    }

    h2 {
        color: #4e2a8e; /* Purple color */
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
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

    input, select {
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
        background-color: brown; /* Blue color */
        color: #fff;
        cursor: pointer;
        border: none;
        padding: 8px;
        border-radius: 4px;
    }

    .delete-btn:hover {
        background-color: indigo; /* Darker shade of blue on hover */
    }

</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Absences</title>
    
</head>
<header>
    <?php include 'home_teacher.php'; ?>
</header>
<body>
    

    <main>
        <h2>Gestion des Absences :</h2>

        <!-- Display Absences -->
        <?php if (!empty($absencesData)) : ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Étudiant</th>
                        <th>Date d'Absence</th>
                        <th>start_time</th>
                        <th>end_time</th>
                        <th>Raison</th>
                        <th>Suprimer_absence</th>
                        <!-- Add additional fields as needed -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absencesData as $row) : ?>
                        <tr>
                           <td><?php echo $row['module_id']; ?></td>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['absence_date']; ?></td>
                            <td><?php echo $row['start_time']; ?></td>
                            <td><?php echo $row['end_time']; ?></td>

                            <td><?php echo $row['reason']; ?></td>
                          
                            <!-- Add additional cells as needed -->

                            <td>
                                <form method="get" action="absences_teacher.php">
                                    <input type="hidden" name="delete_id" value="<?php echo $row['id_absence']; ?>">
                                    <button type="submit" class="delete-btn">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                
            </table>
        <?php else : ?>
            <p>Aucune absence enregistrée pour le moment.</p>
        <?php endif; ?>

        <!-- Form to Add Absence -->
        <!-- Form to Add Absence -->
        <form method="post" action="absences_teacher.php">
    <label for="module_id">Module ID:</label>
    <input type="text" name="module_id" placeholder="Entrez l'ID du module" required>

    <label for="student_id">Étudiant ID:</label>
    <input type="text" name="student_id" placeholder="Entrez l'ID de l'étudiant" required>

    <label for="date_absence">Date d'Absence:</label>
    <input type="date" name="absence_date" placeholder="Sélectionnez la date d'absence" required>

    <label for="start_absence">Début d'Absence:</label>
    <input type="time" name="start_absence" step="1800" placeholder="Sélectionnez l'heure de début" required>

    <label for="end_absence">Fin d'Absence:</label>
    <input type="time" name="end_absence" step="1800" placeholder="Sélectionnez l'heure de fin" required>

    <label for="reason">Raison:</label>
    <input type="text" name="reason" placeholder="Entrez la raison de l'absence" required>

    <input type="submit" value="Ajouter une Absence">
</form>


    </main>
</body>
</html>

<?php
// Close the database connection
$connexion->close();
?>
