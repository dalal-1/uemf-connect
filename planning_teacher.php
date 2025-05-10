<?php


session_start();
include 'config.php';

$teacherId = $_SESSION['user_id'] ?? null;



$sql = "SELECT teachers.id_teacher, teachers.first_name, teachers.last_name,
               teacher_schedule.module_name, teacher_schedule.day_of_week,
               teacher_schedule.start_time, teacher_schedule.end_time,
               teacher_schedule.classroom, teacher_schedule.class_name
        FROM teachers
        LEFT JOIN teacher_schedule ON teachers.id_teacher = teacher_schedule.teacher_id
        WHERE teachers.id_teacher = $teacherId";

$result = $connexion->query($sql);


$scheduleData = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $scheduleData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Enseignant</title>
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
        }

        h2 {
            text-align: center;
            color:  #4e2a8e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4e2a8e; /* Purple color */
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        p {
            margin-top: 20px;
            color: #555;
        }
    </style>
</head>
<header>
    <?php include 'home_teacher.php'; ?>
</header>
<body>
    
    <main>
        <h2>Votre Planning :</h2>

        <?php if (!empty($scheduleData)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Heure de début</th>
                        <th>Heure de fin</th>
                        <th>Module</th>
                        <th>Salle</th>
                        <th>Class_name</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scheduleData as $row) : ?>
                        <tr>
                            <td><?php echo $row['day_of_week']; ?></td>
                            <td><?php echo $row['start_time']; ?></td>
                            <td><?php echo $row['end_time']; ?></td>
                            <td><?php echo $row['module_name']; ?></td>
                            <td><?php echo $row['classroom']; ?></td>
                            <td><?php echo $row['class_name']; ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucune donnée de planning disponible pour le moment.</p>
        <?php endif; ?>
    </main>
</body>
</html>
