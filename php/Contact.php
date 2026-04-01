<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Département informatique de l'efrei</title>
    <link rel="stylesheet" href="../css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="../js/formations.js"></script>
</head>

<body>
    <?php include ("header.php"); ?>
    <main>
        <form method="post" name="Contact" class="box" id="ContactId" onsubmit="return false">
            <fieldset id="Infos">
                <legend>Vos informations</legend>

                <p>
                    <label for="prenom">Prénom :</label><br>
                    <input id="prenom" type="text" name="prenom" placeholder="Prénom" required>
                </p>

                <p>
                    <label for="nom">Nom :</label><br>
                    <input id="nom" type="text" name="nom" placeholder="Nom" required>
                </p>

                <p>
                    <label for="email">Email :</label><br>
                    <input id="email" type="text" name="email" placeholder="Email" required>
                </p>

                <p>
                    <label for="statut">Statut :</label><br>
                    <select id="statut" name="statut" required>
                        <option value="" selected disabled>Choisir un statut</option>
                        <option value="Professeur">Professeur</option>
                        <option value="Eleve">Elève</option>
                        <option value="Intervenant">Intervenant</option>
                        <option value="Autres">Autres</option>
                    </select>
                </p>
            </fieldset>
            <fieldset class="Contact">
                <legend>Votre message</legend>
                <textarea name="message" rows="4"></textarea>
            </fieldset>

            <p>
                <input type="button" value="Envoyer">
            </p>
        </form>

    </main>
    <?php include ("footer.php"); ?>
</body>
</html>