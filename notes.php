<?php
session_start();
include 'config.php';

$student_id = $_SESSION['user_id'] ?? null;

function getStudentNotes($studentId)
{
    global $connexion;
    $query = "SELECT * FROM grades WHERE student_id = $studentId";
    $result = $connexion->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return []; // Return an empty array when no notes are found
        }
    } else {
        // Handle error
        die("Error executing query: " . $connexion->error);
    }
}

$notesData = getStudentNotes($student_id);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Étudiant</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
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
        }

        table {
            border: 1px solid #ccc;
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: left;
        }

       
        th {
            background-color: #4e2a8e;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<header>
    <?php include 'home.php'; ?>
</header>

<body>
    <main>
        <h2>Vos Notes :</h2>
        <table border="1" id="notesTable">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>td_note</th>
                    <th>tp_note</th>
                    <th>projet_note</th>
                    <th>assiduite_note</th>
                    <th>note_finale</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($notesData as $row) {
                    echo "<tr>
                            <td>{$row['module_name']}</td>
                            <td>{$row['td_grade']}</td>
                            <td>{$row['tp_grade']}</td>
                            <td>{$row['projet_grade']}</td>
                            <td>{$row['assiduite_grade']}</td>
                            <td>{$row['total_grade']}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>

</html>
