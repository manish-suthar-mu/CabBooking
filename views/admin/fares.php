<!-- Smart Cab Booking System - Admin Fare Rates Configuration -->
<!-- Location: views/admin/fares.php -->

<div class="container py-4">
    <div class="row">
        
        <!-- Fares Rates List Card -->
        <div class="col-lg-7 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm">
                <h4 class="brand-font text-dark mb-4"><i class="bi bi-currency-rupee text-primary me-2"></i>Fare Configurations</h4>
                <p class="text-muted small">Current rates per vehicle type. Users see estimates based on these rates.</p>

                <div class="table-responsive">
                    <table class="table table-custom table-hover">
                        <thead>
                            <tr>
                                <th>Vehicle Type</th>
                                <th>Base Fare</th>
                                <th>Rate / Kilometer</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rates as $r): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary"><?= ucfirst(e($r['vehicle_type'])) ?></span>
                                    </td>
                                    <td><strong>₹<?= number_format($r['base_fare'], 2) ?></strong></td>
                                    <td><strong>₹<?= number_format($r['per_km_rate'], 2) ?></strong></td>
                                    <td><?= date('d M Y h:i A', strtotime($r['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fares Editor Card -->
        <div class="col-lg-5 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm">
                <h4 class="brand-font text-dark mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Rates</h4>
                
                <form action="index.php?controller=admin&action=postFare" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="mb-3">
                        <label for="vehicle_type" class="form-label fw-semibold">Select Vehicle Type</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-select form-control-custom" required>
                            <option value="car">Car (Sedan/SUV)</option>
                            <option value="auto">Auto Rickshaw</option>
                            <option value="bike">Bike (Motorcycle)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="base_fare" class="form-label fw-semibold">Base Fare Rate (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted">₹</span>
                            <input type="number" name="base_fare" id="base_fare" step="0.01" class="form-control form-control-custom" placeholder="e.g. 50.00" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="per_km_rate" class="form-label fw-semibold">Rate per Kilometer (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted">₹</span>
                            <input type="number" name="per_km_rate" id="per_km_rate" step="0.01" class="form-control form-control-custom" placeholder="e.g. 12.00" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5">
                        <i class="bi bi-save me-1"></i> Save Rate Configuration
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
