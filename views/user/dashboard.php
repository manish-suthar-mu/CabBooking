<!-- Smart Cab Booking System - User Dashboard (Book Ride) -->
<!-- Location: views/user/dashboard.php -->

<div class="container py-4">
    
    <?php if ($activeBooking): ?>
        <!-- Active Booking Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary-light border-primary border-dashed rounded-3 p-4 text-center shadow-sm">
                    <i class="bi bi-clock-history text-primary fs-1 mb-2"></i>
                    <h4 class="brand-font text-dark mb-1">Active Ride Request Found</h4>
                    <p class="text-muted mb-3">You currently have a ride status: <strong><?= ucfirst(e($activeBooking['status'])) ?></strong></p>
                    <a href="index.php?controller=booking&action=tracking&booking_id=<?= $activeBooking['id'] ?>" class="btn btn-primary-custom px-4 py-2">
                        <i class="bi bi-geo-alt-fill me-2"></i> Go to Live Tracking Map
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Booking Form Panel -->
        <div class="row">
            
            <div class="col-lg-5 mb-4">
                <div class="card-premium p-4 border-0 shadow-sm h-100">
                    <h4 class="brand-font text-dark mb-4"><i class="bi bi-taxi-front text-primary me-2"></i>Request a Ride</h4>
                    
                    <form id="bookingForm">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <!-- Pick-up Location -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Pick-up Landmark</label>
                            <select id="pickup_select" class="form-select form-control-custom" required>
                                <option value="" disabled selected>Select pick-up point</option>
                                <option value="Airport" data-lat="28.5562" data-lng="77.1000">Indira Gandhi Intl Airport (A)</option>
                                <option value="Metro Station" data-lat="28.6139" data-lng="77.2090">Connaught Place Metro (B)</option>
                                <option value="Downtown Mall" data-lat="28.6304" data-lng="77.2177">Select Citywalk Mall (C)</option>
                                <option value="University Campus" data-lat="28.6863" data-lng="77.2218">Delhi University (D)</option>
                                <option value="IT Park" data-lat="28.5355" data-lng="77.3910">Noida IT Park Sector 62 (E)</option>
                            </select>
                            <input type="hidden" name="pickup_location" id="pickup_location">
                            <input type="hidden" name="pickup_lat" id="pickup_lat">
                            <input type="hidden" name="pickup_lng" id="pickup_lng">
                        </div>

                        <!-- Drop-off Location -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Drop-off Landmark</label>
                            <select id="drop_select" class="form-select form-control-custom" required>
                                <option value="" disabled selected>Select drop-off point</option>
                                <option value="Airport" data-lat="28.5562" data-lng="77.1000">Indira Gandhi Intl Airport (A)</option>
                                <option value="Metro Station" data-lat="28.6139" data-lng="77.2090">Connaught Place Metro (B)</option>
                                <option value="Downtown Mall" data-lat="28.6304" data-lng="77.2177">Select Citywalk Mall (C)</option>
                                <option value="University Campus" data-lat="28.6863" data-lng="77.2218">Delhi University (D)</option>
                                <option value="IT Park" data-lat="28.5355" data-lng="77.3910">Noida IT Park Sector 62 (E)</option>
                            </select>
                            <input type="hidden" name="drop_location" id="drop_location">
                            <input type="hidden" name="drop_lat" id="drop_lat">
                            <input type="hidden" name="drop_lng" id="drop_lng">
                        </div>

                        <!-- Vehicle Type Selector -->
                        <label class="form-label fw-semibold text-dark mb-2">Select Vehicle Type</label>
                        <input type="hidden" name="vehicle_type" id="vehicle_type" value="car">
                        <div class="row g-2 mb-4">
                            <div class="col-4">
                                <div class="vehicle-card selected" data-type="car">
                                    <i class="bi bi-car-front-fill fs-2 d-block mb-1 text-primary"></i>
                                    <span class="d-block fw-bold text-dark small">Car</span>
                                    <span class="text-muted" style="font-size: 10px;">Premium Ride</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="vehicle-card" data-type="auto">
                                    <i class="bi bi-ev-front-fill fs-2 d-block mb-1 text-success"></i>
                                    <span class="d-block fw-bold text-dark small">Auto</span>
                                    <span class="text-muted" style="font-size: 10px;">Budget Ride</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="vehicle-card" data-type="bike">
                                    <i class="bi bi-bicycle fs-2 d-block mb-1 text-warning"></i>
                                    <span class="d-block fw-bold text-dark small">Bike</span>
                                    <span class="text-muted" style="font-size: 10px;">Quick Solo</span>
                                </div>
                            </div>
                        </div>

                        <!-- Estimate Display Container -->
                        <div id="fareEstimateContainer" class="card bg-light border-0 rounded-3 p-3 mb-4 d-none">
                            <h6 class="brand-font mb-3 text-dark border-bottom pb-2"><i class="bi bi-calculator me-1 text-primary"></i>Fare Breakdown</h6>
                            <div class="d-flex justify-content-between mb-1 small text-muted">
                                <span>Simulated Distance:</span>
                                <span id="est_distance" class="fw-semibold text-dark">0.0 km</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small text-muted">
                                <span>Base Fare Rate:</span>
                                <span id="est_base" class="fw-semibold text-dark">₹0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Rate / KM:</span>
                                <span id="est_per_km" class="fw-semibold text-dark">₹0.00</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span class="fw-bold text-dark">Estimated Fare:</span>
                                <span id="est_total" class="fw-bold text-primary fs-5">₹0.00</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" id="getEstimateBtn" class="btn btn-outline-secondary w-100 py-2.5 fw-semibold">
                                    <i class="bi bi-calculator me-1"></i> Calculate Fare
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" id="bookCabBtn" class="btn btn-primary-custom w-100 py-2.5" disabled>
                                    <i class="bi bi-check-circle me-1"></i> Request Cab
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-premium p-4 border-0 shadow-sm h-100">
                    <h4 class="brand-font text-dark mb-3"><i class="bi bi-map-fill text-primary me-2"></i>Coverage Map & Rates</h4>
                    <p class="text-muted small">Select your landmarks. Below are the current rates configured by administration.</p>
                    
                    <!-- Grid of rates configured -->
                    <div class="row g-2 mb-4">
                        <?php foreach ($fareRates as $rate): ?>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded-3 text-center border">
                                    <h6 class="fw-bold text-dark brand-font mb-1"><?= ucfirst(e($rate['vehicle_type'])) ?></h6>
                                    <div class="small text-muted mb-0">Base: <strong>₹<?= number_format($rate['base_fare'], 2) ?></strong></div>
                                    <div class="small text-muted">Per KM: <strong>₹<?= number_format($rate['per_km_rate'], 2) ?></strong></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Canvas for showing landmarks -->
                    <div class="map-canvas-container">
                        <canvas id="preBookingMap"></canvas>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>

