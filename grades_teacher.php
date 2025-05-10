<?php
session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;

// Fetch grades data for all students
$sql = "SELECT * FROM grades WHERE teacher_id = ?";
$stmt = $connexion->prepare($sql);
$stmt->bind_param("s", $teacherId);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the grades data into an array
$gradesData = array();
while ($row = $result->fetch_assoc()) {
    $gradesData[] = $row;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_grades'])) {
        // Validate and sanitize form data
        $newStudentId = $_POST['new_student_id'] ?? '';
        $newTdGrade = $_POST['new_td_grade'] ?? '';
        $newTpGrade = $_POST['new_tp_grade'] ?? '';
        $newProjetGrade = $_POST['new_projet_grade'] ?? '';
        $newAssiduiteGrade = $_POST['new_assiduite_grade'] ?? '';

        
        $moduleSql = "SELECT id_module, module_name FROM modules WHERE teacher_id = ?";
        $moduleStmt = $connexion->prepare($moduleSql);
        $moduleStmt->bind_param("s", $teacherId);
        $moduleStmt->execute();
        $moduleResult = $moduleStmt->get_result();

        if ($moduleResult->num_rows > 0) {
            
            while ($moduleData = $moduleResult->fetch_assoc()) {
                $newModuleId = $moduleData['id_module'];
                $newModuleName = $moduleData['module_name'];

                // Perform database insert
                $insertSql = "INSERT INTO grades (teacher_id, student_id, module_id, module_name, td_grade, tp_grade, projet_grade, assiduite_grade, total_grade)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $insertStmt = $connexion->prepare($insertSql);

                // Convert grades to numeric values
                $newTdGrade = (float)$newTdGrade;
                $newTpGrade = (float)$newTpGrade;
                $newProjetGrade = (float)$newProjetGrade;
                $newAssiduiteGrade = (float)$newAssiduiteGrade;

                // Calculate total grade
                $total_grade = ($newTdGrade + $newTpGrade + $newProjetGrade + $newAssiduiteGrade) / 4;

                $insertStmt->bind_param("sssssssss", $teacherId, $newStudentId, $newModuleId, $newModuleName, $newTdGrade, $newTpGrade, $newProjetGrade, $newAssiduiteGrade, $total_grade);

                if ($insertStmt->execute()) {
                    // New grades added successfully
                    header("Location: grades_teacher.php");
                    exit();
                } else {
                    // Handle error
                    echo "Error adding new grades: " . $insertStmt->error;
                }
            }
        } else {
            // Handle case where the teacher has no associated module
            echo "Error: Teacher has no associated module.";
        }
    } elseif (isset($_POST['update_grades'])) {
        // Retrieve student ID to update
        $updateStudentId = $_POST['student_id'] ?? '';

        // Build and execute the update query
        $updateSql = "UPDATE grades SET td_grade = ?, tp_grade = ?, projet_grade = ?, assiduite_grade = ?, total_grade = ? WHERE teacher_id = ? AND student_id = ?";
        $updateStmt = $connexion->prepare($updateSql);

        // Convert grades to numeric values
        $newTdGrade = (float)$_POST['new_td_grade'];
        $newTpGrade = (float)$_POST['new_tp_grade'];
        $newProjetGrade = (float)$_POST['new_projet_grade'];
        $newAssiduiteGrade = (float)$_POST['new_assiduite_grade'];

        // Calculate total grade
        $total_grade = ($newTdGrade + $newTpGrade + $newProjetGrade + $newAssiduiteGrade) / 4;

       
        $updateStmt->bind_param("sssssss", $newTdGrade, $newTpGrade, $newProjetGrade, $newAssiduiteGrade, $total_grade, $teacherId, $updateStudentId);

        if ($updateStmt->execute()) {
            // Grades updated successfully
            header("Location: grades_teacher.php");
            exit();
        } else {
            // Handle error
            echo "Error updating grades: " . $updateStmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes</title>
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

        input,
        select {
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
        <h2>Gestion des Notes :</h2>

        <!-- Display Grades -->
        <?php if (!empty($gradesData)) : ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Module ID</th>
                        <th>Module Name</th>
                        <th>TD Note</th>
                        <th>TP Note</th>
                        <th>Projet Note</th>
                        <th>Assiduité Note</th>
                        <th>Total Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gradesData as $row) : ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['module_id']; ?></td>
                            <td><?php echo $row['module_name']; ?></td>
                            <td><?php echo $row['td_grade']; ?></td>
                            <td><?php echo $row['tp_grade']; ?></td>
                            <td><?php echo $row['projet_grade']; ?></td>
                            <td><?php echo $row['assiduite_grade']; ?></td>
                            <td><?php echo $row['total_grade']; ?></td>
                            <td>
                                
<form method="post" action="grades_teacher.php">
    <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
    <label for="new_td_grade">Nouvelle Note TD:</label>
    <input type="text" name="new_td_grade" placeholder="Entrez la nouvelle note TD" required>

    <label for="new_tp_grade">Nouvelle Note TP:</label>
    <input type="text" name="new_tp_grade" placeholder="Entrez la nouvelle note TP" required>

    <label for="new_projet_grade">Nouvelle Note Projet:</label>
    <input type="text" name="new_projet_grade" placeholder="Entrez la nouvelle note Projet" required>

    <label for="new_assiduite_grade">Nouvelle Note Assiduité:</label>
    <input type="text" name="new_assiduite_grade" placeholder="Entrez la nouvelle note Assiduité" required>

    <input type="submit" name="update_grades" value="Modifier les Notes">
</form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucune note enregistrée pour le moment.</p>
        <?php endif; ?>

        <!-- Form to add new grades for other students -->
        <form method="post" action="grades_teacher.php">
            <h2>Ajouter des Notes pour d'autres Étudiants</h2>

            <label for="new_student_id">Étudiant ID:</label>
            <input type="text" name="new_student_id" placeholder="Entrez l'ID de l'étudiant" required>

            <label for="new_td_grade">Note TD:</label>
            <input type="text" name="new_td_grade" placeholder="Entrez la note TD" required>

            <label for="new_tp_grade">Note TP:</label>
            <input type="text" name="new_tp_grade" placeholder="Entrez la note TP" required>

            <label for="new_projet_grade">Note Projet:</label>
            <input type="text" name="new_projet_grade" placeholder="Entrez la note Projet" required>

            <label for="new_assiduite_grade">Note Assiduité:</label>
            <input type="text" name="new_assiduite_grade" placeholder="Entrez la note Assiduité" required>

            <input type="submit" name="add_grades" value="Ajouter des Notes">
        </form>
    </main>

</body>

</html>

<?php

$connexion->close();
?>

