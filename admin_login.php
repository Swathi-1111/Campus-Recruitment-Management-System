<?php
session_start();
include 'db_connect.php'; // Include the database connection file

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Secure login query using prepared statements
    $sql = "SELECT * FROM users WHERE username = ? AND password = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Login successful
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;
        header('Location: admin_dashboard.php');
        exit();
    } else {
        // Login failed
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* Global Styles */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f0f8ff; /* Light blue background */
            overflow: hidden; /* Prevent scrolling */
        }

        /* Particle Canvas */
        #particles-js {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0; /* Place particles behind content but visible */
        }

        /* Centered Content */
        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-top: 100px;
        }
        h1 {
            font-size: 3rem;
            color: #0056b3;
            margin-bottom: 20px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }
        p.error {
            color: red;
            font-size: 1rem;
            margin-bottom: 20px;
            font-weight: bold;
            background-color: rgba(255, 220, 220, 0.7);
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }

        /* Login Form Styling */
        form {
            display: inline-block;
            background-color: rgba(248, 249, 250, 0.85);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            text-align: left;
            width: 300px; /* Fixed width for form */
            backdrop-filter: blur(5px); /* Creates a frosted glass effect */
        }
        label {
            font-size: 1rem;
            color: #0056b3;
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #0056b3;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.9);
            color: #333333;
            font-size: 1rem;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #003d80;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #0056b3;
            color: white;
            border: none;
            font-size: 1.2rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        }
        button:hover {
            background-color: #003d80;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(1px);
        }
        
        /* Back to home link */
        .back-home {
            display: block;
            margin: 15px auto 0;
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }
        .back-home:hover {
            text-decoration: underline;
        }
        
        /* Form container to keep form and back link together */
        .form-container {
            display: inline-block;
        }
    </style>
    <!-- Load Particle.js Library from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
</head>
<body>
    <!-- 3D Particle Background -->
    <div id="particles-js"></div>

    <!-- Main Content -->
    <div class="content">
        <h1>Admin Login</h1>
        <?php
        if (!empty($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <div class="form-container">
            <form method="post">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Login</button>
            </form>
            <a href="index.php" class="back-home">Back to Home</a>
        </div>
    </div>

    <!-- Particle.js Configuration - Enhanced for 3D particles with mouse interactivity -->
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": {
                    "value": 150, /* Increased number of particles */
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": ["#0056b3", "#1e90ff", "#00bfff", "#87cefa", "#4682b4"] /* Various blue shades */
                },
                "shape": {
                    "type": "circle", /* Strictly round particles */
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    }
                },
                "opacity": {
                    "value": 0.7,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 1,
                        "opacity_min": 0.3,
                        "sync": false
                    }
                },
                "size": {
                    "value": 7, /* Larger particles for better 3D effect */
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 4,
                        "size_min": 2,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": false /* No connecting lines between particles */
                },
                "move": {
                    "enable": true,
                    "speed": 2, /* Moderate default speed */
                    "direction": "none",
                    "random": true,
                    "straight": false,
                    "out_mode": "bounce", /* Bounce off edges */
                    "bounce": true,
                    "attract": {
                        "enable": false /* No attraction between particles */
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "bubble" /* Creates bubble effect when hovering */
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "repulse" /* Repels particles on click */
                    },
                    "resize": true
                },
                "modes": {
                    "bubble": {
                        "distance": 200, /* Interaction distance */
                        "size": 12, /* Bubble size on hover */
                        "duration": 2,
                        "opacity": 0.8,
                        "speed": 3
                    },
                    "repulse": {
                        "distance": 200, /* Stronger repulsion */
                        "duration": 0.4
                    },
                    "push": {
                        "particles_nb": 4
                    },
                    "remove": {
                        "particles_nb": 2
                    }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>