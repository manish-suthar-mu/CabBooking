// Smart Cab Booking System - Live Map Simulation Canvas Engine
// Location: assets/js/map.js

class MapSimulation {
    constructor(canvasId, bookingId, role, coordinates) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;
        this.ctx = this.canvas.bind ? this.canvas.getContext('2d') : this.canvas.getContext('2d');
        this.bookingId = bookingId;
        this.role = role; // 'user' or 'driver'
        
        // Coordinates: pickup {lat, lng}, drop {lat, lng}, driver {lat, lng}
        this.pickup = coordinates.pickup;
        this.drop = coordinates.drop;
        this.driver = coordinates.driver || { lat: coordinates.pickup.lat + 0.01, lng: coordinates.pickup.lng - 0.01 };
        
        this.status = 'pending';
        this.routePoints = [];
        this.currentStep = 0;
        this.isAnimating = false;

        // Generate street layout grid based on dimensions
        this.generateCityGrid();

        this.resizeCanvas();
        window.addEventListener('resize', () => this.resizeCanvas());
        
        // Start polling/updating loop
        this.initLoop();
    }

    resizeCanvas() {
        const container = this.canvas.parentElement;
        this.canvas.width = container.clientWidth;
        this.canvas.height = 400; // fixed height
        this.draw();
    }

    generateCityGrid() {
        // Draw standard blocks of roads
        this.roads = [];
        const w = this.canvas.width;
        const h = this.canvas.height;
        
        // Add vertical main streets
        for (let x = 50; x < w; x += 100) {
            this.roads.push({ x1: x, y1: 0, x2: x, y2: h, type: 'vertical' });
        }
        // Add horizontal main streets
        for (let y = 50; y < h; y += 80) {
            this.roads.push({ x1: 0, y1: y, x2: w, y2: y, type: 'horizontal' });
        }
    }

    // Convert GPS coordinates to local Canvas pixels
    gpsToPixel(lat, lng) {
        // Find boundaries
        // Delhi: lat ~ 28.6, lng ~ 77.2
        // We calculate bounds using pickup, drop, and driver locations with padding
        const points = [this.pickup, this.drop, this.driver];
        const lats = points.map(p => p.lat);
        const lngs = points.map(p => p.lng);
        
        const minLat = Math.min(...lats) - 0.005;
        const maxLat = Math.max(...lats) + 0.005;
        const minLng = Math.min(...lngs) - 0.005;
        const maxLng = Math.max(...lngs) + 0.005;

        // Map linearly to canvas space
        const x = ((lng - minLng) / (maxLng - minLng)) * (this.canvas.width - 100) + 50;
        // Latitude increases upwards, but canvas y increases downwards, so we flip it
        const y = this.canvas.height - (((lat - minLat) / (maxLat - minLat)) * (this.canvas.height - 100) + 50);

        return { x, y };
    }

    // Generate step points on the grid representing a road path between point A and B
    generatePath(startGps, endGps) {
        const start = this.gpsToPixel(startGps.lat, startGps.lng);
        const end = this.gpsToPixel(endGps.lat, endGps.lng);
        
        const points = [];
        const steps = 60; // 60 ticks for trip
        
        // Simple Manhattan routing (horizontal then vertical)
        const midX = end.x;
        const midY = start.y;

        // Step from start to corner (midX, midY)
        const halfSteps = Math.floor(steps / 2);
        for (let i = 0; i <= halfSteps; i++) {
            const ratio = i / halfSteps;
            points.push({
                x: start.x + (midX - start.x) * ratio,
                y: start.y + (midY - start.y) * ratio
            });
        }
        
        // Step from corner to end
        for (let i = 1; i <= halfSteps; i++) {
            const ratio = i / halfSteps;
            points.push({
                x: midX,
                y: midY + (end.y - midY) * ratio
            });
        }

        return points;
    }

    // Convert local canvas pixels back to mock GPS coordinates
    pixelToGps(x, y) {
        const points = [this.pickup, this.drop, this.driver];
        const lats = points.map(p => p.lat);
        const lngs = points.map(p => p.lng);
        
        const minLat = Math.min(...lats) - 0.005;
        const maxLat = Math.max(...lats) + 0.005;
        const minLng = Math.min(...lngs) - 0.005;
        const maxLng = Math.max(...lngs) + 0.005;

        const lng = minLng + ((x - 50) / (this.canvas.width - 100)) * (maxLng - minLng);
        const lat = minLat + (((this.canvas.height - y) - 50) / (this.canvas.height - 100)) * (maxLat - minLat);

        return { lat, lng };
    }

    draw() {
        const ctx = this.ctx;
        const w = this.canvas.width;
        const h = this.canvas.height;
        
        // 1. Clear background (parks green)
        ctx.fillStyle = '#e8f5e9';
        ctx.fillRect(0, 0, w, h);

        // Draw park features (circles / rectangles)
        ctx.fillStyle = '#c8e6c9';
        ctx.beginPath();
        ctx.arc(100, 100, 45, 0, 2 * Math.PI);
        ctx.arc(w - 150, h - 100, 60, 0, 2 * Math.PI);
        ctx.fill();

        // 2. Draw streets grid
        ctx.lineWidth = 14;
        ctx.strokeStyle = '#f1f5f9';
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        if (this.roads) {
            this.roads.forEach(road => {
                ctx.beginPath();
                ctx.moveTo(road.x1, road.y1);
                ctx.lineTo(road.x2, road.y2);
                ctx.stroke();
            });

            // Draw yellow dashed dividers on roads
            ctx.lineWidth = 1;
            ctx.strokeStyle = '#fef08a';
            ctx.setLineDash([5, 8]);
            this.roads.forEach(road => {
                ctx.beginPath();
                ctx.moveTo(road.x1, road.y1);
                ctx.lineTo(road.x2, road.y2);
                ctx.stroke();
            });
        }
        ctx.setLineDash([]); // Reset line dash

        // Get coordinates in pixels
        const pickupPixel = this.gpsToPixel(this.pickup.lat, this.pickup.lng);
        const dropPixel = this.gpsToPixel(this.drop.lat, this.drop.lng);
        const driverPixel = this.gpsToPixel(this.driver.lat, this.driver.lng);

        // 3. Draw route line if active (accepted/ongoing)
        if (this.status === 'ongoing') {
            ctx.lineWidth = 4;
            ctx.strokeStyle = '#3b82f6';
            ctx.beginPath();
            ctx.moveTo(pickupPixel.x, pickupPixel.y);
            ctx.lineTo(dropPixel.x, dropPixel.y);
            ctx.stroke();
        } else if (this.status === 'accepted') {
            ctx.lineWidth = 4;
            ctx.strokeStyle = '#f59e0b';
            ctx.beginPath();
            ctx.moveTo(driverPixel.x, driverPixel.y);
            ctx.lineTo(pickupPixel.x, pickupPixel.y);
            ctx.stroke();
        }

        // 4. Draw Markers
        // Pickup Marker (Green Pin)
        this.drawPin(pickupPixel.x, pickupPixel.y, '#10b981', 'A');
        
        // Drop Marker (Red Pin)
        this.drawPin(dropPixel.x, dropPixel.y, '#ef4444', 'B');

        // Cab Indicator
        this.drawCab(driverPixel.x, driverPixel.y);
    }

    drawPin(x, y, color, label) {
        const ctx = this.ctx;
        ctx.save();
        ctx.beginPath();
        ctx.arc(x, y - 12, 8, 0, 2 * Math.PI);
        ctx.fillStyle = color;
        ctx.fill();
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 2;
        ctx.stroke();
        
        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.lineTo(x - 4, y - 6);
        ctx.lineTo(x + 4, y - 6);
        ctx.closePath();
        ctx.fillStyle = color;
        ctx.fill();

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 9px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(label, x, y - 12);
        ctx.restore();
    }

    drawCab(x, y) {
        const ctx = this.ctx;
        ctx.save();
        
        // Cab shadow
        ctx.beginPath();
        ctx.arc(x, y + 2, 7, 0, 2 * Math.PI);
        ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
        ctx.fill();

        // Cab Body (Gold/Yellow Cab Theme)
        ctx.beginPath();
        ctx.arc(x, y, 7, 0, 2 * Math.PI);
        ctx.fillStyle = '#f59e0b';
        ctx.fill();
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#1e293b';
        ctx.stroke();

        // Inner core (windshield mock-up)
        ctx.beginPath();
        ctx.arc(x, y - 1, 3, 0, Math.PI, true);
        ctx.fillStyle = '#38bdf8';
        ctx.fill();

        // Cab Light Badge
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(x - 2, y - 1, 4, 1.5);
        ctx.restore();
    }

    initLoop() {
        const checkInterval = 2000; // poll every 2s
        
        const updateStatus = () => {
            $.ajax({
                url: 'index.php?controller=booking&action=status&booking_id=' + this.bookingId,
                method: 'GET',
                dataType: 'json',
                success: (res) => {
                    if (res.status === 'success') {
                        const newStatus = res.booking_status; // Use booking_status from response!
                        const driverLoc = res.driver;

                        // Detect state transitions
                        if (newStatus !== this.status) {
                            this.status = newStatus;
                            this.currentStep = 0;
                            
                            // Rebuild animation path on state transition
                            if (this.role === 'driver') {
                                if (this.status === 'accepted') {
                                    this.routePoints = this.generatePath(this.driver, this.pickup);
                                } else if (this.status === 'ongoing') {
                                    this.routePoints = this.generatePath(this.pickup, this.drop);
                                }
                            }
                        }

                        // For User Role: Listen for coordinates updates from the database
                        if (this.role === 'user' && driverLoc) {
                            this.driver = { lat: parseFloat(driverLoc.latitude), lng: parseFloat(driverLoc.longitude) };
                            
                            // Check if state is completed or cancelled, trigger redirection
                            if (this.status === 'completed') {
                                window.location.href = 'index.php?controller=booking&action=payment&booking_id=' + this.bookingId;
                                return;
                            } else if (this.status === 'cancelled') {
                                window.showToast('Trip Cancelled', 'The driver has cancelled the trip.', 'danger');
                                setTimeout(() => window.location.href = 'index.php', 2000);
                                return;
                            }
                        }
                        
                        this.draw();
                    }
                }
            });
        };

        // Initialize state check
        updateStatus();
        this.statusPoller = setInterval(updateStatus, checkInterval);

        // For Driver Role: Automatically drive the car along the route, pushing coordinates
        if (this.role === 'driver') {
            this.simulationTicker = setInterval(() => {
                if ((this.status === 'accepted' || this.status === 'ongoing') && this.routePoints.length > 0) {
                    if (this.currentStep < this.routePoints.length) {
                        const pt = this.routePoints[this.currentStep];
                        
                        // Convert pixel back to GPS coordinates
                        const gps = this.pixelToGps(pt.x, pt.y);
                        this.driver = gps;
                        
                        // Push coordinates update to database
                        $.ajax({
                            url: 'index.php?controller=driver&action=postLocation',
                            method: 'POST',
                            data: {
                                latitude: gps.lat,
                                longitude: gps.lng,
                                csrf_token: window.csrfToken
                            },
                            dataType: 'json',
                            success: (res) => {
                                // Redraw map locally
                                this.draw();
                            }
                        });

                        this.currentStep++;
                    } else {
                        // Finished this section
                        if (this.status === 'accepted') {
                            // Reached Pickup Location
                            $('#arriveBtn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                            window.showToast('Arrived', 'You have arrived at the pickup location. Tap Start Ride.', 'success');
                        } else if (this.status === 'ongoing') {
                            // Reached Drop-off Location
                            $('#completeBtn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                            window.showToast('Destination Reached', 'You have reached the drop-off location. Tap Complete Ride.', 'success');
                        }
                    }
                }
            }, 1000); // Step every 1s
        }
    }

    destroy() {
        clearInterval(this.statusPoller);
        if (this.simulationTicker) {
            clearInterval(this.simulationTicker);
        }
    }
}
