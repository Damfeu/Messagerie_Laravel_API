<!DOCTYPE html>
<html>
<head>
    <title>Information de création de compte</title>
</head>
<body>
    <p>Bonjour,Bonsoir</p>
    @if($password)
        <p>Votre compte <strong>EcoCollecte</strong> a été créer avec succès. Voici votre mot de pass pour vous connecter: <strong>{{ $password }}</strong></p>

        
    @endif

    @if($authCode)
        <p>Your authentication code is: <strong>{{ $authCode }}</strong></p>
    @endif

    <p>Veuillez utiliser ce code pour vous connecter</p>
</body>
</html>
