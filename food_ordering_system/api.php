<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

function isDashUser() { return isset($_SESSION['admin_session']); }

// Operators: admin OR kitchen(ops)
function isOperator() { return isset($_SESSION['admin_session']) || isset($_SESSION['ops_session']); }

$action = $_GET['action'] ?? '';

if ($action == 'get_orders') {
    if(!isOperator()) { echo json_encode([]); exit; }
    
    // Only include active workflow statuses explicitly to avoid enum/empty-status issues
    $sql = "SELECT * FROM orders WHERE status IN ('unpaid','pending','preparing','ready') ORDER BY created_at ASC";
    $result = $conn->query($sql);
    $orders = [];
    while($row = $result->fetch_assoc()) {
        $oid = $row['id'];
        $items = [];
        $i_res = $conn->query("SELECT * FROM order_items WHERE order_id=$oid");
        while($i = $i_res->fetch_assoc()) {
            // Try to fetch product price (fallback to 0.00)
            $price = 0.00;
            $p_stmt = $conn->prepare("SELECT price FROM products WHERE name=? LIMIT 1");
            $p_stmt->bind_param("s", $i['product_name']);
            $p_stmt->execute();
            $p_res = $p_stmt->get_result();
            if($p_row = $p_res->fetch_assoc()) $price = floatval($p_row['price']);
            $items[] = ['name'=>$i['product_name'], 'qty'=>$i['quantity'], 'price'=>$price];
        }
        $row['items'] = $items;
        $orders[] = $row;
    }
    echo json_encode($orders);
}

if ($action == 'get_history') {
    if(!isDashUser()) { echo json_encode([]); exit; }
    
    $start = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
    $end = $_GET['end'] ?? date('Y-m-d');
    $status = $_GET['status'] ?? 'all';
    
    $where = "WHERE DATE(created_at) BETWEEN '$start' AND '$end'";
    if($status == 'completed') $where .= " AND status='completed'";
    elseif($status == 'cancelled') $where .= " AND status='cancelled'";
    else $where .= " AND status IN ('completed', 'cancelled')";
    
    $history = []; $rev = 0; $count = 0;
    $result = $conn->query("SELECT * FROM orders $where ORDER BY created_at DESC");
    while($row = $result->fetch_assoc()) {
        if($row['status'] == 'completed') $rev += $row['total_price'];
        $count++;
        
        $oid = $row['id'];
        $i_res = $conn->query("SELECT product_name, quantity FROM order_items WHERE order_id=$oid");
        $str = [];
        while($i = $i_res->fetch_assoc()) {
            // attempt to append price info
            $price = 0.00;
            $p_stmt = $conn->prepare("SELECT price FROM products WHERE name=? LIMIT 1");
            $p_stmt->bind_param("s", $i['product_name']);
            $p_stmt->execute();
            $p_res = $p_stmt->get_result();
            if($p_row = $p_res->fetch_assoc()) $price = floatval($p_row['price']);
            $str[] = $i['quantity']."x ".$i['product_name']." (₱".number_format($price,2).")";
        }
        $row['items_summary'] = implode(", ", $str);
        $history[] = $row;
    }
    echo json_encode(["stats"=>["total_revenue"=>$rev, "total_orders"=>$count], "history"=>$history]);
}

if ($action == 'update_status') {
    if(!isOperator()) exit;
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id']; $status = $data['status'];
    
    if($status == 'delete') {
        $conn->query("DELETE FROM orders WHERE id=$id");
    } else {
        // Whitelist status values to prevent invalid enums / empty states
        $allowed = ['unpaid','pending','preparing','ready','completed','cancelled'];
        if(!in_array($status, $allowed)) {
            echo json_encode(["success"=>false, "message"=>"Invalid status"]);
            exit;
        }
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
    echo json_encode(["success"=>true]);
}
?>