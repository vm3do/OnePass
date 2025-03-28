<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de l'appareil</title>
</head>
<body>
    <h1>Bonjour,</h1>
    <p>Nous avons détecté une tentative de connexion depuis un nouvel appareil avec l'IP suivante : {{ $verificationLink }}</p>
    <p>Veuillez cliquer sur le lien ci-dessous pour confirmer votre connexion :</p>
    <a href="{{ $verificationLink }}">Cliquez ici pour valider votre connexion</a>
</body>
</html>
