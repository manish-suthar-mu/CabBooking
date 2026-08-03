<!-- Smart Cab Booking System - Admin Dashboard -->
<!-- Location: views/admin/dashboard.php -->

<div class="container py-4">

    <!-- 1. Statistics Cards Grid -->
    <div class="row g-3 mb-4">
        
        <!-- Total Users -->
        <div class="col-6 col-md-3">
            <div class="card-premium p-3 border-0 shadow-sm text-center">
                <i class="bi bi-people-fill text-primary fs-3 mb-1"></i>
                <div class="text-muted small fw-semibold">Total Users</div>
                <h4 class="brand-font text-dark mb-0 font-weight-bold"><?= $stats['total_users'] ?></h4>
            </div>
        </div>

        <!-- Total Drivers -->
        <div class="col-6 col-md-3">
            <div class="card-premium p-3 border-0 shadow-sm text-center">
                <i class="bi bi-person-badge-fill text-warning fs-3 mb-1"></i>
                <div class="text-muted small fw-semibold">Total Drivers</div>
                <h4 class="brand-font text-dark mb-0 font-weight-bold"><?= $stats['total_drivers'] ?></h4>
                <div class="text-muted" style="font-size: 10px;">Active: <strong><?= $stats['active_drivers'] ?> online</strong></div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="col-6 col-md-3">
            <div class="card-premium p-3 border-0 shadow-sm text-center">
                <i class="bi bi-calendar3-event text-success fs-3 mb-1"></i>
                <div class="text-muted small fw-semibold">Total Bookings</div>
                <h4 class="brand-font text-dark mb-0 font-weight-bold"><?= $stats['total_bookings'] ?></h4>
                <div class="text-muted" style="font-size: 10px;">Done: <strong><?= $stats['completed_rides'] ?></strong> | Cancel: <strong><?= $stats['cancelled_rides'] ?></strong></div>
            </div>
        </div>

        <!-- Platform Commission -->
        <div class="col-6 col-md-3">
            <div class="card-premium p-3 border-0 shadow-sm bg-primary text-white text-center">
                <i class="bi bi-wallet2 text-warning fs-3 mb-1"></i>
                <div class="text-white-50 small fw-semibold">Admin Commission</div>
                <h4 class="brand-font text-white mb-0 font-weight-bold">₹<?= number_format($stats['total_earnings'], 2) ?></h4>
                <div class="text-white-50" style="font-size: 10px;">Gross: <strong>₹<?= number_format($stats['total_revenue'], 2) ?></strong></div>
            </div>
        </div>

    </div>

    <!-- 2. Charts and Driver Tracking Map -->
    <div class="row mb-4">
        
        <!-- Live Driver GPS Simulation Map -->
        <div class="col-lg-8 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm h-100">
                <h5 class="brand-font text-dark mb-2"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Live Driver Simulator Dispatch Map</h5>
                <p class="text-muted small">Real-time coordinates visualizer tracking online driver locations and active coordinates.</p>
                
                <div class="map-canvas-container">
                    <canvas id="adminMapCanvas"></canvas>
                </div>
            </div>
        </div>

        <!-- Metric Graphics -->
        <div class="col-lg-4 mb-4">
            <div class="card-premium p-4 border-0 shadow-sm h-100 text-center">
                <h5 class="brand-font text-dark mb-3 text-start"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Booking Share Analytics</h5>
                
                <?php if ($stats['total_bookings'] == 0): ?>
                    <div class="py-5 text-muted">
                        <i class="bi bi-pie-chart display-3 mb-2"></i>
                        <p class="small">No booking charts data generated yet.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-center my-4">
                        <!-- SVG Donut Chart -->
                        <svg width="180" height="180" viewBox="0 0 42 42" class="donut">
                            <circle class="donut-hole" cx="21" cy="21" r="15.91549430918954" fill="#fff"></circle>
                            <circle class="donut-ring" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="#f1f5f9" stroke-width="3"></circle>
                            
                            <?php 
                                $comp = $stats['completed_rides'];
                                $canc = $stats['cancelled_rides'];
                                $tot = $comp + $canc;
                                if ($tot == 0) $tot = 1;
                                $comp_pct = ($comp / $tot) * 100;
                                $canc_pct = ($canc / $tot) * 100;
                            ?>
                            <!-- Completed Segment (Success Green) -->
                            <circle class="donut-segment" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="var(--success)" stroke-width="3.2" 
                                    stroke-dasharray="<?= $comp_pct ?> <?= 100 - $comp_pct ?>" stroke-dashoffset="25"></circle>
                            
                            <!-- Cancelled Segment (Danger Red) -->
                            <circle class="donut-segment" cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="var(--danger)" stroke-width="3.2" 
                                    stroke-dasharray="<?= $canc_pct ?> <?= 100 - $canc_pct ?>" stroke-dashoffset="<?= 125 - $comp_pct ?>"></circle>
                        </svg>
                    </div>
                    
                    <div class="text-start border-top pt-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span><i class="bi bi-circle-fill text-success me-1"></i> Completed Rides:</span>
                            <span class="fw-bold text-dark"><?= $stats['completed_rides'] ?> (<?= round($comp_pct, 1) ?>%)</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span><i class="bi bi-circle-fill text-danger me-1"></i> Cancelled Rides:</span>
                            <span class="fw-bold text-dark"><?= $stats['cancelled_rides'] ?> (<?= round($canc_pct, 1) ?>%)</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- 3. Latest Bookings Audit Table -->
    <div class="row">
        <div class="col-12">
            <div class="card-premium p-4 border-0 shadow-sm">
                <h5 class="brand-font text-dark mb-4"><i class="bi bi-list-columns-reverse text-primary me-2"></i>Recent Booking Activities</h5>
                
                <?php if (empty($latestBookings)): ?>
                    <p class="text-muted text-center py-4 mb-0">No booking activities registered inside database.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Passenger</th>
                                    <th>Driver Assigned</th>
                                    <th>Pickup Location</th>
                                    <th>Drop-off Location</th>
                                    <th>Distance</th>
                                    <th>Actual Fare</th>
                                    <th>Trip Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latestBookings as $ride): ?>
                                    <tr>
                                        <td><strong>#<?= $ride['id'] ?></strong></td>
                                        <td><?= e($ride['user_name']) ?></td>
                                        <td><?= $ride['driver_name'] ? e($ride['driver_name']) : '<span class="text-muted small">Not assigned</span>' ?></td>
                                        <td><div class="small fw-semibold text-dark"><?= e($ride['pickup_location']) ?></div></td>
                                        <td><div class="small fw-semibold text-dark"><?= e($ride['drop_location']) ?></div></td>
                                        <td><?= e($ride['distance']) ?> km</td>
                                        <td><strong>₹<?= number_format($ride['actual_fare'] ?? $ride['estimated_fare'], 2) ?></strong></td>
                                        <td>
                                            <?php 
                                                $badgeColor = 'bg-warning text-dark';
                                                if ($ride['status'] === 'completed') $badgeColor = 'bg-success';
                                                if ($ride['status'] === 'cancelled') $badgeColor = 'bg-danger';
                                                if ($ride['status'] === 'ongoing') $badgeColor = 'bg-primary';
                                            ?>
                                            <span class="badge <?= $badgeColor ?>"><?= ucfirst(e($ride['status'])) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Admin Live coordinates Map Simulator Logic -->
