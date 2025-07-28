<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Recruitment</title>
    <style>
        /* Global Styles */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
            overflow: hidden; /* Prevent scrollbar */
            background-color: #f0f8ff; /* Light blue background to enhance particle visibility */
            transition: background-color 0.5s ease;
        }

        /* Welcome Text Styles */
        h1 {
            color: #0056b3; /* Deep blue for header text */
            font-size: 3rem;
            margin-top: 40px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
            transition: color 0.5s ease;
        }
        p {
            color: #333333; /* Neutral color for the subtitle */
            font-size: 1.2rem;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
            transition: color 0.5s ease;
        }

        /* Boxes Container */
        .login-boxes {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }

        /* Individual Box Styling */
        .login-box {
            background-color: #0056b3;
            color: white;
            padding: 20px;
            width: 180px;
            height: 120px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box:hover {
            background-color: #003d80;
            transform: translateY(-8px);
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.4);
        }

        /* Canvas for 3D Animation - Make it more prominent */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1; /* Higher than background but behind content */
            top: 0;
            left: 0;
        }

        /* Centered Content without background box */
        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
        }

        /* GLASSMORPHISM STYLES */
        .glass-container {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 40px;
            margin-top: 30px;
            transition: all 0.5s ease;
        }

        .glass-title {
            background: linear-gradient(45deg, #0056b3, #00a2ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
            transition: all 0.5s ease;
        }

        .glass-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
            padding: 25px;
            width: 180px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .glass-box:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 0 15px 35px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .glass-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .glass-box:hover::before {
            left: 100%;
        }

        .glass-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #0056b3;
            transition: color 0.5s ease;
        }

        .glass-text {
            color: #003d80;
            font-weight: 600;
            font-size: 1.3rem;
            transition: color 0.5s ease;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 86, 179, 0.7);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(0, 86, 179, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 86, 179, 0);
            }
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        /* Decorative elements */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            z-index: 1;
            transition: background 0.5s ease;
        }

        .circle-1 {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
        }

        .circle-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 20%;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .theme-icon {
            font-size: 1.5rem;
            color: #0056b3;
            transition: all 0.3s ease;
        }

        /* Dark Theme Styles */
        body.dark-theme {
            background-color: #121212;
        }

        .dark-theme h1 {
            color: #7bb9ff;
        }

        .dark-theme p {
            color: #e0e0e0;
        }

        .dark-theme .glass-container {
            background: rgba(30, 30, 30, 0.5);
            border: 1px solid rgba(100, 100, 100, 0.18);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .dark-theme .glass-title {
            background: linear-gradient(45deg, #7bb9ff, #00a2ff);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .dark-theme .glass-box {
            background: rgba(30, 30, 30, 0.3);
            border: 1px solid rgba(100, 100, 100, 0.2);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .dark-theme .glass-icon {
            color: #7bb9ff;
        }

        .dark-theme .glass-text {
            color: #e0e0e0;
        }

        .dark-theme .circle {
            background: rgba(30, 30, 30, 0.3);
        }

        .dark-theme .theme-toggle {
            background: rgba(30, 30, 30, 0.5);
            border: 1px solid rgba(100, 100, 100, 0.3);
        }

        .dark-theme .theme-icon {
            color: #7bb9ff;
        }

        .dark-theme .pulse {
            animation: dark-pulse 2s infinite;
        }

        @keyframes dark-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(123, 185, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(123, 185, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(123, 185, 255, 0);
            }
        }

        /* Theme transition effect */
        .theme-transition {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9;
            transition: transform 1s ease;
            transform: scale(0);
        }

        .theme-transition.active {
            transform: scale(100);
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Theme toggle button -->
    <div class="theme-toggle" id="theme-toggle">
        <i class="fas fa-moon theme-icon" id="theme-icon"></i>
    </div>

    <!-- Theme transition effect -->
    <div class="theme-transition" id="theme-transition"></div>

    <!-- Decorative circles -->
    <div class="circle circle-1"></div>
    <div class="circle circle-2"></div>
    <div class="circle circle-3"></div>

    <!-- 3D Animated Background - Now more prominent -->
    <div id="particles-js"></div>

    <!-- Centered Content with glassmorphism -->
    <div class="content">
        <h1 class="glass-title floating">Welcome to Campus Recruitment</h1>
        <div class="glass-container">
            <p>Choose your login below:</p>
            <div class="login-boxes">
                <div class="glass-box pulse" onclick="window.location.href='admin_login.php'" id="admin-box">
                    <i class="fas fa-user-shield glass-icon"></i>
                    <span class="glass-text">Admin</span>
                </div>
                <div class="glass-box" onclick="window.location.href='student_login.php'" id="student-box">
                    <i class="fas fa-user-graduate glass-icon"></i>
                    <span class="glass-text">Student</span>
                </div>
                <div class="glass-box" onclick="window.location.href='company_login.php'" id="company-box">
                    <i class="fas fa-building glass-icon"></i>
                    <span class="glass-text">Company</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize particles.js with light theme
        function initParticles(isDark = false) {
            particlesJS("particles-js", {
                "particles": {
                    "number": {
                        "value": 150,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": isDark ? 
                            ["#7bb9ff", "#a9cdff", "#00a2ff", "#3f8cff"] : 
                            ["#0056b3", "#1e90ff", "#00bfff", "#87cefa"]
                    },
                    "shape": {
                        "type": ["circle", "triangle"],
                        "stroke": {
                            "width": 1,
                            "color": isDark ? "#454545" : "#ffffff"
                        }
                    },
                    "opacity": {
                        "value": 0.7,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 1.5,
                            "opacity_min": 0.3,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 6,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 4,
                            "size_min": 1,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": isDark ? "#7bb9ff" : "#0056b3",
                        "opacity": 0.4,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 3,
                        "direction": "none",
                        "random": true,
                        "straight": false,
                        "out_mode": "bounce",
                        "bounce": true,
                        "attract": {
                            "enable": true,
                            "rotateX": 600,
                            "rotateY": 1200
                        }
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "grab"
                        },
                        "onclick": {
                            "enable": true,
                            "mode": "push"
                        },
                        "resize": true
                    },
                    "modes": {
                        "grab": {
                            "distance": 180,
                            "line_linked": {
                                "opacity": 0.8
                            }
                        },
                        "push": {
                            "particles_nb": 4
                        },
                        "bubble": {
                            "distance": 200,
                            "size": 12,
                            "duration": 2,
                            "opacity": 0.8,
                            "speed": 3
                        },
                        "repulse": {
                            "distance": 150,
                            "duration": 0.4
                        }
                    }
                },
                "retina_detect": true
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize particles
            initParticles();
            
            // Theme toggle functionality
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const themeTransition = document.getElementById('theme-transition');
            let isDarkTheme = false;
            
            themeToggle.addEventListener('click', function() {
                // Show transition effect
                themeTransition.classList.add('active');
                
                setTimeout(() => {
                    // Toggle theme
                    isDarkTheme = !isDarkTheme;
                    document.body.classList.toggle('dark-theme');
                    
                    // Change icon
                    if (isDarkTheme) {
                        themeIcon.classList.remove('fa-moon');
                        themeIcon.classList.add('fa-sun');
                    } else {
                        themeIcon.classList.remove('fa-sun');
                        themeIcon.classList.add('fa-moon');
                    }
                    
                    // Reinitialize particles
                    initParticles(isDarkTheme);
                    
                    // Hide transition after theme change
                    setTimeout(() => {
                        themeTransition.classList.remove('active');
                    }, 500);
                }, 500);
            });
            
            // Create animation sequence for login boxes
            const boxes = document.querySelectorAll('.glass-box');
            
            // Remove pulse class from admin box after 3 seconds
            setTimeout(() => {
                document.getElementById('admin-box').classList.remove('pulse');
                document.getElementById('student-box').classList.add('pulse');
            }, 3000);
            
            // Shift pulse effect to company box after another 3 seconds
            setTimeout(() => {
                document.getElementById('student-box').classList.remove('pulse');
                document.getElementById('company-box').classList.add('pulse');
            }, 6000);
            
            // Reset the animation cycle
            setTimeout(() => {
                document.getElementById('company-box').classList.remove('pulse');
                document.getElementById('admin-box').classList.add('pulse');
            }, 9000);
            
            // Add hover effect that changes particle density
            boxes.forEach(box => {
                box.addEventListener('mouseenter', function() {
                    particlesJS.pJS.particles.number.value = 250;
                    particlesJS.pJS.fn.particlesRefresh();
                    
                    // Create ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.width = '20px';
                    ripple.style.height = '20px';
                    ripple.style.background = 'rgba(255, 255, 255, 0.7)';
                    ripple.style.borderRadius = '50%';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 1s linear';
                    ripple.style.opacity = '1';
                    
                    box.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 1000);
                });
                
                box.addEventListener('mouseleave', function() {
                    particlesJS.pJS.particles.number.value = 150;
                    particlesJS.pJS.fn.particlesRefresh();
                });
            });
        });
        
        // Add ripple animation
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                @keyframes ripple {
                    0% {
                        transform: scale(0);
                        opacity: 1;
                    }
                    100% {
                        transform: scale(20);
                        opacity: 0;
                    }
                }
            </style>
        `);
    </script>
</body>
</html>