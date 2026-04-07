<?php
if (isset($_SESSION['user'])) {
    header('Location: index.php?action=home');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify Email - UtangListo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="auth-page">

<div class="auth-card shadow-sm" style="max-width: 500px; width: 100%;">
    <div class="card-body">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="UtangListo Logo" />
        </div>
        <h4 class="auth-header text-center">Verify Email</h4>
        <p class="auth-subtitle text-center">Complete your registration</p>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $alert_type; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($message) || $alert_type === 'alert-warning'): ?>
            <p class="text-muted">We've sent a verification link to your email. Click the link to verify your account.</p>
            
            <form method="POST" action="index.php?action=resend-verify">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input class="form-control" type="email" name="email" placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Resend Verification Email</button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="index.php?action=login" class="auth-link">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>
