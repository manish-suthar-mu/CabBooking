<!-- Smart Cab Booking System - Admin Drivers Management -->
<!-- Location: views/admin/drivers.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-person-badge-fill text-primary me-2"></i>Driver Applications Manager</h4>
        
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>Driver ID</th>
                        <th>Name</th>
                        <th>License & Contact</th>
                        <th>Vehicle Assigned</th>
                        <th>Availability</th>
                        <th>Application Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drivers as $d): ?>
                        <tr id="driver_row_<?= $d['id'] ?>">
                            <td><strong>#<?= $d['id'] ?></strong></td>
                            <td><div class="fw-semibold text-dark"><?= e($d['name']) ?></div></td>
                            <td>
                                <div class="small fw-semibold text-dark">DL: <?= e($d['license_no']) ?></div>
                                <div class="text-muted small">E: <?= e($d['email']) ?> | P: <?= e($d['phone']) ?></div>
                            </td>
                            <td>
                                <?php if ($d['vehicle_type']): ?>
                                    <span class="badge bg-secondary"><?= ucfirst(e($d['vehicle_type'])) ?></span>
                                    <div class="text-muted small" style="font-size: 11px;"><?= e($d['model']) ?> (<?= e($d['plate_number']) ?>)</div>
                                <?php else: ?>
                                    <span class="text-muted small">No vehicle registered</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($d['status'] === 'approved'): ?>
                                    <span class="badge <?= $d['is_online'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $d['is_online'] ? 'Online' : 'Offline' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $statusBadge = 'bg-warning text-dark';
                                    if ($d['status'] === 'approved') $statusBadge = 'bg-success';
                                    if ($d['status'] === 'suspended') $statusBadge = 'bg-danger';
                                ?>
                                <span class="badge driver-badge-<?= $d['id'] ?> <?= $statusBadge ?>">
                                    <?= ucfirst(str_replace('_', ' ', $d['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btn-group-<?= $d['id'] ?>">
                                    <?php if ($d['status'] === 'pending_approval'): ?>
                                        <!-- Approve / Reject controls -->
                                        <button type="button" class="btn btn-sm btn-success approve-driver-btn fw-bold me-1" data-id="<?= $d['id'] ?>">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    <?php elseif ($d['status'] === 'approved'): ?>
                                        <!-- Suspend control -->
                                        <button type="button" class="btn btn-sm btn-outline-danger suspend-driver-btn" data-id="<?= $d['id'] ?>" data-status="suspended">
                                            <i class="bi bi-shield-slash"></i> Suspend
                                        </button>
                                    <?php elseif ($d['status'] === 'suspended'): ?>
                                        <!-- Unsuspend control -->
                                        <button type="button" class="btn btn-sm btn-outline-success suspend-driver-btn" data-id="<?= $d['id'] ?>" data-status="approved">
                                            <i class="bi bi-shield-check"></i> Activate
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. Approve Application Action
    $(document).on('click', '.approve-driver-btn', function() {
        const btn = $(this);
        const driverId = btn.data('id');
        btn.prop('disabled', true);

        $.ajax({
            url: 'index.php?controller=admin&action=approveDriver',
            method: 'POST',
            data: { driver_id: driverId, csrf_token: window.csrfToken },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    window.showToast('Application Approved', res.message, 'success');
                    
                    // Update Badge and Action buttons
                    $('.driver-badge-' + driverId).removeClass('bg-warning text-dark').addClass('bg-success').text('Approved');
                    
                    const actionGroup = $('.action-btn-group-' + driverId);
                    actionGroup.html(`
                        <button type="button" class="btn btn-sm btn-outline-danger suspend-driver-btn" data-id="${driverId}" data-status="suspended">
                            <i class="bi bi-shield-slash"></i> Suspend
                        </button>
                    `);
                } else {
                    window.showToast('Approval Failed', res.message, 'danger');
                    btn.prop('disabled', false);
                }
            }
        });
    });

    // 2. Suspend/Unsuspend Toggle Action
    $(document).on('click', '.suspend-driver-btn', function() {
        const btn = $(this);
        const driverId = btn.data('id');
        const targetStatus = btn.data('status'); // approved or suspended
        btn.prop('disabled', true);

        $.ajax({
            url: 'index.php?controller=admin&action=suspendDriver',
            method: 'POST',
            data: { driver_id: driverId, status: targetStatus, csrf_token: window.csrfToken },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    window.showToast('Driver Status Updated', res.message, 'success');
                    
                    const badge = $('.driver-badge-' + driverId);
                    const actionGroup = $('.action-btn-group-' + driverId);

                    if (targetStatus === 'suspended') {
                        badge.removeClass('bg-success').addClass('bg-danger').text('Suspended');
                        actionGroup.html(`
                            <button type="button" class="btn btn-sm btn-outline-success suspend-driver-btn" data-id="${driverId}" data-status="approved">
                                <i class="bi bi-shield-check"></i> Activate
                            </button>
                        `);
                    } else {
                        badge.removeClass('bg-danger').addClass('bg-success').text('Approved');
                        actionGroup.html(`
                            <button type="button" class="btn btn-sm btn-outline-danger suspend-driver-btn" data-id="${driverId}" data-status="suspended">
                                <i class="bi bi-shield-slash"></i> Suspend
                            </button>
                        `);
                    }
                } else {
                    window.showToast('Action Failed', res.message, 'danger');
                    btn.prop('disabled', false);
                }
            }
        });
    });
});
</script>
