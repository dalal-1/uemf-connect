
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
    <a href="student.php"><i class="fas fa-home"></i> Home</a>
    <a href="planning.php"><i class="fas fa-calendar-alt"></i> Planning</a>
    <a href="absences.php"><i class="fas fa-times-circle"></i> Absences</a>
    <a href="notes.php"><i class="fas fa-clipboard"></i> Notes</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="Activities.php"><i class="fas fa-star"></i> Activités Parascolaires</a>
    <a href="projet.php" class="nav-button"><i class="fas fa-tasks"></i> Projets</a>
    
    <a href="login.php"><i class="fas fa-sign-out-alt"></i> Se Déconnecter</a>
</nav>


