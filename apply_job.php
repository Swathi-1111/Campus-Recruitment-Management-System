<?php
session_start();
include 'db_connect.php'; // Include the database connection

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header('Location: index.php'); // Redirect to the login page
    exit();
}

if (!isset($_SESSION['user_id'])) {
    die("User details are missing from the session. Please log in again.");
}

$user_id = $_SESSION['user_id']; // Get the student's user ID from the session

// Fetch all open jobs with complete details from the jobs table
$sql = "SELECT jobs.job_id, jobs.job_title, jobs.company_name, jobs.job_type, jobs.location, 
               jobs.salary_min, jobs.salary_max, jobs.department, jobs.posted_date, jobs.job_description, 
               jobs.requirements, jobs.company_username, jobs.status, jobs.user_id AS company_id
        FROM jobs
        WHERE jobs.status = 'Open'"; // Fetch only jobs with 'Open' status
$result = $conn->query($sql);

// Handle the 'Apply' button click
if (isset($_GET['apply_job_id']) && isset($_GET['company_id'])) {
    $job_id = intval($_GET['apply_job_id']);
    $company_id = intval($_GET['company_id']); // Reference the company ID

    // Fetch all details of the selected job
    $job_sql = "SELECT job_id, company_name, company_username FROM jobs WHERE job_id = ?";
    $stmt_job = $conn->prepare($job_sql);
    $stmt_job->bind_param("i", $job_id);
    $stmt_job->execute();
    $job_result = $stmt_job->get_result();
    $job_details = $job_result->fetch_assoc();

    if ($job_details) {
        $company_name = $job_details['company_name'];
        $company_username = $job_details['company_username'];

        // Verify if the student has already applied for the job
        $check_sql = "SELECT * FROM applications WHERE user_id = ? AND job_id = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("ii", $user_id, $job_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows == 0) {
            // Insert the application into the 'applications' table
            $apply_sql = "INSERT INTO applications (job_id, user_id, company_id, company_username, status) VALUES (?, ?, ?, ?, 'Pending')";
            $stmt_apply = $conn->prepare($apply_sql);
            $stmt_apply->bind_param("iiis", $job_id, $user_id, $company_id, $company_username);

            if ($stmt_apply->execute()) {
                // Insert a notification into the 'news' table
                $news_sql = "INSERT INTO news (user_id, message) VALUES (?, ?)";
                $stmt_news = $conn->prepare($news_sql);
                $message = "You have successfully applied for Job ID $job_id.";
                $stmt_news->bind_param("is", $user_id, $message);

                if ($stmt_news->execute()) {
                    // Show success alert on the same page
                    echo "<script>alert('Applied successfully! Your application is now visible to the company.');</script>";
                } else {
                    echo "<script>alert('Error adding notification. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Error applying for the job. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('You have already applied for this job.');</script>";
        }
    } else {
        echo "<script>alert('Error: Job details not found.');</script>";
    }

    // Close the statements
    $stmt_job->close();
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Jobs</title>
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
        
        .apply-btn {
            background: linear-gradient(135deg, #0069d9, #0288d1);
            color: white;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(2, 136, 209, 0.25);
            transition: all 0.3s ease;
            font-size: 0.9em;
        }
        
        .apply-btn:hover {
            background: linear-gradient(135deg, #0288d1, #01579b);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(2, 136, 209, 0.35);
        }
        
        .apply-btn:active {
            transform: translateY(0px);
            box-shadow: 0 2px 4px rgba(2, 136, 209, 0.25);
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
            
            th, td {
                padding: 12px 8px;
                font-size: 0.85em;
            }
            
            .apply-btn {
                padding: 8px 12px;
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <div id="particles-container"></div>
    
    <div class="content-wrapper">
        <div class="header-container">
            <a href="student_dashboard.php" class="back-btn">Back to Dashboard</a>
            <h1>Available Jobs</h1>
            <div style="width: 150px;"></div> <!-- Spacer for centering the title -->
        </div>
        
        <div class="table-container">
            <table>
                <tr>
                    <th>Job Title</th>
                    <th>Company Name</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['job_title']}</td>";
                        echo "<td>{$row['company_name']}</td>";
                        echo "<td>{$row['location']}</td>";
                        echo "<td>₹{$row['salary_min']} - ₹{$row['salary_max']}</td>";
                        echo "<td><a href='apply_job.php?apply_job_id={$row['job_id']}&company_id={$row['company_id']}' class='apply-btn'>Apply</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='no-jobs'>No jobs available at the moment.</td></tr>";
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