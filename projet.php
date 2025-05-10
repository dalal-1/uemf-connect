<?php
session_start();
include 'config.php';

$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['role'] ?? null;


$tasksSql = "SELECT project_tasks.*, modules.module_name 
              FROM project_tasks 
              LEFT JOIN modules ON project_tasks.module_id = modules.id_module";
$tasksResult = $connexion->query($tasksSql);

$projectTasks = [];
while ($task = $tasksResult->fetch_assoc()) {
    $taskId = $task['id_task'];

    
    $studentsAffirmedSql = "SELECT s.first_name, s.last_name
                            FROM students s
                            JOIN students_affirmed_tasks sat ON s.id_student = sat.student_id
                            WHERE sat.task_id = $taskId";
    $studentsAffirmedResult = $connexion->query($studentsAffirmedSql);

    
    $task['affirmed_students'] = $studentsAffirmedResult->fetch_all(MYSQLI_ASSOC);

    $projectTasks[] = $task;
}


if ($userRole == 'teacher' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskDescription = $_POST['task_description'] ?? '';
    $module = $_POST['module'] ?? '';

    
    $insertTaskSql = "INSERT INTO project_tasks (description, module_id) VALUES ('$taskDescription', '$module')";

    if ($connexion->query($insertTaskSql) === TRUE) {
        
        header("Location: projet.php");
        exit();
    } else {
        
        echo "Error: " . $connexion->error;
    }
}


if ($userRole == 'student' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'] ?? '';

    
    $updateTaskSql = "UPDATE project_tasks SET completed = 1 WHERE id_task = $taskId";

    if ($connexion->query($updateTaskSql) === TRUE) {
        
        $checkAffirmationSql = "SELECT * FROM students_affirmed_tasks WHERE student_id = '$userId' AND task_id = '$taskId'";
        $checkResult = $connexion->query($checkAffirmationSql);

        if ($checkResult->num_rows == 0) {
            $insertAffirmationSql = "INSERT INTO students_affirmed_tasks (student_id, task_id) VALUES ('$userId', '$taskId')";
            $connexion->query($insertAffirmationSql);
        }
    }

    header("Location: projet.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Projet</title>
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
        }

        h2 {
            color: #4e2a8e;
            margin-bottom: 20px;
        }

        .task-list {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            border: 2px solid #4e2a8e;
        }

        th, td {
            border: 1px solid #4e2a8e;
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

        .task-form {
            margin-top: 20px;
            text-align: center;
        }

        .task-form label {
            display: block;
            margin-bottom: 8px;
            color: #4e2a8e;
            font-weight: bold;
        }

        .task-form input, .task-form select {
            padding: 10px;
            margin-bottom: 12px;
            width: calc(100% - 24px);
            box-sizing: border-box;
        }

        .task-form input[type="submit"] {
            background-color: #4e2a8e;
            color: #fff;
            cursor: pointer;
            border: none;
            padding: 12px;
            border-radius: 4px;
            width: auto;
        }

        .task-form input[type="submit"]:hover {
            background-color: #301966;
        }

        /* Style for the dropdown */
        select {
            padding: 10px;
            margin-bottom: 12px;
            width: calc(100% - 24px);
            box-sizing: border-box;
            background-color: #fff;
            color: #4e2a8e;
            border: 1px solid #4e2a8e;
            border-radius: 4px;
            appearance: none;
            -webkit-appearance: none;
            text-indent: 1px;
            text-overflow: '';
            cursor: pointer;
        }

        /* Custom arrow for the dropdown */
        select::after {
            content: '\25BC';
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
            color: black;
        }

        
        select:hover, select:focus {
            border-color: black;
        }
    </style>
</head>
<header>
    <?php include 'home.php'; ?>
</header>
<body>
    <main>
        <h2>Gestion de Projet</h2>

        
        <?php if (!empty($projectTasks)) : ?>
            <table class="task-list">
                <thead>
                    <tr>
                        <th>Module</th>
                        <?php if ($userRole == 'teacher') : ?>
                            <th>Étudiants qui ont affirmé</th>
                        <?php endif; ?>
                        <?php if ($userRole == 'student') : ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projectTasks as $task) : ?>
                        <tr>
                            <td><?php echo $task['module_name']; ?></td>
                            <?php if ($userRole == 'teacher') : ?>
                                <td>
                                   
                                    <?php if (!empty($task['affirmed_students'])) : ?>
                                        <ul>
                                            <?php foreach ($task['affirmed_students'] as $affirmedStudent) : ?>
                                                <li><?php echo $affirmedStudent['first_name'] . ' ' . $affirmedStudent['last_name']; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <p>Aucun étudiant n'a affirmé cette tâche.</p>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <?php if ($userRole == 'student') : ?>
                                <td>
                                    <form method="post" action="projet.php">
                                        <label for="task_id"></label>
                                        <select name="task_id" required>
                                            <option value="" disabled selected>Sélectionnez une tâche</option>
                                            <?php
                                            $tasksForModuleSql = "SELECT id_task, description FROM project_tasks WHERE module_id = '{$task['module_id']}'";
                                            $tasksForModuleResult = $connexion->query($tasksForModuleSql);

                                            while ($taskForModule = $tasksForModuleResult->fetch_assoc()) {
                                                echo "<option value='{$taskForModule['id_task']}'>{$taskForModule['description']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <input class="task-form" type="submit" value="Affirmer la complétion">
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucune tâche de projet disponible pour le moment.</p>
        <?php endif; ?>

        
        <?php if ($userRole == 'teacher') : ?>
            <div class="task-form">
                <h3>Ajouter une Nouvelle Tâche</h3>
                <form method="post" action="projet.php">
                    <label for="module">Module:</label>
                    <select name="module" required>
                        <?php
                        $modulesSql = "SELECT * FROM modules";
                        $modulesResult = $connexion->query($modulesSql);
                        while ($module = $modulesResult->fetch_assoc()) {
                            echo "<option value='{$module['id_module']}'>{$module['module_name']}</option>";
                        }
                        ?>
                    </select>
                    <label for="task_description">Description de la Tâche:</label>
                    <input type="text" name="task_description" placeholder="Entrez la description de la tâche" required>
                    <input class="task-form" type="submit" value="Ajouter la Tâche">
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>

<?php

$connexion->close();
?>