<!-- Script for pre-booking landmark drawing and estimate triggers -->
<script>
$(document).ready(function() {
    // 1. Synchronize Dropdown selection values
    function updateCoordinateInputs(type) {
        const select = $(`#${type}_select option:selected`);
        if (select.val() === "") return;
        
        $(`#${type}_location`).val(select.val());
        $(`#${type}_lat`).val(select.data('lat'));
        $(`#${type}_lng`).val(select.data('lng'));
        
        drawLocations();
    }

    $('#pickup_select').on('change', () => updateCoordinateInputs('pickup'));
    $('#drop_select').on('change', () => updateCoordinateInputs('drop'));

    // 2. Manage vehicle type selection styling
    $('.vehicle-card').on('click', function() {
        $('.vehicle-card').removeClass('selected');
        $(this).addClass('selected');
        $('#vehicle_type').val($(this).data('type'));
        
        // If estimate already visible, recalculate
        if (!$('#fareEstimateContainer').hasClass('d-none')) {
            calculateEstimate();
        }
    });

    // 3. Trigger AJAX Fare Estimate
    $('#getEstimateBtn').on('click', function() {
        calculateEstimate();
    });

    function calculateEstimate() {
        const pickup_val = $('#pickup_select').val();
        const drop_val = $('#drop_select').val();

        if (!pickup_val || !drop_val) {
            window.showToast('Validation Error', 'Please select both Pick-up and Drop-off landmarks.', 'warning');
            return;
        }

        if (pickup_val === drop_val) {
            window.showToast('Validation Error', 'Pick-up and Drop-off locations cannot be identical.', 'warning');
            return;
        }

        $.ajax({
            url: 'index.php?controller=booking&action=estimate',
            method: 'POST',
            data: {
                vehicle_type: $('#vehicle_type').val(),
                pickup_lat: $('#pickup_lat').val(),
                pickup_lng: $('#pickup_lng').val(),
                drop_lat: $('#drop_lat').val(),
                drop_lng: $('#drop_lng').val()
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#est_distance').text(res.distance + ' km');
                    $('#est_base').text('₹' + res.base_fare.toFixed(2));
                    $('#est_per_km').text('₹' + res.per_km_rate.toFixed(2));
                    $('#est_total').text('₹' + res.estimated_fare.toFixed(2));

                    $('#fareEstimateContainer').removeClass('d-none');
                    $('#bookCabBtn').prop('disabled', false);
                    window.showToast('Calculation Complete', 'Fare breakdown loaded.', 'success');
                } else {
                    window.showToast('Estimation Error', res.message, 'danger');
                }
            }
        });
    }

    // 4. Drawing coordinates preview on Canvas
    const canvas = document.getElementById('preBookingMap');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        const container = canvas.parentElement;
        canvas.width = container.clientWidth;
        canvas.height = 360;

        const landmarks = {
            'Airport': { x: 80, y: 300, color: '#a855f7', label: 'Airport (A)' },
            'Metro Station': { x: 220, y: 150, color: '#f59e0b', label: 'Connaught Place (B)' },
            'Downtown Mall': { x: 380, y: 240, color: '#ef4444', label: 'Select Citywalk (C)' },
            'University Campus': { x: 250, y: 60, color: '#3b82f6', label: 'Delhi University (D)' },
            'IT Park': { x: 500, y: 120, color: '#10b981', label: 'IT Park Noida (E)' }
        };

        function drawLocations() {
            ctx.fillStyle = '#f8fafc';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Draw connecting path roads for preview
            ctx.lineWidth = 10;
            ctx.strokeStyle = '#e2e8f0';
            ctx.beginPath();
            ctx.moveTo(80, 300); ctx.lineTo(220, 150);
            ctx.lineTo(250, 60); ctx.lineTo(380, 240);
            ctx.lineTo(500, 120);
            ctx.stroke();

            // Highlight selected route if both exist
            const pickupKey = $('#pickup_select').val();
            const dropKey = $('#drop_select').val();

            if (pickupKey && dropKey && pickupKey !== dropKey) {
                const pt1 = landmarks[pickupKey];
                const pt2 = landmarks[dropKey];
                
                ctx.lineWidth = 4;
                ctx.strokeStyle = '#3b82f6';
                ctx.setLineDash([4, 4]);
                ctx.beginPath();
                ctx.moveTo(pt1.x, pt1.y);
                ctx.lineTo(pt2.x, pt2.y);
                ctx.stroke();
                ctx.setLineDash([]);
            }

            // Draw Landmark pins
            for (let name in landmarks) {
                const lm = landmarks[name];
                
                ctx.beginPath();
                ctx.arc(lm.x, lm.y, 8, 0, 2 * Math.PI);
                ctx.fillStyle = lm.color;
                ctx.fill();
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 2;
                ctx.stroke();

                ctx.fillStyle = '#1e293b';
                ctx.font = 'bold 10px Inter';
                ctx.textAlign = 'center';
                ctx.fillText(lm.label, lm.x, lm.y - 12);
            }
        }
        
        drawLocations();
    }
});
</script>
