<?php
session_start();
include 'db_connect.php'; // Include database connection

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header('Location: index.php'); // Redirect to the login page
    exit();
}

if (!isset($_SESSION['user_id'])) {
    die("User details are missing from the session. Please log in again.");
}

$user_id = $_SESSION['user_id']; // Get the student's user ID from the session

// Fetch application stats
$sql_applied = "SELECT COUNT(*) AS total_applied FROM applications WHERE user_id = ?";
$sql_selected = "SELECT COUNT(*) AS total_selected FROM applications WHERE user_id = ? AND status = 'Selected'";
$sql_rejected = "SELECT COUNT(*) AS total_rejected FROM applications WHERE user_id = ? AND status = 'Rejected'";

$stmt = $conn->prepare($sql_applied);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_applied = $stmt->get_result()->fetch_assoc();
$total_applied = $result_applied['total_applied'];

$stmt = $conn->prepare($sql_selected);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_selected = $stmt->get_result()->fetch_assoc();
$total_selected = $result_selected['total_selected'];

$stmt = $conn->prepare($sql_rejected);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_rejected = $stmt->get_result()->fetch_assoc();
$total_rejected = $result_rejected['total_rejected'];

// Fetch details of applied jobs, including company ID
$sql_details = "SELECT jobs.job_title, jobs.location, jobs.salary_min, jobs.salary_max, jobs.user_id AS company_id, applications.status
                FROM applications
                INNER JOIN jobs ON applications.job_id = jobs.job_id
                WHERE applications.user_id = ?";
