<?php session_start(); include 'db_connect.php'; // Include database connection 

$message = ""; 

// Handle login form submission 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; 
    
    // Check if the credentials match a company user 
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND role = 'company'";
    $result = $conn->query($sql); 
    
    if ($result->num_rows > 0) {
        // Fetch user details and set session 
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; 
        
        // Redirect to company dashboard 
        header('Location: company_dashboard.php');
        exit();
    } else {
        $message = "Invalid username, password, or role!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Login</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #0056b3;
            --primary-dark: #01579b;
            --accent: #0288d1;
            --light: #e3f2fd;
            --text-dark: #333;
            --text-light: #fff;
            --shadow: rgba(0, 86, 179, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            height: 100vh;
            overflow: hidden;
            background-color: #ffffff;
            position: relative;
        }
        
        #backgroundCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .login-container {
            position: relative;
            width: 100%;
            max-width: 420px;
            z-index: 2;
            padding: 0 20px;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 24px;
        }
        
        .input-group label {
            position: absolute;
            top: -10px;
            left: 10px;
            padding: 0 5px;
            background-color: white;
            font-size: 14px;
            font-weight: 500;
            color: var(--primary-dark);
            transition: all 0.3s ease;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(2, 136, 209, 0.3);
            border-radius: 8px;
            font-size: 16px;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }
        
        .input-group input:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.2);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, var(--accent), var(--primary));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(2, 136, 209, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        button:hover {
            background: linear-gradient(to right, var(--primary), var(--accent));
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(2, 136, 209, 0.5);
        }
        
        button:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(2, 136, 209, 0.4);
        }
        
        .message {
            text-align: center;
            color: #e53935;
            font-size: 15px;
            margin-top: 20px;
            padding: 10px 15px;
            background: rgba(229, 57, 53, 0.1);
            border-radius: 6px;
            display: <?php echo !empty($message) ? 'block' : 'none'; ?>;
        }
        
        .glow {
            filter: drop-shadow(0 0 15px rgba(2, 136, 209, 0.7));
        }
    </style>
