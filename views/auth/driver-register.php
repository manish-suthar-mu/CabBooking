<!-- Smart Cab Booking System - Driver Registration View -->
<!-- Location: views/auth/driver-register.php -->

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-premium p-4 shadow border-0 mt-3">
                <div class="text-center mb-4">
                    <i class="bi bi-person-badge text-warning display-4"></i>
                    <h3 class="brand-font mt-2 text-dark">Driver Registration</h3>
                    <p class="text-muted small">Submit your application to start earning commissions</p>
                </div>
                
                <form action="index.php?controller=auth&action=postDriverRegister" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Driver Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="name" class="form-control form-control-custom border-start-0 ps-0" placeholder="Enter full name" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control form-control-custom border-start-0 ps-0" placeholder="Enter valid email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                            <input type="tel" name="phone" id="phone" class="form-control form-control-custom border-start-0 ps-0" placeholder="Enter phone number" pattern="[0-9]{10}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="license_no" class="form-label fw-semibold">Commercial Driving License (DL)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-card-heading"></i></span>
                            <input type="text" name="license_no" id="license_no" class="form-control form-control-custom border-start-0 ps-0" placeholder="e.g. DL-142023000xxxx" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control form-control-custom border-start-0 ps-0" placeholder="Create strong password" required>
                        </div>
                    </div>
                    
                    <div class="alert alert-info border-0 rounded-3 small p-2.5 mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i> Applications are placed in <strong>Pending Status</strong>. The Administrator must manually approve your account before you can log in.
                    </div>
                    
                    <button type="submit" class="btn btn-accent-custom w-100 py-2.5 mt-2">
                        <i class="bi bi-send-fill me-2"></i> Submit Application
                    </button>
                </form>
                
                <hr class="text-muted my-4">
                
                <div class="text-center">
                    <p class="text-muted small mb-0">Already registered? <a href="index.php?controller=auth&action=login" class="text-primary fw-semibold text-decoration-none">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
