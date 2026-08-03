<!-- Smart Cab Booking System - User Booking History View -->
<!-- Location: views/user/history.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-clock-history text-primary me-2"></i>Your Booking History</h4>
        
        <?php if (empty($history)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar2-x text-muted display-1"></i>
                <h5 class="brand-font text-muted mt-3">No rides booked yet!</h5>
                <p class="text-muted small">Your complete list of booking records will appear here.</p>
                <a href="index.php?controller=user&action=dashboard" class="btn btn-primary-custom mt-3">Book Your First Ride</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date & Time</th>
                            <th>Route Details</th>
                            <th>Vehicle Type</th>
                            <th>Fare Amount</th>
                            <th>Trip Status</th>
                            <th>Payment Status</th>
                            <th>Review / Feedback</th>
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
                                    <div class="small"><i class="bi bi-geo-alt-fill text-success"></i> <strong>From:</strong> <?= e($ride['pickup_location']) ?></div>
                                    <div class="small mt-1"><i class="bi bi-geo-alt-fill text-danger"></i> <strong>To:</strong> <?= e($ride['drop_location']) ?></div>
                                    <div class="text-muted mt-1" style="font-size: 11px;">Est. Distance: <?= e($ride['distance']) ?> km</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= ucfirst(e($ride['vehicle_type'])) ?></span>
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
                                    <?php 
                                        $payBadge = $ride['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?= $payBadge ?>"><?= ucfirst(e($ride['payment_status'])) ?></span>
                                </td>
                                <td>
                                    <?php if ($ride['status'] === 'completed'): ?>
                                        <?php if (isset($ride['rating'])): ?>
                                            <!-- Review already submitted -->
                                            <div class="text-warning">
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <i class="bi <?= $i <= $ride['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                                <?php endfor; ?>
                                                <div class="text-muted small mt-1" style="font-size: 11px; font-style: italic;">"<?= e($ride['review_text']) ?>"</div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Review option button -->
                                            <button class="btn btn-sm btn-accent-custom px-3 py-1 fw-bold" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#reviewModal"
                                                    data-booking-id="<?= $ride['id'] ?>"
                                                    data-driver-name="<?= e($ride['driver_name'] ?? 'Driver') ?>">
                                                <i class="bi bi-star-fill"></i> Rate Driver
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
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

<!-- Star Rating & Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-light rounded-top-4 py-3">
                <h5 class="modal-title brand-font text-dark" id="reviewModalLabel"><i class="bi bi-star text-warning me-2"></i>Rate Your Trip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="reviewForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="booking_id" id="modal_booking_id">

                <div class="modal-body p-4 text-center">
                    <p class="text-muted">How was your ride experience with <strong id="modal_driver_name">Driver</strong>?</p>
                    
                    <!-- Stars Select -->
                    <div class="star-rating mb-3">
                        <input type="radio" id="star5" name="rating" value="5" required />
                        <label for="star5" title="5 stars"><i class="bi bi-star-fill"></i></label>
                        <input type="radio" id="star4" name="rating" value="4" />
                        <label for="star4" title="4 stars"><i class="bi bi-star-fill"></i></label>
                        <input type="radio" id="star3" name="rating" value="3" />
                        <label for="star3" title="3 stars"><i class="bi bi-star-fill"></i></label>
                        <input type="radio" id="star2" name="rating" value="2" />
                        <label for="star2" title="2 stars"><i class="bi bi-star-fill"></i></label>
                        <input type="radio" id="star1" name="rating" value="1" />
                        <label for="star1" title="1 star"><i class="bi bi-star-fill"></i></label>
                    </div>
                    
                    <div class="mb-3 text-start">
                        <label for="review_text" class="form-label fw-semibold text-dark">Leave a Comment (Optional)</label>
                        <textarea name="review_text" id="review_text" rows="3" class="form-control form-control-custom" placeholder="Share your experience (driving, cleanliness, route selection, etc.)"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light rounded-bottom-4 py-3">
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-accent-custom px-4 py-2">Submit Rating</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inject booking details dynamically into Review modal on show
    $('#reviewModal').on('show.bs.modal', function (e) {
        const button = $(e.relatedTarget);
        const bookingId = button.data('booking-id');
        const driverName = button.data('driver-name');
        
        $('#modal_booking_id').val(bookingId);
        $('#modal_driver_name').text(driverName);
    });
});
</script>
