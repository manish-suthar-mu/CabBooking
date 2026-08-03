<!-- Smart Cab Booking System - Home Landing View -->
<!-- Location: views/home.php -->

<div class="hero-section d-flex align-items-center">
    <div class="container py-5">
        <div class="row align-items-center">
            
            <div class="col-lg-6 text-center text-lg-start">
                <span class="badge bg-warning text-dark px-3 py-2 fs-6 mb-3 rounded-pill fw-semibold">
                    <i class="bi bi-cpu-fill me-1"></i> 100% Offline GPS & Payment Simulator
                </span>
                
                <h1 class="display-3 text-dark fw-bold mb-3 brand-font">
                    Smart Cab <span class="text-primary">Booking</span>
                </h1>
                
                <p class="lead text-muted mb-4 fs-5" style="line-height: 1.6;">
                    Experience a full-scale ride-hailing simulation with multi-driver request broadcasts, interactive HTML5 canvas GPS routing tracking, and administrative finance configurations.
                </p>
                
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="index.php?controller=auth&action=register" class="btn btn-primary-custom btn-lg px-4 py-3">
                        <i class="bi bi-search me-2"></i> Book A Ride
                    </a>
                    <a href="index.php?controller=auth&action=driverRegister" class="btn btn-accent-custom btn-lg px-4 py-3">
                        <i class="bi bi-car-front me-2"></i> Become a Driver
                    </a>
                    <a href="index.php?controller=auth&action=login" class="btn btn-outline-secondary btn-lg px-4 py-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Admin Login
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="card-premium p-4 shadow-lg border-0">
                    <h4 class="brand-font mb-4 text-center text-dark"><i class="bi bi-info-circle-fill text-primary me-2"></i>Simulation Features</h4>
                    
                    <div class="d-flex align-items-start mb-3 gap-3">
                        <div class="bg-primary text-white p-2 rounded-3">
                            <i class="bi bi-broadcast fs-4"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-1">Multi-Driver Request Broadcast</h6>
                            <p class="text-muted small mb-0">Bookings trigger matching driver notifications. First-to-accept gets the trip, other requests expire automatically.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3 gap-3">
                        <div class="bg-warning text-dark p-2 rounded-3">
                            <i class="bi bi-map fs-4"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-1">Interactive Canvas GPS Tracker</h6>
                            <p class="text-muted small mb-0">Follow vehicle movements along simulated grids step-by-step using pure HTML5 canvas and AJAX polling.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3 gap-3">
                        <div class="bg-success text-white p-2 rounded-3">
                            <i class="bi bi-credit-card fs-4"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-1">Integrated Payment Gateway</h6>
                            <p class="text-muted small mb-0">Test UPI, Card, and Cash simulation payments. Distribute driver commission logs automatically inside the DB.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-danger text-white p-2 rounded-3">
                            <i class="bi bi-envelope-paper fs-4"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-1">Local Notification Logs</h6>
                            <p class="text-muted small mb-0">Emails and SMS notices are recorded locally in database tables and presented in real-time alert widgets.</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
