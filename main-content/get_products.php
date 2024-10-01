<?php
header('Content-Type: application/json');
require "../dbconnection.php";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Nilimit ko to 24 for testing reasons
    $sql = "SELECT product_id, product_name, product_quantity FROM products WHERE product_quantity > 0 AND is_archived = 0 LIMIT 24";

 
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
}
?>
