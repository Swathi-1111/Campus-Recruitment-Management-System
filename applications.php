<?php 
session_start(); 
include 'db_connect.php'; // Include the database connection

// Check if the user is logged in as a company
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') {
    header('Location: index.php'); // Redirect to login page if not logged in as a company
    exit();
}

if (!isset($_SESSION['user_id'])) {
    die("User details are missing from the session. Please log in again.");
}

$company_username = $_SESSION['username']; // Retrieve the logged-in company's username
 
// Fetch applications filtered by the company_username
$sql = "SELECT applications.application_id, applications.job_id, applications.user_id, applications.company_id,
                applications.cgpa, applications.status,
                COALESCE(applications.interview_date, 'Not Scheduled') AS interview_date,
                COALESCE(applications.interview_location, 'Not Scheduled') AS interview_location,
                users.username AS applicant_username
         FROM applications
        INNER JOIN jobs ON applications.job_id = jobs.job_id
        INNER JOIN users ON applications.user_id = users.id
        WHERE jobs.company_username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $company_username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Applications Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        body {
            background-color: #0a192f;
            color: #ffffff;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        #background-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .content {
            position: relative;
            z-index: 1;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #4ecca3;
            font-size: 2.2rem;
            font-weight: 600;
        }
        
        .buttons-container {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            background-color: transparent;
            color: #4ecca3;
            border: 1px solid #4ecca3;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background-color: rgba(78, 204, 163, 0.1);
            box-shadow: 0 0 8px rgba(78, 204, 163, 0.5);
        }
        
        .container {
            width: 90%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: rgba(15, 32, 59, 0.7);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        thead {
            background-color: rgba(15, 32, 59, 0.9);
        }
        
        th {
            color: #4ecca3;
            text-align: left;
            padding: 15px;
            font-weight: 500;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(78, 204, 163, 0.2);
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e0e0e0;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background-color: rgba(15, 32, 59, 0.9);
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: 500;
        }
        
        .status-scheduled {
            color: #29b6f6;
            font-weight: 500;
        }
        
        .status-completed {
            color: #4ecca3;
            font-weight: 500;
        }
        
        .no-applications {
            text-align: center;
            padding: 40px 0;
            color: #aaa;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
                text-align: center;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .container {
                width: 100%;
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
    <canvas id="background-canvas"></canvas>
    
    <div class="content">
        <div class="header">
            <h1>Applications Dashboard</h1>
            <div class="buttons-container">
                <a href="company_dashboard.php" class="btn">Back to Dashboard</a>
            </div>
        </div>
        
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Job ID</th>
                        <th>Applicant Username</th>
                        <th>CGPA</th>
                        <th>Status</th>
                        <th>Interview Date</th>
                        <th>Interview Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['application_id']}</td>";
                            echo "<td>{$row['job_id']}</td>";
                            echo "<td>{$row['applicant_username']}</td>";
                            echo "<td>{$row['cgpa']}</td>";
                            echo "<td class='status-" . strtolower($row['status']) . "'>{$row['status']}</td>";
                            echo "<td>{$row['interview_date']}</td>";
                            echo "<td>{$row['interview_location']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='no-applications'>No applications have been received for your jobs.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Three.js 3D background with rectangles
        function initBackground() {
            const canvas = document.getElementById('background-canvas');
            const renderer = new THREE.WebGLRenderer({
                canvas,
                antialias: true,
                alpha: true
            });
            
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(window.devicePixelRatio);
            
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(
                50, window.innerWidth / window.innerHeight, 0.1, 1000
            );
            camera.position.z = 20;
            
            // Create rectangles
            const rectangles = [];
            const rectangleCount = 8;
            
            // Colors that match the dark blue and teal theme
            const colors = [
                new THREE.Color('#0d2546'),
                new THREE.Color('#133057'),
                new THREE.Color('#0f4166'),
                new THREE.Color('#194971'),
                new THREE.Color('#1a3b5d'),
                new THREE.Color('#4ecca3'),
                new THREE.Color('#3aa88b'),
                new THREE.Color('#4eb7a7')
            ];
            
            // Create rectangle geometries with different sizes
            for (let i = 0; i < rectangleCount; i++) {
                const width = Math.random() * 10 + 5;
                const height = Math.random() * 6 + 3;
                const depth = 0.2;
                
                const geometry = new THREE.BoxGeometry(width, height, depth);
                
                // Use predominantly dark blue colors with occasional teal
                const colorIndex = Math.random() > 0.8 ? Math.floor(Math.random() * 3) + 5 : Math.floor(Math.random() * 5);
                const material = new THREE.MeshPhongMaterial({
                    color: colors[colorIndex],
                    transparent: true,
                    opacity: 0.25,
                    specular: 0x333333,
                    shininess: 30
                });
                
                const rectangle = new THREE.Mesh(geometry, material);
                
                // Position rectangles throughout the scene
                rectangle.position.x = (Math.random() - 0.5) * 40;
                rectangle.position.y = (Math.random() - 0.5) * 30;
                rectangle.position.z = (Math.random() - 0.5) * 10 - 5;
                
                // Rotate rectangles slightly
                rectangle.rotation.x = Math.random() * Math.PI * 0.1;
                rectangle.rotation.y = Math.random() * Math.PI * 0.1;
                rectangle.rotation.z = Math.random() * Math.PI * 0.1;
                
                // Define animation properties
                rectangle.userData = {
                    rotationSpeed: {
                        x: (Math.random() - 0.5) * 0.002,
                        y: (Math.random() - 0.5) * 0.002,
                        z: (Math.random() - 0.5) * 0.001
                    },
                    floatSpeed: (Math.random() - 0.5) * 0.003,
                    floatOffset: Math.random() * Math.PI * 2
                };
                
                scene.add(rectangle);
                rectangles.push(rectangle);
            }
            
            // Add subtle lighting to enhance the 3D effect
            const ambientLight = new THREE.AmbientLight(0x404040, 0.6);
            scene.add(ambientLight);
            
            const directionalLight = new THREE.DirectionalLight(0x4ecca3, 0.4);
            directionalLight.position.set(1, 1, 2);
            scene.add(directionalLight);
            
            const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.3);
            directionalLight2.position.set(-1, -1, -1);
            scene.add(directionalLight2);
            
            // Mouse movement for parallax effect
            let mouseX = 0;
            let mouseY = 0;
            
            document.addEventListener('mousemove', (event) => {
                mouseX = (event.clientX / window.innerWidth) * 2 - 1;
                mouseY = -(event.clientY / window.innerHeight) * 2 + 1;
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
            
            // Animation loop
            function animate() {
                requestAnimationFrame(animate);
                
                // Add subtle camera movement based on mouse position
                camera.position.x += (mouseX * 2 - camera.position.x) * 0.02;
                camera.position.y += (mouseY * 2 - camera.position.y) * 0.02;
                camera.lookAt(scene.position);
                
                // Animate rectangles
                const time = Date.now() * 0.001;
                
                rectangles.forEach(rectangle => {
                    // Apply slow rotation
                    rectangle.rotation.x += rectangle.userData.rotationSpeed.x;
                    rectangle.rotation.y += rectangle.userData.rotationSpeed.y;
                    rectangle.rotation.z += rectangle.userData.rotationSpeed.z;
                    
                    // Apply floating movement
                    const floatY = Math.sin(time + rectangle.userData.floatOffset) * 0.05;
                    rectangle.position.y += floatY * rectangle.userData.floatSpeed;
                });
                
                renderer.render(scene, camera);
            }
            
            animate();
        }
        
        // Initialize background when the page loads
        window.addEventListener('load', initBackground);
    </script>
</body>
</html>