<!-- Smart Cab Booking System - Driver Ride History View -->
<!-- Location: views/driver/history.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-clock-history text-primary me-2"></i>Your Trip History</h4>
        
        <?php if (empty($history)): ?>
            <div class="text-center py-5">
                <i class="bi bi-journal-x text-muted display-1"></i>
                <h5 class="brand-font text-muted mt-3">No trips completed yet!</h5>
                <p class="text-muted small">Go online and accept passenger booking requests to start logging trips.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date & Time</th>
                            <th>Passenger Details</th>
                            <th>Route Details</th>
                            <th>Fare Earned</th>
                            <th>Trip Status</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $ride): ?>
                            <tr>
                                <td><strong>#<?= $ride['id'] ?></strong></td>
                                <td>
                                    <div class="small fw-semibold"><?= date('d M Y', strtotime($ride['created_at'])) ?></div>
                                    <div class="text-muted small"><?= date('h:i A', strtotime($ride['created_at'])) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($ride['user_name']) ?></div>
                                </td>
                                <td>
                                    <div class="small"><i class="bi bi-geo-alt text-success"></i> <strong>From:</strong> <?= e($ride['pickup_location']) ?></div>
                                    <div class="small mt-1"><i class="bi bi-geo-alt text-danger"></i> <strong>To:</strong> <?= e($ride['drop_location']) ?></div>
                                    <div class="text-muted mt-1" style="font-size: 11px;">Distance: <?= e($ride['distance']) ?> km</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">₹<?= number_format($ride['actual_fare'] ?? $ride['estimated_fare'], 2) ?></span>
                                    <div class="text-muted small" style="font-size: 10px;"><?= ucfirst(e($ride['payment_method'])) ?></div>
                                </td>
                                <td>
                                    <?php 
                                        $badgeColor = 'bg-warning text-dark';
                                        if ($ride['status'] === 'completed') $badgeColor = 'bg-success';
                                        if ($ride['status'] === 'cancelled') $badgeColor = 'bg-danger';
                                        if ($ride['status'] === 'ongoing') $badgeColor = 'bg-primary';
                                    ?>
                                    <span class="badge <?= $badgeColor ?>"><?= ucfirst(e($ride['status'])) ?></span>
                                </td>
                                <td>
                                    <?php if ($ride['status'] === 'completed' && isset($ride['rating'])): ?>
                                        <div class="text-warning small">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="bi <?= $i <= $ride['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">No rating</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>
