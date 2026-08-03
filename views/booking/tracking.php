<!-- Smart Cab Booking System - Live Trip Tracking View -->
<!-- Location: views/booking/tracking.php -->

<div class="container py-4">
    <div class="row">
        
        <!-- Interactive Tracking Map Canvas -->
        <div class="col-lg-8 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="brand-font text-dark mb-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Live Journey Tracker</h5>
                    <span id="tripStatusBadge" class="badge bg-primary px-3 py-2 rounded-pill"><span class="pulse-indicator"></span> Syncing GPS...</span>
                </div>
                
                <div class="map-canvas-container">
                    <canvas id="mapCanvas"></canvas>
                </div>
            </div>
        </div>

        <!-- Trip / Driver Details Card -->
        <div class="col-lg-4 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm h-100 d-flex flex-column">
                
                <!-- 1. Searching for Driver Panel -->
                <div id="searchingPanel" class="text-center py-4 flex-grow-1 d-none">
                    <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h5 class="brand-font text-dark mb-2">Broadcasting to Drivers...</h5>
                    <p class="text-muted small">We are sending ride requests to available online drivers nearby. Please wait.</p>
                </div>

                <!-- 2. Driver Assigned Panel -->
                <div id="assignedPanel" class="flex-grow-1 d-none">
                    <h5 class="brand-font text-dark mb-3"><i class="bi bi-person-check text-primary me-2"></i>Assigned Cab Details</h5>
                    
                    <div class="bg-light p-3 rounded-3 mb-3 border">
                        <div class="text-muted small">Driver Name:</div>
                        <h6 class="fw-bold text-dark mb-2" id="val_driver_name">N/A</h6>
                        <div class="text-muted small">Contact Number:</div>
                        <h6 class="fw-semibold text-dark"><i class="bi bi-telephone text-success"></i> <span id="val_driver_phone">N/A</span></h6>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3 border">
                        <div class="text-muted small">Vehicle Details:</div>
                        <h6 class="fw-bold text-dark mb-1" id="val_vehicle_model">N/A</h6>
                        <div class="text-muted small"><span id="val_vehicle_color">N/A</span> &bull; <strong id="val_plate_number">N/A</strong></div>
                    </div>

                    <!-- OTP Display -->
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 mb-4 border border-warning border-opacity-25" id="otpSection" style="display:none">
                        <div class="text-muted small">Ride OTP (Share with Driver):</div>
                        <h3 class="fw-bold text-warning text-center" id="val_otp" style="letter-spacing: 12px; font-size: 2.5rem;">0000</h3>
                    </div>
                </div>

                <!-- Shared Trip Parameters -->
                <div class="border-top pt-3 mt-auto">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Pickup Location:</span>
                        <span class="fw-semibold text-dark text-end" style="max-width: 180px;"><?= e($booking['pickup_location']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Drop Location:</span>
                        <span class="fw-semibold text-dark text-end" style="max-width: 180px;"><?= e($booking['drop_location']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Estimated Fare:</span>
                        <span class="fw-bold text-primary">₹<?= number_format($booking['estimated_fare'], 2) ?></span>
                    </div>

                    <!-- Cancel button -->
                    <button type="button" id="cancelTripBtn" class="btn btn-outline-danger w-100 py-2.5 d-none">
                        <i class="bi bi-x-circle me-1"></i> Cancel Ride Request
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Load canvas tracker script -->
<script src="assets/js/map.js"></script>
<script>
$(document).ready(function() {
    const bookingId = <?= $booking['id'] ?>;
    const role = "<?= $_SESSION['role'] ?>";

    // Setup coordinates parameters
    const coordinates = {
        pickup: { lat: parseFloat("<?= $booking['pickup_lat'] ?>"), lng: parseFloat("<?= $booking['pickup_lng'] ?>") },
        drop: { lat: parseFloat("<?= $booking['drop_lat'] ?>"), lng: parseFloat("<?= $booking['drop_lng'] ?>") },
        driver: <?= $driver ? json_encode(['lat' => floatval($driver['latitude']), 'lng' => floatval($driver['longitude'])]) : 'null' ?>
    };

    // Instantiate Canvas GPS Simulation
    const tracker = new MapSimulation('mapCanvas', bookingId, role, coordinates);

    // Watch status changes and update details dashboard panels
    function updateDetailsPanel() {
        $.ajax({
            url: 'index.php?controller=booking&action=status&booking_id=' + bookingId,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log('Status API Response:', res);
                if (res.status === 'success') {
                    const bookingStatus = res.booking_status;
                    
                    // Update header badge status
                    let statusTxt = 'Syncing...';
                    let badgeClass = 'bg-primary';
                    if (bookingStatus === 'pending') {
                        statusTxt = 'Searching Cabs...';
                        badgeClass = 'bg-warning text-dark';
                        
                        $('#searchingPanel').removeClass('d-none');
                        $('#assignedPanel').addClass('d-none');
                        $('#otpSection').hide();
                        $('#cancelTripBtn').removeClass('d-none');
                    } else if (bookingStatus === 'accepted') {
                        statusTxt = 'Cab Arriving';
                        badgeClass = 'bg-info text-dark';
                        
                        $('#searchingPanel').addClass('d-none');
                        $('#assignedPanel').removeClass('d-none');
                        $('#otpSection').show();
                        $('#val_otp').text(res.otp);
                        $('#cancelTripBtn').removeClass('d-none');
                    } else if (bookingStatus === 'ongoing') {
                        statusTxt = 'Ride In Progress';
                        badgeClass = 'bg-primary';
                        
                        $('#searchingPanel').addClass('d-none');
                        $('#assignedPanel').removeClass('d-none');
                        $('#otpSection').hide();
                        $('#cancelTripBtn').addClass('d-none');
                    } else if (bookingStatus === 'completed') {
                        statusTxt = 'Arrived';
                        badgeClass = 'bg-success';
                        
                        if (role === 'user') {
                            window.showToast('Destination Reached', 'Trip complete. Redirecting to payment...', 'success');
                            setTimeout(() => {
                                window.location.href = 'index.php?controller=booking&action=payment&booking_id=' + bookingId;
                            }, 1500);
                        }
                    } else if (bookingStatus === 'cancelled') {
                        statusTxt = 'Cancelled';
                        badgeClass = 'bg-danger';
                        window.showToast('Trip Cancelled', 'This ride has been cancelled.', 'danger');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 2000);
                    }

                    $('#tripStatusBadge').text(statusTxt).removeClass().addClass('badge px-3 py-2 rounded-pill ' + badgeClass);

                    // Fill assigned driver info
                    if (res.driver) {
                        console.log('Updating driver info:', res.driver);
                        $('#val_driver_name').text(res.driver.name);
                        $('#val_driver_phone').text(res.driver.phone);
                        $('#val_vehicle_model').text(res.driver.vehicle_model);
                        $('#val_vehicle_color').text(res.driver.color);
                        $('#val_plate_number').text(res.driver.plate_number);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Status API Error:', status, error, xhr.responseText);
            }
        });
    }

    // Run first update and poll
    updateDetailsPanel();
    const uiPoller = setInterval(updateDetailsPanel, 3000);

    // Cancel Button Action
    $('#cancelTripBtn').on('click', function() {
        if (confirm("Are you sure you want to cancel this booking request?")) {
            $.ajax({
                url: 'index.php?controller=booking&action=cancel',
                method: 'POST',
                data: {
                    booking_id: bookingId,
                    csrf_token: window.csrfToken
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        window.showToast('Cancelled', 'Your booking request has been cancelled.', 'warning');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1000);
                    } else {
                        window.showToast('Cancel Failed', res.message, 'danger');
                    }
                }
            });
        }
    });

    // Cleanup poller on unload
    $(window).on('unload', function() {
        clearInterval(uiPoller);
        tracker.destroy();
    });
});
</script>