$stmt = $conn->prepare($sql_details);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_details = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Jobs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            text-align: center;
            padding: 0;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }
        
        #particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 1;
            padding: 50px 20px;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            max-width: 95%;
            margin: 40px auto;
            box-shadow: 0 10px 25px rgba(0, 105, 217, 0.1);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        
        h1 {
            color: #0069d9;
            font-size: 2.5rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            display: inline-block;
            flex-grow: 1;
            text-align: center;
        }
        
        h1:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #0069d9, #00c6ff);
            border-radius: 2px;
        }
        
        .back-btn {
            background: linear-gradient(135deg, #00c6ff, #0069d9);
            color: white;
            border: none;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 30px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(2, 136, 209, 0.25);
            transition: all 0.3s ease;
            font-size: 0.95em;
        }
        
        .back-btn:before {
            content: "←";
            margin-right: 8px;
            font-size: 1.1em;
        }
        
        .back-btn:hover {
            background: linear-gradient(135deg, #00b4e6, #0057b8);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(2, 136, 209, 0.35);
        }
        
        .back-btn:active {
            transform: translateY(0px);
            box-shadow: 0 2px 4px rgba(2, 136, 209, 0.25);
        }
        
        .stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .stat {
            background: linear-gradient(135deg, #0069d9, #0288d1);
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            font-size: 18px;
            box-shadow: 0px 6px 12px rgba(0, 105, 217, 0.2);
            min-width: 160px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat:hover {
            transform: translateY(-3px);
            box-shadow: 0px 8px 16px rgba(0, 105, 217, 0.3);
        }
        
        .stat:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            border-radius: 10px;
        }
        
        .stat-label {
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 2em;
            font-weight: 700;
        }
        
        .table-container {
            overflow-x: auto;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 105, 217, 0.15);
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        
        th, td {
            padding: 16px 10px;
            text-align: left;
            border-bottom: 1px solid #e0e8f9;
        }
        
        th {
            background: linear-gradient(135deg, #0069d9, #0288d1);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }
        
        tr:nth-child(even) {
            background-color: #f8faff;
        }
        
        tr:hover {
            background-color: #f0f7ff;
            transition: background-color 0.3s ease;
        }
        
        td {
            color: #334;
            font-size: 0.95em;
        }
        
        .status-pending {
            color: #ff9800;
            font-weight: 600;
            background-color: rgba(255, 152, 0, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .status-selected {
            color: #4caf50;
            font-weight: 600;
            background-color: rgba(76, 175, 80, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .status-rejected {
            color: #f44336;
            font-weight: 600;
            background-color: rgba(244, 67, 54, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .no-jobs {
            margin: 50px auto;
            font-size: 18px;
            color: #777;
            padding: 30px;
            background-color: #f8faff;
            border-radius: 10px;
            width: 80%;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05);
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 30px 10px;
                margin: 20px auto;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .header-container {
                flex-direction: column-reverse;
                gap: 20px;
            }
            
            .back-btn {
                margin-bottom: 10px;
            }
            
            .stats {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .stat {
                width: 90%;
                min-width: unset;
                padding: 15px;
            }
            
            th, td {
                padding: 12px 8px;
                font-size: 0.85em;
            }
        }
    </style>
</head>
<body>
    <div id="particles-container"></div>
    
    <div class="content-wrapper">
        <div class="header-container">
            <a href="student_dashboard.php" class="back-btn">Back to Dashboard</a>
            <h1>My Jobs</h1>
            <div style="width: 150px;"></div> <!-- Spacer for centering the title -->
        </div>
        
        <!-- Job Application Stats -->
        <div class="stats">
            <div class="stat">
                <div class="stat-label">Applied</div>
                <div class="stat-value"><?php echo $total_applied; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Selected</div>
                <div class="stat-value"><?php echo $total_selected; ?></div>
            </div>
            <div class="stat">
                <div class="stat-label">Rejected</div>
                <div class="stat-value"><?php echo $total_rejected; ?></div>
            </div>
        </div>

        <!-- Details of Applied Jobs -->
        <div class="table-container">
            <table>
                <tr>
                    <th>Job Title</th>
                    <th>Company ID</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Status</th>
                </tr>
                <?php
                if ($result_details->num_rows > 0) {
                    while ($row = $result_details->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['job_title']}</td>";
                        echo "<td>{$row['company_id']}</td>";
                        echo "<td>{$row['location']}</td>";
                        echo "<td>₹{$row['salary_min']} - ₹{$row['salary_max']}</td>";
                        echo "<td><span class='status-" . strtolower($row['status']) . "'>{$row['status']}</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='no-jobs'>You have not applied for any jobs yet.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    
    <script>
        // Floating Particles JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles-container');
            const particleCount = 35; // Number of particles to create
            
            // Create particles
            for (let i = 0; i < particleCount; i++) {
                createParticle();
            }
            
            function createParticle() {
                const particle = document.createElement('div');
                
                // Random position
                const posX = Math.random() * window.innerWidth;
                const posY = Math.random() * window.innerHeight;
                
                // Random size between 5px and 20px
                const size = Math.random() * 15 + 5;
                
                // Random blue shade
                const hue = 210 + Math.random() * 30; // Blue hues
                const saturation = 50 + Math.random() * 50;
                const lightness = 50 + Math.random() * 30;
                const opacity = 0.2 + Math.random() * 0.4;
                
                // Random movement speed
                const speedX = (Math.random() - 0.5) * 0.5;
                const speedY = (Math.random() - 0.5) * 0.5;
                
                // Set particle styles
                particle.style.position = 'absolute';
                particle.style.left = `${posX}px`;
                particle.style.top = `${posY}px`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.borderRadius = '50%';
                particle.style.background = `hsla(${hue}, ${saturation}%, ${lightness}%, ${opacity})`;
                particle.style.boxShadow = `0 0 ${size/2}px rgba(0, 105, 217, 0.5)`;
                particle.style.zIndex = '-1';
                
                // Add the particle to the container
                particlesContainer.appendChild(particle);
                
                // Animate the particle
                let positionX = posX;
                let positionY = posY;
                
                function animate() {
                    // Update position
                    positionX += speedX;
                    positionY += speedY;
                    
                    // Wrap around edges
                    if (positionX < -size) positionX = window.innerWidth + size;
                    if (positionX > window.innerWidth + size) positionX = -size;
                    if (positionY < -size) positionY = window.innerHeight + size;
                    if (positionY > window.innerHeight + size) positionY = -size;
                    
                    // Apply position
                    particle.style.left = `${positionX}px`;
                    particle.style.top = `${positionY}px`;
                    
                    // Continue animation
                    requestAnimationFrame(animate);
                }
                
                animate();
            }
        });
    </script>
</body>
</html>