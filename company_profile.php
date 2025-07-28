<?php
session_start();
include 'db_connect.php'; // Include the database connection

// Check if the user is logged in as a company
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') {
    header('Location: index.php'); // Redirect to login if not a company user
    exit();
}

// Get the logged-in company's user ID
$company_id = $_SESSION['user_id'];

// Fetch company details
$sql = "SELECT company_name, company_email AS email, address, phone_number AS phone FROM companies WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $company = $result->fetch_assoc();
} else {
    die("Company details not found.");
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    $update_sql = "UPDATE companies SET company_name = ?, company_email = ?, address = ?, phone_number = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssi", $company_name, $email, $address, $phone, $company_id);

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Profile</title>
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
            max-width: 1000px;
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
        
        .form-container {
            background-color: #112240;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(2, 12, 27, 0.7);
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid #1d2d50;
            width: 100%;
            max-width: 700px;
            margin: 0 auto 40px;
        }
        
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4ecca3;
            font-size: 16px;
        }
        
        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            background-color: #0a192f;
            border: 1px solid #4ecca3;
            border-radius: 6px;
            color: #e6f1ff;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-container input:focus,
        .form-container textarea:focus {
            outline: none;
            border-color: #4ecca3;
            box-shadow: 0 0 0 2px rgba(78, 204, 163, 0.2);
        }
        
        .form-container button {
            background-color: #4ecca3;
            color: #0a192f;
            border: none;
            padding: 14px 28px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin: 10px 0;
            width: 100%;
            box-shadow: 0 4px 15px rgba(78, 204, 163, 0.3);
        }
        
        .form-container button:hover {
            background-color: #3db28c;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(78, 204, 163, 0.4);
        }
        
        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .logout-btn {
            background-color: #ff6b6b;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        
        .logout-btn:hover {
            background-color: #ff5252;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(255, 107, 107, 0.4);
        }
        
        .logout-btn:active {
            transform: translateY(-1px);
        }
        
        .success-message {
            color: #4ecca3;
            background-color: rgba(78, 204, 163, 0.1);
            padding: 16px;
            border-radius: 8px;
            margin: 0 auto 25px;
            border-left: 4px solid #4ecca3;
            display: flex;
            align-items: center;
            max-width: 700px;
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
            margin: 0 auto 25px;
            border-left: 4px solid #ff6b6b;
            display: flex;
            align-items: center;
            max-width: 700px;
        }
        
        .error-message::before {
            content: '!';
            font-size: 18px;
            margin-right: 10px;
            font-weight: bold;
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
            
            .button-group {
                flex-direction: column;
                width: 100%;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .form-container input,
            .form-container textarea,
            .form-container button,
            .back-btn,
            .logout-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Company Profile</h1>
            <a href="company_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <!-- Display success or error message -->
        <?php
        if (isset($success_message)) {
            echo "<div class='success-message'>$success_message</div>";
        }
        if (isset($error_message)) {
            echo "<div class='error-message'>$error_message</div>";
        }
        ?>

        <!-- Profile Form -->
        <div class="form-container">
            <form method="POST" action="">
                <label for="company_name">Company Name</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($company['email']); ?>" required>

                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($company['address']); ?></textarea>

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($company['phone']); ?>" required>

                <button type="submit">Update Profile</button>
            </form>
        </div>

        <!-- Just logout button at the bottom -->
        <div class="button-group">
            <a href="index.php?logout=true" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>