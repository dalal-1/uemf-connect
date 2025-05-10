
<style>
    nav {
        display: flex;
        justify-content: space-around;
        align-items: center;
        background-color: rebeccapurple; /* Purple color */
        color: wheat;
        padding: 10px;
        box-sizing: border-box;
    }

    nav a {
        text-decoration: none;
        padding: 15px;
        border-radius: 5px;
        color: wheat;
        display: flex;
        align-items: center; /* Align text and icon vertically */
    }

    nav a i {
        margin-right: 5px;
    }

    nav a:hover {
        background-color: black; /* Darker purple color on hover */
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<nav>
    <a href="teacher.php"><i class="fas fa-home"></i> Home</a>
    <a href="planning_teacher.php" class=nav-button><i class="fas fa-calendar-alt"></i> Planning</a>
    <a href="absences_teacher.php" class=nav-button><i class="fas fa-times-circle"></i> Gérer Absences</a>
    <a href="grades_teacher.php" class=nav-button><i class="fas fa-clipboard"></i> Gérer Notes</a>
    <a href="schedule_management.php" class=nav-button><i class="fas fa-edit"></i> Modifier Planning Étudiants</a>
    <a href="projet.php" class=nav-button><i class="fas fa-tasks"></i> Encadrer Projets</a>
    <a href="view_activities.php" class=nav-button><i class="fas fa-eye"></i> Voir Activités Parascolaires Étudiants</a>
    <a href="teacher_profile.php" class=nav-button><i class="fas fa-user"></i> Profil</a>
    <a href="login.php"><i class="fas fa-sign-out-alt" class=nav-button></i> Se Déconnecter</a>
    
</nav>