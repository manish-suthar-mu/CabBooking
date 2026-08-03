// Smart Cab Booking System - Client Application Script
// Location: assets/js/app.js

$(document).ready(function() {
    // 1. Toast Notification Helper
    window.showToast = function(title, message, type = 'primary') {
        const toastId = 'toast_' + Math.random().toString(36).substr(2, 9);
        let borderColor = 'var(--primary)';
        if (type === 'success') borderColor = 'var(--success)';
        if (type === 'danger') borderColor = 'var(--danger)';
        if (type === 'warning') borderColor = 'var(--warning)';

        const html = `
            <div id="${toastId}" class="custom-toast" style="border-left-color: ${borderColor}">
                <div>
                    <h6 class="mb-1 font-weight-bold text-dark">${title}</h6>
                    <small class="text-muted d-block">${message}</small>
                </div>
            </div>
        `;
        $('body').append(html);

        // Slide out and remove after 4 seconds
        setTimeout(() => {
            $(`#${toastId}`).fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    };

    // 2. AJAX Notifications Polling (Every 5 seconds)
    let lastUnreadCount = 0;
    let shownNotificationIds = new Set(); // Keep track of shown notification IDs
    
    function checkNotifications() {
        if (!window.userId && !window.driverId) return;

        let url = '';
        if (window.userId) {
            url = 'index.php?controller=user&action=getNotifications';
        } else if (window.driverId) {
            url = 'index.php?controller=driver&action=getNotifications';
        }

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const count = response.unread_count;
                    
                    // Update Badge Count
                    if (count > 0) {
                        $('#navNotificationBadge').text(count).show();
                        
                        // Check for new notifications we haven't shown yet
                        response.notifications.forEach(notif => {
                            if (!shownNotificationIds.has(notif.id) && !notif.is_read) {
                                showToast(notif.title, notif.message, 'primary');
                                shownNotificationIds.add(notif.id);
                                
                                // Play a soft chime for notification simulation (optional, standard beep)
                                try {
                                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                                    const oscillator = audioCtx.createOscillator();
                                    oscillator.type = 'sine';
                                    oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
                                    oscillator.connect(audioCtx.destination);
                                    oscillator.start();
                                    oscillator.stop(audioCtx.currentTime + 0.15);
                                } catch(e) { /* ignore audio permission blockers */ }
                                
                                // Auto-mark as read
                                let markReadUrl = '';
                                if (window.userId) {
                                    markReadUrl = 'index.php?controller=user&action=markNotificationRead';
                                } else if (window.driverId) {
                                    markReadUrl = 'index.php?controller=driver&action=markNotificationRead';
                                }
                                
                                if (markReadUrl) {
                                    $.ajax({
                                        url: markReadUrl,
                                        method: 'POST',
                                        data: {
                                            csrf_token: window.csrfToken,
                                            notification_id: notif.id
                                        },
                                        dataType: 'json'
                                    });
                                }
                            }
                        });
                    } else {
                        $('#navNotificationBadge').hide();
                    }
                    
                    lastUnreadCount = count;
                }
            },
            error: function(err) {
                console.error("Failed to fetch notifications:", err);
            }
        });
    }

    // Initialize notification polling
    if (window.userId || window.driverId) {
        checkNotifications();
        setInterval(checkNotifications, 5000);
    }

    // 3. User Booking Form Logic
    $('#bookingForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Searching Drivers...');

        $.ajax({
            url: 'index.php?controller=booking&action=book',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showToast('Booking Placed', response.message, 'success');
                    
                    // Redirect to tracking page
                    setTimeout(() => {
                        window.location.href = 'index.php?controller=booking&action=tracking&booking_id=' + response.booking_id;
                    }, 1500);
                } else {
                    showToast('Booking Failed', response.message, 'danger');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                showToast('Error', 'Unable to reach local server. Check connection.', 'danger');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // 4. Rating and Reviews Modal submission
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        
        btn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: 'index.php?controller=booking&action=submitReview',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showToast('Feedback Received', response.message, 'success');
                    $('#reviewModal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('Submission Failed', response.message, 'danger');
                    btn.prop('disabled', false).text('Submit Rating');
                }
            },
            error: function() {
                showToast('Error', 'Failed to connect to local server.', 'danger');
                btn.prop('disabled', false).text('Submit Rating');
            }
        });
    });
});
