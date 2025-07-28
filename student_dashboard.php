<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        :root {
            --primary: #0056b3;
            --primary-dark: #003d80;
            --primary-light: #e0f7ff;
            --accent: #3498db;
            --text-dark: #333;
            --text-light: #fff;
            --success: #2ecc71;
            --shadow: rgba(0, 86, 179, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f8ff 0%, #e6f7ff 100%);
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        #bubbleCanvas {
            display: block;
            width: 100%;
            height: 100%;
        }
        
        .dashboard-container {
            width: 100%;
            max-width: 1200px;
            z-index: 1;
        }
        
        h1 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .quick-link {
            text-decoration: none;
            background: rgba(0, 86, 179, 0.9);
            color: var(--text-light);
            padding: 1.2rem 2rem;
            border-radius: 50px;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.4s ease;
            font-weight: 500;
            box-shadow: 0 8px 15px rgba(0, 86, 179, 0.2);
            backdrop-filter: blur(5px);
            min-width: 180px;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .quick-link:hover {
            background: var(--primary-dark);
            transform: translateY(-6px);
            box-shadow: 0 12px 20px rgba(0, 86, 179, 0.3);
        }
        
        .quick-link svg {
            width: 24px;
            height: 24px;
            fill: var(--text-light);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .quick-links {
                flex-direction: column;
                align-items: center;
            }
            
            .quick-link {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="canvas-container">
        <canvas id="bubbleCanvas"></canvas>
    </div>
    
    <div class="dashboard-container">
        <h1>Student Dashboard</h1>
        
        <div class="quick-links">
            <a href="apply_job.php" class="quick-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M20 6h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-8 0h-4V4h4v2z" fill="currentColor"></path>
                </svg>
                Apply Job
            </a>
            <a href="my_jobs.php" class="quick-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" fill="currentColor"></path>
                </svg>
                My Jobs
            </a>
            <a href="my_profile.php" class="quick-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"></path>
                </svg>
                My Profile
            </a>
            <a href="news.php" class="quick-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="currentColor"></path>
                </svg>
                News
            </a>
            <a href="#" id="logoutButton" class="quick-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" fill="currentColor"></path>
                </svg>
                Logout
            </a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Interactive bubble background
            const canvas = document.getElementById('bubbleCanvas');
            const ctx = canvas.getContext('2d');
            
            // Set canvas size
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            // Handle window resize
            window.addEventListener('resize', function() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
                // Recreate bubbles when window is resized
                createBubbles();
            });
            
            // Bubble configuration
            let bubbles = [];
            const bubbleCount = 150;
            const bubbleColors = ['rgba(0, 86, 179, 0.3)', 'rgba(52, 152, 219, 0.3)', 'rgba(3, 102, 214, 0.3)'];
            const maxRadius = 20;
            const minRadius = 3;
            
            // Mouse tracking
            let mouse = {
                x: undefined,
                y: undefined,
                radius: 150
            };
            
            // Track mouse movement
            window.addEventListener('mousemove', function(e) {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });
            
            // Create bubble objects
            class Bubble {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.radius = Math.random() * (maxRadius - minRadius) + minRadius;
                    this.originalRadius = this.radius;
                    this.color = bubbleColors[Math.floor(Math.random() * bubbleColors.length)];
                    this.speedX = Math.random() * 0.5 - 0.25;
                    this.speedY = Math.random() * 0.5 - 0.25;
                    this.opacity = Math.random() * 0.5 + 0.2;
                    this.destroyed = false;
                    this.shrinkRate = Math.random() * 0.05 + 0.01;
                    this.respawnDelay = 0;
                }
                
                // Update bubble position
                update() {
                    if (this.destroyed) {
                        // Handle destroyed bubbles
                        this.respawnDelay++;
                        if (this.respawnDelay > 100) {
                            // Respawn bubble
                            this.x = Math.random() * canvas.width;
                            this.y = Math.random() * canvas.height;
                            this.radius = 0;
                            this.destroyed = false;
                            this.respawnDelay = 0;
                        }
                        return;
                    }
                    
                    // Grow bubbles that are respawning
                    if (this.radius < this.originalRadius) {
                        this.radius += 0.1;
                    }
                    
                    // Check for mouse collision
                    const dx = mouse.x - this.x;
                    const dy = mouse.y - this.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < mouse.radius + this.radius) {
                        // Break bubble when mouse comes close
                        this.destroyed = true;
                        this.radius = 0;
                        
                        // Create mini explosion particles
                        for (let i = 0; i < 5; i++) {
                            const angle = Math.random() * Math.PI * 2;
                            const speed = Math.random() * 2 + 1;
                            particles.push({
                                x: this.x,
                                y: this.y,
                                radius: Math.random() * 2 + 0.5,
                                speedX: Math.cos(angle) * speed,
                                speedY: Math.sin(angle) * speed,
                                color: this.color,
                                life: 30 + Math.random() * 20
                            });
                        }
                    }
                    
                    // Move bubbles
                    this.x += this.speedX;
                    this.y += this.speedY;
                    
                    // Wrap around screen
                    if (this.x < -this.radius) this.x = canvas.width + this.radius;
                    if (this.x > canvas.width + this.radius) this.x = -this.radius;
                    if (this.y < -this.radius) this.y = canvas.height + this.radius;
                    if (this.y > canvas.height + this.radius) this.y = -this.radius;
                }
                
                // Draw bubble
                draw() {
                    if (this.destroyed) return;
                    
                    // Draw bubble with gradient
                    ctx.beginPath();
                    const gradient = ctx.createRadialGradient(
                        this.x, this.y, 0,
                        this.x, this.y, this.radius
                    );
                    
                    gradient.addColorStop(0, 'rgba(255, 255, 255, 0.5)');
                    gradient.addColorStop(0.8, this.color);
                    
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    ctx.fillStyle = gradient;
                    ctx.fill();
                    
                    // Add highlight to make it look like a bubble
                    ctx.beginPath();
                    ctx.arc(
                        this.x - this.radius * 0.3,
                        this.y - this.radius * 0.3,
                        this.radius * 0.2,
                        0, Math.PI * 2
                    );
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.6)';
                    ctx.fill();
                }
            }
            
            // Particles for explosion effect
            let particles = [];
            
            // Create all bubbles
            function createBubbles() {
                bubbles = [];
                for (let i = 0; i < bubbleCount; i++) {
                    bubbles.push(new Bubble());
                }
            }
            
            // Update particles
            function updateParticles() {
                for (let i = particles.length - 1; i >= 0; i--) {
                    particles[i].x += particles[i].speedX;
                    particles[i].y += particles[i].speedY;
                    particles[i].life--;
                    
                    if (particles[i].life <= 0) {
                        particles.splice(i, 1);
                    }
                }
            }
            
            // Draw particles
            function drawParticles() {
                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = p.color;
                    ctx.fill();
                }
            }
            
            // Animation loop
            function animate() {
                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Update and draw bubbles
                for (let i = 0; i < bubbles.length; i++) {
                    bubbles[i].update();
                    bubbles[i].draw();
                }
                
                // Update and draw particles
                updateParticles();
                drawParticles();
                
                // Continue animation
                requestAnimationFrame(animate);
            }
            
            // Improved logout functionality
            const logoutButton = document.getElementById('logoutButton');
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('Are you sure you want to logout?')) {
                    // Option 1: If you have a server-side logout script
                    // Create a form and submit it to logout.php
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'logout.php';
                    document.body.appendChild(form);
                    
                    // Add a hidden field for CSRF protection if needed
                    const csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = 'csrf_token';
                    csrfField.value = 'YOUR_CSRF_TOKEN_HERE'; // Replace with actual token
                    form.appendChild(csrfField);
                    
                    form.submit();
                    
                    // Option 2: If you're using PHP sessions and want a simpler approach
                    // You can use this alternative method instead:
                    // window.location.href = 'logout.php';
                    
                    // Option 3: If you want to go directly to index page after confirmation
                    // window.location.href = 'index.php';
                }
            });
            
            // Initialize
            createBubbles();
            animate();
        });
    </script>
</body>
</html>