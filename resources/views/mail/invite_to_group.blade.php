<!DOCTYPE html>
<html>
<head>
    <title>Invitation à rejoindre le groupe</title>
</head>
<body>
    <h1>Bonjour {{ $inviteeName }},</h1>
    <p>Vous avez été invité à rejoindre le groupe <strong>{{ $groupName }}</strong> sur notre plateforme.</p>
    <p>Pour accepter l'invitation et vous inscrire, veuillez cliquer sur le lien ci-dessous :</p>
    <a href="{{ url('/register?email=' . urlencode($inviteeName)) }}">Accepter l'invitation</a>
    <p>Merci!</p>
</body>
</html>