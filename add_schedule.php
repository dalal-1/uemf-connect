<?php
session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;

// Fetch grades data for all students
$sql = "SELECT * FROM grades WHERE teacher_id = $teacherId";
$result = $connexion->query($sql);

// Fetch the grades data into an array
$gradesData = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $gradesData[] = $row;
    }
}

// Process form submission to update grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grades'])) {
    // Validate and sanitize form data
    $studentId = $_POST['student_id'] ?? '';
    $newTdGrade = $_POST['new_td_grade'] ?? '';
    $newTpGrade = $_POST['new_tp_grade'] ?? '';
    $newProjetGrade = $_POST['new_projet_grade'] ?? '';
    $newAssiduiteGrade = $_POST['new_assiduite_grade'] ?? '';

    // Convert grades to numeric values
    $newTdGrade = floatval($newTdGrade);
    $newTpGrade = floatval($newTpGrade);
    $newProjetGrade = floatval($newProjetGrade);
    $newAssiduiteGrade = floatval($newAssiduiteGrade);

    // Ensure grades are numeric
    if (!is_numeric($newTdGrade) || !is_numeric($newTpGrade) || !is_numeric($newProjetGrade) || !is_numeric($newAssiduiteGrade)) {
        echo "Error: Invalid grade values.";
        exit();
    }

    // Calculate total grade
    $total_grade = ($newTdGrade + $newTpGrade + $newProjetGrade + $newAssiduiteGrade) / 4;

    // Check if the student exists before updating grades
    $checkStudentSql = "SELECT * FROM students WHERE id_student = '$studentId'";
    $checkStudentResult = $connexion->query($checkStudentSql);

    if ($checkStudentResult->num_rows > 0) {
        // Perform database update
        $updateSql = "UPDATE grades 
                      SET td_grade = '$newTdGrade', 
                          tp_grade = '$newTpGrade', 
                          projet_grade = '$newProjetGrade', 
                          assiduite_grade = '$newAssiduiteGrade',
                          total_grade = $total_grade
                      WHERE teacher_id = $teacherId AND student_id = '$studentId'";

        if ($connexion->query($updateSql) === TRUE) {
            // Grades updated successfully
            header("Location: grades_teacher.php");
            exit();
        } else {
            // Handle error
            echo "Error: " . $connexion->error;
        }
    } else {
        // Student does not exist
        echo "Error: Student with ID $studentId does not exist.";
    }
}

// Process form submission to add new grades for other students
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_grades'])) {
    // Validate and sanitize form data
    $newStudentId = $_POST['new_student_id'] ?? '';
    $newTdGrade = $_POST['new_td_grade'] ?? '';
    $newTpGrade = $_POST['new_tp_grade'] ?? '';
    $newProjetGrade = $_POST['new_projet_grade'] ?? '';
    $newAssiduiteGrade = $_POST['new_assiduite_grade'] ?? '';

    // Convert grades to numeric values
    $newTdGrade = floatval($newTdGrade);
    $newTpGrade = floatval($newTpGrade);
    $newProjetGrade = floatval($newProjetGrade);
    $newAssiduiteGrade = floatval($newAssiduiteGrade);

    // Ensure grades are numeric
    if (!is_numeric($newTdGrade) || !is_numeric($newTpGrade) || !is_numeric($newProjetGrade) || !is_numeric($newAssiduiteGrade)) {
        echo "Error: Invalid grade values.";
        exit();
    }

    // Calculate total grade for new student
    $total_grade = ($newTdGrade + $newTpGrade + $newProjetGrade + $newAssiduiteGrade) / 4;

    // Check if the student exists before inserting new grades
    $checkStudentSql = "SELECT * FROM students WHERE id_student = '$newStudentId'";
    $checkStudentResult = $connexion->query($checkStudentSql);

    if ($checkStudentResult->num_rows > 0) {
        // Perform database insert
        $insertSql = "INSERT INTO grades (teacher_id, student_id, td_grade, tp_grade, projet_grade, assiduite_grade, total_grade)
                      VALUES ('$teacherId', '$newStudentId', '$newTdGrade', '$newTpGrade', '$newProjetGrade', '$newAssiduiteGrade', $total_grade)";

        if ($connexion->query($insertSql) === TRUE) {
            // New grades added successfully
            header("Location: grades_teacher.php");
            exit();
        } else {
            // Handle error
            if ($connexion->errno == 1452) {
                
                echo "Error: Cannot add or update a child row. Student ID does not exist in the students table.";
            } else {
                echo "Error: " . $connexion->error;
            }
        }
    } else {
        // Student does not exist
        echo "Error: Student with ID $newStudentId does not exist.";
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
    </style>
</head>
<header>
    <?php include 'home_teacher.php'; ?>
</header>
<body>
    <main>
        <h2>Gestion des Notes</h2>

        <!-- Display Grades -->
        <?php if (!empty($gradesData)) : ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Étudiant</th>
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
                            <td><?php echo $row['td_grade']; ?></td>
                            <td><?php echo $row['tp_grade']; ?></td>
                            <td><?php echo $row['projet_grade']; ?></td>
                            <td><?php echo $row['assiduite_grade']; ?></td>
                            <td><?php echo $row['total_grade']; ?></td>
                            <td>
                                <!-- Form for updating grades -->
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

<?php

$connexion->close();
?>
</body>
</html>
