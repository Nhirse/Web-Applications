<?php require_once "db.php";
header("Content-Type: application/json"); 
// --- Read JSON body ---
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON request body"
    ]);
    exit;
}

// --- Pull fields from request ---
$id = isset($data["id"]) ? trim($data["id"]) : "";
$ticker = isset($data["ticker"]) ? strtoupper(trim($data["ticker"])) : "";
$shares = isset($data["shares"]) ? (float)$data["shares"] : 0;
$avgCost = isset($data["avgCost"]) ? (float)$data["avgCost"] : 0;
$notes = isset($data["notes"]) ? trim($data["notes"]) : "";
$archived = !empty($data["archived"]) ? 1 : 0;
$updatedAt = isset($data["updatedAt"]) ? trim($data["updatedAt"]) : date("Y-m-d H:i:s");

// --- Basic validation ---
if ($id === "" || $ticker === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields: id and ticker"
    ]);
    exit;
}

if ($shares < 0 || $avgCost < 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Shares and avgCost must be non-negative"
    ]);
    exit;
}

// --- Convert ISO datetime to MySQL DATETIME if needed ---
$timestamp = strtotime($updatedAt);
if ($timestamp !== false) {
    $updatedAt = date("Y-m-d H:i:s", $timestamp);
} else {
    $updatedAt = date("Y-m-d H:i:s");
}

// --- Upsert query ---
// This requires `id` to be PRIMARY KEY or UNIQUE in your holdings table
$sql = "
    INSERT INTO portfolio_holdings (id, ticker, shares, avg_cost, notes, archived, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        ticker = VALUES(ticker),
        shares = VALUES(shares),
        avg_cost = VALUES(avg_cost),
        notes = VALUES(notes),
        archived = VALUES(archived),
        updated_at = VALUES(updated_at)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

// s = string, d = double, i = integer
$stmt->bind_param(
    "ssddsis",
    $id,
    $ticker,
    $shares,
    $avgCost,
    $notes,
    $archived,
    $updatedAt
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Holding saved successfully",
        "holding" => [
            "id" => $id,
            "ticker" => $ticker,
            "shares" => $shares,
            "avgCost" => $avgCost,
            "notes" => $notes,
            "archived" => (bool)$archived,
            "updatedAt" => $updatedAt
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Execute failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>