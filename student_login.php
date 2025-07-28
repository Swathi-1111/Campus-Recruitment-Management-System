<?php session_start(); include 'db_connect.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input to prevent SQL injection
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Use a prepared statement for secure database queries
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? AND password = ? AND role = 'student'");
    $stmt->bind_param("ss", $username, $password); // Bind user inputs
    $stmt->execute(); // Execute the query
    $result = $stmt->get_result(); // Get the query results
    
    if ($result->num_rows == 1) {
        // Fetch user details and store in session
        $user = $result->fetch_assoc();
        $_SESSION['role'] = $user['role']; // Save user role in session
        $_SESSION['user_id'] = $user['id']; // Save user ID in session
        $_SESSION['username'] = $username; // Save username in session
        
        // Redirect to student dashboard
        header('Location: student_dashboard.php');
        exit();
    } else {
        // Login failed, show error message
        $error = "Invalid username or password!";
    }
    
    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            overflow: hidden;
            position: relative;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #e0f7ff 0%, #87ceeb 100%);
        }
        
        #bubbles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.8), rgba(173, 216, 230, 0.4));
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(1px);
            transition: transform 0.2s ease, opacity 0.3s ease;
        }
        
        .bubble.popping {
            animation: pop-bubble 0.5s forwards;
        }
        
        @keyframes pop-bubble {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
            100% {
                transform: scale(0.1);
                opacity: 0;
            }
        }
        
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        h1 {
            color: #0056b3;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        form {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 86, 179, 0.15);
            backdrop-filter: blur(10px);
            text-align: left;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        form:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 86, 179, 0.2);
        }
        
        label {
            font-size: 16px;
            color: #0056b3;
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 2px solid #c8e3ff;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.2);
        }
        
        button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 14px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
        }
        
        button:hover {
            background-color: #003d80;
            transform: translateY(-2px);
        }
        
        button:active {
            transform: translateY(1px);
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 6px;
            text-align: center;
        }
        
        .school-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .school-name {
            color: #003d80;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }
            
            form {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div id="bubbles-container"></div>
    
    <div class="login-container">
        <div class="school-logo">
            <!-- School logo -->
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M40 5L10 20L40 35L70 20L40 5Z" fill="#0056B3"/>
                <path d="M20 25V50L40 65L60 50V25" stroke="#0056B3" stroke-width="4" fill="none"/>
                <circle cx="40" cy="20" r="8" fill="#FFFFFF"/>
            </svg>
        </div>
        
        <div class="school-name">ACADEMIC LEARNING CENTER</div>
        
        <h1>Student Login</h1>
        
        <?php
        if (!empty($error)) {
            echo "<div class='error'>$error</div>"; // Show error message if login fails
        }
        ?>
        
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="username">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            
            <button type="submit">Sign In</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bubblesContainer = document.getElementById('bubbles-container');
            const bubbleCount = 50; // Increased number of bubbles
            const bubbles = [];
            
            // Bubble size range
            const minSize = 20;
            const maxSize = 120;
            
            // Bubble speed range
            const minSpeed = 0.5;
            const maxSpeed = 2.5;
            
            // Mouse position tracking
            let mouseX = 0;
            let mouseY = 0;
            let lastMouseX = 0;
            let lastMouseY = 0;
            let isMouseMoving = false;
            
            // Create bubbles with motion properties
            for (let i = 0; i < bubbleCount; i++) {
                createBubble();
            }
            
            function createBubble() {
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                
                // Random size
                const size = Math.random() * (maxSize - minSize) + minSize;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                
                // Random position
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                bubble.style.left = `${posX}%`;
                bubble.style.top = `${posY}%`;
                
                // Random opacity
                bubble.style.opacity = Math.random() * 0.4 + 0.2; // Between 0.2-0.6
                
                // Add motion properties
                const bubbleObj = {
                    element: bubble,
                    size: size,
                    posX: posX,
                    posY: posY,
                    speedX: (Math.random() - 0.5) * (maxSpeed - minSpeed) + minSpeed,
                    speedY: (Math.random() - 0.5) * (maxSpeed - minSpeed) + minSpeed,
                    popped: false
                };
                
                bubbles.push(bubbleObj);
                bubblesContainer.appendChild(bubble);
                
                // Reset bubble when it pops
                bubble.addEventListener('animationend', () => {
                    if (bubble.classList.contains('popping')) {
                        bubble.classList.remove('popping');
                        bubbleObj.popped = false;
                        bubbleObj.posX = Math.random() * 100;
                        bubbleObj.posY = Math.random() * 100;
                        bubble.style.left = `${bubbleObj.posX}%`;
                        bubble.style.top = `${bubbleObj.posY}%`;
                    }
                });
            }
            
            // Track mouse movement
            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                
                // Check if mouse is moving enough to consider it "moving"
                const moveThreshold = 5;
                if (Math.abs(mouseX - lastMouseX) > moveThreshold || 
                    Math.abs(mouseY - lastMouseY) > moveThreshold) {
                    isMouseMoving = true;
                    
                    // Check collision with bubbles
                    checkBubbleCollision();
                    
                    // Update last mouse position
                    lastMouseX = mouseX;
                    lastMouseY = mouseY;
                    
                    // Reset mouse moving after a short delay
                    setTimeout(() => {
                        isMouseMoving = false;
                    }, 100);
                }
            });
            
            // Check if mouse collides with bubbles
            function checkBubbleCollision() {
                bubbles.forEach(bubble => {
                    if (bubble.popped) return;
                    
                    // Convert percentage position to pixels
                    const bubbleX = (bubble.posX / 100) * window.innerWidth;
                    const bubbleY = (bubble.posY / 100) * window.innerHeight;
                    
                    // Check distance between mouse and bubble
                    const dx = mouseX - bubbleX;
                    const dy = mouseY - bubbleY;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    // Collision radius (half of bubble size + some margin)
                    const collisionRadius = bubble.size / 2 + 20;
                    
                    if (distance < collisionRadius && isMouseMoving) {
                        // Pop the bubble
                        bubble.element.classList.add('popping');
                        bubble.popped = true;
                    }
                });
            }
            
            // Animation loop for bubble movement
            function animateBubbles() {
                bubbles.forEach(bubble => {
                    if (bubble.popped) return;
                    
                    // Update position
                    bubble.posX += bubble.speedX * 0.05;
                    bubble.posY += bubble.speedY * 0.05;
                    
                    // Boundary check - wrap around screen
                    if (bubble.posX > 105) bubble.posX = -5;
                    if (bubble.posX < -5) bubble.posX = 105;
                    if (bubble.posY > 105) bubble.posY = -5;
                    if (bubble.posY < -5) bubble.posY = 105;
                    
                    // Update bubble position
                    bubble.element.style.left = `${bubble.posX}%`;
                    bubble.element.style.top = `${bubble.posY}%`;
                });
                
                requestAnimationFrame(animateBubbles);
            }
            
            // Start animation
            animateBubbles();
        });
    </script>
</body>
</html>