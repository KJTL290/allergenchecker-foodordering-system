<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

// --- AUTH ---
if ($action == 'register') {
    $user = $input['username']; $pass = md5($input['password']); $name = $input['full_name']; $email = $input['email'];
    $check = $conn->query("SELECT id FROM users WHERE username = '$user'");
    if ($check->num_rows > 0) { echo json_encode(["status" => "error", "message" => "Username taken"]); exit; }
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user, $pass, $name, $email);
    if ($stmt->execute()) { $_SESSION['user_id'] = $conn->insert_id; echo json_encode(["status" => "success"]); }
    else echo json_encode(["status" => "error"]);
}

if ($action == 'login') {
    $user = $input['username']; $pass = md5($input['password']);
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) { $_SESSION['user_id'] = $row['id']; echo json_encode(["status" => "success"]); }
    else echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}

if ($action == 'logout') { session_destroy(); echo json_encode(["status" => "success"]); }

if ($action == 'delete_account') {
    if (!isset($_SESSION['user_id'])) exit;
    $uid = $_SESSION['user_id'];
    $conn->query("DELETE FROM users WHERE id=$uid");
    session_destroy();
    echo json_encode(["status" => "success"]);
}

// --- DATA FETCHING ---
if ($action == 'get_profile') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(["status" => "error"]); exit; }
    $uid = $_SESSION['user_id'];

    // User Info
    $userRes = $conn->query("SELECT full_name FROM users WHERE id=$uid");
    $userRow = $userRes->fetch_assoc();

    // Common Allergies
    $algRes = $conn->query("SELECT allergy_name FROM user_allergies WHERE user_id=$uid");
    $allergies = [];
    while($row = $algRes->fetch_assoc()) $allergies[] = $row['allergy_name'];

    // Custom Allergies
    $custom = [];
    $custSql = "SELECT * FROM custom_allergens WHERE user_id=$uid";
    $custRes = $conn->query($custSql);
    while($row = $custRes->fetch_assoc()) {
        $cid = $row['id'];
        $row['keywords'] = [];
        $kwSql = "SELECT word FROM custom_keywords WHERE custom_allergen_id=$cid";
        $kwRes = $conn->query($kwSql);
        while($k = $kwRes->fetch_assoc()) $row['keywords'][] = $k['word'];
        $custom[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "name" => $userRow['full_name'],
        "allergies" => $allergies,
        "custom_allergies" => $custom
    ]);
}

if ($action == 'get_common_allergens') {
    $sql = "SELECT name, icon FROM common_allergens ORDER BY name ASC";
    $result = $conn->query($sql);
    $common = [];
    while($row = $result->fetch_assoc()) $common[] = $row;
    echo json_encode($common);
}

// --- SAVING ---
if ($action == 'save_profile') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(["status" => "error"]); exit; }
    $uid = $_SESSION['user_id'];
    $allergies = $input['allergies']; 

    $conn->query("DELETE FROM user_allergies WHERE user_id=$uid");
    if (!empty($allergies)) {
        $stmt = $conn->prepare("INSERT INTO user_allergies (user_id, allergy_name) VALUES (?, ?)");
        foreach ($allergies as $alg) {
            $stmt->bind_param("is", $uid, $alg);
            $stmt->execute();
        }
    }
    echo json_encode(["status" => "success"]);
}

// --- CUSTOM ALLERGY CRUD ---

// Add
if ($action == 'add_custom_allergy') {
    if (!isset($_SESSION['user_id'])) exit;
    $uid = $_SESSION['user_id'];
    $name = $input['name'];
    $words = $input['keywords'];

    $stmt = $conn->prepare("INSERT INTO custom_allergens (user_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $uid, $name);
    if($stmt->execute()) {
        $cid = $conn->insert_id;
        $kStmt = $conn->prepare("INSERT INTO custom_keywords (custom_allergen_id, word) VALUES (?, ?)");
        foreach($words as $w) {
            $kStmt->bind_param("is", $cid, $w);
            $kStmt->execute();
        }
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}

// Update (The Logic to Prevent Duplication)
if ($action == 'update_custom_allergy') {
    if (!isset($_SESSION['user_id'])) exit;
    $id = $input['id'];
    $name = $input['name'];
    $words = $input['keywords'];

    $stmt = $conn->prepare("UPDATE custom_allergens SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
    
    if($stmt->execute()) {
        // Delete old keywords
        $conn->query("DELETE FROM custom_keywords WHERE custom_allergen_id=$id");
        // Insert new ones
        $kStmt = $conn->prepare("INSERT INTO custom_keywords (custom_allergen_id, word) VALUES (?, ?)");
        foreach($words as $w) {
            $kStmt->bind_param("is", $id, $w);
            $kStmt->execute();
        }
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}

// Delete
if ($action == 'delete_custom_allergy') {
    if (!isset($_SESSION['user_id'])) exit;
    $id = $input['id'];
    $conn->query("DELETE FROM custom_allergens WHERE id=$id");
    echo json_encode(["status" => "success"]);
}

// --- PASSWORD RECOVERY ---
if ($action == 'recover_password') {
    $email = $input['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit;
    }

    // Find user by email
    $stmt = $conn->prepare("SELECT id, username, full_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $userId = $row['id'];
        $username = $row['username'];
        $fullName = $row['full_name'];

        // Generate a unique reset token
        $resetToken = bin2hex(random_bytes(32)); // 64 character hex string
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Save the reset token to the database
        $updateStmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $resetToken, $expiry, $userId);

        if ($updateStmt->execute()) {
            // In a real application, you would send an email with the reset link
            // For now, we'll simulate sending the email and return success
            // You would use PHPMailer or similar library to send actual emails

            // Example of how to send email (uncomment and configure in production):
            /*
            $to = $email;
            $subject = "Password Reset Request - AllergyPass";
            $resetLink = "https://yourdomain.com/reset_password.php?token=" . $resetToken;
            $message = "Hello $fullName,\n\nYou have requested to reset your password. Please click the link below to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nThe AllergyPass Team";
            $headers = "From: noreply@allergypass.com";

            if(mail($to, $subject, $message, $headers)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Password reset link has been sent to your email address."
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to send email. Please contact support."
                ]);
            }
            */

            // For demonstration purposes, we'll return success
            echo json_encode([
                "status" => "success",
                "message" => "Password reset link has been sent to your email address."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to generate reset token. Please try again."
            ]);
        }
    } else {
        // Don't reveal if email exists or not for security
        echo json_encode([
            "status" => "success",
            "message" => "If an account exists with this email, a password reset link has been sent."
        ]);
    }
    }
}

// --- RESET PASSWORD ---
if ($action == 'reset_password') {
    $token = $input['token'];
    $newPassword = md5($input['new_password']);

    if (empty($token) || empty($newPassword)) {
        echo json_encode(["status" => "error", "message" => "Token and new password are required"]);
        exit;
    }

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $userId = $row['id'];

        // Update the password and clear the reset token
        $updateStmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $updateStmt->bind_param("si", $newPassword, $userId);

        if ($updateStmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Password has been reset successfully."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to reset password. Please try again."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid or expired token."
        ]);
    }
}
?>