<?php
session_start();
include 'db_connect.php'; // Include database connection

// Check if the user is logged in as a company
if ($_SESSION['role'] != 'company') {
    header('Location: index.php'); // Redirect to login if not a company user
    exit();
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape all input data to prevent SQL injection and fix the syntax error
    $job_title = $conn->real_escape_string($_POST['job_title']);
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $job_type = $conn->real_escape_string($_POST['job_type']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary_min = $conn->real_escape_string($_POST['salary_min']);
    $salary_max = $conn->real_escape_string($_POST['salary_max']);
    $department = $conn->real_escape_string($_POST['department']);
    $job_description = $conn->real_escape_string($_POST['job_description']);
    $requirements = $conn->real_escape_string($_POST['requirements']);
    $status = "Open"; // Default status
    $company_username = $_SESSION['username'];

    $sql = "INSERT INTO jobs (job_title, company_name, job_type, location, salary_min, salary_max, department, job_description, requirements, company_username, status, posted_date) 
            VALUES ('$job_title', '$company_name', '$job_type', '$location', '$salary_min', '$salary_max', '$department', '$job_description', '$requirements', '$company_username', '$status', NOW())";

    if ($conn->query($sql) === TRUE) {
        header('Location: job_postings.php'); // Redirect to job postings page
        exit();
    } else {
        $message = "Error posting job: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post New Job</title>
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
        
        input, select, textarea {
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
        
        input:focus, select:focus, textarea:focus {
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
            background-color: rgba(100, 255, 218, 0.1);
            color: #64ffda;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(100, 255, 218, 0.3);
        }
        
        .error-message {
            background-color: rgba(255, 100, 100, 0.1);
            color: #ff6464;
            border: 1px solid rgba(255, 100, 100, 0.3);
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
        }
    </style>
</head>
<body>
    <div id="canvas-container"></div>
    
    <div class="content-wrapper">
        <div class="header">
            <h1>Create New Job Posting</h1>
            <a href="job_postings.php" class="btn">Back to Job Postings</a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error-message' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" required>
            </div>
            
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" required>
            </div>
            
            <div class="form-group">
                <label for="job_type">Job Type:</label>
                <select id="job_type" name="job_type" required style="color: #000000;">
                    <option value="" style="color: #000000;">Select job type</option>
                    <option value="Full-time" style="color: #000000;">Full-time</option>
                    <option value="Part-time" style="color: #000000;">Part-time</option>
                    <option value="Contract" style="color: #000000;">Contract</option>
                    <option value="Internship" style="color: #000000;">Internship</option>
                    <option value="Remote" style="color: #000000;">Remote</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            
            <div class="form-group">
                <label>Salary Range:</label>
                <div class="salary-range">
                    <input type="number" name="salary_min" placeholder="Minimum Salary" required>
                    <input type="number" name="salary_max" placeholder="Maximum Salary" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" id="department" name="department" required>
            </div>
            
            <div class="form-group">
                <label for="job_description">Job Description:</label>
                <textarea id="job_description" name="job_description" rows="6" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="requirements">Requirements:</label>
                <textarea id="requirements" name="requirements" rows="6" required></textarea>
            </div>
            
            <button type="submit">Post Job</button>
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
        
        // Add a subtle ambient light
        const ambientLight = new THREE.AmbientLight(0x64ffda, 0.2);
        scene.add(ambientLight);
        
        // Add a directional light for depth
        const directionalLight = new THREE.DirectionalLight(0x64ffda, 0.5);
        directionalLight.position.set(5, 5, 5);
        scene.add(directionalLight);
        
        // Create form-like floating elements
        const elements = [];
        
        // Create placeholder rectangle geometry (form fields)
        const fieldGeometry = new THREE.BoxGeometry(3, 0.5, 0.05);
        
        // Create larger rectangle geometry (text areas)
        const textareaGeometry = new THREE.BoxGeometry(3, 1.2, 0.05);
        
        // Create floating form elements
        function createFormElements() {
            const fieldMaterial = new THREE.MeshPhongMaterial({
                color: 0x0a192f,
                emissive: 0x64ffda,
                emissiveIntensity: 0.1,
                transparent: true,
                opacity: 0.5,
                specular: 0x64ffda,
                shininess: 100
            });
            
            // Create short form fields
            for (let i = 0; i < 10; i++) {
                const field = new THREE.Mesh(fieldGeometry, fieldMaterial);
                
                field.position.x = (Math.random() - 0.5) * 20;
                field.position.y = (Math.random() - 0.5) * 20;
                field.position.z = (Math.random() - 0.5) * 10 - 5;
                
                field.rotation.x = Math.random() * Math.PI * 0.1;
                field.rotation.y = Math.random() * Math.PI * 0.1;
                
                scene.add(field);
                elements.push({
                    mesh: field,
                    rotationSpeed: {
                        x: (Math.random() - 0.5) * 0.002,
                        y: (Math.random() - 0.5) * 0.002
                    },
                    floatSpeed: Math.random() * 0.005 + 0.002
                });
            }
            
            // Create textarea fields
            for (let i = 0; i < 5; i++) {
                const textarea = new THREE.Mesh(textareaGeometry, fieldMaterial);
                
                textarea.position.x = (Math.random() - 0.5) * 20;
                textarea.position.y = (Math.random() - 0.5) * 20;
                textarea.position.z = (Math.random() - 0.5) * 10 - 5;
                
                textarea.rotation.x = Math.random() * Math.PI * 0.1;
                textarea.rotation.y = Math.random() * Math.PI * 0.1;
                
                scene.add(textarea);
                elements.push({
                    mesh: textarea,
                    rotationSpeed: {
                        x: (Math.random() - 0.5) * 0.001,
                        y: (Math.random() - 0.5) * 0.001
                    },
                    floatSpeed: Math.random() * 0.003 + 0.001
                });
            }
        }
        
        createFormElements();
        
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
                // Rotate elements subtly
                element.mesh.rotation.x += element.rotationSpeed.x;
                element.mesh.rotation.y += element.rotationSpeed.y;
                
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