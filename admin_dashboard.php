<?php
// Start the session if not already started
session_start();

// Initialize the username variable to prevent the warning
if(!isset($_SESSION['username'])) {
    $_SESSION['username'] = "admin"; // Default value if not set
}

// Check for admin role or set it for testing
if(!isset($_SESSION['role'])) {
    $_SESSION['role'] = "admin"; // Default for testing
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy all session data
    header('Location: index.php'); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Global Styles */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #0a0e1a;
            color: #e0e6ff;
            overflow: hidden;
        }

        /* CodeNebula Background */
        #background-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* Admin Dashboard Layout */
        .dashboard-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            height: 100vh;
            box-sizing: border-box;
            z-index: 1;
        }

        /* Header Section */
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .dashboard-header h1 {
            font-size: 3rem;
            color: #64a0ff;
            margin-bottom: 10px;
            text-shadow: 0 0 15px rgba(100, 160, 255, 0.4);
            letter-spacing: 2px;
        }

        .dashboard-header p {
            color: #9db6ff;
            font-size: 1.2rem;
            margin: 0;
        }

        /* Admin Controls */
        .admin-controls {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 100%;
            max-width: 1000px;
        }

        .control-card {
            background-color: rgba(13, 20, 40, 0.7);
            border: 1px solid rgba(100, 160, 255, 0.3);
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s ease;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3), 0 0 20px rgba(100, 160, 255, 0.2);
            backdrop-filter: blur(8px);
        }

        .control-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(100, 160, 255, 0.3), 0 0 30px rgba(100, 160, 255, 0.3);
            border-color: rgba(100, 160, 255, 0.7);
        }

        .control-card i {
            font-size: 2.5rem;
            color: #64a0ff;
            margin-bottom: 15px;
            display: block;
            text-shadow: 0 0 10px rgba(100, 160, 255, 0.6);
        }

        .control-card h3 {
            font-size: 1.3rem;
            color: #a7c7ff;
            margin-bottom: 10px;
        }

        .control-card p {
            color: #8eaeff;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .control-card a {
            display: inline-block;
            background-color: rgba(100, 160, 255, 0.15);
            color: #64a0ff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: 1px solid rgba(100, 160, 255, 0.3);
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .control-card a:hover {
            background-color: #64a0ff;
            color: #050a18;
            box-shadow: 0 0 10px rgba(100, 160, 255, 0.5);
        }

        /* Logout Button */
        .logout-button {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .logout-button button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 0 10px rgba(231, 76, 60, 0.3);
        }

        .logout-button button:hover {
            background-color: #c0392b;
            box-shadow: 0 0 15px rgba(231, 76, 60, 0.5);
        }

        /* User Info */
        .user-info {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #9db6ff;
            font-size: 0.9rem;
        }

        .user-info span {
            color: #64a0ff;
            font-weight: bold;
            text-shadow: 0 0 5px rgba(100, 160, 255, 0.4);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .admin-controls {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .admin-controls {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header h1 {
                font-size: 2rem;
            }
            
            .control-card {
                padding: 15px;
            }
        }
    </style>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <!-- CodeNebula Background Canvas -->
    <canvas id="background-canvas"></canvas>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- User Info Display -->
        <div class="user-info">
            Logged in as: <span><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'guest'; ?></span>
        </div>

        <!-- Logout Button -->
        <div class="logout-button">
            <button onclick="window.location.href='admin_dashboard.php?logout=true'">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>

        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Control center for system administration</p>
        </div>

        <!-- Admin Control Cards -->
        <div class="admin-controls">
            <div class="control-card">
                <i class="fas fa-user-plus"></i>
                <h3>User Management</h3>
                <p>Add, view, or modify system users</p>
                <a href="add_user.php">Add User</a>
                <a href="view_users.php">View All</a>
            </div>
            
            <div class="control-card">
                <i class="fas fa-sitemap"></i>
                <h3>Department Control</h3>
                <p>Manage organizational departments</p>
                <a href="manage_departments.php">Manage</a>
            </div>
            
            <div class="control-card">
                <i class="fas fa-chart-line"></i>
                <h3>Placement Results</h3>
                <p>View and analyze placement statistics</p>
                <a href="placement_results.php">View Results</a>
            </div>
        </div>
    </div>

    <!-- Include three.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <!-- CodeNebula Background Script -->
    <script>
        // Make sure Three.js is loaded before proceeding
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Three.js is properly loaded
            if (typeof THREE === 'undefined') {
                console.error("Three.js not loaded properly");
                return;
            }
            
            console.log("Initializing CodeNebula background");
            
            // Get the canvas element
            const canvas = document.getElementById('background-canvas');
            
            // Create renderer
            const renderer = new THREE.WebGLRenderer({
                canvas: canvas,
                antialias: true
            });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(window.devicePixelRatio);
            renderer.setClearColor(0x0a0e1a, 1); // Dark blue background
            
            // Create scene
            const scene = new THREE.Scene();
            
            // Create camera
            const camera = new THREE.PerspectiveCamera(
                75,  // Field of view
                window.innerWidth / window.innerHeight,  // Aspect ratio
                0.1,  // Near clipping plane
                1000  // Far clipping plane
            );
            camera.position.z = 50;
            
            // Create ambient light
            const ambientLight = new THREE.AmbientLight(0x404080, 0.2);
            scene.add(ambientLight);
            
            // Create directional light
            const directionalLight = new THREE.DirectionalLight(0x6080ff, 0.5);
            directionalLight.position.set(1, 1, 1);
            scene.add(directionalLight);
            
            // Create point lights for glow effects
            const pointLights = [];
            const lightColors = [0x64a0ff, 0x3a70dd, 0x4287f5];
            
            for (let i = 0; i < 4; i++) {
                const light = new THREE.PointLight(
                    lightColors[i % lightColors.length],
                    1,  // Intensity
                    100  // Distance
                );
                light.position.set(
                    (Math.random() - 0.5) * 60,
                    (Math.random() - 0.5) * 60,
                    (Math.random() - 0.5) * 60
                );
                scene.add(light);
                pointLights.push(light);
            }
            
            // Create stars (small particles in background)
            const starGeometry = new THREE.BufferGeometry();
            const starCount = 2000;
            const starPositions = new Float32Array(starCount * 3);
            
            for (let i = 0; i < starCount * 3; i += 3) {
                starPositions[i] = (Math.random() - 0.5) * 2000;
                starPositions[i + 1] = (Math.random() - 0.5) * 2000;
                starPositions[i + 2] = (Math.random() - 0.5) * 2000;
            }
            
            starGeometry.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
            
            const starMaterial = new THREE.PointsMaterial({
                color: 0xffffff,
                size: 1,
                transparent: true,
                opacity: 0.8,
                sizeAttenuation: true
            });
            
            const stars = new THREE.Points(starGeometry, starMaterial);
            scene.add(stars);
            
            // Create floating particles
            const particles = [];
            const particleCount = 300;
            
            for (let i = 0; i < particleCount; i++) {
                // Create particle geometry
                const size = 0.1 + Math.random() * 0.5;
                const geometry = new THREE.SphereGeometry(size, 8, 8);
                
                // Choose color
                const blueShades = [0x64a0ff, 0x3a70dd, 0x4287f5, 0x0056b3];
                const color = blueShades[Math.floor(Math.random() * blueShades.length)];
                
                // Create material
                const material = new THREE.MeshPhongMaterial({
                    color: color,
                    transparent: true,
                    opacity: 0.7,
                    emissive: color,
                    emissiveIntensity: 0.5,
                    shininess: 90
                });
                
                // Create mesh
                const particle = new THREE.Mesh(geometry, material);
                
                // Position randomly
                particle.position.set(
                    (Math.random() - 0.5) * 100,
                    (Math.random() - 0.5) * 100,
                    (Math.random() - 0.5) * 100
                );
                
                // Store particle properties for animation
                particle.userData = {
                    velocity: {
                        x: (Math.random() - 0.5) * 0.05,
                        y: (Math.random() - 0.5) * 0.05,
                        z: (Math.random() - 0.5) * 0.05
                    },
                    rotationSpeed: {
                        x: (Math.random() - 0.5) * 0.02,
                        y: (Math.random() - 0.5) * 0.02,
                        z: (Math.random() - 0.5) * 0.02
                    },
                    pulseSpeed: 0.01 + Math.random() * 0.03,
                    pulsePhase: Math.random() * Math.PI * 2
                };
                
                scene.add(particle);
                particles.push(particle);
            }
            
            // Create connections between some particles
            const connections = [];
            const connectionCount = 100;
            
            for (let i = 0; i < connectionCount; i++) {
                // Select two random particles
                const particle1 = particles[Math.floor(Math.random() * particles.length)];
                const particle2 = particles[Math.floor(Math.random() * particles.length)];
                
                // Skip if same particle or too far away
                if (particle1 === particle2) continue;
                
                const distance = particle1.position.distanceTo(particle2.position);
                if (distance > 30) continue;
                
                // Create line geometry
                const geometry = new THREE.BufferGeometry().setFromPoints([
                    particle1.position,
                    particle2.position
                ]);
                
                // Create line material
                const material = new THREE.LineBasicMaterial({
                    color: 0x64a0ff,
                    transparent: true,
                    opacity: 0.3
                });
                
                // Create line
                const connection = new THREE.Line(geometry, material);
                
                // Store connected particles
                connection.userData = {
                    particle1: particle1,
                    particle2: particle2,
                    opacity: 0.3
                };
                
                scene.add(connection);
                connections.push(connection);
            }
            
            // Create nebula clouds
            const nebulaClouds = [];
            const nebulaCount = 5;
            
            for (let i = 0; i < nebulaCount; i++) {
                // Create cloud geometry
                const cloudSize = 20 + Math.random() * 30;
                const cloudGeometry = new THREE.SphereGeometry(cloudSize, 32, 32);
                
                // Create cloud material
                const cloudMaterial = new THREE.MeshPhongMaterial({
                    color: 0x64a0ff,
                    transparent: true,
                    opacity: 0.05,
                    emissive: 0x3a70dd,
                    emissiveIntensity: 0.2,
                    side: THREE.DoubleSide
                });
                
                // Create cloud mesh
                const cloud = new THREE.Mesh(cloudGeometry, cloudMaterial);
                
                // Position cloud
                cloud.position.set(
                    (Math.random() - 0.5) * 100,
                    (Math.random() - 0.5) * 100,
                    (Math.random() - 0.5) * 100
                );
                
                // Scale cloud randomly
                cloud.scale.set(
                    0.5 + Math.random() * 0.5,
                    0.5 + Math.random() * 0.5,
                    0.5 + Math.random() * 0.5
                );
                
                // Store cloud properties
                cloud.userData = {
                    rotationSpeed: {
                        x: (Math.random() - 0.5) * 0.001,
                        y: (Math.random() - 0.5) * 0.001,
                        z: (Math.random() - 0.5) * 0.001
                    }
                };
                
                scene.add(cloud);
                nebulaClouds.push(cloud);
            }
            
            // Animation loop
            let time = 0;
            
            function animate() {
                requestAnimationFrame(animate);
                time += 0.01;
                
                // Camera gentle movement
                camera.position.x = Math.sin(time * 0.1) * 3;
                camera.position.y = Math.cos(time * 0.1) * 3;
                camera.lookAt(0, 0, 0);
                
                // Animate point lights
                pointLights.forEach((light, index) => {
                    light.position.x = Math.sin(time * 0.2 + index) * 30;
                    light.position.y = Math.cos(time * 0.2 + index) * 30;
                    light.intensity = 0.5 + Math.sin(time * 0.3 + index) * 0.2;
                });
                
                // Animate floating particles
                particles.forEach(particle => {
                    // Move particle based on velocity
                    particle.position.x += particle.userData.velocity.x;
                    particle.position.y += particle.userData.velocity.y;
                    particle.position.z += particle.userData.velocity.z;
                    
                    // Rotate particle
                    particle.rotation.x += particle.userData.rotationSpeed.x;
                    particle.rotation.y += particle.userData.rotationSpeed.y;
                    particle.rotation.z += particle.userData.rotationSpeed.z;
                    
                    // Make particle pulse (change size and opacity)
                    const pulseValue = Math.sin(time * particle.userData.pulseSpeed + particle.userData.pulsePhase);
                    const scale = 1 + pulseValue * 0.2;
                    particle.scale.set(scale, scale, scale);
                    particle.material.opacity = 0.5 + pulseValue * 0.2;
                    
                    // Boundary check - if particle goes too far, bounce back
                    const bounds = 50;
                    ['x', 'y', 'z'].forEach(axis => {
                        if (Math.abs(particle.position[axis]) > bounds) {
                            particle.userData.velocity[axis] *= -1;
                        }
                    });
                });
                
                // Update connections between particles
                connections.forEach(connection => {
                    const p1 = connection.userData.particle1;
                    const p2 = connection.userData.particle2;
                    
                    // Update line geometry to follow particles
                    connection.geometry.dispose();
                    connection.geometry = new THREE.BufferGeometry().setFromPoints([
                        p1.position, 
                        p2.position
                    ]);
                    
                    // Adjust opacity based on distance
                    const distance = p1.position.distanceTo(p2.position);
                    connection.material.opacity = Math.max(0.02, 0.3 - distance * 0.01);
                });
                
                // Animate nebula clouds
                nebulaClouds.forEach(cloud => {
                    // Rotate cloud slowly
                    cloud.rotation.x += cloud.userData.rotationSpeed.x;
                    cloud.rotation.y += cloud.userData.rotationSpeed.y;
                    cloud.rotation.z += cloud.userData.rotationSpeed.z;
                    
                    // Make cloud pulse
                    const pulseValue = Math.sin(time * 0.1) * 0.1;
                    cloud.material.opacity = 0.05 + pulseValue * 0.02;
                });
                
                // Rotate star field very slowly
                stars.rotation.y += 0.0001;
                
                // Render the scene
                renderer.render(scene, camera);
            }
            
            // Handle window resize
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
            
            // Start animation loop
            animate();
        });
    </script>
</body>
</html>