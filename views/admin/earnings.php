<!-- Smart Cab Booking System - Admin Financials Overview -->
<!-- Location: views/admin/earnings.php -->

<div class="container py-4">

    <!-- Financial Aggregates -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card-premium p-4 border-0 shadow-sm text-center bg-light">
                <i class="bi bi-wallet2 text-primary display-6 mb-2"></i>
                <h6 class="text-muted mb-1 font-weight-bold">Gross Platform Bookings Volume</h6>
                <h3 class="brand-font text-dark fw-bold mb-0">₹<?= number_format($total_revenue, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-premium p-4 border-0 shadow-sm text-center bg-primary text-white">
                <i class="bi bi-percent text-warning display-6 mb-2"></i>
                <h6 class="text-white-50 mb-1 font-weight-bold">Net Platform Revenue (15% Commission)</h6>
                <h3 class="brand-font text-white fw-bold mb-0">₹<?= number_format($total_commission, 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Earnings statement registry -->
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Financial Distributions Log</h4>
        
        <?php if (empty($earnings)): ?>
            <p class="text-muted text-center py-5 mb-0">No financial distributions recorded inside databases.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Distribution ID</th>
                            <th>Date & Time</th>
                            <th>Driver Name</th>
                            <th>Trip Details</th>
                            <th>Total Fare Amount</th>
                            <th>Deducted Commission (15%)</th>
                            <th>Net paid to Driver (85%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($earnings as $earn): ?>
                            <tr>
                                <td><strong>#EARN-<?= $earn['id'] ?></strong></td>
                                <td>
                                    <div class="small fw-semibold"><?= date('d M Y', strtotime($earn['created_at'])) ?></div>
                                    <div class="text-muted small"><?= date('h:i A', strtotime($earn['created_at'])) ?></div>
                                </td>
                                <td><div class="fw-semibold text-dark"><?= e($earn['driver_name']) ?></div></td>
                                <td>
                                    <div class="small text-muted"><?= e($earn['pickup_location']) ?> <i class="bi bi-arrow-right mx-1"></i> <?= e($earn['drop_location']) ?></div>
                                    <div class="text-muted small" style="font-size: 10px;">Distance: <?= e($earn['distance']) ?> km</div>
                                </td>
                                <td><strong>₹<?= number_format($earn['amount'], 2) ?></strong></td>
                                <td><span class="text-primary fw-semibold">+₹<?= number_format($earn['commission_deducted'], 2) ?></span></td>
                                <td><span class="text-success fw-semibold">₹<?= number_format($earn['net_amount'], 2) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
