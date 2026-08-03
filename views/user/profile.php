<!-- Smart Cab Booking System - User Profile View -->
<!-- Location: views/user/profile.php -->

<div class="container py-4">
    <div class="row">
        
        <!-- Profile Info Update Card -->
        <div class="col-md-6 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm h-100">
                <h4 class="brand-font text-dark mb-4"><i class="bi bi-person-gear text-primary me-2"></i>Profile Settings</h4>
                
                <form action="index.php?controller=user&action=postProfile" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control form-control-custom" value="<?= e($user['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address (Read-Only)</label>
                        <input type="email" class="form-control form-control-custom bg-light text-muted" value="<?= e($user['email']) ?>" readonly>
                        <div class="form-text text-muted small">Registered email address cannot be changed.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="form-control form-control-custom" value="<?= e($user['phone']) ?>" pattern="[0-9]{10}" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-custom px-4 py-2.5">
                        <i class="bi bi-save me-1"></i> Update Profile Settings
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Password Settings Card -->
        <div class="col-md-6 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm h-100">
                <h4 class="brand-font text-dark mb-4"><i class="bi bi-shield-lock text-primary me-2"></i>Change Password</h4>
                
                <form action="index.php?controller=user&action=postPassword" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="old_password" class="form-label fw-semibold">Current Password</label>
                        <input type="password" name="old_password" id="old_password" class="form-control form-control-custom" placeholder="Enter current password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control form-control-custom" placeholder="Enter new password" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-custom" placeholder="Confirm new password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-custom px-4 py-2.5">
                        <i class="bi bi-key me-1"></i> Change Account Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
