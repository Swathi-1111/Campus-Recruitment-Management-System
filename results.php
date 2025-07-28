<?php
session_start();
include 'db_connect.php'; // Include the database connection

// Check if the user is logged in as a company
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') {
    header('Location: index.php'); // Redirect to the login page if not logged in as a company
    exit();
}

if (!isset($_SESSION['username'])) {
    die("User details are missing from the session. Please log in again.");
}

$company_username = $_SESSION['username']; // Retrieve the logged-in company's username

// Fetch applications filtered by company username with student username
$sql = "SELECT applications.application_id, applications.job_id, applications.status, 
               jobs.job_title, students.user_id, students.cgpa, students.department, students.graduated_year, 
               students.register_number, students.mobile_number, users.username
        FROM applications
        INNER JOIN jobs ON applications.job_id = jobs.job_id
        INNER JOIN students ON applications.user_id = students.user_id
        INNER JOIN users ON students.user_id = users.id
        WHERE jobs.company_username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $company_username);
$stmt->execute();
$result = $stmt->get_result();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_id = $_POST['application_id'];
    $status = $_POST['status']; // 'Selected' or 'Rejected'

    // Update the status in the applications table
    $update_sql = "UPDATE applications SET status = ? WHERE application_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("si", $status, $application_id);

    if ($stmt_update->execute()) {
        // Fetch the student ID for the application
        $fetch_user_sql = "SELECT user_id FROM applications WHERE application_id = ?";
        $stmt_fetch_user = $conn->prepare($fetch_user_sql);
        $stmt_fetch_user->bind_param("i", $application_id);
        $stmt_fetch_user->execute();
        $user_result = $stmt_fetch_user->get_result();

        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $user_id = $user_row['user_id'];

            // Insert a notification into the student's news section
            $news_sql = "INSERT INTO news (user_id, message) VALUES (?, ?)";
            $stmt_news = $conn->prepare($news_sql);
            $message = "Your application for Job Application ID $application_id has been $status.";
            $stmt_news->bind_param("is", $user_id, $message);
            $stmt_news->execute();

            $success_message = "Status updated successfully as '$status' and notification sent to the student.";
        }
    } else {
        $error_message = "Error updating status. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Application Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #e8e8e8;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .container {
            width: 95%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            padding: 30px 0;
            position: relative;
        }
        
        h1 {
            color: #4db8ff;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(77, 184, 255, 0.3);
        }
        
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #4db8ff;
            color: #16213e;
            border: none;
            padding: 10px 15px;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 15px rgba(77, 184, 255, 0.4);
        }
        
        .back-btn:hover {
            background-color: #2196f3;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(77, 184, 255, 0.6);
        }
        
        .back-btn i {
            margin-right: 8px;
        }
        
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        .success-message {
            background-color: rgba(76, 175, 80, 0.2);
            border: 1px solid #4CAF50;
            color: #b9f6ca;
        }
        
        .error-message {
            background-color: rgba(244, 67, 54, 0.2);
            border: 1px solid #F44336;
            color: #ffcdd2;
        }
        
        .applications-container {
            padding: 20px 0;
        }
        
        .application-card {
            background: rgba(27, 38, 59, 0.8);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            margin-bottom: 25px;
            padding: 20px;
            position: relative;
            border: 1px solid rgba(77, 184, 255, 0.3);
            transition: box-shadow 0.3s ease;
        }
        
        .application-card:hover {
            box-shadow: 0 15px 40px rgba(77, 184, 255, 0.4);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .job-title {
            font-size: 1.4rem;
            color: #4db8ff;
            font-weight: bold;
        }
        
        .application-id {
            background-color: rgba(77, 184, 255, 0.2);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .card-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-group {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #a0a0a0;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .card-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffd54f;
            border: 1px solid #ffc107;
        }
        
        .status-selected {
            background-color: rgba(76, 175, 80, 0.2);
            color: #b9f6ca;
            border: 1px solid #4CAF50;
        }
        
        .status-rejected {
            background-color: rgba(244, 67, 54, 0.2);
            color: #ffcdd2;
            border: 1px solid #F44336;
        }
        
        .form-container {
            display: flex;
            align-items: center;
        }
        
        .form-container select {
            padding: 8px 15px;
            margin-right: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            color: #e8e8e8;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .form-container select:focus {
            border-color: #4db8ff;
            box-shadow: 0 0 10px rgba(77, 184, 255, 0.5);
        }
        
        .form-container button {
            background-color: #4db8ff;
            color: #16213e;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .form-container button:hover {
            background-color: #2196f3;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(77, 184, 255, 0.4);
        }
        
        .no-applications {
            text-align: center;
            padding: 50px 0;
            font-size: 1.2rem;
            color: #a0a0a0;
        }
        
        /* Icons for cards */
        .icon {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }
        
        .icon-briefcase {
            top: 20px;
            right: 20px;
            font-size: 3rem;
        }
        
        .icon-user {
            bottom: 20px;
            left: 20px;
            font-size: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .card-content {
                grid-template-columns: 1fr;
            }
            
            .card-footer {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="company_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1>Manage Application Results</h1>
        </div>

        <!-- Display success or error messages -->
        <?php
        if (isset($success_message)) {
            echo "<div class='message success-message'>$success_message</div>";
        }
        if (isset($error_message)) {
            echo "<div class='message error-message'>$error_message</div>";
        }
        ?>

        <div class="applications-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Determine status class
                    $status_class = 'status-pending';
                    if ($row['status'] == 'Selected') {
                        $status_class = 'status-selected';
                    } else if ($row['status'] == 'Rejected') {
                        $status_class = 'status-rejected';
                    }
                    
                    echo "<div class='application-card' data-id='{$row['application_id']}'>
                            <div class='icon icon-briefcase'><i class='fas fa-briefcase'></i></div>
                            <div class='icon icon-user'><i class='fas fa-user-graduate'></i></div>
                            
                            <div class='card-header'>
                                <div class='job-title'>{$row['job_title']}</div>
                                <div class='application-id'>App ID: {$row['application_id']}</div>
                            </div>
                            
                            <div class='card-content'>
                                <div class='info-group'>
                                    <span class='info-label'>Username</span>
                                    <span class='info-value'>{$row['username']}</span>
                                </div>
                                
                                <div class='info-group'>
                                    <span class='info-label'>Department</span>
                                    <span class='info-value'>{$row['department']}</span>
                                </div>
                                
                                <div class='info-group'>
                                    <span class='info-label'>CGPA</span>
                                    <span class='info-value'>{$row['cgpa']}</span>
                                </div>
                                
                                <div class='info-group'>
                                    <span class='info-label'>Graduation Year</span>
                                    <span class='info-value'>{$row['graduated_year']}</span>
                                </div>
                                
                                <div class='info-group'>
                                    <span class='info-label'>Register Number</span>
                                    <span class='info-value'>{$row['register_number']}</span>
                                </div>
                                
                                <div class='info-group'>
                                    <span class='info-label'>Mobile Number</span>
                                    <span class='info-value'>{$row['mobile_number']}</span>
                                </div>
                            </div>
                            
                            <div class='card-footer'>
                                <div class='status {$status_class}'>{$row['status']}</div>
                                <form method='POST' class='form-container'>
                                    <input type='hidden' name='application_id' value='{$row['application_id']}'>
                                    <select name='status' required style='color: #000000;'>
                                        <option value='' style='color: #000000;'>Update Status</option>
                                        <option value='Selected' style='color: #000000;'>Selected</option>
                                        <option value='Rejected' style='color: #000000;'>Rejected</option>
                                    </select>
                                    <button type='submit'>Update</button>
                                </form>
                            </div>
                        </div>";
                }
            } else {
                echo "<div class='no-applications'>
                        <i class='fas fa-folder-open' style='font-size: 3rem; margin-bottom: 20px;'></i>
                        <p>No applications available for managing results.</p>
                      </div>";
            }
            ?>
        </div>
    </div>
</body>
</html>