<?php
if (!isset($_SESSION['user'])) {
    header('Location: index.php?action=login');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Sales - UtangListo</title>
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
            <a href="index.php?action=customers">Customers</a>
            <a href="index.php?action=inventory">Products</a>
            <a class="active" href="index.php?action=sales">Transaction</a>
            <a href="index.php?action=logout">Log Out</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Transaction</h1>
                <p class="text-muted">Record a sale and choose cash or credit.</p>
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
            <div class="col-lg-5">
                <div class="card card-custom form-card">
                    <div class="card-body">
                        <h5 class="card-title">Record Purchase</h5>
                        <form method="POST" action="index.php?action=sales">
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" placeholder="Juan Dela Cruz" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Product</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Select product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>">
                                            <?php echo htmlspecialchars($product['name']); ?> — ₱<?php echo number_format($product['price'], 2); ?> (<?php echo $product['stock']; ?> left)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Save Purchase</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Recent Transactions</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                        <th>Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($sales)): ?>
                                        <tr><td colspan="7" class="text-center">No transactions yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($sales as $sale): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                                                <td><?php echo $sale['quantity']; ?></td>
                                                <td>₱<?php echo number_format($sale['total_amount'], 2); ?></td>
                                                <td><?php echo ucfirst($sale['payment_method']); ?></td>
                                                <td>₱<?php echo number_format($sale['outstanding_amount'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
