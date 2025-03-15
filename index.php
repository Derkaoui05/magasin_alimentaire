<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo $title; ?></h1>
        </header>
        
        <main>
            <div class="content">
                <p><?php echo $message; ?></p>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Magasin Alimentaire</p>
        </footer>
    </div>
</body>
</html>

