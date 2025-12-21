<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// Prevent PHP warnings/notices from being printed into JSON responses
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Register shutdown handler to capture fatal errors and log them
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err) {
        $msg = date('c') . " SHUTDOWN_ERROR type={$err['type']} file={$err['file']} line={$err['line']} message=" . str_replace("\n", " ", $err['message']) . "\n";
        file_put_contents(__DIR__ . '/debug_allergy.log', $msg, FILE_APPEND);
    }
});

// Set timezone to UTC or your local timezone to ensure consistency
date_default_timezone_set('UTC'); // Change to your local timezone if needed, e.g., 'America/New_York'

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

// --- AUTH ---
if ($action == 'register') {
    $user = $input['username'];
    $pass = md5($input['password']);
    $name = $input['full_name'];
    $email = $input['email'];

    // Check if email column exists in users table
    $columns = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
    if ($columns->num_rows == 0) {
        // If email column doesn't exist, add it
        $conn->query("ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE");
    }

    $check = $conn->query("SELECT id FROM users WHERE username = '$user'");
    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username taken"]);
        exit;
    }

    // Check if email already exists
    $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user, $pass, $name, $email);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        echo json_encode(["status" => "success"]);
    }
    else echo json_encode(["status" => "error", "message" => $stmt->error]);
}

if ($action == 'login') {
    $user = $input['username'];
    $pass = md5($input['password']);

    file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " LOGIN_ATTEMPT user=" . json_encode($user) . "\n", FILE_APPEND);

    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE username=? AND password=?");
    if (!$stmt) {
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " LOGIN_PREPARE_FAILED=" . json_encode($conn->error) . "\n", FILE_APPEND);
        echo json_encode(["status" => "error", "message" => "Server error" ]);
        exit;
    }
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        // Clear any previous session data to ensure clean state
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }

        $_SESSION['user_id'] = $row['id'];
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " LOGIN_SUCCESS id=" . $row['id'] . "\n", FILE_APPEND);
        echo json_encode(["status" => "success"]);
    } else {
        // For debugging - check if user exists but password is wrong
        $checkUser = $conn->prepare("SELECT id FROM users WHERE username=?");
        if (!$checkUser) {
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " LOGIN_CHECKUSER_PREPARE_FAILED=" . json_encode($conn->error) . "\n", FILE_APPEND);
            echo json_encode(["status" => "error", "message" => "Server error" ]);
            exit;
        }
        $checkUser->bind_param("s", $user);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows > 0) {
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " LOGIN_INCORRECT_PW user=" . json_encode($user) . "\n", FILE_APPEND);
            echo json_encode(["status" => "error", "message" => "Incorrect password"]);
        } else {
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " LOGIN_INVALID_USER user=" . json_encode($user) . "\n", FILE_APPEND);
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        }
    }
}

if ($action == 'logout') { session_destroy(); echo json_encode(["status" => "success"]); }

if ($action == 'check_session') {
    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            echo json_encode(["status" => "logged_in", "user_id" => $row['id'], "full_name" => $row['full_name']]);
        } else {
            echo json_encode(["status" => "logged_out"]);
        }
    } else {
        echo json_encode(["status" => "logged_out"]);
    }
}

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

// Helper: get normalized product ingredients
function get_product_ingredients($conn) {
    $set = [];
    file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " GET_PROD_ING_START\n", FILE_APPEND);

    // Try local DB first (works if products table exists in same DB)
    $res = @$conn->query("SELECT ingredients FROM products WHERE ingredients IS NOT NULL AND ingredients != ''");
    if ($res !== false) {
        while($row = $res->fetch_assoc()) {
            $parts = preg_split('/[,;\/\\|]+/', $row['ingredients']);
            foreach($parts as $p) {
                $w = trim(strtolower($p));
                if($w !== '') $set[$w] = true;
            }
        }
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " GET_PROD_ING_DONE local_count=" . count($set) . "\n", FILE_APPEND);
        return array_keys($set);
    }

    // Local query failed (products table might not exist in this DB). Try fallback to food ordering system endpoint.
    file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " GET_PROD_ING_LOCAL_FAILED\n", FILE_APPEND);
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $url = $scheme . '://' . $host . '/food_ordering_system/admin_api.php?action=get_ingredients';

    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $body = @file_get_contents($url, false, $ctx);
    if ($body) {
        $data = json_decode($body, true);
        if (is_array($data)) {
            foreach($data as $p) {
                $w = trim(strtolower($p));
                if($w !== '') $set[$w] = true;
            }
        }
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " GET_PROD_ING_DONE remote_count=" . count($set) . "\n", FILE_APPEND);
    } else {
        // Log fallback failure for diagnostics
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " FALLBACK_FETCH_FAILED url={$url}\n", FILE_APPEND);
    }

    return array_keys($set);
}

