<!-- Smart Cab Booking System - Login View -->
<!-- Location: views/auth/login.php -->

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-premium p-4 shadow border-0 mt-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-lock text-primary display-4"></i>
                    <h3 class="brand-font mt-2 text-dark">Sign In</h3>
                    <p class="text-muted small">Access your customized dashboard portal</p>
                </div>
                
                <form action="index.php?controller=auth&action=postLogin" method="POST">
                    <!-- CSRF Token Input -->
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Select Account Role</label>
                        <select name="role" id="role" class="form-select form-control-custom" required>
                            <option value="user">Passenger / Customer</option>
                            <option value="driver">Cab Driver</option>
                            <option value="admin">System Administrator</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address / Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="text" name="email" id="email" class="form-control form-control-custom border-start-0 ps-0" placeholder="Enter email or admin username" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control form-control-custom border-start-0 ps-0" placeholder="Enter password" required>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label text-muted small" for="rememberMe">Remember me</label>
                        </div>
                        <a href="index.php?controller=auth&action=forgotPassword" class="text-primary small fw-semibold text-decoration-none">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Access Account
                    </button>
                </form>
                
                <hr class="text-muted my-4">
                
                <div class="text-center">
                    <p class="text-muted small mb-1">New Passenger? <a href="index.php?controller=auth&action=register" class="text-primary fw-semibold text-decoration-none">Register here</a></p>
                    <p class="text-muted small mb-0">Want to drive? <a href="index.php?controller=auth&action=driverRegister" class="text-primary fw-semibold text-decoration-none">Register as Driver</a></p>
                </div>
            </div>
            
            <!-- Quick Demo Login Credentials Help card -->
            <div class="card bg-light border-0 rounded-3 mt-4 p-3 shadow-sm">
                <h6 class="brand-font text-dark mb-2"><i class="bi bi-info-circle-fill text-warning me-1"></i> Demo Access Accounts:</h6>
                <table class="table table-borderless table-sm mb-0 text-muted" style="font-size: 12px;">
                    <tr>
                        <td><strong>Passenger:</strong></td>
                        <td>user@example.com</td>
                        <td><code>password</code></td>
                    </tr>
                    <tr>
                        <td><strong>Driver:</strong></td>
                        <td>driver1@example.com</td>
                        <td><code>password</code></td>
                    </tr>
                    <tr>
                        <td><strong>Admin:</strong></td>
                        <td>admin</td>
                        <td><code>admin123</code></td>
                    </tr>
                </table>
            </div>
            
        </div>
    </div>
</div>
