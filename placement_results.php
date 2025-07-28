<?php
include 'db_connect.php';
session_start();

// Verify admin access
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php'); // Redirect non-admin users to the home page
    exit();
}

// Fetch total applied applications with username
$applications_sql = "SELECT applications.application_id, applications.status, 
                            jobs.job_title, students.user_id, students.cgpa, 
                            students.department, students.graduated_year, 
                            students.register_number, students.mobile_number,
                            users.username 
                     FROM applications
                     INNER JOIN jobs ON applications.job_id = jobs.job_id
                     INNER JOIN students ON applications.user_id = students.user_id
                     INNER JOIN users ON students.user_id = users.id";
$applications_result = $conn->query($applications_sql);

// Initialize counters
$total_applied = 0;
$total_selected = 0;
$total_rejected = 0;

// Process the data
$applications = [];
if ($applications_result->num_rows > 0) {
    while ($row = $applications_result->fetch_assoc()) {
        $applications[] = $row;

        // Count total applications
        $total_applied++;

        // Count selected and rejected applications
        if ($row['status'] === 'Selected') {
            $total_selected++;
        } elseif ($row['status'] === 'Rejected') {
            $total_rejected++;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Placement Results</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        body {
            color: #e0e0ff;
            background: linear-gradient(135deg, #0a192f 0%, #051937 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        #particles-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 1;
            padding: 40px 20px;
        }
        
        h1 {
            color: #64ffda;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 40px;
            text-shadow: 0 0 10px rgba(100, 255, 218, 0.3);
            letter-spacing: 1px;
        }
        
        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto 20px;
        }
        
        .back-button {
            position: absolute;
            left: 0;
            display: flex;
            align-items: center;
            background: rgba(100, 255, 218, 0.1);
            color: #64ffda;
            border: 1px solid rgba(100, 255, 218, 0.3);
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .back-button:hover {
            background: rgba(100, 255, 218, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .back-button:active {
            transform: translateY(0);
        }
        
        .back-button svg {
            margin-right: 8px;
        }
        
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin: 30px auto;
            max-width: 1200px;
        }
        
        .stat {
            background: rgba(9, 25, 42, 0.7);
            border: 1px solid rgba(100, 255, 218, 0.2);
            backdrop-filter: blur(5px);
            color: #e0e0ff;
            padding: 25px 35px;
            border-radius: 10px;
            font-size: 18px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            min-width: 200px;
            text-align: center;
        }
        
        .stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-color: rgba(100, 255, 218, 0.5);
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 600;
            margin: 10px 0;
            color: #64ffda;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        table {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            overflow: hidden;
        }
        
        th, td {
            padding: 15px;
            text-align: center;
        }
        
        th {
            background-color: rgba(9, 25, 42, 0.9);
            color: #64ffda;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(100, 255, 218, 0.2);
        }
        
        td {
            background-color: rgba(13, 33, 56, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e0e0ff;
            font-size: 0.95rem;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background-color: rgba(20, 45, 76, 0.8);
        }
        
        .no-results {
            color: #8892b0;
            font-style: italic;
            margin-top: 40px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Status styling */
        .status-selected {
            color: #64ffda;
            font-weight: 500;
        }
        
        .status-rejected {
            color: #ff6b6b;
            font-weight: 500;
        }
        
        .status-pending {
            color: #ffd166;
            font-weight: 500;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .back-button {
                position: relative;
                margin-bottom: 20px;
            }
            
            .stats-container {
                flex-direction: column;
                align-items: center;
            }
            
            .stat {
                width: 100%;
                max-width: 300px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Particles canvas -->
    <canvas id="particles-canvas"></canvas>
    
    <div class="content-wrapper">
        <div class="header-container">
            <a href="admin_dashboard.php" class="back-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back to Dashboard
            </a>
            <h1>Placement Results</h1>
        </div>

        <!-- Display Statistics -->
        <div class="stats-container">
            <div class="stat">
                <div class="stat-value"><?php echo $total_applied; ?></div>
                <div class="stat-label">Total Applied</div>
            </div>
            <div class="stat">
                <div class="stat-value"><?php echo $total_selected; ?></div>
                <div class="stat-label">Selected</div>
            </div>
            <div class="stat">
                <div class="stat-value"><?php echo $total_rejected; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <!-- Display Applications Table -->
        <?php if ($total_applied > 0) { ?>
        <table>
            <tr>
                <th>App ID</th>
                <th>Username</th>
                <th>Job Title</th>
                <th>Department</th>
                <th>CGPA</th>
                <th>Graduated Year</th>
                <th>Register Number</th>
                <th>Mobile Number</th>
                <th>Status</th>
            </tr>
            <?php foreach ($applications as $application) { 
                $statusClass = '';
                if ($application['status'] === 'Selected') {
                    $statusClass = 'status-selected';
                } elseif ($application['status'] === 'Rejected') {
                    $statusClass = 'status-rejected';
                } else {
                    $statusClass = 'status-pending';
                }
            ?>
            <tr>
                <td><?php echo $application['application_id']; ?></td>
                <td><?php echo $application['username']; ?></td>
                <td><?php echo $application['job_title']; ?></td>
                <td><?php echo $application['department']; ?></td>
                <td><?php echo $application['cgpa']; ?></td>
                <td><?php echo $application['graduated_year']; ?></td>
                <td><?php echo $application['register_number']; ?></td>
                <td><?php echo $application['mobile_number']; ?></td>
                <td class="<?php echo $statusClass; ?>"><?php echo $application['status']; ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <p class="no-results">No applications found in the system.</p>
        <?php } ?>
    </div>

    <!-- THREE.js for 3D particles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // 3D Particles Animation
        document.addEventListener('DOMContentLoaded', function() {
            // Scene setup
            const canvas = document.getElementById('particles-canvas');
            const renderer = new THREE.WebGLRenderer({
                canvas: canvas,
                antialias: true,
                alpha: true
            });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(window.devicePixelRatio > 1 ? 2 : 1);
            
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.z = 20;
            
            // Mouse tracking for interactive particles
            const mouse = {
                x: 0,
                y: 0
            };
            
            document.addEventListener('mousemove', (event) => {
                mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
                mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            });
            
            // Create particles
            const particlesCount = 1500;
            const particlesGeometry = new THREE.BufferGeometry();
            const positions = new Float32Array(particlesCount * 3);
            const colors = new Float32Array(particlesCount * 3);
            const sizes = new Float32Array(particlesCount);
            
            // Initialize particle properties
            for (let i = 0; i < particlesCount; i++) {
                // Position
                positions[i * 3] = (Math.random() - 0.5) * 50;     // x
                positions[i * 3 + 1] = (Math.random() - 0.5) * 50; // y
                positions[i * 3 + 2] = (Math.random() - 0.5) * 50; // z
                
                // Color - shades of blue and cyan
                colors[i * 3] = Math.random() * 0.3;           // R - low red for blue/cyan
                colors[i * 3 + 1] = 0.5 + Math.random() * 0.5; // G - high green for cyan tint
                colors[i * 3 + 2] = 0.7 + Math.random() * 0.3; // B - high blue
                
                // Size variation
                sizes[i] = Math.random() * 2;
            }
            
            particlesGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            particlesGeometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));
            particlesGeometry.setAttribute('size', new THREE.BufferAttribute(sizes, 1));
            
            // Particle material
            const particlesMaterial = new THREE.PointsMaterial({
                size: 0.2,
                transparent: true,
                opacity: 0.6,
                vertexColors: true,
                blending: THREE.AdditiveBlending,
                sizeAttenuation: true,
            });
            
            // Create point cloud
            const particles = new THREE.Points(particlesGeometry, particlesMaterial);
            scene.add(particles);
            
            // Animation loop
            const clock = new THREE.Clock();
            
            function animate() {
                const elapsedTime = clock.getElapsedTime();
                
                // Update particles
                for (let i = 0; i < particlesCount; i++) {
                    const i3 = i * 3;
                    
                    // Get current position
                    const x = particlesGeometry.attributes.position.array[i3];
                    const y = particlesGeometry.attributes.position.array[i3 + 1];
                    const z = particlesGeometry.attributes.position.array[i3 + 2];
                    
                    // Update position with wave-like motion
                    particlesGeometry.attributes.position.array[i3] = x + Math.sin(elapsedTime * 0.2 + x * 0.1) * 0.02;
                    particlesGeometry.attributes.position.array[i3 + 1] = y + Math.cos(elapsedTime * 0.2 + y * 0.1) * 0.02;
                    particlesGeometry.attributes.position.array[i3 + 2] = z + Math.sin(elapsedTime * 0.2 + z * 0.1) * 0.02;
                }
                
                // Influence from mouse
                particles.rotation.x += mouse.y * 0.0005;
                particles.rotation.y += mouse.x * 0.0005;
                
                // Update attributes
                particlesGeometry.attributes.position.needsUpdate = true;
                
                // Render
                renderer.render(scene, camera);
                
                // Continue animation
                requestAnimationFrame(animate);
            }
            
            // Handle window resize
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
            
            // Start animation
            animate();
        });
    </script>
</body>
</html>