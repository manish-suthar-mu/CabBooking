<!-- Smart Cab Booking System - Driver Dashboard -->
<!-- Location: views/driver/dashboard.php -->

<div class="container py-4">

    <!-- 1. Handle Unapproved Application Status -->
    <?php if ($driver['status'] === 'pending_approval'): ?>
        <div class="row">
            <div class="col-lg-8 mx-auto text-center py-5">
                <i class="bi bi-clock-history text-warning display-1"></i>
                <h3 class="brand-font text-dark mt-4">Verification Pending</h3>
                <p class="text-muted leading-relaxed">
                    Your driver registration application (License: <strong><?= e($driver['license_no']) ?></strong>) is currently undergoing administrative background checks.
                </p>
                <div class="alert alert-info border-0 rounded-3 mt-4 text-start">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>What to do next?</strong> Log in to the <a href="index.php?controller=auth&action=login" class="fw-bold">Admin Panel</a> (username: <code>admin</code> / password: <code>admin123</code>) to approve your application instantly!
                </div>
            </div>
        </div>

    <!-- 2. Profile approved, check if vehicle details are missing -->
    <?php elseif (!$vehicle): ?>
        <div class="row">
            <div class="col-lg-8 mx-auto text-center py-5">
                <i class="bi bi-car-front text-muted display-1"></i>
                <h3 class="brand-font text-dark mt-4">Vehicle Profile Required</h3>
                <p class="text-muted">You must specify your vehicle type, model, and plate number before you can receive booking requests.</p>
                <a href="index.php?controller=driver&action=vehicle" class="btn btn-accent-custom px-4 py-2 mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Register Vehicle Details
                </a>
            </div>
        </div>

    <!-- 3. Driver approved and vehicle loaded -->
    <?php else: ?>
        
        <!-- Welcome banner with Online toggle -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card-premium p-4 border-0 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h4 class="brand-font text-dark mb-1">Welcome, <?= e($driver['name']) ?></h4>
                        <span class="text-muted small"><i class="bi bi-car-front-fill text-primary"></i> <?= ucfirst(e($vehicle['type'])) ?>: <?= e($vehicle['model']) ?> (<?= e($vehicle['plate_number']) ?>)</span>
                    </div>
                    
                    <?php if (!$activeBooking): ?>
                        <div class="d-flex align-items-center gap-3">
                            <span id="onlineLabel" class="fw-semibold text-muted">Offline</span>
                            <label class="switch">
                                <input type="checkbox" id="onlineToggle" <?= $driver['is_online'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                            <span id="onlineBadge" class="badge bg-success rounded-pill px-3 py-2" style="display: <?= $driver['is_online'] ? 'inline-block' : 'none' ?>;">
                                <span class="pulse-indicator"></span> Receiving Jobs
                            </span>
                        </div>
                    <?php else: ?>
                        <div>
                            <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill"><span class="pulse-indicator"></span> Active Trip #<?= $activeBooking['id'] ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            
            <!-- Trip Panel & Maps -->
            <div class="col-lg-8 mb-4">
                
                <?php if ($activeBooking): ?>
                    <!-- Active Ride Info card & Map -->
                    <div class="card-premium p-4 border-0 shadow-sm mb-4">
                        <h5 class="brand-font text-dark mb-3"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Active Ride Tracking</h5>
                        
                        <!-- Canvas simulation -->
                        <div class="map-canvas-container mb-4">
                            <canvas id="mapCanvas"></canvas>
                        </div>
                        
                        <div class="row border-top pt-3 g-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Passenger Name:</span>
                                <h6 class="fw-bold text-dark mb-2"><?= e($activeBooking['user_name']) ?></h6>
                                <span class="text-muted small d-block">Contact Number:</span>
                                <h6 class="fw-semibold text-dark"><i class="bi bi-telephone text-success"></i> <?= e($activeBooking['user_phone']) ?></h6>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Pickup Point:</span>
                                <p class="small text-dark mb-2 fw-medium"><i class="bi bi-geo-alt-fill text-success"></i> <?= e($activeBooking['pickup_location']) ?></p>
                                <span class="text-muted small d-block">Drop Point:</span>
                                <p class="small text-dark mb-0 fw-medium"><i class="bi bi-geo-alt-fill text-danger"></i> <?= e($activeBooking['drop_location']) ?></p>
                            </div>
                        </div>

                        <!-- Action controls -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <?php if ($activeBooking['status'] === 'accepted'): ?>
                                <div class="mb-3 w-100">
                                    <label class="fw-semibold text-dark">Enter OTP from User</label>
                                    <input type="text" id="otpInput" class="form-control" placeholder="Enter 4-digit OTP" maxlength="4" style="letter-spacing: 8px; text-align: center; font-size: 1.5rem;">
                                    <button type="button" id="verifyOtpBtn" class="btn btn-primary-custom mt-2 w-100">
                                        Verify OTP
                                    </button>
                                </div>
                                <button type="button" id="arriveBtn" class="btn btn-secondary px-4 py-2.5 fw-semibold d-none">
                                    <i class="bi bi-geo"></i> Arriving...
                                </button>
                                <button type="button" id="startRideBtn" class="btn btn-success px-4 py-2.5 d-none">
                                    <i class="bi bi-play-fill"></i> Start Ride
                                </button>
                            <?php elseif ($activeBooking['status'] === 'ongoing'): ?>
                                <button type="button" id="completeBtn" class="btn btn-secondary px-4 py-2.5 fw-semibold" disabled>
                                    <i class="bi bi-flag"></i> Driving...
                                </button>
                                <button type="button" id="completeRideBtn" class="btn btn-success px-4 py-2.5 d-none">
                                    <i class="bi bi-check2-circle"></i> Complete Ride
                                </button>
                            <?php endif; ?>
                            
                            <button type="button" id="cancelRideBtn" class="btn btn-outline-danger px-4 py-2.5">
                                <i class="bi bi-x-circle"></i> Cancel Ride
                            </button>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Welcome view / Map overview -->
                    <div class="card-premium p-4 border-0 shadow-sm text-center py-5" id="offlineScreen" style="display: <?= $driver['is_online'] ? 'none' : 'block' ?>;">
                        <i class="bi bi-shield-slash text-muted display-1"></i>
                        <h4 class="brand-font text-dark mt-4">You are currently offline</h4>
                        <p class="text-muted">Toggle the online switch above to start receiving client ride requests in your area.</p>
                    </div>

                    <div class="card-premium p-4 border-0 shadow-sm" id="onlineScreen" style="display: <?= $driver['is_online'] ? 'block' : 'none' ?>;">
                        <h5 class="brand-font text-dark mb-3"><i class="bi bi-radar text-primary me-2"></i>Incoming Booking Requests</h5>
                        
                        <div id="requestsList">
                            <?php if (empty($pendingRequests)): ?>
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary mb-3" role="status"></div>
                                    <h6 class="text-muted mb-0">Listening for bookings of type <strong><?= ucfirst(e($vehicle['type'])) ?></strong>...</h6>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pendingRequests as $req): ?>
                                    <div class="card bg-white border-0 shadow-sm rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">Ride Request #<?= $req['id'] ?></h6>
                                                <small class="text-muted">Estimated Distance: <?= e($req['distance']) ?> km</small>
                                            </div>
                                            <span class="fs-5 fw-bold text-primary">₹<?= number_format($req['estimated_fare'], 2) ?></span>
                                        </div>
                                        <div class="small mb-3">
                                            <div class="mb-1"><i class="bi bi-geo-alt text-success me-1"></i><strong>Pickup:</strong> <?= e($req['pickup_location']) ?></div>
                                            <div><i class="bi bi-geo-alt text-danger me-1"></i><strong>Drop:</strong> <?= e($req['drop_location']) ?></div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-success btn-sm px-4 fw-bold accept-request-btn" data-id="<?= $req['id'] ?>">Accept</button>
                                            <button class="btn btn-outline-danger btn-sm px-4 fw-bold reject-request-btn" data-id="<?= $req['id'] ?>">Reject</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endif; ?>

            </div>

            <!-- Side notification log logs -->
            <div class="col-lg-4" id="notif-pane">
                <div class="card-premium p-4 border-0 shadow-sm h-100">
                    <h5 class="brand-font text-dark mb-4"><i class="bi bi-bell text-primary me-2"></i>Recent Alerts</h5>
                    
                    <div id="driverAlertsList" class="list-group list-group-flush" style="font-size: 13px;">
                        <!-- Alerts populated via AJAX -->
                        <p class="text-muted text-center py-4">No recent activity logs.</p>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>

