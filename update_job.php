<?php
session_start();
include 'db_connect.php'; // Include database connection

// Ensure the user is logged in as a company
if ($_SESSION['role'] != 'company') {
    header('Location: index.php'); // Redirect to login page
    exit();
}

$message = ""; // Initialize message variable

// Handle form submission for job update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = $_POST['job_id']; // Get job ID
    $job_title = $conn->real_escape_string($_POST['job_title']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary_min = $conn->real_escape_string($_POST['salary_min']);
    $salary_max = $conn->real_escape_string($_POST['salary_max']);
    $job_description = $conn->real_escape_string($_POST['job_description']);

    // Update query
    $sql = "UPDATE jobs SET 
            job_title = '$job_title', 
            location = '$location', 
            salary_min = '$salary_min', 
            salary_max = '$salary_max', 
            job_description = '$job_description' 
            WHERE job_id = '$job_id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: job_postings.php'); // Redirect to job postings page
        exit();
    } else {
        $message = "Error updating job: " . $conn->error;
    }
}

// Fetch job details to populate the form
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    $sql = "SELECT * FROM jobs WHERE job_id = '$job_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
    } else {
        die("Job not found.");
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Job Posting</title>
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
            max-width: 900px;
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
        
        form {
            background-color: rgba(10, 25, 47, 0.7);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(100, 255, 218, 0.1);
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #64ffda;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        input, textarea {
            width: 100%;
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(100, 255, 218, 0.3);
            border-radius: 5px;
            color: #e6f1ff;
            font-family: 'Segoe UI', Arial, sans-serif;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #64ffda;
            box-shadow: 0 0 0 2px rgba(100, 255, 218, 0.2);
        }
        
        ::placeholder {
            color: rgba(230, 241, 255, 0.5);
        }
        
        .salary-range {
            display: flex;
            gap: 15px;
        }
        
        .salary-range input {
            flex: 1;
        }
        
        button[type="submit"] {
            background-color: rgba(100, 255, 218, 0.1);
            color: #64ffda;
            border: 1px solid #64ffda;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        button[type="submit"]:hover {
            background-color: rgba(100, 255, 218, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(100, 255, 218, 0.2);
        }
        
        .message {
            background-color: rgba(255, 100, 100, 0.1);
            color: #ff6464;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(255, 100, 100, 0.3);
        }
        
        .form-header {
            border-bottom: 1px solid rgba(100, 255, 218, 0.2);
            margin-bottom: 25px;
            padding-bottom: 15px;
        }
        
        .form-header h2 {
            color: #64ffda;
            margin: 0;
        }
        
        .form-header p {
            color: rgba(230, 241, 255, 0.7);
            margin: 5px 0 0 0;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px 15px;
            }
            
            .salary-range {
                flex-direction: column;
                gap: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div id="canvas-container"></div>
    
    <div class="content-wrapper">
        <div class="header">
            <h1>Update Job Posting</h1>
            <a href="job_postings.php" class="btn">Back to Job Postings</a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-header">
                <h2><?php echo htmlspecialchars($job['job_title']); ?></h2>
                <p>Last modified: <?php echo date('F j, Y', strtotime($job['posted_date'])); ?></p>
            </div>
            
            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
            
            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($job['job_title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Salary Range:</label>
                <div class="salary-range">
                    <input type="number" name="salary_min" value="<?php echo htmlspecialchars($job['salary_min']); ?>" placeholder="Minimum Salary" required>
                    <input type="number" name="salary_max" value="<?php echo htmlspecialchars($job['salary_max']); ?>" placeholder="Maximum Salary" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="job_description">Job Description:</label>
                <textarea id="job_description" name="job_description" rows="8" required><?php echo htmlspecialchars($job['job_description']); ?></textarea>
            </div>
            
            <button type="submit">Save Changes</button>
        </form>
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
        
        // Add lights
        const ambientLight = new THREE.AmbientLight(0x64ffda, 0.2);
        scene.add(ambientLight);
        
        const directionalLight = new THREE.DirectionalLight(0x64ffda, 0.5);
        directionalLight.position.set(5, 5, 5);
        scene.add(directionalLight);
        
        // Create elements for the background
        const elements = [];
        
        // Create document-like floating elements
        function createDocumentElements() {
            // Document page geometry
            const pageGeometry = new THREE.PlaneGeometry(3, 4);
            
            // Create pencil-like geometry
            const pencilGeometry = new THREE.CylinderGeometry(0.1, 0.1, 3, 32);
            
            // Materials
            const pageMaterial = new THREE.MeshPhongMaterial({
                color: 0x0a192f,
                emissive: 0x64ffda,
                emissiveIntensity: 0.1,
                transparent: true,
                opacity: 0.5,
                specular: 0x64ffda,
                shininess: 100
            });
            
            const pencilMaterial = new THREE.MeshPhongMaterial({
                color: 0x64ffda,
                emissive: 0x64ffda,
                emissiveIntensity: 0.2,
                transparent: true,
                opacity: 0.7
            });
            
            // Create paper pages
            for (let i = 0; i < 8; i++) {
                const page = new THREE.Mesh(pageGeometry, pageMaterial);
                
                page.position.x = (Math.random() - 0.5) * 20;
                page.position.y = (Math.random() - 0.5) * 20;
                page.position.z = (Math.random() - 0.5) * 10 - 5;
                
                page.rotation.x = Math.random() * Math.PI * 0.2;
                page.rotation.y = Math.random() * Math.PI * 0.2;
                
                scene.add(page);
                elements.push({
                    mesh: page,
                    rotationSpeed: {
                        x: (Math.random() - 0.5) * 0.001,
                        y: (Math.random() - 0.5) * 0.001
                    },
                    floatSpeed: Math.random() * 0.003 + 0.001
                });
            }
            
            // Create pencils
            for (let i = 0; i < 5; i++) {
                const pencil = new THREE.Mesh(pencilGeometry, pencilMaterial);
                
                pencil.position.x = (Math.random() - 0.5) * 20;
                pencil.position.y = (Math.random() - 0.5) * 20;
                pencil.position.z = (Math.random() - 0.5) * 10 - 5;
                
                // Rotate to look more like a pencil
                pencil.rotation.x = Math.PI / 2;
                pencil.rotation.z = Math.random() * Math.PI;
                
                scene.add(pencil);
                elements.push({
                    mesh: pencil,
                    rotationSpeed: {
                        x: 0,
                        y: 0,
                        z: (Math.random() - 0.5) * 0.002
                    },
                    floatSpeed: Math.random() * 0.004 + 0.002
                });
            }
        }
        
        createDocumentElements();
        
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
            
            // Animate the elements
            elements.forEach(element => {
                // Rotate elements
                if (element.rotationSpeed.x) element.mesh.rotation.x += element.rotationSpeed.x;
                if (element.rotationSpeed.y) element.mesh.rotation.y += element.rotationSpeed.y;
                if (element.rotationSpeed.z) element.mesh.rotation.z += element.rotationSpeed.z;
                
                // Make elements float up and down
                element.mesh.position.y += Math.sin(Date.now() * element.floatSpeed) * 0.002;
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
    </script>
</body>
</html>