<!-- Smart Cab Booking System - Forgot Password View -->
<!-- Location: views/auth/forgot-password.php -->

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-premium p-4 shadow border-0 mt-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock text-primary display-4"></i>
                    <h3 class="brand-font mt-2 text-dark">Reset Password</h3>
                    <p class="text-muted small">Enter your email and role to request reset link</p>
                </div>
                
                <form action="index.php?controller=auth&action=postForgotPassword" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Select Account Role</label>
                        <select name="role" id="role" class="form-select form-control-custom" required>
                            <option value="user">Passenger / Customer</option>
                            <option value="driver">Cab Driver</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Registered Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control form-control-custom border-start-0 ps-0" placeholder="e.g. john@example.com" required>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning border-0 rounded-3 small p-2.5 mb-4">
                        <i class="bi bi-shield-fill-exclamation me-1"></i> <strong>Simulation Warning:</strong> No external email servers are configured. The system will record the reset link in the database logs and simulate delivery on the screen.
                    </div>
                    
                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5">
                        <i class="bi bi-arrow-clockwise me-2"></i> Request Reset Link
                    </button>
                </form>
                
                <hr class="text-muted my-4">
                
                <div class="text-center">
                    <p class="text-muted small mb-0">Back to safety? <a href="index.php?controller=auth&action=login" class="text-primary fw-semibold text-decoration-none">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
