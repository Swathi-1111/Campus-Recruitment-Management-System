<?php 
session_start(); 
include 'db_connect.php'; // Include the database connection  

// Check if the user is logged in as a student 
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {     
    header('Location: index.php'); // Redirect to login page     
    exit(); 
}  

if (!isset($_SESSION['user_id'])) {     
    die("User details are missing from the session. Please log in again."); 
}  

$user_id = $_SESSION['user_id']; // Retrieve the user ID from the session  

// Fetch notifications from the `news` table for the logged-in student 
$sql = "SELECT message, created_at FROM news WHERE user_id = ? ORDER BY created_at DESC"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $user_id); // Bind the user ID 
$stmt->execute(); // Execute the query 
$result = $stmt->get_result(); // Fetch the result set 
?>  

<!DOCTYPE html> 
<html> 
<head>     
    <title>Student Dashboard - News</title>     
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
        
        h1, h2 {             
            color: #0056b3;             
            margin-bottom: 30px;         
        }         
        
        table {             
            width: 80%;             
            margin: 20px auto;             
            border-collapse: collapse;             
            background-color: #fff;             
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }         
        
        th, td {             
            border: 1px solid #c8d8e6;             
            padding: 15px;             
            text-align: left;         
        }         
        
        th {             
            background-color: #0056b3;             
            color: white;
            font-weight: 500;
        }         
        
        td {             
            color: #333;
            transition: background-color 0.3s ease;
        }

        tr:hover td {
            background-color: rgba(0, 86, 179, 0.05);
        }
        
        .no-news {             
            margin-top: 20px;             
            color: #666;             
            font-style: italic;
            padding: 30px;
        }

        .back-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
        }
        
        .back-btn:hover {
            background-color: #5a6268;
        }

        .message-content {
            position: relative;
            padding-left: 15px;
        }

        .message-content:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background-color: rgba(0, 86, 179, 0.2);
            border-radius: 3px;
        }

        .date-badge {
            background-color: rgba(0, 86, 179, 0.1);
            border-radius: 15px;
            padding: 5px 12px;
            display: inline-block;
            font-size: 0.9em;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.9), rgba(0, 86, 179, 0.5));
            box-shadow: 0 0 10px rgba(0, 86, 179, 0.3);
            transform-style: preserve-3d;
        }

        /* Animation for new messages */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .new-message {
            animation: fadeIn 0.8s ease-out;
        }
    </style> 
</head> 
<body>     
    <div id="particles-container"></div>
    
    <div class="content-wrapper">
        <h1>Welcome to Your Dashboard</h1>     
        <h2>News Section</h2>     
        <table>         
            <tr>             
                <th>Message</th>             
                <th>Date</th>         
            </tr>         
            <?php         
            if ($result->num_rows > 0) {             
                // Display each message in the table      
                $count = 0;       
                while ($row = $result->fetch_assoc()) {
                    $animationDelay = $count * 0.2;
                    echo "<tr class='new-message' style='animation-delay: {$animationDelay}s;'>";                 
                    echo "<td><div class='message-content'>{$row['message']}</div></td>";                 
                    echo "<td><span class='date-badge'>" . date("d M Y, H:i", strtotime($row['created_at'])) . "</span></td>";                 
                    echo "</tr>";
                    $count++;             
                }         
            } else {             
                // If no messages, display a fallback message             
                echo "<tr><td colspan='2' class='no-news'>No news available at the moment.</td></tr>";         
            }         
            ?>     
        </table>
        
        <a href="student_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

    <script>
        // 3D Sphere particles animation script
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles-container');
            const particlesCount = 50; // Number of particles
            let containerWidth = window.innerWidth;
            let containerHeight = window.innerHeight;
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

            // Add hover effect for rows
            const tableRows = document.querySelectorAll('table tr:not(:first-child)');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    // Create a small burst of particles around the row
                    const rect = this.getBoundingClientRect();
                    const centerX = rect.left + rect.width/2;
                    const centerY = rect.top + rect.height/2;
                    
                    // Add subtle highlight to the row
                    this.style.boxShadow = '0 0 15px rgba(0, 86, 179, 0.2)';
                    this.style.zIndex = '2';
                    
                    // Push nearby particles away
                    particles.forEach(particle => {
                        const dx = centerX - particle.posX;
                        const dy = centerY - particle.posY;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        if (distance < 200) {
                            const pushFactor = 5.0;
                            particle.speedX -= (dx / distance) * pushFactor * 0.05;
                            particle.speedY -= (dy / distance) * pushFactor * 0.05;
                        }
                    });
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'none';
                    this.style.zIndex = '1';
                });
            });
        });
    </script>
</body> 
</html>