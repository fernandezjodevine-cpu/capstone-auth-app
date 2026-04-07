<?php
if (!isset($_SESSION['user'])) {
    header('Location: index.php?action=login');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard - UtangListo</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo">
                <img src="assets/images/logo.png" alt="UtangListo Logo" />
            </div>
            <div class="brand-text">UtangListo</div>
        </div>
        <nav class="sidebar-nav">
            <a class="active" href="index.php?action=home">Dashboard</a>
            <a href="index.php?action=customers">Customers</a>
            <a href="index.php?action=inventory">Products</a>
            <a href="index.php?action=sales">Transaction</a>
            <a href="index.php?action=logout">Log Out</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Welcome!</h1>
                <p class="text-muted">Good to see you again, <?php echo htmlspecialchars($_SESSION['user']['firstname']); ?>.</p>
            </div>
            <div class="topbar-actions">
                <a href="index.php?action=logout">Sign Out</a>
            </div>
        </div>

        <section class="grid-cards">
            <div class="dashboard-card">
                <small>Total Outstanding Balance</small>
                <strong>₱ <?php echo number_format($dashboard['totalOutstanding'], 2); ?></strong>
            </div>
            <div class="dashboard-card">
                <small>Total Customers</small>
                <strong><?php echo number_format($dashboard['totalCustomers']); ?></strong>
            </div>
            <div class="dashboard-card">
                <small>Payments Today</small>
                <strong>₱ <?php echo number_format($dashboard['paymentsToday'], 2); ?></strong>
            </div>
        </section>

        <div class="card card-custom">
            <div class="card-body">
                <h5 class="card-title">Quick Actions</h5>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-primary" href="index.php?action=sales">Record Purchase</a>
                    <a class="btn btn-secondary" href="index.php?action=inventory">Restock Inventory</a>
                    <a class="btn btn-secondary" href="index.php?action=customers">Manage Credits</a>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