</head>
<body class="flex justify-center items-center">
    <canvas id="backgroundCanvas"></canvas>
    
    <div class="login-container">
        <div class="bg-white bg-opacity-90 backdrop-filter backdrop-blur-md rounded-xl p-8 shadow-2xl transform transition-all duration-500 hover:shadow-3xl relative z-10">
            <div class="flex justify-center mb-6">
                <div class="glow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-16 h-16 fill-current text-blue-600">
                        <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-2xl font-bold text-center text-blue-800 mb-6 relative">
                Company Login
                <span class="block w-16 h-1 bg-blue-500 rounded-full mx-auto mt-2"></span>
            </h1>
            
            <form method="post" id="loginForm" class="space-y-6">
                <div class="input-group shadow-sm">
                    <label for="username" class="text-blue-800">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="username" class="focus:ring-2 focus:ring-blue-400">
                </div>
                
                <div class="input-group shadow-sm">
                    <label for="password" class="text-blue-800">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" class="focus:ring-2 focus:ring-blue-400">
                </div>
                
                <button type="submit" class="group">
                    <span class="relative z-10">Login</span>
                    <span class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </button>
            </form>
            
            <div class="message mt-4 hidden">
                Invalid username, password, or role!
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Three.js - Polygon Mesh Background
            let scene, camera, renderer;
            let polygonMesh, mouse = { x: 0, y: 0 };
            
            function initScene() {
                // Create scene
                scene = new THREE.Scene();
                
                // Create camera
                const aspectRatio = window.innerWidth / window.innerHeight;
                camera = new THREE.PerspectiveCamera(75, aspectRatio, 0.1, 1000);
                camera.position.z = 20;
                
                // Create renderer
                renderer = new THREE.WebGLRenderer({ 
                    canvas: document.getElementById('backgroundCanvas'),
                    antialias: true,
                    alpha: true
                });
                renderer.setSize(window.innerWidth, window.innerHeight);
                renderer.setPixelRatio(window.devicePixelRatio);
                
                // Create polygon mesh
                createPolygonMesh();
                
                // Add lights
                const ambientLight = new THREE.AmbientLight(0x404040, 1);
                scene.add(ambientLight);
                
                const directionalLight = new THREE.DirectionalLight(0x0288d1, 1);
                directionalLight.position.set(1, 1, 1);
                scene.add(directionalLight);
                
                const pointLight1 = new THREE.PointLight(0x0288d1, 2, 50);
                pointLight1.position.set(10, 5, 10);
                scene.add(pointLight1);
                
                const pointLight2 = new THREE.PointLight(0x01579b, 2, 50);
                pointLight2.position.set(-10, -5, 10);
                scene.add(pointLight2);
                
                // Handle mouse movement for parallax effect
                document.addEventListener('mousemove', onMouseMove);
                
                // Handle window resize
                window.addEventListener('resize', onWindowResize);
                
                // Start animation
                animate();
            }
            
            function createPolygonMesh() {
                // Create a low-poly sphere geometry
                const geometry = new THREE.IcosahedronGeometry(10, 2);
                
                // Create a material with shiny, glossy appearance
                const material = new THREE.MeshPhongMaterial({
                    color: 0xffffff,
                    shininess: 80,
                    transparent: true,
                    opacity: 0.9,
                    wireframe: true,
                    emissive: 0x0288d1,
                    emissiveIntensity: 0.2
                });
                
                // Create the mesh and add to scene
                polygonMesh = new THREE.Mesh(geometry, material);
                scene.add(polygonMesh);
                
                // Create the particles at the vertices
                const particlesGeometry = new THREE.BufferGeometry();
                const particlesCount = geometry.attributes.position.count;
                const particlesPositions = new Float32Array(particlesCount * 3);
                
                // Copy vertices positions to particles
                for (let i = 0; i < particlesCount * 3; i++) {
                    particlesPositions[i] = geometry.attributes.position.array[i];
                }
                
                particlesGeometry.setAttribute('position', new THREE.BufferAttribute(particlesPositions, 3));
                
                const particlesMaterial = new THREE.PointsMaterial({
                    color: 0x0288d1,
                    size: 0.2,
                    transparent: true,
                    opacity: 0.8,
                    sizeAttenuation: true
                });
                
                const particles = new THREE.Points(particlesGeometry, particlesMaterial);
                scene.add(particles);
                
                // Apply GSAP animation
                gsap.to(polygonMesh.rotation, {
                    x: Math.PI * 2,
                    y: Math.PI * 2,
                    duration: 60,
                    ease: "none",
                    repeat: -1
                });
                
                gsap.to(particles.rotation, {
                    x: Math.PI * 2,
                    y: Math.PI * 2,
                    duration: 60,
                    ease: "none",
                    repeat: -1
                });
                
                // Add pulse animation to the particles
                gsap.to(particlesMaterial, {
                    size: 0.4,
                    duration: 1.5,
                    ease: "power1.inOut",
                    repeat: -1,
                    yoyo: true
                });
                
                // Add breathing effect to the mesh
                gsap.to(polygonMesh.scale, {
                    x: 1.05,
                    y: 1.05,
                    z: 1.05,
                    duration: 2,
                    ease: "power1.inOut",
                    repeat: -1,
                    yoyo: true
                });
            }
            
            function onMouseMove(event) {
                // Calculate mouse position in normalized device coordinates
                // (-1 to +1) for both axes
                mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
                mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
                
                // Apply parallax effect to the mesh
                gsap.to(polygonMesh.rotation, {
                    x: polygonMesh.rotation.x + mouse.y * 0.02,
                    y: polygonMesh.rotation.y + mouse.x * 0.02,
                    duration: 1,
                    ease: "power2.out"
                });
                
                // Also apply parallax to the camera
                gsap.to(camera.position, {
                    x: mouse.x * 2,
                    y: mouse.y * 2,
                    duration: 1,
                    ease: "power2.out"
                });
            }
            
            function onWindowResize() {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            }
            
            function animate() {
                requestAnimationFrame(animate);
                renderer.render(scene, camera);
            }
            
            // Initialize the 3D scene
            initScene();
            
            // Form input focus effects
            const inputElements = document.querySelectorAll('.input-group input');
            
            inputElements.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.querySelector('label').style.color = 'var(--accent)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.querySelector('label').style.color = 'var(--primary-dark)';
                });
            });
            
            // Login form entrance animation
            gsap.from('.login-container', {
                y: 30,
                opacity: 0,
                duration: 1,
                ease: 'power3.out'
            });
            
            // Add ripple effect to button
            document.querySelector('button').addEventListener('click', function(e) {
                let x = e.clientX - e.target.getBoundingClientRect().left;
                let y = e.clientY - e.target.getBoundingClientRect().top;
                
                let ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.width = '1px';
                ripple.style.height = '1px';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.7)';
                ripple.style.transform = 'scale(0)';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                this.appendChild(ripple);
                
                gsap.to(ripple, {
                    scale: 100,
                    opacity: 0,
                    duration: 0.8,
                    ease: 'power1.out',
                    onComplete: function() {
                        ripple.remove();
                    }
                });
            });
        });
    </script>
</body>
</html>