function is_allowed_keyword($kw, $allowedList) {
    $lk = strtolower(trim($kw));
    if($lk === '') return false;
    foreach($allowedList as $a) {
        $a = strtolower($a);
        if($a === '') continue;
        if(strpos($a, $lk) !== false || strpos($lk, $a) !== false) return true;
    }
    return false;
}

// Add
if ($action == 'add_custom_allergy') {
    // Capture any unexpected output to ensure we always return valid JSON
    ob_start();

    if (!isset($_SESSION['user_id'])) { ob_end_clean(); echo json_encode(["status" => "error", "message" => "Session expired" ]); exit; }
    $uid = $_SESSION['user_id'];
    $name = $input['name'];
    $words = isset($input['keywords']) && is_array($input['keywords']) ? $input['keywords'] : [];

    // Debug log the incoming request to help diagnose failures
    file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " ADD_REQUEST uid={$uid} name=" . json_encode($name) . " keywords=" . json_encode($words) . "\n", FILE_APPEND);

    // TRACE: about to call get_product_ingredients
    file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " CALLING_GET_PRODUCT_ING\n", FILE_APPEND);
    // Validate keywords: must exist in product ingredients
    $validation_skipped = false;
    try {
        $allowedList = get_product_ingredients($conn);
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " ALLOWED_COUNT=" . count($allowedList) . "\n", FILE_APPEND);
    } catch (Throwable $e) {
        // Log the original exception and attempt HTTP fallback directly
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " GET_PROD_ING_EXCEPTION=" . json_encode([$e->getMessage()]) . "\n", FILE_APPEND);
        // Try fallback remote fetch directly
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $url = $scheme . '://' . $host . '/food_ordering_system/admin_api.php?action=get_ingredients';
        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body) {
            $data = json_decode($body, true);
            if (is_array($data)) {
                $allowedList = array_map('strtolower', array_map('trim', $data));
            }
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " FALLBACK_REMOTE_COUNT=" . count($allowedList) . "\n", FILE_APPEND);
        } else {
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " FALLBACK_FETCH_FAILED url={$url}\n", FILE_APPEND);
            // If no ingredient source is available, skip strict validation to avoid blocking users
            $allowedList = [];
            $validation_skipped = true;
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " VALIDATION_SKIPPED\n", FILE_APPEND);
        }
    }

    if(count($allowedList) === 0 && !$validation_skipped) {
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status"=>"error", "message"=>"No product ingredients found; cannot create custom categories.", "debug_output" => $extra ]);
        exit;
    }
    $invalid = [];
    if (!$validation_skipped) {
        foreach($words as $w) {
            if(!is_allowed_keyword($w, $allowedList)) $invalid[] = $w;
        }
        if(count($invalid) > 0) {
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " INVALID=" . json_encode($invalid) . "\n", FILE_APPEND);
            $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
            echo json_encode(["status"=>"error", "message"=>"Invalid ingredient(s): " . implode(', ', $invalid), "debug_output" => $extra ]);
            exit;
        }
    }

    // If validation was skipped due to missing ingredient source, add a warning but continue
    $warning = null;
    if ($validation_skipped) {
        $warning = 'Ingredient validation skipped — ingredient source unavailable. Category saved without strict validation.';
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " WARNING_VALIDATION_SKIPPED\n", FILE_APPEND);
    }

    $stmt = $conn->prepare("INSERT INTO custom_allergens (user_id, name) VALUES (?, ?)");
    if(!$stmt) {
        $err = $conn->error;
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " PREPARE_INSERT_FAILED=" . json_encode($err) . "\n", FILE_APPEND);
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status"=>"error", "message"=>"Prepare failed: " . $err, "debug_output" => $extra]);
        exit;
    }
    if(!$stmt->bind_param("is", $uid, $name)) {
        $err = $stmt->error;
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " BIND_INSERT_FAILED=" . json_encode($err) . "\n", FILE_APPEND);
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status"=>"error", "message"=>"Bind failed: " . $err, "debug_output" => $extra]);
        exit;
    }
    if($stmt->execute()) {
        $cid = $conn->insert_id;
        $kStmt = $conn->prepare("INSERT INTO custom_keywords (custom_allergen_id, word) VALUES (?, ?)");
        if(!$kStmt) {
            $err = $conn->error;
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " PREPARE_KEYWORD_FAILED=" . json_encode($err) . "\n", FILE_APPEND);
            $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
            echo json_encode(["status"=>"error", "message"=>"Prepare keyword failed: " . $err, "debug_output" => $extra]);
            exit;
        }
        foreach($words as $w) {
            if(!$kStmt->bind_param("is", $cid, $w)) {
                $err = $kStmt->error;
                file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " BIND_KEYWORD_FAILED=" . json_encode([$w,$err]) . "\n", FILE_APPEND);
                $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
                echo json_encode(["status"=>"error", "message"=>"Bind keyword failed: " . $err, "debug_output" => $extra]);
                exit;
            }
            if(!$kStmt->execute()) {
                // return keyword insert error
                file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " KEYWORD_INSERT_ERROR=" . json_encode([$w, $kStmt->error]) . "\n", FILE_APPEND);
                $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
                echo json_encode(["status" => "error", "message" => "Keyword insert failed: " . $kStmt->error, "debug_output" => $extra]);
                exit;
            }
        }
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " ADD_SUCCESS cid={$cid}\n", FILE_APPEND);
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        $response = ["status" => "success", "debug_output" => $extra];
        echo json_encode($response);
    } else {
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " INSERT_ERROR=" . json_encode($stmt->error) . "\n", FILE_APPEND);
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error, "debug_output" => $extra]);
    }
}