</div>

<?php if ($activeBooking): ?>
    <!-- Initialize active map engine script -->
    <script src="assets/js/map.js"></script>
    <script>
        $(document).ready(function() {
            const coordinates = {
                pickup: { lat: parseFloat("<?= $activeBooking['pickup_lat'] ?>"), lng: parseFloat("<?= $activeBooking['pickup_lng'] ?>") },
                drop: { lat: parseFloat("<?= $activeBooking['drop_lat'] ?>"), lng: parseFloat("<?= $activeBooking['drop_lng'] ?>") },
                driver: { lat: parseFloat("<?= $driver['latitude'] ?>"), lng: parseFloat("<?= $driver['longitude'] ?>") }
            };

            const map = new MapSimulation('mapCanvas', <?= $activeBooking['id'] ?>, 'driver', coordinates);
            
            // OTP Verification
            $('#verifyOtpBtn').on('click', function() {
                const otp = $('#otpInput').val();
                console.log('Entered OTP:', otp, 'Booking ID:', <?= $activeBooking['id'] ?>); // Debug log
                if (otp.length !== 4) {
                    window.showToast('Error', 'Please enter valid 4-digit OTP', 'danger');
                    return;
                }
                
                $.ajax({
                    url: 'index.php?controller=booking&action=verifyOtp',
                    method: 'POST',
                    data: { booking_id: <?= $activeBooking['id'] ?>, otp: otp, csrf_token: window.csrfToken },
                    dataType: 'json',
                    success: function(res) {
                        console.log('OTP Verification Response:', res); // Debug log
                        if (res.status === 'success') {
                            window.showToast('Success', res.message, 'success');
                            $('#verifyOtpBtn').addClass('d-none');
                            $('#otpInput').prop('disabled', true);
                            $('#startRideBtn').removeClass('d-none');
                        } else {
                            window.showToast('Error', res.message, 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('OTP Verification Error:', status, error, xhr.responseText);
                    }
                });
            });
            
            // Start Ride Action
            $('#startRideBtn').on('click', function() {
                $.ajax({
                    url: 'index.php?controller=booking&action=start',
                    method: 'POST',
                    data: { booking_id: <?= $activeBooking['id'] ?>, csrf_token: window.csrfToken },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            window.showToast('Ride Started', 'You can now navigate to destination.', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            window.showToast('Error', res.message, 'danger');
                        }
                    }
                });
            });

            // Arrived at Drop
            $('#completeBtn').on('click', function() {
                $(this).addClass('d-none');
                $('#completeRideBtn').removeClass('d-none');
            });
            // Manual click fallback
            setTimeout(() => {
                $('#completeBtn').removeClass('btn-secondary').addClass('btn-success').text('Reached Destination? Tap here').prop('disabled', false);
            }, 6000); // safety fallback

            // Complete Ride Action
            $('#completeRideBtn').on('click', function() {
                $.ajax({
                    url: 'index.php?controller=booking&action=complete',
                    method: 'POST',
                    data: { booking_id: <?= $activeBooking['id'] ?>, csrf_token: window.csrfToken },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            window.showToast('Trip Complete', 'Trip finished. Redirecting to receipt.', 'success');
                            setTimeout(() => window.location.href = 'index.php?controller=driver&action=receipt&booking_id=<?= $activeBooking['id'] ?>', 1500);
                        } else {
                            window.showToast('Error', res.message, 'danger');
                        }
                    }
                });
            });

            // Cancel Ride Action
            $('#cancelRideBtn').on('click', function() {
                if (confirm("Are you sure you want to cancel this ride request? This affects your rating.")) {
                    $.ajax({
                        url: 'index.php?controller=booking&action=cancel',
                        method: 'POST',
                        data: { booking_id: <?= $activeBooking['id'] ?>, csrf_token: window.csrfToken },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                window.showToast('Cancelled', 'Ride request has been cancelled.', 'warning');
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                window.showToast('Error', res.message, 'danger');
                            }
                        }
                    });
                }
            });
        });
    </script>
