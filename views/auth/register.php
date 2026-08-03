<!-- Smart Cab Booking System - User Registration View -->
<!-- Location: views/auth/register.php -->

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-premium p-4 shadow border-0 mt-3">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus text-primary display-4"></i>
                    <h3 class="brand-font mt-2 text-dark">Passenger Signup</h3>
                    <p class="text-muted small">Register to book cabs, check estimates, and track rides</p>
                </div>
                
                <form action="index.php?controller=auth&action=postRegister" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
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
                            <input type="tel" name="phone" id="phone" class="form-control form-control-custom border-start-0 ps-0" placeholder="Enter 10-digit number" pattern="[0-9]{10}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control form-control-custom border-start-0 ps-0" placeholder="Create strong password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5 mt-3">
                        <i class="bi bi-person-check-fill me-2"></i> Register Account
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
