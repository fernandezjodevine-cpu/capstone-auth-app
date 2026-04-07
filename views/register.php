<!DOCTYPE html>
<html>
<head>
<title>Register - UtangListo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="auth-page">

<div class="auth-card shadow-sm" style="max-width: 420px; width: 100%;">
    <div class="card-body">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="UtangListo Logo" />
        </div>
        <h4 class="auth-header text-center">Create Account</h4>
        <p class="auth-subtitle text-center">Join UtangListo Today</p>

        <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" action="index.php?action=register">
            <input class="form-control mb-2" type="text" name="firstname" placeholder="First Name" required>
            <input class="form-control mb-2" type="text" name="lastname" placeholder="Last Name" required>
            <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
            <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
            <button class="btn btn-primary w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="index.php?action=login" class="auth-link">Already have an account?</a>
        </div>
    </div>
</div>

</body>
</html>