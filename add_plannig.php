<?php
session_start();
include 'config.php';


$teacherId = $_SESSION['user_id'] ?? null;
$teacherDetails = getTeacherDetailsById($teacherId, $connexion);

$modulesQuery = "SELECT id_module, module_name FROM modules";
$modulesResult = $connexion->query($modulesQuery);

$teachersQuery = "SELECT id_teacher, first_name, last_name FROM teachers";
$teachersResult = $connexion->query($teachersQuery);

$classesQuery = "SELECT id_class, class_name FROM classes";
$classesResult = $connexion->query($classesQuery);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Nouveau Planning</title>
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
        input[type="time"],
        button {
            padding: 10px;
            margin-bottom: 12px;
            width: calc(100% - 24px);
            box-sizing: border-box;
        }

        button {
            background-color: #4e2a8e;
            color: #fff;
            cursor: pointer;
            border: none;
            padding: 12px;
            border-radius: 4px;
            width: auto;
        }

        button:hover {
            background-color: #301966;
        }
    </style>
</head>
<header>
    <?php include 'home_teacher.php'; ?>
</header>

<body>
    <main>
        <h2>Ajouter un Nouveau Planning</h2>

        <form method="post" action="process_add_schedule.php">
            <label for="class_id">Sélectionner une Classe:</label>
            <select name="class_id" required>
                <?php
                while ($class = $classesResult->fetch_assoc()) {
                    echo "<option value=\"{$class['id_class']}\">{$class['class_name']}</option>";
                }
                ?>
            </select>

            <label for="day_of_week">Jour de la Semaine:</label>
            <select name="day_of_week" required>
                <?php
                $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
                foreach ($daysOfWeek as $day) {
                    echo "<option value=\"$day\">$day</option>";
                }
                ?>
            </select>

            <label for="module_id">ID du Module:</label>
            <select name="module_id" required>
                <?php
                while ($module = $modulesResult->fetch_assoc()) {
                    echo "<option value=\"{$module['id_module']}\">{$module['module_name']}</option>";
                }
                ?>
            </select>

            <label for="teacher_id">ID Professeur:</label>
            <select name="teacher_id" required>
                <?php
                while ($teacher = $teachersResult->fetch_assoc()) {
                    $fullName = $teacher['first_name'] . ' ' . $teacher['last_name'];
                    echo "<option value=\"{$teacher['id_teacher']}\">$fullName</option>";
                }
                ?>
            </select>

            <label for="start_time">Heure de Début:</label>
            <input type="time" name="start_time" step="1800" required>

            <label for="end_time">Heure de Fin:</label>
            <input type="time" name="end_time" step="1800" required>

            <label for="classroom">Salle de Classe:</label>
            <input type="text" name="classroom" required>

            <button type="submit">Ajouter Planning</button>
        </form>
    </main>
</body>

</html>

<?php
$connexion->close();
?>

