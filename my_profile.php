<?php
session_start();
include 'db_connect.php'; // Include database connection

// Check if the user is logged in as a student
if ($_SESSION['role'] != 'student') {
    header('Location: index.php');
    exit();
}

// Fetch the logged-in student's information
$username = $_SESSION['username'];
$sql = "SELECT users.username, students.department, students.cgpa, students.graduated_year, 
               students.register_number, students.mobile_number, students.resume_path 
        FROM users 
        INNER JOIN students ON users.id = students.user_id 
        WHERE users.username = '$username'";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

// Handle profile update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $cgpa = $_POST['cgpa'];
    $graduated_year = $_POST['graduated_year'];
    $register_number = $_POST['register_number'];
    $mobile_number = $_POST['mobile_number'];

    // Handle resume upload
    $resume_path = $student['resume_path']; // Keep the existing path unless a new file is uploaded
    if (!empty($_FILES['resume']['name'])) {
        $target_dir = "uploads/resumes/";
        $resume_path = $target_dir . basename($_FILES['resume']['name']);

        // Move uploaded file to the target directory
        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
            $message = "Error uploading resume file.";
        }
    }

    // Update profile details in the database
    $update_sql = "UPDATE students 
                   INNER JOIN users ON students.user_id = users.id 
                   SET students.department = '$department', 
                       students.cgpa = '$cgpa', 
                       students.graduated_year = '$graduated_year', 
                       students.register_number = '$register_number', 
                       students.mobile_number = '$mobile_number', 
                       students.resume_path = '$resume_path' 
                   WHERE users.username = '$username'";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Profile updated successfully!";
        // Refresh the page to show updated data
        header("Refresh:0");
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            text-align: center;
            overflow-x: hidden;
        }
        
        #particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            perspective: 1000px;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 1;
            padding: 50px 20px;
        }
        
        h1 {
            color: #0056b3;
            margin-bottom: 30px;
        }
        
        form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-align: left;
            max-width: 500px;
            width: 100%;
            margin-bottom: 20px;
        }
        
        label {
            font-size: 16px;
            color: #0056b3;
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #c8d8e6;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }
        
        input:focus, select:focus {
            border-color: #0056b3;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 86, 179, 0.2);
        }
        
        button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }
        
        button:hover {
            background-color: #003d80;
        }
        
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        
        .back-btn {
            background-color: #6c757d;
        }
        
        .back-btn:hover {
            background-color: #5a6268;
        }
        
        .message {
            color: #28a745;
            font-size: 16px;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: rgba(40, 167, 69, 0.1);
            display: inline-block;
        }
        
        .resume-link {
            color: #0056b3;
            text-decoration: none;
        }
        
        .resume-link:hover {
            text-decoration: underline;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.9), rgba(0, 86, 179, 0.5));
            box-shadow: 0 0 10px rgba(0, 86, 179, 0.3);
            transform-style: preserve-3d;
        }
    </style>
