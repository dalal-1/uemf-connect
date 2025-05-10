<?php
session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $scheduleId = $_POST['schedule_id'] ?? null;
    $dayOfWeek = $_POST['day_of_week'] ?? '';
    $moduleId = $_POST['module_id'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    $classroom = $_POST['classroom'] ?? '';
    $teacherId = $_POST['teacher_id'] ?? '';

    
    $moduleName = getModuleNameById($moduleId, $connexion);

    
    $teacherFullName = getTeacherFullNameById($teacherId, $connexion);

    
    $updateScheduleQuery = "UPDATE student_schedule
                            SET day_of_week = ?, module_id = ?, module_name = ?, start_time = ?, end_time = ?, classroom = ?, teacher_id = ?, teacher_first_name = ?, teacher_last_name = ?
                            WHERE id_schedule = ?";

    $updateScheduleStmt = $connexion->prepare($updateScheduleQuery);

    if (!$updateScheduleStmt) {
        echo "Prepare failed: (" . $connexion->errno . ") " . $connexion->error;
    }

   
    $teacherDetails = getTeacherDetailsById($teacherId, $connexion);

    $updateScheduleStmt->bind_param(
        "sssssssssi",
        $dayOfWeek,
        $moduleId,
        $moduleName,
        $startTime,
        $endTime,
        $classroom,
        $teacherId,
        $teacherDetails['first_name'],
        $teacherDetails['last_name'],
        $scheduleId
    );

    if (!$updateScheduleStmt->execute()) {
        echo "Execute failed: (" . $updateScheduleStmt->errno . ") " . $updateScheduleStmt->error;
    }

    
    header("Location: schedule_management.php");
    exit();
}


$scheduleId = $_GET['schedule_id'] ?? null;


$fetchScheduleQuery = "SELECT * FROM student_schedule WHERE id_schedule = ?";
$fetchScheduleStmt = $connexion->prepare($fetchScheduleQuery);
$fetchScheduleStmt->bind_param("i", $scheduleId);
$fetchScheduleStmt->execute();
$fetchScheduleResult = $fetchScheduleStmt->get_result();
$scheduleDetails = $fetchScheduleResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'Emploi du Temps</title>
    <link rel="stylesheet" href="modify.css">
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
        input[type="submit"] {
            padding: 10px;
            margin-bottom: 12px;
            width: calc(100% - 24px);
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4e2a8e;
            color: #fff;
            cursor: pointer;
            border: none;
            padding: 12px;
            border-radius: 4px;
            width: auto;
        }

        input[type="submit"]:hover {
            background-color: #301966;
        }
    </style>
    
</head>
<header>
    <?php include 'home_teacher.php'; ?>
</header>

<body>
    <main>
        <h2>Modifier l'Emploi du Temps</h2>

        <?php if ($scheduleDetails) : ?>
          
            <form method="post" action="modify_schedule.php">
                
                <input type="hidden" name="schedule_id" value="<?php echo $scheduleDetails['id_schedule']; ?>">

                <label for="day_of_week">Jour de la Semaine:</label>
                <select name="day_of_week" required>
                    <?php
                  
                    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');

                    
                    foreach ($daysOfWeek as $day) {
                        $selected = ($scheduleDetails['day_of_week'] == $day) ? 'selected' : '';
                        echo "<option value=\"$day\" $selected>$day</option>";
                    }
                    ?>
                </select>

                <label for="module_id">ID du Module:</label>
                <select name="module_id" required>
                    <?php
                   
                    $modulesQuery = "SELECT id_module, module_name FROM modules";
                    $modulesResult = $connexion->query($modulesQuery);

                   
                    while ($module = $modulesResult->fetch_assoc()) {
                        $selected = ($scheduleDetails['module_id'] == $module['id_module']) ? 'selected' : '';
                        echo "<option value=\"{$module['id_module']}\" $selected>{$module['module_name']}</option>";
                    }
                    ?>
                </select>

                <label for="teacher_id">ID Professeur:</label>
                <select name="teacher_id" required>
                    <?php
                   
                    $teachersQuery = "SELECT id_teacher, first_name, last_name FROM teachers";
                    $teachersResult = $connexion->query($teachersQuery);

                   
                    while ($teacher = $teachersResult->fetch_assoc()) {
                        $selected = ($scheduleDetails['teacher_id'] == $teacher['id_teacher']) ? 'selected' : '';
                        $fullName = $teacher['first_name'] . ' ' . $teacher['last_name'];
                        echo "<option value=\"{$teacher['id_teacher']}\" $selected>$fullName</option>";
                    }
                    ?>
                </select>

                <label for="start_time">Heure de Début:</label>
                <input type="time" name="start_time" value="<?php echo $scheduleDetails['start_time']; ?>" step="1800" required>

                <label for="end_time">Heure de Fin:</label>
                <input type="time" name="end_time" value="<?php echo $scheduleDetails['end_time']; ?>" step="1800" required>

                <label for="classroom">Salle de Classe:</label>
                <input type="text" name="classroom" value="<?php echo $scheduleDetails['classroom']; ?>" required>

                
                <input type="submit" value="Enregistrer les Modifications">
            </form>
        <?php else : ?>
            <p>L'horaire spécifié n'existe pas.</p>
        <?php endif; ?>
    </main>
</body>

</html>

<?php

$connexion->close();
?>






