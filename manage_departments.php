<?php
// Start session at the very beginning of the file, before any output
session_start();
include 'db_connect.php'; // Include database connection

// Initialize message variable
$message = '';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Handle form submission for adding a department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_department'])) {
    $department_name = mysqli_real_escape_string($conn, $_POST['department_name']);
    $sql = "INSERT INTO departments (department_name) VALUES ('$department_name')";
    if ($conn->query($sql) === TRUE) {
        $message = "Department added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle deletion of a department
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql = "DELETE FROM departments WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        $message = "Department deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle updating a department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_department'])) {
    $update_id = (int)$_POST['update_id'];
    $updated_name = mysqli_real_escape_string($conn, $_POST['updated_name']);
    $sql = "UPDATE departments SET department_name = '$updated_name' WHERE id = $update_id";
    if ($conn->query($sql) === TRUE) {
        $message = "Department updated successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            color: #ffffff;
            background: linear-gradient(135deg, #1a3a6c, #2c5282);
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 0;
            position: relative;
            z-index: 1;
        }
        
        h1 {
            color: #ffffff;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 300;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.3);
            animation: fadeInDown 0.8s ease-out;
        }
        
        h2 {
            color: #ffffff;
            font-size: 1.8rem;
            margin: 40px 0 20px;
            font-weight: 300;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .card {
            background: rgba(30, 64, 124, 0.7);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            animation: fadeIn 1s ease-out;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.4);
        }
        
        form {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        label {
            font-size: 0.9rem;
            color: #ffffff;
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            border-color: #ffffff;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }
        
        button {
            background: linear-gradient(135deg, #4a90e2, #3176d3);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(49, 118, 211, 0.3);
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(49, 118, 211, 0.4);
        }
        
        button:active {
            transform: translateY(1px);
        }
        
        button:after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        button:hover:after {
            left: 100%;
        }
        
        .message {
            color: #4ade80;
            font-size: 1rem;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: rgba(74, 222, 128, 0.1);
            border-radius: 8px;
            border-left: 4px solid #4ade80;
            animation: fadeIn 0.5s ease-out;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
            overflow: hidden;
        }
        
        th, td {
            padding: 16px;
            text-align: left;
        }
        
        th {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-weight: 500;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        tr:nth-child(odd) td {
            background: rgba(255, 255, 255, 0.05);
        }
        
        tr:nth-child(even) td {
            background: rgba(255, 255, 255, 0.02);
        }
        
        tr td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        tr:hover td {
            background: rgba(255, 255, 255, 0.1);
        }
        
        td:first-child, th:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        
        td:last-child, th:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        .action-buttons a {
            text-decoration: none;
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            margin: 0 5px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .delete-btn {
            background: rgba(239, 68, 68, 0.1);
            color: #ff9999;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .delete-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            transform: translateY(-2px);
        }
        
        .edit-btn {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .edit-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .nav-link {
            display: inline-block;
            margin: 10px 0;
            color: #ffffff;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .nav-link:hover {
            color: #d4e6ff;
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        #edit-form {
            margin-top: 30px;
            transform: scale(0.98);
            opacity: 0;
            transition: all 0.4s ease;
        }
        
        #edit-form.active {
            transform: scale(1);
            opacity: 1;
        }
        
        /* Animation keyframes */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Enhanced interaction effects */
        .pulse-effect {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    
    <div class="container">
        <h1>Manage Departments</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <label>Department Name:</label>
                <input type="text" name="department_name" required placeholder="Enter department name...">
                <button type="submit" name="add_department">Add Department</button>
            </form>
        </div>
        
        <h2>Existing Departments</h2>
        <div class="card">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Department Name</th>
                    <th>Actions</th>
                </tr>
                <?php
                $sql = "SELECT * FROM departments";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['department_name']) . "</td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='manage_departments.php?delete_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this department?\")'>Delete</a>";
                        echo "<a href='javascript:void(0)' onclick='showEditForm(" . $row['id'] . ", \"" . htmlspecialchars($row['department_name'], ENT_QUOTES) . "\")' class='edit-btn'>Edit</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center'>No departments found</td></tr>";
                }
                ?>
            </table>
        </div>
        
        <!-- Edit Form -->
        <div id="edit-form" class="card" style="display:none">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="update_id" id="update_id">
                <label>Updated Department Name:</label>
                <input type="text" name="updated_name" id="updated_name" required>
                <button type="submit" name="update_department">Update Department</button>
            </form>
        </div>
        
        <div style="position: absolute; top: 20px; left: 20px;">
    <a href="admin_dashboard.php" class="nav-link">← Back to Dashboard</a>
</div>
    </div>

    <!-- Particle.js for the interactive background -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Initialize particles.js
        document.addEventListener('DOMContentLoaded', function() {
            // Particles.js configuration for white particles on blue background
            particlesJS('particles-js', {
                "particles": {
                    "number": {
                        "value": 100,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#ffffff"
                    },
                    "shape": {
                        "type": "circle",
                        "stroke": {
                            "width": 0,
                            "color": "#000000"
                        },
                        "polygon": {
                            "nb_sides": 5
                        }
                    },
                    "opacity": {
                        "value": 0.4,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 3,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 2,
                            "size_min": 0.1,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#ffffff",
                        "opacity": 0.2,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 1,
                        "direction": "none",
                        "random": true,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
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
                            "distance": 140,
                            "line_linked": {
                                "opacity": 0.5
                            }
                        },
                        "bubble": {
                            "distance": 400,
                            "size": 4,
                            "duration": 2,
                            "opacity": 0.8,
                            "speed": 3
                        },
                        "repulse": {
                            "distance": 100,
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
            
            // Add animation to table rows
            const tableRows = document.querySelectorAll('table tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                row.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100 + (index * 50));
            });
        });
        
        // Function to show edit form with animation
        function showEditForm(id, name) {
            const editForm = document.getElementById('edit-form');
            document.getElementById('update_id').value = id;
            document.getElementById('updated_name').value = name;
            
            // Display the form first
            editForm.style.display = 'block';
            
            // Trigger reflow
            void editForm.offsetWidth;
            
            // Add active class for animation
            editForm.classList.add('active');
            
            // Scroll to edit form
            editForm.scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        // Add button press effect
        const buttons = document.querySelectorAll('button');
        buttons.forEach(button => {
            button.addEventListener('mousedown', function() {
                this.style.transform = 'scale(0.95)';
            });
            
            button.addEventListener('mouseup', function() {
                this.style.transform = '';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
    </script>
</body>
</html>