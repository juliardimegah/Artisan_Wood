<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<header class="main-header">
    <div class="logo" onclick="window.location.href='index.php'">ARTISAN WOOD</div>
</header>

<main class="main-content">
    <div class="signin-container">
        <button class="close-btn" onclick="window.location.href='index.php'">&times;</button>
        <h2>Sign in to your account</h2>
        <p class="subtitle">Don't have an account yet? <a href="create-account.php">Create account</a></p>
        <div id="error-message" class="error-message" style="display: none;"></div>
        
        <form id="signin-form" action="server-login.php" method="POST">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Continue</button>
        </form>

        <div class="divider">or</div>
        <button class="btn btn-secondary">Continue with Google</button>
        <button class="btn btn-secondary">Continue with Facebook</button>

        <div class="stay-signed-in">
            <input type="checkbox" id="stay-signed">
            <label for="stay-signed">stay signed in</label>
        </div>
    </div>
</main>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if (error) {
        const errorMessageDiv = document.getElementById('error-message');
        errorMessageDiv.textContent = decodeURIComponent(error);
        errorMessageDiv.style.display = 'block';
    }
</script>

</body>
</html>
