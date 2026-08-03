<!-- Smart Cab Booking System - Admin Vehicles Overview -->
<!-- Location: views/admin/vehicles.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-car-front text-primary me-2"></i>Registered Vehicles Registry</h4>
        
        <?php if (empty($vehicles)): ?>
            <p class="text-muted text-center py-4 mb-0">No driver vehicle specifications logged in database records.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Vehicle ID</th>
                            <th>Driver Owner</th>
                            <th>Category Type</th>
                            <th>Manufacturer Model</th>
                            <th>Plate Registration No</th>
                            <th>Body Color</th>
                            <th>Register Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td><strong>#<?= $v['id'] ?></strong></td>
                                <td><div class="fw-semibold text-dark"><?= e($v['driver_name']) ?></div></td>
                                <td>
                                    <span class="badge bg-secondary"><?= ucfirst(e($v['type'])) ?></span>
                                </td>
                                <td><?= e($v['model']) ?></td>
                                <td><strong><?= e($v['plate_number']) ?></strong></td>
                                <td><?= e($v['color']) ?></td>
                                <td><?= date('d M Y', strtotime($v['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