<?php elseif ($driver['status'] === 'approved' && $vehicle): ?>
    <!-- Driver Dashboard Polling Code for online status toggles & broadcast requests checks -->
    <script>
        $(document).ready(function() {
            // Online Status Toggle
            $('#onlineToggle').on('change', function() {
                const isOnline = $(this).is(':checked') ? 1 : 0;
                
                $.ajax({
                    url: 'index.php?controller=driver&action=postOnlineStatus',
                    method: 'POST',
                    data: { is_online: isOnline, csrf_token: window.csrfToken },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            window.showToast('Availability Updated', res.message, 'success');
                            if (isOnline) {
                                $('#onlineLabel').text('Online');
                                $('#onlineBadge').show();
                                $('#offlineScreen').hide();
                                $('#onlineScreen').show();
                                pollRequests();
                            } else {
                                $('#onlineLabel').text('Offline');
                                $('#onlineBadge').hide();
                                $('#offlineScreen').show();
                                $('#onlineScreen').hide();
                            }
                        } else {
                            window.showToast('Toggle Failed', res.message, 'danger');
                            $('#onlineToggle').prop('checked', !isOnline); // revert
                        }
                    },
                    error: function() {
                        window.showToast('Error', 'Unable to toggle online status.', 'danger');
                        $('#onlineToggle').prop('checked', !isOnline); // revert
                    }
                });
            });

            // Poll pending booking requests and notification list
            function pollRequests() {
                if (!$('#onlineToggle').is(':checked')) return;

                // Load logs
                $.ajax({
                    url: 'index.php?controller=driver&action=getNotifications',
                    method: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            let html = '';
                            if (res.notifications.length === 0) {
                                html = '<p class="text-muted text-center py-4">No recent activity logs.</p>';
                            } else {
                                res.notifications.forEach(n => {
                                    html += `
                                        <div class="border-bottom py-2">
                                            <div class="fw-bold text-dark">${n.title}</div>
                                            <div class="text-muted small">${n.message}</div>
                                        </div>
                                    `;
                                });
                            }
                            $('#driverAlertsList').html(html);
                        }
                    }
                });

                // Load booking requests
                $.ajax({
                    url: 'index.php?controller=driver&action=dashboard', // fetches page dashboard logic content
                    method: 'GET',
                    success: function(data) {
                        // Find requestsList container from response
                        const list = $(data).find('#requestsList').html();
                        $('#requestsList').html(list);
                    }
                });
            }

            // Poll loop
            setInterval(pollRequests, 4000);

            // Accept and Reject Action Bindings (using Event Delegation since requests are loaded via AJAX)
            $(document).on('click', '.accept-request-btn', function() {
                const bookingId = $(this).data('id');
                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: 'index.php?controller=booking&action=accept',
                    method: 'POST',
                    data: { booking_id: bookingId, csrf_token: window.csrfToken },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            window.showToast('Ride Accepted', 'Trip assigned. Redirecting to tracking map...', 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            window.showToast('Failed to Accept', res.message, 'danger');
                            pollRequests(); // refresh list
                        }
                    }
                });
            });

            $(document).on('click', '.reject-request-btn', function() {
                const bookingId = $(this).data('id');
                const row = $(this).closest('.card');

                $.ajax({
                    url: 'index.php?controller=booking&action=reject',
                    method: 'POST',
                    data: { booking_id: bookingId, csrf_token: window.csrfToken },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            row.fadeOut(300, function() { $(this).remove(); });
                            window.showToast('Rejected', 'Booking request rejected.', 'warning');
                        }
                    }
                });
            });
        });
    </script>
<?php endif; ?>