</head>
<body>
    <div id="particles-container"></div>
    
    <div class="content-wrapper">
        <h1>My Profile</h1>
        <?php
        if (!empty($message)) {
            echo "<p class='message'>$message</p>";
        }
        ?>

        <form method="post" enctype="multipart/form-data">
            <label>Username:</label>
            <input type="text" value="<?php echo $student['username']; ?>" readonly><br>

            <label>Department:</label>
            <input type="text" name="department" value="<?php echo $student['department']; ?>" required><br>

            <label>CGPA:</label>
            <input type="number" step="0.01" name="cgpa" value="<?php echo $student['cgpa']; ?>" required><br>

            <label>Graduated Year:</label>
            <input type="number" name="graduated_year" value="<?php echo $student['graduated_year']; ?>" required><br>

            <label>Register Number:</label>
            <input type="text" name="register_number" value="<?php echo $student['register_number']; ?>" required><br>

            <label>Mobile Number:</label>
            <input type="text" name="mobile_number" value="<?php echo $student['mobile_number']; ?>" required><br>

            <label>Upload Resume:</label>
            <input type="file" name="resume"><br>
            <?php
            if (!empty($student['resume_path'])) {
                echo "<p>Current Resume: <a class='resume-link' href='" . $student['resume_path'] . "' target='_blank'>View Resume</a></p>";
            }
            ?>

            <div class="button-group">
                <button type="submit">Update Profile</button>
                <button type="button" class="back-btn" onclick="window.location.href='student_dashboard.php'">Back to Dashboard</button>
            </div>
        </form>
    </div>

    <script>
        // 3D Sphere particles animation script
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles-container');
            const particlesCount = 50; // Increased number of particles
            const containerWidth = window.innerWidth;
            const containerHeight = window.innerHeight;
            const particles = [];
            
            // Create particles
            for (let i = 0; i < particlesCount; i++) {
                createParticle();
            }
            
            function createParticle() {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random size between 8 and 25px for more 3D effect
                const size = Math.random() * 17 + 8;
                
                // Style the particle
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                
                // Set initial position
                const posX = Math.random() * containerWidth;
                const posY = Math.random() * containerHeight;
                const posZ = Math.random() * 500 - 250; // Z position for 3D effect
                
                particle.style.left = posX + 'px';
                particle.style.top = posY + 'px';
                
                // Set 3D transform
                particle.style.transform = `translateZ(${posZ}px)`;
                
                // Set opacity based on Z position for depth effect
                const opacity = Math.max(0.2, (500 - Math.abs(posZ)) / 500);
                particle.style.opacity = opacity;
                
                // Add to container
                particlesContainer.appendChild(particle);
                
                // Animation properties - faster movement
                const speedX = (Math.random() - 0.5) * 2.5; // Increased speed
                const speedY = (Math.random() - 0.5) * 2.5; // Increased speed
                const speedZ = (Math.random() - 0.5) * 2; // Z-axis speed
                
                // Rotation speed
                const rotateX = (Math.random() - 0.5) * 0.5;
                const rotateY = (Math.random() - 0.5) * 0.5;
                
                particles.push({
                    element: particle,
                    posX: posX,
                    posY: posY,
                    posZ: posZ,
                    speedX: speedX,
                    speedY: speedY,
                    speedZ: speedZ,
                    rotateX: rotateX,
                    rotateY: rotateY
                });
            }
            
            // Animation loop
            function animateParticles() {
                particles.forEach(particle => {
                    // Update position
                    particle.posX += particle.speedX;
                    particle.posY += particle.speedY;
                    particle.posZ += particle.speedZ;
                    
                    // Boundary check and bounce
                    if (particle.posX < 0 || particle.posX > containerWidth) {
                        particle.speedX = -particle.speedX;
                    }
                    
                    if (particle.posY < 0 || particle.posY > containerHeight) {
                        particle.speedY = -particle.speedY;
                    }
                    
                    if (particle.posZ < -250 || particle.posZ > 250) {
                        particle.speedZ = -particle.speedZ;
                    }
                    
                    // Update opacity based on Z position
                    const opacity = Math.max(0.2, (500 - Math.abs(particle.posZ)) / 500);
                    
                    // Apply new position and transform
                    particle.element.style.left = particle.posX + 'px';
                    particle.element.style.top = particle.posY + 'px';
                    particle.element.style.transform = `translateZ(${particle.posZ}px) rotateX(${particle.rotateX}deg) rotateY(${particle.rotateY}deg)`;
                    particle.element.style.opacity = opacity;
                    
                    // Scale based on Z position for perspective effect
                    const scale = (500 + particle.posZ) / 500;
                    particle.element.style.transform += ` scale(${scale})`;
                });
                
                requestAnimationFrame(animateParticles);
            }
            
            // Start animation
            animateParticles();
            
            // Handle window resize
            window.addEventListener('resize', function() {
                containerWidth = window.innerWidth;
                containerHeight = window.innerHeight;
            });
            
            // Interactive effect with mouse
            document.addEventListener('mousemove', function(e) {
                const mouseX = e.clientX;
                const mouseY = e.clientY;
                
                particles.forEach(particle => {
                    // Calculate distance between mouse and particle
                    const dx = mouseX - particle.posX;
                    const dy = mouseY - particle.posY;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    // If mouse is close to the particle, push it away slightly
                    if (distance < 100) {
                        const pushFactor = 2.0; // Stronger push effect
                        particle.posX -= (dx / distance) * pushFactor;
                        particle.posY -= (dy / distance) * pushFactor;
                        
                        // Add a slight spin when interacting
                        particle.rotateX += 2;
                        particle.rotateY += 2;
                    }
                });
            });
        });
    </script>
</body>
</html>