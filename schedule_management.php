<?php
session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['modify_schedule'])) {
       
        $scheduleId = $_POST['schedule_id'] ?? null;
        if ($scheduleId) {
            header("Location: modify_schedule.php?schedule_id=" . urlencode($scheduleId));
            exit();
        }
    } elseif (isset($_POST['delete_schedule'])) {
        
        $scheduleId = $_POST['schedule_id'] ?? null;
        if ($scheduleId) {
            $deleteScheduleQuery = "DELETE FROM student_schedule WHERE id_schedule = ? AND teacher_id = ?";
            $deleteScheduleStmt = $connexion->prepare($deleteScheduleQuery);
            $deleteScheduleStmt->bind_param("si", $scheduleId, $teacherId);
            $deleteScheduleStmt->execute();
        }
    }
}


$classNamesQuery = "SELECT id_class, class_name FROM classes";
$classNamesResult = $connexion->query($classNamesQuery);
$classNames = $classNamesResult->fetch_all(MYSQLI_ASSOC);


$classId = $_POST['class_id'] ?? null;


$scheduleQuery = "SELECT * FROM student_schedule WHERE class_id = ?";
$scheduleStmt = $connexion->prepare($scheduleQuery);
$scheduleStmt->bind_param("i", $classId);
$scheduleStmt->execute();
$scheduleResult = $scheduleStmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Planning</title>

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
            color: #4e2a8e;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4e2a8e;
            font-weight: bold;
        }

        select,
        input[type="text"],
        input[type="submit"],
        button {
            padding: 10px;
            margin-bottom: 12px;
            width: calc(100% - 24px);
            box-sizing: border-box;
        }

        input[type="submit"],
        button {
            background-color: #4e2a8e;
            color: #fff;
            cursor: pointer;
            border: none;
            padding: 12px;
            border-radius: 4px;
            width: auto;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: #301966;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4e2a8e;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .action-buttons button {
            width: auto;
        }
    </style>
</head>
<header>
    <?php include 'home_teacher.php'; ?>
</header>

<body>
    <main>
        <h2>Gestion du Planning</h2>

        
        <form method="post" action="schedule_management.php">
            <label for="class_id">Sélectionnez la Classe:</label>
            <select name="class_id" required>
                <?php foreach ($classNames as $class) : ?>
                    <option value="<?php echo $class['id_class']; ?>" <?php echo ($class['id_class'] == $classId) ? 'selected' : ''; ?>>
                        <?php echo $class['class_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="retrieve_schedule" value="Afficher le Planning">
        </form>

        <?php if ($classId && isset($scheduleResult)) : ?>
           
            <form method="post" action="schedule_management.php">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Jour de la Semaine</th>
                            <th>Nom du Module</th>
                            <th>Heure de Début</th>
                            <th>Heure de Fin</th>
                            <th>Salle de Classe</th>
                            <th>ID du Professeur</th>
                            <th>Prénom du Professeur</th>
                            <th>Nom du Professeur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $scheduleResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['day_of_week']; ?></td>
                                <td><?php echo $row['module_name']; ?></td>
                                <td><?php echo $row['start_time']; ?></td>
                                <td><?php echo $row['end_time']; ?></td>
                                <td><?php echo $row['classroom']; ?></td>
                                <td><?php echo $row['teacher_id']; ?></td>
                                <td><?php echo $row['teacher_first_name']; ?></td>
                                <td><?php echo $row['teacher_last_name']; ?></td>
                                <td class="action-buttons">
                                    <form method="post" action="schedule_management.php">
                                        <input type="hidden" name="schedule_id" value="<?php echo $row['id_schedule']; ?>">
                                        <button type="submit" name="modify_schedule">Modifier</button>
                                    </form>
                                    <form method="post" action="schedule_management.php">
                                        <input type="hidden" name="schedule_id" value="<?php echo $row['id_schedule']; ?>">
                                        <button type="submit" name="delete_schedule" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée de planning?');">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </form>
        <?php endif; ?>
        <button onclick="window.location.href='add_plannig.php'">Ajouter Planning</button>
    </main>
</body>

</html>

<?php

$connexion->close();
?>
