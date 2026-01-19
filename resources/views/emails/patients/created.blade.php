<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro de paciente</title>
</head>
<body>
    <p>Hola {{ $patient->first_name }},</p>

    <p>
        Te confirmamos que tu registro fue realizado correctamente.
    </p>

    <p>
        Tus datos ya se encuentran cargados en nuestro sistema.
        En caso de ser necesario, nos pondremos en contacto contigo.
    </p>

    <p>
        Muchas gracias por confiar en nosotros.
    </p>

    <p>
        Saludos cordiales,<br>
        El equipo de atención
    </p>
</body>
</html>
