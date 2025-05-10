<?php
session_start();
include 'config.php';

$student_id = $_SESSION['user_id'] ?? null;

function getStudentAbsences($studentId) {
    global $connexion;
    $query = "SELECT absences.id_absence, absences.student_id, absences.module_id,
               absences.absence_date, absences.start_time, absences.end_time,
               absences.created_at, absences.updated_at, absences.teacher_id,
               absences.reason, absences.teacher_first_name, absences.teacher_last_name,
               modules.module_name
        FROM absences
        LEFT JOIN modules ON absences.module_id = modules.id_module
        WHERE absences.student_id = $studentId";
    $result = $connexion->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            echo "No absences found!";
        }
    } else {
        echo "Error executing query: " . $connexion->error;
    }

    return [];
}

$absenceData = getStudentAbsences($student_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absences Étudiant</title>
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

        th, td {
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

        input, select {
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
    <?php include 'home.php'; ?>
</header>

<body>
    <main>
        <h2>Vos absences</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Date</th>
                    <th>Heure de début</th>
                    <th>Heure de fin</th>
                    <th>Raison</th>
                    <th>Professeur</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($absenceData as $row) {
                    echo "<tr>
                            <td>{$row['module_name']}</td>
                            <td>{$row['absence_date']}</td>
                            <td>{$row['start_time']}</td>
                            <td>{$row['end_time']}</td>
                            <td>{$row['reason']}</td>
                            <td>{$row['teacher_first_name']} {$row['teacher_last_name']}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>


