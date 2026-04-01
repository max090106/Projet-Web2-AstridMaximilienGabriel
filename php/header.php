<?php session_start(); ?>

<header>
    <h1>
        <img src="../img/logo-efrei.png" id="logo" width="430" height="120">
        DEPARTEMENT INFORMATIQUE DE L'EFREI
    </h1>
    
    <nav>
        <a href="Accueil.php">Accueil</a>
        <a href="Formations.php">Formations</a>
        <a href="Temoignages.php">Témoignages</a>
        <a href="Apropos.php">A propos</a>
        <a href="Equipes.php">Equipes</a>
    </nav>

    <?php if(isset($_SESSION['pseudo'])): ?>
        <span><?php echo $_SESSION['pseudo']; ?></span>
    <?php endif; ?>
</header>