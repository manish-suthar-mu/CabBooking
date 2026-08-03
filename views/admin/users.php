<!-- Smart Cab Booking System - Admin Users Management -->
<!-- Location: views/admin/users.php -->

<div class="container py-4">
    <div class="card-premium p-4 border-0 shadow-sm">
        <h4 class="brand-font text-dark mb-4"><i class="bi bi-people-fill text-primary me-2"></i>Passenger Accounts Manager</h4>
        
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Join Date</th>
                        <th>Account Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><strong>#<?= $user['id'] ?></strong></td>
                            <td><div class="fw-semibold text-dark"><?= e($user['name']) ?></div></td>
                            <td><?= e($user['email']) ?></td>
                            <td><?= e($user['phone']) ?></td>
                            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-status-<?= $user['id'] ?> <?= $user['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm toggle-user-btn <?= $user['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                        data-id="<?= $user['id'] ?>" 
                                        data-status="<?= $user['status'] ?>">
                                    <i class="bi <?= $user['status'] === 'active' ? 'bi-shield-slash' : 'bi-shield-check' ?>"></i>
                                    <span class="btn-label-<?= $user['id'] ?>"><?= $user['status'] === 'active' ? 'Suspend' : 'Activate' ?></span>
                                </button>
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
    $('.toggle-user-btn').on('click', function() {
        const btn = $(this);
        const userId = btn.data('id');
        const currentStatus = btn.data('status');
        const targetStatus = currentStatus === 'active' ? 'suspended' : 'active';
        
        btn.prop('disabled', true);
        
        $.ajax({
            url: 'index.php?controller=admin&action=toggleUserStatus',
            method: 'POST',
            data: {
                user_id: userId,
                status: targetStatus,
                csrf_token: window.csrfToken
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    window.showToast('Account Updated', res.message, 'success');
                    
                    // Update Badge status
                    const badge = $('.badge-status-' + userId);
                    const label = $('.btn-label-' + userId);
                    
                    if (targetStatus === 'suspended') {
                        badge.removeClass('bg-success').addClass('bg-danger').text('Suspended');
                        label.text('Activate');
                        btn.removeClass('btn-outline-danger').addClass('btn-outline-success');
                        btn.data('status', 'suspended');
                    } else {
                        badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                        label.text('Suspend');
                        btn.removeClass('btn-outline-success').addClass('btn-outline-danger');
                        btn.data('status', 'active');
                    }
                } else {
                    window.showToast('Update Failed', res.message, 'danger');
                }
                btn.prop('disabled', false);
            },
            error: function() {
                window.showToast('Server Error', 'Failed to connect to local server.', 'danger');
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
