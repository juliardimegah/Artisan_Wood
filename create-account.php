<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<header class="main-header">
    <div class="logo" onclick="window.location.href='index.php'">ARTISAN WOOD</div>
</header>

<main class="main-content">
    <div class="create-account-container">
        <button class="close-btn" onclick="window.location.href='index.php'">&times;</button>
        <h2>Create an account</h2>
        <p class="subtitle">Already have an account? <a href="signin.php">Sign in</a></p>
        <div id="error-message" class="error-message" style="display: none;"></div>

        <form id="create-account-form" action="server-register.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="first-name" name="first-name" placeholder="First name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="last-name" name="last-name" placeholder="Last name" required>
                </div>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password (min. 6 characters)" required>
            </div>

            <p class="terms">
                By selecting <strong>Create personal account</strong>, you agree to our 
                <a href="#">User Agreement</a> and acknowledge reading our <a href="#">Privacy Notice</a>.
            </p>

            <button type="submit" class="btn btn-primary">Create personal account</button>
        </form>

        <div class="divider">or continue with</div>

        <div class="social-buttons">
            <button class="btn btn-secondary">Google</button>
            <button class="btn btn-secondary">Facebook</button>
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
