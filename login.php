<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Twitter Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Sign in to X</h1>
            <form id="loginForm">
                <div class="form-group">
                    <input type="text" name="username" class="form-input" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-primary">Log in</button>
            </form>
            <div id="error" style="color: var(--error-color); margin-top: 10px;"></div>
            <a href="register.php" class="auth-link">Don't have an account? Sign up</a>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'login');

            try {
                const res = await fetch('actions/auth.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    document.getElementById('error').textContent = data.message;
                }
            } catch (err) {
                console.error(err);
            }
        });
    </script>
</body>
</html>
