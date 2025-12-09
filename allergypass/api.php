<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

// --- AUTH ---
if ($action == 'register') {
    $user = $input['username']; $pass = md5($input['password']); $name = $input['full_name'];
    $check = $conn->query("SELECT id FROM users WHERE username = '$user'");
    if ($check->num_rows > 0) { echo json_encode(["status" => "error", "message" => "Username taken"]); exit; }
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $pass, $name);
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

// Update (The Fix!)
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
?>