// Update (The Logic to Prevent Duplication)
if ($action == 'update_custom_allergy') {
    // Capture any unexpected output
    ob_start();

    if (!isset($_SESSION['user_id'])) { ob_end_clean(); echo json_encode(["status" => "error", "message" => "Session expired" ]); exit; }
    $id = $input['id'];
    $name = $input['name'];
    $words = isset($input['keywords']) && is_array($input['keywords']) ? $input['keywords'] : [];

    // Debug logging
    file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " UPDATE_REQUEST id={$id} name=" . json_encode($name) . " keywords=" . json_encode($words) . "\n", FILE_APPEND);

    // Validate keywords: must exist in product ingredients, with graceful fallback
    $validation_skipped = false;
    try {
        $allowedList = get_product_ingredients($conn);
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " ALLOWED_COUNT=" . count($allowedList) . "\n", FILE_APPEND);
    } catch (Throwable $e) {
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " GET_PROD_ING_EXCEPTION=" . json_encode([$e->getMessage()]) . "\n", FILE_APPEND);
        // fallback remote fetch
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $url = $scheme . '://' . $host . '/food_ordering_system/admin_api.php?action=get_ingredients';
        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body) {
            $data = json_decode($body, true);
            if (is_array($data)) {
                $allowedList = array_map('strtolower', array_map('trim', $data));
            }
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " FALLBACK_REMOTE_COUNT=" . count($allowedList) . "\n", FILE_APPEND);
        } else {
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " FALLBACK_FETCH_FAILED url={$url}\n", FILE_APPEND);
            $allowedList = [];
            $validation_skipped = true;
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " VALIDATION_SKIPPED\n", FILE_APPEND);
        }
    }

    if(count($allowedList) === 0 && !$validation_skipped) {
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status"=>"error", "message"=>"No product ingredients found; cannot update custom categories.", "debug_output" => $extra ]);
        exit;
    }
    $invalid = [];
    foreach($words as $w) {
        if(!$validation_skipped && !is_allowed_keyword($w, $allowedList)) $invalid[] = $w;
    }
    if(count($invalid) > 0) {
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " INVALID=" . json_encode($invalid) . "\n", FILE_APPEND);
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status"=>"error", "message"=>"Invalid ingredient(s): " . implode(', ', $invalid), "debug_output" => $extra ]);
        exit;
    }

    $warning = null;
    if ($validation_skipped) {
        $warning = 'Ingredient validation skipped — ingredient source unavailable. Category saved without strict validation.';
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " WARNING_VALIDATION_SKIPPED\n", FILE_APPEND);
    }

    $stmt = $conn->prepare("UPDATE custom_allergens SET name=? WHERE id=?");
    if(!$stmt) {
        $err = $conn->error;
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " PREPARE_UPDATE_FAILED=" . json_encode($err) . "\n", FILE_APPEND);
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status"=>"error", "message"=>"Prepare failed: " . $err, "debug_output" => $extra]);
        exit;
    }
    if(!$stmt->bind_param("si", $name, $id)) {
        $err = $stmt->error;
        file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " BIND_UPDATE_FAILED=" . json_encode($err) . "\n", FILE_APPEND);
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status"=>"error", "message"=>"Bind failed: " . $err, "debug_output" => $extra]);
        exit;
    }
    
    if($stmt->execute()) {
        // Delete old keywords
        $conn->query("DELETE FROM custom_keywords WHERE custom_allergen_id=$id");
        // Insert new ones
        $kStmt = $conn->prepare("INSERT INTO custom_keywords (custom_allergen_id, word) VALUES (?, ?)");
        if(!$kStmt) {
            $err = $conn->error;
            file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " PREPARE_KEYWORD_FAILED=" . json_encode($err) . "\n", FILE_APPEND);
            $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
            echo json_encode(["status"=>"error", "message"=>"Prepare keyword failed: " . $err, "debug_output" => $extra]);
            exit;
        }
        foreach($words as $w) {
            if(!$kStmt->bind_param("is", $id, $w)) {
                $err = $kStmt->error;
                file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " BIND_KEYWORD_FAILED=" . json_encode([$w,$err]) . "\n", FILE_APPEND);
                $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
                echo json_encode(["status"=>"error", "message"=>"Bind keyword failed: " . $err, "debug_output" => $extra]);
                exit;
            }
            if(!$kStmt->execute()) {
                echo json_encode(["status" => "error", "message" => "Keyword insert failed: " . $kStmt->error]);
                exit;
            }
        }
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        $resp = ["status" => "success", "debug_output" => $extra];
        echo json_encode($resp);
    } else {
        $extra = trim(ob_get_clean()); if($extra) file_put_contents(__DIR__ . '/debug_allergy.log', date('c') . " EXTRA_OUTPUT=" . substr($extra,0,1000) . "\n", FILE_APPEND);
        echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error, "debug_output" => $extra]);
    }
}

