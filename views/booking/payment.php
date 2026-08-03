<!-- Smart Cab Booking System - Dummy Payment Page View -->
<!-- Location: views/booking/payment.php -->

<div class="container py-4">
    <div class="row justify-content-center">
        
        <!-- Booking Receipt Invoice -->
        <div class="col-md-5 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <i class="bi bi-file-earmark-text text-primary fs-1"></i>
                    <h4 class="brand-font text-dark mt-2 mb-0">Booking Invoice Receipt</h4>
                    <span class="text-muted small">Booking Ref: #<?= $booking['id'] ?></span>
                </div>

                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>Driver Assigned:</span>
                    <span class="fw-semibold text-dark"><?= e($driver['name'] ?? 'Cab Driver') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>Route Traveled:</span>
                    <span class="fw-semibold text-dark text-end" style="max-width: 180px;"><?= e($booking['pickup_location']) ?> to <?= e($booking['drop_location']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 small text-muted">
                    <span>Distance (Simulated):</span>
                    <span class="fw-semibold text-dark"><?= e($booking['distance']) ?> km</span>
                </div>

                <div class="card bg-light border-0 rounded-3 p-3 text-center mb-0">
                    <span class="text-muted small d-block">TOTAL FARE PAYABLE</span>
                    <h2 class="brand-font text-primary fw-bold mb-0 mt-1">₹<?= number_format($booking['actual_fare'] ?? $booking['estimated_fare'], 2) ?></h2>
                </div>
            </div>
        </div>

        <!-- Simulated Payment Portal Form -->
        <div class="col-md-6 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm">
                <h4 class="brand-font text-dark mb-4"><i class="bi bi-credit-card-2-front text-primary me-2"></i>Choose Payment Gateway</h4>
                
                <form id="dummyPaymentForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                    <!-- Selector Buttons -->
                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="cash" checked autocomplete="off">
                            <label class="btn btn-outline-secondary w-100 py-3 fw-semibold" for="pay_cash">
                                <i class="bi bi-cash d-block fs-3 mb-1"></i> Cash
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_upi" value="upi" autocomplete="off">
                            <label class="btn btn-outline-secondary w-100 py-3 fw-semibold" for="pay_upi">
                                <i class="bi bi-qr-code d-block fs-3 mb-1"></i> UPI Pay
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_card" value="card" autocomplete="off">
                            <label class="btn btn-outline-secondary w-100 py-3 fw-semibold" for="pay_card">
                                <i class="bi bi-credit-card d-block fs-3 mb-1"></i> Card Pay
                            </label>
                        </div>
                    </div>

                    <!-- 1. Cash Details Panel -->
                    <div id="cashPanel" class="payment-details-panel alert alert-secondary border-0 p-3 rounded-3 mb-4">
                        <i class="bi bi-info-circle-fill me-1"></i> Pay the cash amount of <strong>₹<?= number_format($booking['actual_fare'] ?? $booking['estimated_fare'], 2) ?></strong> directly to the driver. The driver will verify the currency.
                    </div>

                    <!-- 2. UPI Details Panel -->
                    <div id="upiPanel" class="payment-details-panel d-none mb-4">
                        <div class="text-center p-3 border rounded-3 bg-light mb-3">
                            <!-- Draw a simulated SVG QR Code -->
                            <svg width="120" height="120" viewBox="0 0 100 100" class="mb-2">
                                <rect width="100" height="100" fill="white"></rect>
                                <path d="M10,10 h20 v20 h-20 z M15,15 h10 v10 h-10 z" fill="black"></path>
                                <path d="M70,10 h20 v20 h-20 z M75,15 h10 v10 h-10 z" fill="black"></path>
                                <path d="M10,70 h20 v20 h-20 z M15,75 h10 v10 h-10 z" fill="black"></path>
                                <path d="M40,40 h20 v20 h-20 z" fill="black"></path>
                                <path d="M10,40 h10 v10 h-10 z M30,10 h10 v10 h-10 z M50,10 h10 v10 h-10 z M80,40 h10 v10 h-10 z M90,50 h10 v10 h-10 z" fill="black"></path>
                                <path d="M10,90 h10 v10 h-10 z M40,80 h10 v10 h-10 z M80,80 h10 v10 h-10 z M90,90 h10 v10 h-10 z" fill="black"></path>
                            </svg>
                            <div class="small fw-bold text-dark">UPI ID: <code>pay-smartcab@upi</code></div>
                            <div class="text-muted" style="font-size: 11px;">Scan QR using Google Pay, PhonePe, or Paytm</div>
                        </div>
                        <div class="mb-3">
                            <label for="upi_id" class="form-label fw-semibold text-dark">Enter your UPI Handle</label>
                            <input type="text" name="upi_id" id="upi_id" class="form-control form-control-custom" placeholder="e.g. username@okaxis">
                        </div>
                    </div>

                    <!-- 3. Card Details Panel -->
                    <div id="cardPanel" class="payment-details-panel d-none mb-4">
                        <div class="mb-3">
                            <label for="card_name" class="form-label fw-semibold text-dark">Cardholder Name</label>
                            <input type="text" id="card_name" class="form-control form-control-custom" placeholder="Enter name on card">
                        </div>
                        <div class="mb-3">
                            <label for="card_number" class="form-label fw-semibold text-dark">Card Number</label>
                            <input type="text" id="card_number" class="form-control form-control-custom" placeholder="0000 0000 0000 0000" pattern="[0-9]{16}">
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="card_expiry" class="form-label fw-semibold text-dark">Expiration Date</label>
                                <input type="text" id="card_expiry" class="form-control form-control-custom" placeholder="MM/YY">
                            </div>
                            <div class="col-6">
                                <label for="card_cvv" class="form-label fw-semibold text-dark">CVV Security Code</label>
                                <input type="password" id="card_cvv" class="form-control form-control-custom" placeholder="***" pattern="[0-9]{3}">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 rounded-3 small p-2.5 mb-4">
                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> <strong>Simulation Protection:</strong> This is a simulated transaction. Any credentials input will be verified locally and processed as successful automatically.
                    </div>

                    <button type="submit" id="processPayBtn" class="btn btn-primary-custom w-100 py-3 fs-6">
                        <i class="bi bi-shield-check me-1"></i> Authorize Payment & Finish
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle details panels based on radio selects
    $('input[name="payment_method"]').on('change', function() {
        const val = $(this).val();
        
        // Hide all
        $('.payment-details-panel').addClass('d-none');
        
        // Show target
        if (val === 'cash') {
            $('#cashPanel').removeClass('d-none');
        } else if (val === 'upi') {
            $('#upiPanel').removeClass('d-none');
        } else if (val === 'card') {
            $('#cardPanel').removeClass('d-none');
        }
    });

    // Form Submission
    $('#dummyPaymentForm').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#processPayBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing Transaction...');

        $.ajax({
            url: 'index.php?controller=booking&action=pay',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    window.showToast('Transaction Success', 'Payment marked as Successful. Transaction ID: ' + res.transaction_id, 'success');
                    
                    // Redirect to history after 2s
                    setTimeout(() => {
                        window.location.href = 'index.php?controller=user&action=history';
                    }, 2000);
                } else {
                    window.showToast('Transaction Failed', res.message, 'danger');
                    btn.prop('disabled', false).html('<i class="bi bi-shield-check me-1"></i> Authorize Payment & Finish');
                }
            },
            error: function() {
                window.showToast('Server Error', 'Failed to connect to local gateway.', 'danger');
                btn.prop('disabled', false).html('<i class="bi bi-shield-check me-1"></i> Authorize Payment & Finish');
            }
        });
    });
});
</script>
