<?php
session_start();
include 'db_connect.php'; // Include database connection

if ($_SESSION['role'] != 'admin') {
    header('Location: index.php'); // Redirect non-admin users
    exit();
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = "";

// Fetch departments from database for dropdown
$departments = array();
$dept_sql = "SELECT * FROM departments ORDER BY department_name";
$dept_result = $conn->query($dept_sql);

if ($dept_result && $dept_result->num_rows > 0) {
    while ($dept_row = $dept_result->fetch_assoc()) {
        $departments[] = $dept_row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (empty($username) || empty($password) || empty($role)) {
        $message = "All fields are required!";
    } else {
        // Check if the username already exists
        $check_sql = "SELECT id FROM users WHERE username = '$username'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $message = "Username already exists! Please choose a different username.";
        } else {
            // Insert into the users table
            $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
            if ($conn->query($sql) === TRUE) {
                $user_id = $conn->insert_id;

                // Handle student user creation
                if ($role === 'student') {
                    $department = mysqli_real_escape_string($conn, $_POST['department']);
                    $cgpa = mysqli_real_escape_string($conn, $_POST['cgpa']);
                    $graduated_year = mysqli_real_escape_string($conn, $_POST['graduated_year']);
                    $register_number = mysqli_real_escape_string($conn, $_POST['register_number']);
                    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);

                    $sql_student = "INSERT INTO students (user_id, department, cgpa, graduated_year, register_number, mobile_number) 
                                    VALUES ('$user_id', '$department', '$cgpa', '$graduated_year', '$register_number', '$mobile_number')";
                    if ($conn->query($sql_student) === TRUE) {
                        $message = "Student user added successfully!";
                    } else {
                        $message = "Error adding student: " . $conn->error;
                    }
                }

                // Handle company user creation
                elseif ($role === 'company') {
                    // Check if form fields exist before accessing them
                    $company_name = isset($_POST['company_name']) ? mysqli_real_escape_string($conn, $_POST['company_name']) : '';
                    $industry = isset($_POST['industry']) ? mysqli_real_escape_string($conn, $_POST['industry']) : '';
                    $number_of_employees = isset($_POST['number_of_employees']) ? mysqli_real_escape_string($conn, $_POST['number_of_employees']) : '';
                    $company_email = isset($_POST['company_email']) ? mysqli_real_escape_string($conn, $_POST['company_email']) : '';
                    $company_number = isset($_POST['company_number']) ? mysqli_real_escape_string($conn, $_POST['company_number']) : '';

                    // First check if companies table exists
                    $table_check_sql = "SHOW TABLES LIKE 'companies'";
                    $table_check_result = $conn->query($table_check_sql);
                    
                    if ($table_check_result->num_rows == 0) {
                        // Table doesn't exist, create it
                        $create_table_sql = "CREATE TABLE companies (
                            id INT(11) AUTO_INCREMENT PRIMARY KEY,
                            user_id INT(11) NOT NULL,
                            company_name VARCHAR(255) NOT NULL,
                            industry VARCHAR(255),
                            number_of_employees INT(11),
                            company_email VARCHAR(255),
                            company_number VARCHAR(20),
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (user_id) REFERENCES users(id)
                        )";
                        $conn->query($create_table_sql);
                    } else {
                        // Check if the company_number column exists; otherwise, add it
                        $column_check_sql = "SHOW COLUMNS FROM companies LIKE 'company_number'";
                        $column_check_result = $conn->query($column_check_sql);

                        if ($column_check_result->num_rows == 0) {
                            $add_column_sql = "ALTER TABLE companies ADD company_number VARCHAR(20)";
                            $conn->query($add_column_sql);
                        }
                    }

                    $sql_company = "INSERT INTO companies (user_id, company_name, industry, number_of_employees, company_email, company_number) 
                                    VALUES ('$user_id', '$company_name', '$industry', '$number_of_employees', '$company_email', '$company_number')";
                    if ($conn->query($sql_company) === TRUE) {
                        $message = "Company user added successfully!";
                    } else {
                        $message = "Error adding company: " . $conn->error;
                    }
                } else {
                    // For other roles like admin, just add the user
                    $message = "User added successfully!";
                }
            } else {
                $message = "Error adding user: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            width: 100%;
            /* Removed overflow: hidden to allow scrolling */
            font-family: Arial, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: #002147; /* Dark blue background */
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Changed from center to flex-start to better support scrolling */
            min-height: 100vh;
            padding: 50px 0; /* Added padding to give space at top and bottom */
        }
        
        h1 {
            color: #0288d1;
            margin-bottom: 20px;
            font-size: 32px;
            z-index: 10;
            position: relative;
        }
        
        form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            width: 500px;
            z-index: 10;
            position: relative;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 50px; /* Added margin to ensure space at bottom */
        }
        
        label {
            font-size: 14px;
            color: #01579b;
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #0288d1;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(2, 136, 209, 0.5);
            border-color: #01579b;
        }
        
        button {
            background-color: #0288d1;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            transition: background-color 0.3s;
            font-weight: bold;
        }
        
        button:hover {
            background-color: #01579b;
        }
        
        .message {
            color: #4CAF50;
            font-size: 16px;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
            z-index: 10;
            position: relative;
        }
        
        .error {
            color: #F44336;
            font-size: 16px;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
            z-index: 10;
            position: relative;
        }
        
        #particle-container {
            position: fixed; /* Changed from fixed to absolute to allow scrolling */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background: linear-gradient(135deg, #001f3f 0%, #002147 100%);
        }
        
        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 20px 0; /* Added padding for better spacing */
        }
        
        /* Ensure the white line doesn't appear at the bottom */
        #particle-canvas {
            display: block; /* Remove any default spacing */
        }
        
        /* Adding styles for the Back to Dashboard button */
        .nav-button {
            background-color: #0288d1;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            z-index: 10;
            position: relative;
        }
        
        .nav-button:hover {
            background-color: #01579b;
        }
        
        /* Styles for the department management link */
        .department-link {
            color: #0288d1;
            font-size: 14px;
            text-decoration: none;
            margin-left: 10px;
            font-weight: bold;
            display: inline-block;
        }
        
        .department-link:hover {
            text-decoration: underline;
            color: #01579b;
        }
        
        .department-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .department-select {
            flex-grow: 1;
            margin-bottom: 0;
        }
    </style>
    <script>
        // Function to toggle fields based on selected role
        function toggleFields() {
            const role = document.getElementById('role').value;
            document.getElementById('student-fields').style.display = (role === 'student') ? 'block' : 'none';
            document.getElementById('company-fields').style.display = (role === 'company') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Call toggleFields on load to initialize field visibility
            toggleFields();
            
            // Particle swarm initialization
            const canvas = document.getElementById('particle-canvas');
            const ctx = canvas.getContext('2d');
            
            // Set canvas to full window size
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            // Mouse position tracking
            let mouseX = canvas.width / 2;
            let mouseY = canvas.height / 2;
            let targetMouseX = mouseX;
            let targetMouseY = mouseY;
            
            // Particle 3D class
            class Particle3D {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.z = Math.random() * 2000 - 1000; // Z depth (-1000 to 1000)
                    this.radius = Math.random() * 3 + 1;
                    this.originalRadius = this.radius;
                    this.speedX = Math.random() * 0.8 - 0.4;
                    this.speedY = Math.random() * 0.8 - 0.4;
                    this.speedZ = Math.random() * 1.5 - 0.75;
                    this.opacity = Math.random() * 0.5 + 0.1;
                    this.originalOpacity = this.opacity;
                    
                    // Use blue shades for particles
                    const blueShade = Math.floor(Math.random() * 80) + 175; // 175-255 range
                    this.color = `rgba(${Math.floor(Math.random() * 50)}, ${Math.floor(Math.random() * 50) + 100}, ${blueShade}, ${this.opacity})`;
                    
                    // Pulse effect
                    this.angle = Math.random() * Math.PI * 2;
                    this.pulseSpeed = Math.random() * 0.02 + 0.005;
                }
                
                update() {
                    // Normal movement
                    this.x += this.speedX;
                    this.y += this.speedY;
                    this.z += this.speedZ;
                    
                    // Pulse effect
                    this.angle += this.pulseSpeed;
                    const pulse = Math.sin(this.angle) * 0.2 + 0.8;
                    this.radius = this.originalRadius * pulse;
                    this.opacity = this.originalOpacity * pulse;
                    
                    // Mouse influence (3D parallax effect)
                    const dx = targetMouseX - this.x;
                    const dy = targetMouseY - this.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < 200) {
                        const angle = Math.atan2(dy, dx);
                        const force = (200 - distance) / 10000;
                        this.speedX += Math.cos(angle) * force;
                        this.speedY += Math.sin(angle) * force;
                    }
                    
                    // Dampen speed to prevent excessive acceleration
                    this.speedX *= 0.985;
                    this.speedY *= 0.985;
                    this.speedZ *= 0.99;
                    
                    // Boundary checks with wrap-around
                    if (this.x < 0) this.x = canvas.width;
                    if (this.x > canvas.width) this.x = 0;
                    if (this.y < 0) this.y = canvas.height;
                    if (this.y > canvas.height) this.y = 0;
                    
                    // Z boundary with smooth transition
                    if (this.z < -1000) this.z = 1000;
                    if (this.z > 1000) this.z = -1000;
                }
                
                draw() {
                    // 3D projection
                    const scale = 1000 / (1000 + this.z);
                    const x2d = (this.x - canvas.width/2) * scale + canvas.width/2;
                    const y2d = (this.y - canvas.height/2) * scale + canvas.height/2;
                    const r2d = this.radius * scale;
                    
                    // Only draw if in front of camera
                    if (this.z < 800) {
                        ctx.beginPath();
                        ctx.arc(x2d, y2d, r2d, 0, Math.PI * 2);
                        
                        // Extract RGB components from color
                        const rgbaMatch = this.color.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([.\d]+)\)/);
                        if (rgbaMatch) {
                            const r = parseInt(rgbaMatch[1]);
                            const g = parseInt(rgbaMatch[2]);
                            const b = parseInt(rgbaMatch[3]);
                            // Adjust opacity based on z-depth
                            const zOpacity = this.opacity * (1 - this.z / 1000);
                            ctx.fillStyle = `rgba(${r}, ${g}, ${b}, ${zOpacity})`;
                        } else {
                            ctx.fillStyle = this.color;
                        }
                        
                        ctx.fill();
                        
                        // Add glow for closer particles
                        if (this.z > -200) {
                            const glowSize = (1 - this.z / 1000) * 3;
                            ctx.shadowBlur = glowSize;
                            ctx.shadowColor = this.color;
                        } else {
                            ctx.shadowBlur = 0;
                        }
                    }
                }
            }
            
            // Create particle array
            const particles = [];
            const particleCount = 150;
            
            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle3D());
            }
            
            // Connection lines between particles with depth effect
            function drawConnections() {
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i; j < particles.length; j++) {
                        // Calculate 2D projected positions
                        const scale1 = 1000 / (1000 + particles[i].z);
                        const scale2 = 1000 / (1000 + particles[j].z);
                        
                        const x1 = (particles[i].x - canvas.width/2) * scale1 + canvas.width/2;
                        const y1 = (particles[i].y - canvas.height/2) * scale1 + canvas.height/2;
                        const x2 = (particles[j].x - canvas.width/2) * scale2 + canvas.width/2;
                        const y2 = (particles[j].y - canvas.height/2) * scale2 + canvas.height/2;
                        
                        const dx = x1 - x2;
                        const dy = y1 - y2;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        // Only connect particles within threshold and in front of camera
                        if (distance < 80 && particles[i].z < 800 && particles[j].z < 800) {
                            // Calculate opacity based on distance and depth
                            const depthFactor = ((2000 - particles[i].z - particles[j].z) / 4000);
                            const opacity = (1 - distance / 80) * 0.15 * depthFactor;
                            
                            ctx.beginPath();
                            ctx.strokeStyle = `rgba(100, 180, 255, ${opacity})`;
                            ctx.lineWidth = 0.5 * ((scale1 + scale2) / 2);
                            ctx.moveTo(x1, y1);
                            ctx.lineTo(x2, y2);
                            ctx.stroke();
                        }
                    }
                }
            }
            
            // Interactive elements
            window.addEventListener('mousemove', function(e) {
                targetMouseX = e.clientX;
                targetMouseY = e.clientY;
            });
            
            // Click event creates ripple effect
            window.addEventListener('click', function(e) {
                // Create ripple effect - push particles away from click
                for (let i = 0; i < particles.length; i++) {
                    const scale = 1000 / (1000 + particles[i].z);
                    const x2d = (particles[i].x - canvas.width/2) * scale + canvas.width/2;
                    const y2d = (particles[i].y - canvas.height/2) * scale + canvas.height/2;
                    
                    const dx = x2d - e.clientX;
                    const dy = y2d - e.clientY;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < 150) {
                        const angle = Math.atan2(dy, dx);
                        const force = (150 - distance) / 800;
                        particles[i].speedX += Math.cos(angle) * force;
                        particles[i].speedY += Math.sin(angle) * force;
                        particles[i].speedZ += (Math.random() - 0.5) * force;
                    }
                }
            });
            
            // Smooth mouse movement
            function updateMousePosition() {
                mouseX += (targetMouseX - mouseX) * 0.05;
                mouseY += (targetMouseY - mouseY) * 0.05;
            }
            
            // Animation loop
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                updateMousePosition();
                
                // Update all particles
                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                }
                
                // Sort particles by Z depth for proper 3D rendering
                particles.sort((a, b) => b.z - a.z);
                
                // Draw connections first (behind particles)
                drawConnections();
                
                // Draw particles
                for (let i = 0; i < particles.length; i++) {
                    particles[i].draw();
                }
                
                requestAnimationFrame(animate);
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
            
            // Start animation
            animate();
        });
    </script>
