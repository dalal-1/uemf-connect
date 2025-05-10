<?php
session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;


$studentsSql = "SELECT * FROM students";
$studentsResult = $connexion->query($studentsSql);


$studentsActivities = array();
while ($student = $studentsResult->fetch_assoc()) {
    $studentId = $student['id_student'];
    $activitiesSql = "SELECT * FROM extracurricular_activities WHERE student_id = $studentId";
    $activitiesResult = $connexion->query($activitiesSql);

    $studentsActivities[$studentId]['name'] = array(
        'first_name' => $student['first_name'],
        'last_name' => $student['last_name']
    );
    $studentsActivities[$studentId]['activities'] = $activitiesResult->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Activités des Étudiants</title>
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

        .student-box {
            background-color: #4e2a8e;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            border:2px solid black;
            width: 50%;
            margin-left: 500px;
           
        }

        table {
            width: 100%; 
            margin: 20px auto; 
            border-collapse: collapse;
            border: 2px solid black; 
        }

        th, td {
            border: 1px solid black; 
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
    </style>
</head>
<header>
    <?php include 'home.php'; ?>
</header>
<body>
    <main>
        <h2>Voir Activités des Étudiants :</h2>

        <?php if (!empty($studentsActivities)) : ?>
            <?php foreach ($studentsActivities as $studentData) : ?>
                <div class="student-box">
                    <h3><?php echo $studentData['name']['first_name'] . ' ' . $studentData['name']['last_name']  ; ?></h3>
                </div>

                <?php if (!empty($studentData['activities'])) : ?>
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Nom de l'activité</th>
                                <th>Description</th>
                                <th>Date de l'activité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($studentData['activities'] as $activity) : ?>
                                <tr>
                                    <td><?php echo $activity['activity_name']; ?></td>
                                    <td><?php echo $activity['description']; ?></td>
                                    <td><?php echo $activity['date_activite']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>Aucune activité enregistrée pour cet étudiant.</p>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Aucun étudiant trouvé.</p>
        <?php endif; ?>
    </main>
</body>
</html>

