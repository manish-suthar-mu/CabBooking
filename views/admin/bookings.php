<!-- Smart Cab Booking System - Admin Bookings Overview -->
<!-- Location: views/admin/bookings.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-calendar3 text-primary me-2"></i>Trip Bookings Registry</h4>
        
        <?php if (empty($bookings)): ?>
            <p class="text-muted text-center py-4 mb-0">No booking histories stored in database logs.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Date & Time</th>
                            <th>Passenger</th>
                            <th>Assigned Driver</th>
                            <th>Pickup / Drop Locations</th>
                            <th>Distance</th>
                            <th>Actual Fare</th>
                            <th>Trip Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $ride): ?>
                            <tr>
                                <td><strong>#<?= $ride['id'] ?></strong></td>
                                <td>
                                    <div class="small fw-semibold"><?= date('d M Y', strtotime($ride['created_at'])) ?></div>
                                    <div class="text-muted small"><?= date('h:i A', strtotime($ride['created_at'])) ?></div>
                                </td>
                                <td><div class="fw-semibold text-dark"><?= e($ride['user_name']) ?></div></td>
                                <td><?= $ride['driver_name'] ? '<div class="fw-semibold text-dark">' . e($ride['driver_name']) . '</div>' : '<span class="text-muted small">Not assigned</span>' ?></td>
                                <td>
                                    <div class="small text-muted"><span class="text-success">From:</span> <?= e($ride['pickup_location']) ?></div>
                                    <div class="small text-muted mt-0.5"><span class="text-danger">To:</span> <?= e($ride['drop_location']) ?></div>
                                </td>
                                <td><?= e($ride['distance']) ?> km</td>
                                <td><strong>₹<?= number_format($ride['actual_fare'] ?? $ride['estimated_fare'], 2) ?></strong></td>
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
                                    <span class="badge <?= $ride['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                        <?= ucfirst(e($ride['payment_status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
