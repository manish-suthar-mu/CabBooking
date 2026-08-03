<!-- Smart Cab Booking System - Driver Earnings View -->
<!-- Location: views/driver/earnings.php -->

<div class="container py-4">

    <!-- Earnings Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card-premium p-4 border-0 shadow-sm bg-light text-center">
                <i class="bi bi-wallet2 text-primary display-6 mb-2"></i>
                <h6 class="text-muted mb-1 font-weight-bold">Gross Bookings Fare</h6>
                <h3 class="brand-font text-dark fw-bold mb-0">₹<?= number_format($totalEarned, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium p-4 border-0 shadow-sm bg-light text-center">
                <i class="bi bi-percent text-danger display-6 mb-2"></i>
                <h6 class="text-muted mb-1 font-weight-bold">Platform Commission (15%)</h6>
                <h3 class="brand-font text-danger fw-bold mb-0">₹<?= number_format($totalCommission, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium p-4 border-0 shadow-sm bg-primary text-white text-center">
                <i class="bi bi-cash-stack text-warning display-6 mb-2"></i>
                <h6 class="text-white-50 mb-1 font-weight-bold">Your Net Earnings (85%)</h6>
                <h3 class="brand-font text-white fw-bold mb-0">₹<?= number_format($totalNet, 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Earnings Statement Table -->
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-file-earmark-text text-primary me-2"></i>Earnings Wallet Statement</h4>
        
        <?php if (empty($earningsLogs)): ?>
            <div class="text-center py-5">
                <i class="bi bi-wallet-fill text-muted display-1"></i>
                <h5 class="brand-font text-muted mt-3">No earnings logged!</h5>
                <p class="text-muted small">Your wallet transaction log history will appear here once trips are completed.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Trip Date</th>
                            <th>Route details</th>
                            <th>Fare Amount</th>
                            <th>Platform Commission (15%)</th>
                            <th>Net Deposited</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($earningsLogs as $log): ?>
                            <tr>
                                <td><strong>#EARN-<?= $log['id'] ?></strong></td>
                                <td>
                                    <div class="small fw-semibold"><?= date('d M Y', strtotime($log['trip_date'])) ?></div>
                                    <div class="text-muted small"><?= date('h:i A', strtotime($log['trip_date'])) ?></div>
                                </td>
                                <td>
                                    <div class="small"><i class="bi bi-geo-alt text-success"></i> <?= e($log['pickup_location']) ?></div>
                                    <div class="small mt-1"><i class="bi bi-geo-alt text-danger"></i> <?= e($log['drop_location']) ?></div>
                                </td>
                                <td>
                                    <span class="text-muted small">₹<?= number_format($log['amount'], 2) ?></span>
                                </td>
                                <td>
                                    <span class="text-danger small">-₹<?= number_format($log['commission_deducted'], 2) ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">₹<?= number_format($log['net_amount'], 2) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>
