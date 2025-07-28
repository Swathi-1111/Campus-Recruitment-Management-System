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

// Fetch applications filtered by company username with student details
$sql = "SELECT applications.application_id, applications.job_id, applications.interview_date, applications.interview_location, 
               applications.status, jobs.job_title, 
               students.user_id, students.department, students.cgpa, students.graduated_year, students.register_number, students.mobile_number, students.resume_path
        FROM applications
        INNER JOIN jobs ON applications.job_id = jobs.job_id
        INNER JOIN students ON applications.user_id = students.user_id
        WHERE jobs.company_username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $company_username);
$stmt->execute();
$result = $stmt->get_result();

// Handle interview details submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_id = $_POST['application_id'];
    $interview_date = $_POST['interview_date'];
    $interview_location = $_POST['interview_location'];

    // Update the interview details in the applications table
    $update_sql = "UPDATE applications SET interview_date = ?, interview_location = ? WHERE application_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ssi", $interview_date, $interview_location, $application_id);

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
            $message = "Your interview for Job Application ID $application_id is scheduled on $interview_date at $interview_location.";
            $stmt_news->bind_param("is", $user_id, $message);
            $stmt_news->execute();

            $success_message = "Interview details updated successfully and notification sent to the student.";
        }
    } else {
        $error_message = "Error updating interview details. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Interviews</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a192f;
            color: #e6f1ff;
            margin: 0;
            padding: 0;
            position: relative;
        }
        
        /* Geometric decorations */
        body::before {
            content: '';
            position: fixed;
            top: 20px;
            left: 20px;
            width: 100px;
            height: 100px;
            border: 2px solid rgba(78, 204, 163, 0.1);
            border-radius: 10px;
            z-index: -1;
            transform: rotate(45deg);
        }
        
        body::after {
            content: '';
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 150px;
            height: 150px;
            border: 2px solid rgba(78, 204, 163, 0.1);
            border-radius: 50%;
            z-index: -1;
        }
        
        .container {
            width: 95%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        h1 {
            color: #4ecca3;
            margin: 0;
            font-size: 32px;
            font-weight: 600;
            position: relative;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: #4ecca3;
        }
        
        .back-btn {
            background-color: #4ecca3;
            color: #0a192f;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(78, 204, 163, 0.3);
        }
        
        .back-btn:hover {
            background-color: #3db28c;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(78, 204, 163, 0.4);
        }
        
        .back-btn:active {
            transform: translateY(-1px);
        }
        
        .card {
            background-color: #112240;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(2, 12, 27, 0.7);
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid #1d2d50;
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
        }
        
        th, td {
            padding: 16px;
            text-align: left;
        }
        
        th {
            background-color: #172a45;
            color: #4ecca3;
            font-weight: 500;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 2px solid #4ecca3;
        }
        
        th:first-child {
            border-top-left-radius: 8px;
        }
        
        th:last-child {
            border-top-right-radius: 8px;
        }
        
        tr {
            transition: background-color 0.3s;
        }
        
        tr:nth-child(even) {
            background-color: #112240;
        }
        
        tr:nth-child(odd) {
            background-color: #0e1c36;
        }
        
        tr:hover {
            background-color: #1d3b66;
        }
        
        td {
            border-bottom: 1px solid #1d2d50;
        }
        
        .form-container {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .form-container input {
            padding: 10px 12px;
            background-color: #0a192f;
            border: 1px solid #4ecca3;
            border-radius: 6px;
            font-size: 14px;
            color: #e6f1ff;
            transition: all 0.3s;
        }
        
        .form-container input:focus {
            outline: none;
            border-color: #4ecca3;
            box-shadow: 0 0 0 2px rgba(78, 204, 163, 0.2);
        }
        
        .form-container input::placeholder {
            color: #8892b0;
        }
        
        .form-container button {
            background-color: #4ecca3;
            color: #0a192f;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .form-container button:hover {
            background-color: #3db28c;
            transform: translateY(-2px);
        }
        
        .success-message {
            color: #4ecca3;
            background-color: rgba(78, 204, 163, 0.1);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #4ecca3;
            display: flex;
            align-items: center;
        }
        
        .success-message::before {
            content: '✓';
            font-size: 18px;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .error-message {
            color: #ff6b6b;
            background-color: rgba(255, 107, 107, 0.1);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #ff6b6b;
            display: flex;
            align-items: center;
        }
        
        .error-message::before {
            content: '!';
            font-size: 18px;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .resume-link {
            color: #4ecca3;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-block;
            padding: 6px 12px;
            border: 1px solid #4ecca3;
            border-radius: 4px;
        }
        
        .resume-link:hover {
            background-color: rgba(78, 204, 163, 0.1);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .back-btn {
                align-self: flex-start;
            }
            
            .form-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-container input,
            .form-container button {
                width: 100%;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            th, td {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Manage Interviews</h1>
            <a href="company_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <!-- Display success or error messages -->
        <?php
        if (isset($success_message)) {
            echo "<div class='success-message'>$success_message</div>";
        }
        if (isset($error_message)) {
            echo "<div class='error-message'>$error_message</div>";
        }
        ?>

        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>CGPA</th>
                            <th>Graduated Year</th>
                            <th>Register Number</th>
                            <th>Mobile Number</th>
                            <th>Resume</th>
                            <th>Status</th>
                            <th>Interview Date</th>
                            <th>Interview Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['application_id']}</td>";
                                echo "<td>{$row['job_title']}</td>";
                                echo "<td>{$row['department']}</td>";
                                echo "<td>{$row['cgpa']}</td>";
                                echo "<td>{$row['graduated_year']}</td>";
                                echo "<td>{$row['register_number']}</td>";
                                echo "<td>{$row['mobile_number']}</td>";
                                echo "<td><a href='{$row['resume_path']}' target='_blank' class='resume-link'>View Resume</a></td>";
                                echo "<td>{$row['status']}</td>";
                                echo "<td>{$row['interview_date']}</td>";
                                echo "<td>{$row['interview_location']}</td>";
                                echo "<td>
                                    <form method='POST' class='form-container'>
                                        <input type='hidden' name='application_id' value='{$row['application_id']}'>
                                        <input type='date' name='interview_date' required>
                                        <input type='text' name='interview_location' placeholder='Enter location' required>
                                        <button type='submit'>Update</button>
                                    </form>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12' style='text-align: center; padding: 20px;'>No applications available for scheduling interviews.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>