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
        
        <form id="signin-form">
            <div class="form-group">
                <input type="email" id="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" placeholder="Password" required>
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

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
    import { getAuth, signInWithEmailAndPassword, signInAnonymously, signInWithCustomToken } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

    const firebaseConfig = typeof __firebase_config !== 'undefined' 
        ? JSON.parse(__firebase_config) 
        : { apiKey: "DEMO_API_KEY", authDomain: "DEMO_AUTH_DOMAIN", projectId: "DEMO_PROJECT_ID" };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) {
        signInWithCustomToken(auth, __initial_auth_token).catch(error => {
            console.error("Custom token sign-in error:", error);
            signInAnonymously(auth);
        });
    } else {
        signInAnonymously(auth);
    }

    const signinForm = document.getElementById('signin-form');
    const errorMessageDiv = document.getElementById('error-message');

    signinForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        errorMessageDiv.style.display = 'none';

        signInWithEmailAndPassword(auth, email, password)
            .then((userCredential) => {
                window.location.href = 'index.php';
            })
            .catch((error) => {
                errorMessageDiv.textContent = 'Email atau password salah. Silakan coba lagi.';
                errorMessageDiv.style.display = 'block';
                console.error("Sign in error:", error);
            });
    });
</script>

</body>
</html>