<script>
$(document).ready(function() {
    const canvas = document.getElementById('adminMapCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const container = canvas.parentElement;
    canvas.width = container.clientWidth;
    canvas.height = 360;

    // Load registered driver locations from server payload
    const drivers = <?= json_encode($drivers) ?>;

    // Standard road mapping boundaries (Delhi/New Delhi bounds)
    const minLat = 28.40;
    const maxLat = 28.70;
    const minLng = 77.00;
    const maxLng = 77.40;

    function gpsToPixel(lat, lng) {
        const x = ((lng - minLng) / (maxLng - minLng)) * (canvas.width - 100) + 50;
        const y = canvas.height - (((lat - minLat) / (maxLat - minLat)) * (canvas.height - 100) + 50);
        return { x, y };
    }

    function drawAdminMap() {
        // Clear background
        ctx.fillStyle = '#e2e8f0';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Draw basic layout grid
        ctx.lineWidth = 8;
        ctx.strokeStyle = '#ffffff';
        ctx.lineCap = 'round';
        
        // Draw grid mock lines
        for(let offset = 40; offset < canvas.width; offset += 100) {
            ctx.beginPath();
            ctx.moveTo(offset, 0); ctx.lineTo(offset, canvas.height);
            ctx.stroke();
        }
        for(let offset = 30; offset < canvas.height; offset += 80) {
            ctx.beginPath();
            ctx.moveTo(0, offset); ctx.lineTo(canvas.width, offset);
            ctx.stroke();
        }

        // Draw Drivers pins
        drivers.forEach(d => {
            const lat = parseFloat(d.latitude);
            const lng = parseFloat(d.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            const pt = gpsToPixel(lat, lng);
            const color = d.is_online == 1 ? '#10b981' : '#64748b'; // online green, offline slate
            
            // Draw driver pin dot
            ctx.beginPath();
            ctx.arc(pt.x, pt.y, 7, 0, 2 * Math.PI);
            ctx.fillStyle = color;
            ctx.fill();
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Label text
            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 9px Inter';
            ctx.textAlign = 'center';
            const statusLabel = d.is_online == 1 ? ' (Online)' : ' (Offline)';
            ctx.fillText(d.name + statusLabel, pt.x, pt.y - 10);
        });
    }

    drawAdminMap();
});
</script>
