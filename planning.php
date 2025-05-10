

<?php
session_start();
include 'config.php';

function getStudentSchedule($studentId) {
    global $connexion;

    
    $classQuery = "SELECT class_id FROM students WHERE id_student = $studentId";
    $classResult = $connexion->query($classQuery);

    if ($classResult) {
        $classRow = $classResult->fetch_assoc();
        $classId = $classRow['class_id'];

       
        $query = "SELECT ss.*, c.class_name
                  FROM student_schedule ss
                  JOIN classes c ON ss.class_id = c.id_class
                  WHERE ss.class_id = $classId";

        $result = $connexion->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC);
            } else {
                echo "No rows found in getStudentSchedule!";
            }
        } else {
            echo "Error executing query: " . $connexion->error;
        }
    } else {
        echo "Error executing class query: " . $connexion->error;
    }

    return [];
}

$student_id = $_SESSION['user_id'] ?? null;
$scheduleData = getStudentSchedule($student_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Plannig etudiant</title>
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
    <h2> Votre Planning :</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Jour</th>
                <th>Module</th>
                <th>Professeur</th>
                <th>Salle</th>
                <th>Heure de début</th>
                <th>Heure de fin</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($scheduleData as $row) {
                echo "<tr>
                        <td>{$row['day_of_week']}</td>
                        <td>{$row['module_name']}</td>
                        <td>{$row['teacher_first_name']} {$row['teacher_last_name']}</td>
                        <td>{$row['classroom']}</td>
                        <td>{$row['start_time']}</td>
                        <td>{$row['end_time']}</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
