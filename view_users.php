<?php
include 'db_connect.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Fetch students and their information
$students_sql = "SELECT users.id, 
                        users.username, 
                        students.department, 
                        students.cgpa, 
                        students.graduated_year, 
                        students.register_number, 
                        students.mobile_number 
                 FROM users 
                 INNER JOIN students ON users.id = students.user_id 
                 WHERE users.role = 'student'";
$students_result = $conn->query($students_sql);
$student_count = $students_result->num_rows;

// Fetch companies and their information
$companies_sql = "SELECT users.id, 
                         users.username, 
                         companies.company_name, 
                         companies.industry, 
                         companies.number_of_employees, 
                         companies.company_email, 
                         companies.company_number 
                  FROM users 
                  INNER JOIN companies ON users.id = companies.user_id 
                  WHERE users.role = 'company'";
$companies_result = $conn->query($companies_sql);
$company_count = $companies_result->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            width: 100%;
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
            justify-content: flex-start;
            min-height: 100vh;
            padding: 50px 0;
        }
        
        h1 {
            color: #0288d1;
            margin-bottom: 20px;
            font-size: 32px;
            z-index: 10;
            position: relative;
        }
        
        h2 {
            color: #0288d1;
            margin: 20px 0;
            font-size: 24px;
            z-index: 10;
            position: relative;
        }
        
        .summary {
            font-size: 18px;
            color: #fff;
            margin: 10px 0 30px 0;
            z-index: 10;
            position: relative;
            background-color: rgba(2, 136, 209, 0.3);
            padding: 10px 20px;
            border-radius: 5px;
            backdrop-filter: blur(5px);
        }
        
        table {
            width: 90%;
            margin: 10px auto 30px auto;
            border-collapse: collapse;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            z-index: 10;
            position: relative;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            backdrop-filter: blur(8px);
        }
        
        th, td {
            border: 1px solid rgba(2, 136, 209, 0.3);
            padding: 12px 15px;
            text-align: left;
        }
        
        th {
            background-color: #0288d1;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        td {
            background-color: rgba(255, 255, 255, 0.8);
            color: #01579b;
            font-size: 14px;
        }
        
        tr:nth-child(even) td {
            background-color: rgba(240, 248, 255, 0.8);
        }
        
        #particle-container {
            position: fixed;
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
            padding: 20px 0;
        }
        
        tr:hover td {
            background-color: rgba(2, 136, 209, 0.1);
        }
        
        /* Add navigation button */
        .nav-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            z-index: 10;
            position: relative;
        }
        
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
        }
        
        .nav-button:hover {
            background-color: #01579b;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        <h1>View Users</h1>
        
        <div class="nav-buttons">
            <a href="add_user.php" class="nav-button">Add New User</a>
            <a href="admin_dashboard.php" class="nav-button">Back to Dashboard</a>
        </div>
        
        <p class="summary">Total Students: <?php echo $student_count; ?> | Total Companies: <?php echo $company_count; ?></p>

        <!-- Students Section -->
        <h2>Students</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Department</th>
                <th>CGPA</th>
                <th>Graduated Year</th>
                <th>Register Number</th>
                <th>Mobile Number</th>
            </tr>
            <?php
            if ($student_count > 0) {
                while ($student = $students_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $student['id'] . "</td>";
                    echo "<td>" . $student['username'] . "</td>";
                    echo "<td>" . $student['department'] . "</td>";
                    echo "<td>" . $student['cgpa'] . "</td>";
                    echo "<td>" . $student['graduated_year'] . "</td>";
                    echo "<td>" . $student['register_number'] . "</td>";
                    echo "<td>" . $student['mobile_number'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align: center;'>No students found</td></tr>";
            }
            ?>
        </table>

        <!-- Companies Section -->
        <h2>Companies</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Company Name</th>
                <th>Industry</th>
                <th>Number of Employees</th>
                <th>Company Email</th>
                <th>Company Phone Number</th>
            </tr>
            <?php
            if ($company_count > 0) {
                while ($company = $companies_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $company['id'] . "</td>";
                    echo "<td>" . $company['username'] . "</td>";
                    echo "<td>" . $company['company_name'] . "</td>";
                    echo "<td>" . $company['industry'] . "</td>";
                    echo "<td>" . $company['number_of_employees'] . "</td>";
                    echo "<td>" . $company['company_email'] . "</td>";
                    echo "<td>" . ($company['company_number'] ? $company['company_number'] : "No Phone Number Available") . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align: center;'>No companies found</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>