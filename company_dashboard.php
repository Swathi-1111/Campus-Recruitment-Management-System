<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --glass-bg: rgba(27, 38, 59, 0.8);
            --glass-border: rgba(77, 184, 255, 0.3);
            --primary-color: #4db8ff;
            --secondary-color: #2196f3;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            perspective: 1000px;
            color: #e8e8e8;
        }

        #globe-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.4;
        }

        .dashboard-container {
            width: 90%;
            max-width: 1200px;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.3),
                0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 30px;
            border: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            position: relative;
            display: inline-block;
            text-shadow: 0 0 10px rgba(77, 184, 255, 0.3);
        }

        .dashboard-header h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(45deg, #4db8ff, #2196f3);
        }

        .dashboard-header h3 {
            color: #a0a0a0;
            margin-top: 10px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .dashboard-item {
            background: rgba(27, 38, 59, 0.6);
            border: 1px solid rgba(77, 184, 255, 0.3);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .dashboard-item:hover {
            transform: 
                scale(1.05) 
                rotateX(10deg) 
                rotateY(-10deg);
            box-shadow: 0 15px 35px rgba(77, 184, 255, 0.3);
            border-color: rgba(77, 184, 255, 0.5);
        }

        .dashboard-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
            position: relative;
            z-index: 1;
            font-size: 1.1rem;
            display: block;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .dashboard-item a:hover {
            text-shadow: 0 0 10px rgba(77, 184, 255, 0.6);
        }

        .dashboard-item .item-details {
            margin-top: 10px;
            color: #a0a0a0;
            font-size: 0.85rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .dashboard-item:hover .item-details {
            opacity: 1;
        }

        .dashboard-item::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(
                circle at center,
                rgba(77, 184, 255, 0.1) 0%,
                rgba(27, 38, 59, 0) 70%
            );
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .dashboard-item:hover::before {
            opacity: 1;
        }

        .logout-btn {
            display: block;
            width: 200px;
            margin: 30px auto 0;
            padding: 12px 20px;
            background: rgba(244, 67, 54, 0.2);
            color: #ffcdd2;
            text-align: center;
            border-radius: 30px;
            text-decoration: none;
            border: 1px solid rgba(244, 67, 54, 0.3);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: rgba(244, 67, 54, 0.3);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.4);
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div id="globe-container"></div>
    
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Company Dashboard</h1>
            <h3>Welcome, <?php echo htmlspecialchars($username); ?>!</h3>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-item">
                <a href="job_postings.php">
                    <i class="material-icons" style="font-size: 2rem; margin-bottom: 10px; color: #4db8ff;">work</i>
                    Manage Job Postings
                </a>
                <div class="item-details">
                    Create, edit, and manage job listings
                </div>
            </div>
            <div class="dashboard-item">
                <a href="applications.php">
                    <i class="material-icons" style="font-size: 2rem; margin-bottom: 10px; color: #4db8ff;">description</i>
                    View Applications
                </a>
                <div class="item-details">
                    Review and track candidate submissions
                </div>
            </div>
            <div class="dashboard-item">
                <a href="interviews.php">
                    <i class="material-icons" style="font-size: 2rem; margin-bottom: 10px; color: #4db8ff;">event</i>
                    Schedule Interviews
                </a>
                <div class="item-details">
                    Manage interview slots and candidates
                </div>
            </div>
            <div class="dashboard-item">
                <a href="company_profile.php">
                    <i class="material-icons" style="font-size: 2rem; margin-bottom: 10px; color: #4db8ff;">business</i>
                    Edit Company Profile
                </a>
                <div class="item-details">
                    Update company information and branding
                </div>
            </div>
            <div class="dashboard-item">
                <a href="results.php">
                    <i class="material-icons" style="font-size: 2rem; margin-bottom: 10px; color: #4db8ff;">analytics</i>
                    Update Results
                </a>
                <div class="item-details">
                    Analyze recruitment performance
                </div>
            </div>
        </div>

        <a href="company_dashboard.php?logout=true" class="logout-btn">
            <i class="material-icons" style="vertical-align: middle; margin-right: 5px;">logout</i>
            Logout
        </a>
    </div>

    <script>
        // 3D Globe Visualization with Mouse-Responsive Movement
        const globeContainer = document.getElementById('globe-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ alpha: true });
        
        renderer.setSize(window.innerWidth, window.innerHeight);
        globeContainer.appendChild(renderer.domElement);

        // Create Globe
        const geometry = new THREE.SphereGeometry(5, 64, 64);
        const material = new THREE.MeshPhongMaterial({
            color: 0x4db8ff, // Updated to match the theme's primary color
            wireframe: true,
            transparent: true,
            opacity: 0.2
        });
        const globe = new THREE.Mesh(geometry, material);
        scene.add(globe);

        // Network Points
        function createNetworkPoints() {
            const pointsGeometry = new THREE.BufferGeometry();
            const pointsMaterial = new THREE.PointsMaterial({
                color: 0x2196f3, // Updated to match the theme's secondary color
                size: 0.1,
                transparent: true,
                opacity: 0.7
            });

            const positions = [];
            for (let i = 0; i < 500; i++) {
                const phi = Math.acos(-1 + (2 * i) / 500);
                const theta = Math.sqrt(500 * Math.PI) * phi;

                const x = 5 * Math.cos(theta) * Math.sin(phi);
                const y = 5 * Math.sin(theta) * Math.sin(phi);
                const z = 5 * Math.cos(phi);

                positions.push(x, y, z);
            }

            pointsGeometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
            const points = new THREE.Points(pointsGeometry, pointsMaterial);
            scene.add(points);
        }
        createNetworkPoints();

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);
        const pointLight = new THREE.PointLight(0xffffff, 1);
        pointLight.position.set(10, 10, 10);
        scene.add(pointLight);

        camera.position.z = 10;

        // Mouse-Responsive Globe Movement
        let mouseX = 0, mouseY = 0;
        let targetRotationX = 0, targetRotationY = 0;

        // Mouse move event to track mouse position
        window.addEventListener('mousemove', (event) => {
            // Normalize mouse coordinates
            mouseX = (event.clientX / window.innerWidth) * 2 - 1;
            mouseY = -(event.clientY / window.innerHeight) * 2 + 1;

            // Calculate target rotations based on mouse position
            targetRotationX = mouseY * Math.PI / 4;
            targetRotationY = mouseX * Math.PI / 4;
        });

        // Smooth rotation animation
        function animate() {
            requestAnimationFrame(animate);

            // Smoothly interpolate globe rotation
            globe.rotation.x += (targetRotationX - globe.rotation.x) * 0.05;
            globe.rotation.y += (targetRotationY - globe.rotation.y) * 0.05;

            // Subtle continuous rotation
            globe.rotation.y += 0.002;

            renderer.render(scene, camera);
        }

        animate();

        // Responsive Handling
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>