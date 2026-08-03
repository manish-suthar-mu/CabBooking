<!-- Smart Cab Booking System - User Notifications View -->
<!-- Location: views/user/notifications.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm col-lg-8 mx-auto">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-bell text-primary me-2"></i>Notifications Inbox</h4>
        <p class="text-muted small mb-4">View notifications, simulated email dispatches, and SMS text message alerts delivered to you.</p>

        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash text-muted display-1"></i>
                <h5 class="brand-font text-muted mt-3">No notifications found!</h5>
                <p class="text-muted small">Notifications generated during bookings, payments, and cancellations appear here.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $n): ?>
                    <div class="list-group-item py-3 px-0 border-bottom border-light <?= $n['is_read'] ? '' : 'bg-light rounded-3 p-3 mb-2' ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">
                                    <?php if (!$n['is_read']): ?>
                                        <span class="badge bg-primary me-1">New</span>
                                    <?php endif; ?>
                                    <?= e($n['title']) ?>
                                </h6>
                                <p class="text-muted small mb-0"><?= e($n['message']) ?></p>
                            </div>
                            <span class="text-muted small" style="font-size: 11px;"><?= date('d M h:i A', strtotime($n['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
