<!-- Smart Cab Booking System - Driver Vehicle Specs View -->
<!-- Location: views/driver/vehicle.php -->

<div class="container py-4">
    <div class="row">
        
        <div class="col-md-6 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm">
                <h4 class="brand-font text-dark mb-4"><i class="bi bi-car-front-fill text-primary me-2"></i>Vehicle Profile Specifications</h4>
                
                <form action="index.php?controller=driver&action=postVehicle" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="type" class="form-label fw-semibold">Vehicle Category Type</label>
                        <select name="type" id="type" class="form-select form-control-custom" required>
                            <option value="car" <?= ($vehicle && $vehicle['type'] === 'car') ? 'selected' : '' ?>>Car (Sedan/SUV)</option>
                            <option value="auto" <?= ($vehicle && $vehicle['type'] === 'auto') ? 'selected' : '' ?>>Auto Rickshaw (3-Wheeler)</option>
                            <option value="bike" <?= ($vehicle && $vehicle['type'] === 'bike') ? 'selected' : '' ?>>Bike (Motorcycle)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="model" class="form-label fw-semibold">Manufacturer Model Name</label>
                        <input type="text" name="model" id="model" class="form-control form-control-custom" value="<?= $vehicle ? e($vehicle['model']) : '' ?>" placeholder="e.g. Hyundai i20 / Bajaj RE" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="plate_number" class="form-label fw-semibold">License Plate Registration Number</label>
                        <input type="text" name="plate_number" id="plate_number" class="form-control form-control-custom" value="<?= $vehicle ? e($vehicle['plate_number']) : '' ?>" placeholder="e.g. DL-3C-CA-1111" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="color" class="form-label fw-semibold">Body Color</label>
                        <input type="text" name="color" id="color" class="form-control form-control-custom" value="<?= $vehicle ? e($vehicle['color']) : '' ?>" placeholder="e.g. White / Yellow-Green" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-custom px-4 py-2.5">
                        <i class="bi bi-save me-1"></i> Save Vehicle Specifications
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card bg-light border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
                <h5 class="brand-font text-dark mb-3"><i class="bi bi-info-circle text-primary me-2"></i>About Vehicle Types</h5>
                <p class="text-muted small leading-relaxed">
                    Selecting your vehicle type controls what kind of booking requests you receive. Passengers will see your vehicle details (model, plate number, color) when you accept their bookings.
                </p>
                <hr>
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-shield-check text-success fs-2"></i>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Safety Regulation</h6>
                        <small class="text-muted d-block">Ensure plate details match legal registration cards.</small>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
