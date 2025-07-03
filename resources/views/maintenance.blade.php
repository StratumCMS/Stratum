<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-background text-foreground dark:bg-[#0f172a] dark:text-white flex items-center justify-center min-h-screen p-6">
<div class="text-center max-w-xl">
    <div class="text-6xl mb-6 text-primary">
        🛠️
    </div>
    <h1 class="text-3xl font-bold mb-4">
        {{ setting('maintenance_title', 'Site en maintenance') }}
    </h1>
    <p class="text-muted-foreground mb-6">
        {{ setting('maintenance_message', 'Nous effectuons actuellement une maintenance. Merci de revenir plus tard.') }}
    </p>
</div>
</body>
</html>
