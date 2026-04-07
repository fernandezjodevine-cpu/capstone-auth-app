<?php
if (!isset($_SESSION['user'])) {
    header('Location: index.php?action=login');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Customers - UtangListo</title>
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
            <a href="index.php?action=home">Dashboard</a>
            <a class="active" href="index.php?action=customers">Customers</a>
            <a href="index.php?action=inventory">Products</a>
            <a href="index.php?action=sales">Transaction</a>
            <a href="index.php?action=logout">Log Out</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Customers</h1>
                <p class="text-muted">Review outstanding accounts and receive payments.</p>
            </div>
            <div class="topbar-actions">
                <a href="index.php?action=home">Dashboard</a>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row gy-4">
            <div class="col-lg-7">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Outstanding Balances</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Credit Limit</th>
                                        <th>Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($customers)): ?>
                                        <tr><td colspan="3" class="text-center">No customer records yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($customers as $customer): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($customer['fullname']); ?></td>
                                                <td>₱<?php echo number_format($customer['credit_limit'], 2); ?></td>
                                                <td>₱<?php echo number_format($customer['outstanding_balance'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-custom form-card">
                    <div class="card-body">
                        <h5 class="card-title">Pay Outstanding Balance</h5>
                        <form method="POST" action="index.php?action=customers">
                            <div class="mb-3">
                                <label class="form-label">Select Customer</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Choose customer</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>">
                                            <?php echo htmlspecialchars($customer['fullname']); ?> — ₱<?php echo number_format($customer['outstanding_balance'], 2); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Amount</label>
                                <input type="number" step="0.01" min="1" name="payment_amount" class="form-control" placeholder="Enter payment amount" required>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Pay Balance</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