// Delete
if ($action == 'delete_custom_allergy') {
    if (!isset($_SESSION['user_id'])) { echo json_encode(["status" => "error", "message" => "Session expired" ]); exit; }
    $id = $input['id'];
    $conn->query("DELETE FROM custom_allergens WHERE id=$id");
    echo json_encode(["status" => "success"]);
}

// --- DEBUG: return recent debug log (safe for local dev only) ---
if ($action == 'get_debug_log') {
    $path = __DIR__ . '/debug_allergy.log';
    if (!file_exists($path)) { echo json_encode(["status"=>"error","message"=>"No debug log found"]); exit; }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $last = array_slice($lines, -200);
    echo json_encode(["status"=>"success","lines"=>array_values($last)]);
    exit;
}

// --- PASSWORD RECOVERY ---
if ($action == 'recover_password') {
    $email = $input['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit;
    }

    // Check if email column exists in users table
    $columns = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
    if ($columns->num_rows == 0) {
        // If email column doesn't exist, add it
        $conn->query("ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE");
    }

    // Also add reset_token and reset_token_expiry columns if they don't exist
    $resetTokenColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
    if ($resetTokenColumn->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255)");
    }

    $resetExpiryColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_token_expiry'");
    if ($resetExpiryColumn->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN reset_token_expiry DATETIME");
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
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour based on server time

        // Save the reset token to the database
        $updateStmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $resetToken, $expiry, $userId);

        if ($updateStmt->execute()) {
            // Send password reset email using PHPMailer
            require_once 'config.php';
            require_once '../PHPMailer-master/src/PHPMailer.php';
            require_once '../PHPMailer-master/src/SMTP.php';
            require_once '../PHPMailer-master/src/Exception.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer();

            // Gmail SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = EMAIL_HOST;
            $mail->SMTPAuth   = EMAIL_SMTP_AUTH;
            $mail->Username   = EMAIL_USERNAME;
            $mail->Password   = EMAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = EMAIL_PORT;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients and content
            $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
            $mail->addAddress($email, $fullName);
            $mail->addReplyTo(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - AllergyPass';

            // Using relative URL for the reset link (works for local development)
            $resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['REQUEST_URI']) . "/reset_password.html?token=" . $resetToken;

            $mail->Body = "
                <html>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Hello <strong>$fullName</strong>,</p>
                    <p>You have requested to reset your password. Please click the link below to reset your password:</p>
                    <p><a href='$resetLink'>Reset Your Password</a></p>
                    <p><em>This link will expire in 1 hour.</em></p>
                    <p>If you did not request this, please ignore this email.</p>
                    <br>
                    <p>Best regards,<br>The AllergyPass Team</p>
                </body>
                </html>
            ";

            if($mail->send()) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Password reset link has been sent to your email address."
                ]);
            } else {
                // If email sending fails, clear the reset token
                $conn->query("UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE id = $userId");

                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to send email. Error: " . $mail->ErrorInfo
                ]);
            }
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

