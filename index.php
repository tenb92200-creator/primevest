<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Grow Your Wealth with <span class="text-primary">PrimeVest</span></h1>
                <p class="lead text-muted mb-4">
                    A modern investment platform designed for everyone. Choose a plan, invest securely, and watch your money grow — all in a clean, professional interface.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-primary btn-lg">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary btn-lg">Get Started Free</a>
                        <a href="plans.php" class="btn btn-outline-primary btn-lg">View Plans</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <div class="card p-4 shadow-lg" style="max-width: 380px; margin: 0 auto;">
                    <div class="text-start">
                        <small class="text-muted">Your Balance</small>
                        <h2 class="fw-bold text-primary mb-3">$1,250.00</h2>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Active Investments</span>
                            <strong>3</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Profit</span>
                            <strong class="text-success">+$187.50</strong>
                        </div>
                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 65%"></div>
                        </div>
                        <small class="text-muted">65% of monthly goal</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose PrimeVest?</h2>
            <p class="text-muted">Everything you need to start investing confidently</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <div class="mb-3"><i class="bi bi-shield-check display-4 text-primary"></i></div>
                    <h5>Secure Platform</h5>
                    <p class="text-muted mb-0">Bank-level security practices and encrypted data storage.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <div class="mb-3"><i class="bi bi-graph-up-arrow display-4 text-primary"></i></div>
                    <h5>Transparent Returns</h5>
                    <p class="text-muted mb-0">Clear ROI percentages and fixed durations on every plan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <div class="mb-3"><i class="bi bi-lightning-charge display-4 text-primary"></i></div>
                    <h5>Instant Virtual Deposits</h5>
                    <p class="text-muted mb-0">Demo mode — deposit and withdraw instantly for testing.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Investment Plans</h2>
            <p class="text-muted">Choose the plan that matches your goals</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            require_once 'includes/db.php';
            $stmt = $pdo->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY min_amount ASC LIMIT 3");
            $plans = $stmt->fetchAll();
            $featured = 1;
            foreach ($plans as $i => $plan):
            ?>
            <div class="col-md-4">
                <div class="card card-plan h-100 <?php echo $i === $featured ? 'featured' : ''; ?>">
                    <div class="card-body p-4">
                        <h4 class="fw-bold"><?php echo sanitize($plan['name']); ?></h4>
                        <p class="text-muted small"><?php echo sanitize($plan['description']); ?></p>
                        <div class="my-4">
                            <span class="display-6 fw-bold text-primary"><?php echo number_format($plan['roi_percent'], 1); ?>%</span>
                            <span class="text-muted"> ROI</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Min: <?php echo formatMoney($plan['min_amount']); ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Max: <?php echo formatMoney($plan['max_amount']); ?></li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Duration: <?php echo $plan['duration_days']; ?> days</li>
                        </ul>
                        <a href="<?php echo isLoggedIn() ? 'plans.php' : 'register.php'; ?>" class="btn btn-primary w-100">
                            <?php echo isLoggedIn() ? 'Invest Now' : 'Get Started'; ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="plans.php" class="btn btn-outline-primary">View All Plans</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
