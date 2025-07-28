<?php
session_start();
include 'db_connect.php'; // Include database connection

// Ensure the user is logged in as a company
if ($_SESSION['role'] != 'company') {
    header('Location: index.php'); // Redirect to login page
    exit();
}

// Fetch job postings for the logged-in company
$company_username = $_SESSION['username'];
$sql = "SELECT * FROM jobs WHERE company_username = '$company_username' ORDER BY posted_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Postings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0a192f;
            color: #e6f1ff;
            overflow-x: hidden;
        }
        
        #canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .content-wrapper {
            position: relative;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            z-index: 1;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        h1 {
            color: #64ffda;
            font-size: 2.5rem;
            margin: 0;
            text-shadow: 0 0 15px rgba(100, 255, 218, 0.3);
        }
        
        .button-container {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            background-color: transparent;
            color: #64ffda;
            border: 1px solid #64ffda;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background-color: rgba(100, 255, 218, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(100, 255, 218, 0.2);
        }
        
        .primary-btn {
            background-color: rgba(100, 255, 218, 0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            overflow: hidden;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(100, 255, 218, 0.2);
        }
        
        th {
            background-color: rgba(100, 255, 218, 0.1);
            color: #64ffda;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        td {
            background-color: rgba(10, 25, 47, 0.7);
            backdrop-filter: blur(10px);
        }
        
        tr:hover td {
            background-color: rgba(100, 255, 218, 0.05);
        }
        
        .edit-btn, .delete-btn {
            padding: 8px 15px;
            margin-right: 5px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            display: inline-block;
            text-decoration: none;
        }
        
        .edit-btn {
            background-color: rgba(100, 255, 218, 0.1);
            color: #64ffda;
            border: 1px solid #64ffda;
        }
        
        .edit-btn:hover {
            background-color: rgba(100, 255, 218, 0.2);
        }
        
        .delete-btn {
            background-color: rgba(255, 100, 100, 0.1);
            color: #ff6464;
            border: 1px solid #ff6464;
        }
        
        .delete-btn:hover {
            background-color: rgba(255, 100, 100, 0.2);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            background-color: rgba(10, 25, 47, 0.7);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div id="canvas-container"></div>
    
    <div class="content-wrapper">
        <div class="header">
            <h1>Your Job Postings</h1>
            <div class="button-container">
                <a href="company_dashboard.php" class="btn">Back to Dashboard</a>
                <a href="post_new_job.php" class="btn primary-btn">Post New Job</a>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Posted Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="row_<?php echo $row["job_id"]; ?>">
                        <td><?php echo $row["job_title"]; ?></td>
                        <td><?php echo $row["location"]; ?></td>
                        <td>$<?php echo $row["salary_min"]; ?> - $<?php echo $row["salary_max"]; ?></td>
                        <td><?php echo $row["posted_date"]; ?></td>
                        <td>
                            <a href="update_job.php?job_id=<?php echo $row["job_id"]; ?>" class="edit-btn">Edit</a>
                            <button class="delete-btn" onclick="confirmDelete(<?php echo $row["job_id"]; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No job postings available. Click "Post New Job" to create one.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Initialize Three.js scene
        const canvas = document.createElement('canvas');
        document.getElementById('canvas-container').appendChild(canvas);
        
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
        
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        
        // Add a subtle ambient light
        const ambientLight = new THREE.AmbientLight(0x64ffda, 0.2);
        scene.add(ambientLight);
        
        // Add a directional light for depth
        const directionalLight = new THREE.DirectionalLight(0x64ffda, 0.5);
        directionalLight.position.set(5, 5, 5);
        scene.add(directionalLight);
        
        // Create job cards
        const cards = [];
        const cardGeometry = new THREE.BoxGeometry(2, 1, 0.1);
        
        // Create 15-20 floating cards
        for (let i = 0; i < 20; i++) {
            const cardMaterial = new THREE.MeshPhongMaterial({
                color: 0x0a192f,
                emissive: 0x64ffda,
                emissiveIntensity: 0.1,
                transparent: true,
                opacity: 0.7,
                specular: 0x64ffda,
                shininess: 100
            });
            
            const card = new THREE.Mesh(cardGeometry, cardMaterial);
            
            // Position cards randomly in 3D space
            card.position.x = (Math.random() - 0.5) * 20;
            card.position.y = (Math.random() - 0.5) * 20;
            card.position.z = (Math.random() - 0.5) * 10 - 5; // Keep cards mostly in front of camera
            
            // Give each card a random rotation
            card.rotation.x = Math.random() * Math.PI;
            card.rotation.y = Math.random() * Math.PI;
            
            // Add each card to the scene and our cards array
            scene.add(card);
            cards.push({
                mesh: card,
                rotationSpeed: {
                    x: (Math.random() - 0.5) * 0.002,
                    y: (Math.random() - 0.5) * 0.002
                },
                floatSpeed: Math.random() * 0.005 + 0.002
            });
        }
        
        // Position camera
        camera.position.z = 10;
        
        // Mouse movement effect
        let mouseX = 0;
        let mouseY = 0;
        let targetX = 0;
        let targetY = 0;
        
        document.addEventListener('mousemove', (event) => {
            mouseX = (event.clientX - window.innerWidth / 2) * 0.001;
            mouseY = (event.clientY - window.innerHeight / 2) * 0.001;
        });
        
        // Animation function
        function animate() {
            requestAnimationFrame(animate);
            
            // Smooth camera movement based on mouse position
            targetX += (mouseX - targetX) * 0.05;
            targetY += (mouseY - targetY) * 0.05;
            camera.rotation.y = targetX;
            camera.rotation.x = targetY;
            
            // Animate the cards
            cards.forEach(card => {
                // Rotate the cards slowly
                card.mesh.rotation.x += card.rotationSpeed.x;
                card.mesh.rotation.y += card.rotationSpeed.y;
                
                // Make the cards float up and down
                card.mesh.position.y += Math.sin(Date.now() * card.floatSpeed) * 0.002;
            });
            
            renderer.render(scene, camera);
        }
        
        // Handle window resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
        
        // Start animation
        animate();
        
        // Delete job confirmation function
        function confirmDelete(jobId) {
            if (confirm("Are you sure you want to delete this job?")) {
                fetch('delete_job.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ job_id: jobId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Job deleted successfully!");
                        document.getElementById(`row_${jobId}`).remove();
                    } else {
                        alert("Error deleting job: " + data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        }
    </script>
</body>
</html>