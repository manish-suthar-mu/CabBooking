<!-- Smart Cab Booking System - Driver's Ride Receipt Page -->
<!-- Location: views/driver/receipt.php -->

<div class="container py-4">
    <div class="row justify-content-center">
        
        <!-- Booking Receipt Invoice for Driver -->
        <div class="col-md-6 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <i class="bi bi-file-earmark-check text-success fs-1"></i>
                    <h4 class="brand-font text-dark mt-2 mb-0">Ride Completed Successfully</h4>
                    <span class="text-muted small">Booking Ref: #<?= $booking['id'] ?></span>
                </div>

                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>Passenger Name:</span>
                    <span class="fw-semibold text-dark"><?= e($user['name'] ?? 'Customer') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>Route Traveled:</span>
                    <span class="fw-semibold text-dark text-end" style="max-width: 180px;"><?= e($booking['pickup_location']) ?> to <?= e($booking['drop_location']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 small text-muted">
                    <span>Distance (Simulated):</span>
                    <span class="fw-semibold text-dark"><?= e($booking['distance']) ?> km</span>
                </div>

                <div class="card bg-light border-0 rounded-3 p-3 text-center mb-4">
                    <span class="text-muted small d-block">TOTAL FARE COLLECTED</span>
                    <h2 class="brand-font text-success fw-bold mb-0 mt-1">₹<?= number_format($booking['actual_fare'] ?? $booking['estimated_fare'], 2) ?></h2>
                    <div class="text-muted small mt-1">Your Earnings: ₹<?= number_format(($booking['actual_fare'] ?? $booking['estimated_fare']) * 0.85, 2) ?> (85%)</div>
                </div>
                
                <a href="index.php?controller=driver&action=dashboard" class="btn btn-primary-custom w-100 py-3 fs-6">
                    <i class="bi bi-house me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

    </div>
</div>