</head>
<body>
    <div id="particle-container">
        <canvas id="particle-canvas"></canvas>
    </div>
    
    <div class="content-wrapper">
        <h1>Add User</h1>
        
        <!-- Added Back to Dashboard button -->
        <a href="admin_dashboard.php" class="nav-button">Back to Dashboard</a>
        
        <?php
        if (!empty($message)) {
            echo "<p class='" . (strpos($message, 'Error') === false ? 'message' : 'error') . "'>$message</p>";
        }
        ?>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="role">Role:</label>
            <select name="role" id="role" onchange="toggleFields()" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="company">Company</option>
            </select>
            
            <!-- Student Fields -->
            <div id="student-fields" style="display: none;">
                <label for="department">Department:</label>
                <div class="department-wrapper">
                    <select id="department" name="department" class="department-select">
                        <option value="">Select Department</option>
                        <?php
                        // Populate departments dropdown
                        if (!empty($departments)) {
                            foreach ($departments as $dept) {
                                echo "<option value='" . $dept['id'] . "'>" . $dept['department_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <a href="manage_departments.php" class="department-link" target="_blank">Manage</a>
                </div>
                
                <label for="cgpa">CGPA:</label>
                <input type="number" id="cgpa" step="0.01" name="cgpa">
                
                <label for="graduated_year">Graduated Year:</label>
                <input type="number" id="graduated_year" name="graduated_year">
                
                <label for="register_number">Register Number:</label>
                <input type="text" id="register_number" name="register_number">
                
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" id="mobile_number" name="mobile_number">
            </div>
            
            <!-- Company Fields -->
            <div id="company-fields" style="display: none;">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name">
                
                <label for="industry">Industry:</label>
                <input type="text" id="industry" name="industry">
                
                <label for="number_of_employees">Number of Employees:</label>
                <input type="number" id="number_of_employees" name="number_of_employees">
                
                <label for="company_email">Company Email:</label>
                <input type="email" id="company_email" name="company_email">
                
                <label for="company_number">Company Phone Number:</label>
                <input type="text" id="company_number" name="company_number">
            </div>
            
            <button type="submit">Add User</button>
        </form>
    </div>
</body>
</html>