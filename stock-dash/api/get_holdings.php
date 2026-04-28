<?php
require_once "db.php";
header("Content-Type: application/json");

$sql = "SELECT id, ticker, shares, avg_cost, notes, archived, updated_at
        FROM portfolio_holdings
        ORDER BY updated_at DESC";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Query failed: " . $conn->error
    ]);
    exit;
}

$holdings = [];

while ($row = $result->fetch_assoc()) {
    $holdings[] = [
        "id" => $row["id"],
        "ticker" => $row["ticker"],
        "shares" => (float)$row["shares"],
        "avgCost" => (float)$row["avg_cost"],
        "notes" => $row["notes"] ?? "",
        "archived" => (bool)$row["archived"],
        "updatedAt" => $row["updated_at"]
    ];
}

echo json_encode([
    "success" => true,
    "holdings" => $holdings
]);

$conn->close();
?>