// --- RESET PASSWORD ---
if ($action == 'reset_password') {
    $token = $input['token'];
    $newPassword = $input['new_password']; // Don't hash here, do it after validation

    if (empty($token) || empty($newPassword)) {
        echo json_encode(["status" => "error", "message" => "Token and new password are required"]);
        exit;
    }

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT id, password, reset_token_expiry FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $userId = $row['id'];

        // Verify that the new password meets minimum requirements
        if (strlen($newPassword) < 6) {
            echo json_encode([
                "status" => "error",
                "message" => "Password must be at least 6 characters long."
            ]);
            exit;
        }

        $hashedNewPassword = md5($newPassword); // Hash the password here

        // Begin transaction to ensure atomic update
        $conn->begin_transaction();

        try {
            // Update the password and clear the reset token in a single atomic operation
            $updateStmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
            $updateStmt->bind_param("si", $hashedNewPassword, $userId);

            if ($updateStmt->execute() && $updateStmt->affected_rows > 0) {
                // Commit the transaction
                $conn->commit();

                // Double-check that the password was actually updated by fetching the user record again
                // Using a new query to ensure we get fresh data from the database
                $verifyStmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $verifyStmt->bind_param("i", $userId);
                $verifyStmt->execute();
                $verifyResult = $verifyStmt->get_result();
                $verifyRow = $verifyResult->fetch_assoc();

                if ($verifyRow && $verifyRow['password'] === $hashedNewPassword) {
                    // Clear any existing sessions for this user to force re-authentication
                    // This is important to ensure the user can't use old session data
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    session_destroy(); // Destroy current session to ensure clean state

                    // Start a new session for the response
                    session_start();

                    echo json_encode([
                        "status" => "success",
                        "message" => "Password has been reset successfully."
                    ]);
                } else {
                    // The password was not correctly updated in the database
                    echo json_encode([
                        "status" => "error",
                        "message" => "Password reset failed - verification check failed. Please try again."
                    ]);
                }
            } else {
                // Rollback the transaction on failure
                $conn->rollback();
                echo json_encode([
                    "status" => "error",
                    "message" => "Password reset failed - no rows were updated. Please try again."
                ]);
            }
        } catch (Exception $e) {
            // Rollback on any exception
            $conn->rollback();
            echo json_encode([
                "status" => "error",
                "message" => "Password reset failed due to a database error. Please try again."
            ]);
        }
    } else {
        // Check if token exists but is expired
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry <= NOW()");
        $checkStmt->bind_param("s", $token);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "This password reset link has expired. Please request a new one."
            ]);
        } else {
            // Token doesn't exist
            echo json_encode([
                "status" => "error",
                "message" => "Invalid password reset link. Please request a new one."
            ]);
        }
    }
}

// --- VERIFY TOKEN ---
if ($action == 'verify_token') {
    $token = $input['token'];

    if (empty($token)) {
        echo json_encode(["status" => "error", "message" => "Token is required"]);
        exit;
    }

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT id, reset_token_expiry FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // Token is valid
        echo json_encode([
            "status" => "success",
            "message" => "Token is valid"
        ]);
    } else {
        // Check if token exists but is expired
        $checkStmt = $conn->prepare("SELECT id, reset_token_expiry FROM users WHERE reset_token = ? AND reset_token_expiry <= NOW()");
        $checkStmt->bind_param("s", $token);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "This password reset link has expired. Please request a new one."
            ]);
        } else {
            // Check if token exists at all (to differentiate between expired and invalid)
            $anyStmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ?");
            $anyStmt->bind_param("s", $token);
            $anyStmt->execute();
            $anyResult = $anyStmt->get_result();

            if ($anyResult->num_rows > 0) {
                // Token exists but is expired (this case should have been caught above, but just in case)
                echo json_encode([
                    "status" => "error",
                    "message" => "This password reset link has expired. Please request a new one."
                ]);
            } else {
                // Token doesn't exist
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid password reset link. Please request a new one."
                ]);
            }
        }
    }
}
?>