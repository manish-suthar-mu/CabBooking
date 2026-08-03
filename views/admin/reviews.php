<!-- Smart Cab Booking System - Admin Reviews Overview -->
<!-- Location: views/admin/reviews.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm col-lg-10 mx-auto">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-star-fill text-warning me-2"></i>Passenger Ratings & Reviews</h4>
        
        <?php if (empty($reviews)): ?>
            <p class="text-muted text-center py-5 mb-0">No ratings or review feedbacks submitted by passengers.</p>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($reviews as $rev): ?>
                    <div class="list-group-item py-4 px-0 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Trip Booking #<?= $rev['booking_id'] ?></h6>
                                <span class="text-muted small">
                                    By: <strong><?= e($rev['user_name']) ?></strong> to Driver: <strong><?= e($rev['driver_name']) ?></strong>
                                </span>
                            </div>
                            
                            <!-- Star Rating output -->
                            <div class="text-warning">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="bi <?= $i <= $rev['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="small mb-2">
                            <span class="text-muted"><i class="bi bi-geo-alt text-success"></i> <?= e($rev['pickup_location']) ?></span> 
                            <i class="bi bi-arrow-right text-muted mx-1"></i> 
                            <span class="text-muted"><i class="bi bi-geo-alt text-danger"></i> <?= e($rev['drop_location']) ?></span>
                        </div>
                        
                        <p class="text-dark small mb-0 bg-light p-2.5 rounded-3 border-dashed" style="font-style: italic;">
                            "<?= e($rev['review_text'] ? $rev['review_text'] : 'No comments submitted.') ?>"
                        </p>
                        <span class="text-muted small d-block text-end mt-2" style="font-size: 11px;"><?= date('d M Y h:i A', strtotime($rev['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
