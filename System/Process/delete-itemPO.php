<?php
include "../DBConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemCD = $_POST['itemCD'];

    // Query untuk menghapus data dari database
    $queryDelete = "DELETE FROM purchaseorderdetail WHERE ItemCD = ?";
    $stmt = $conn->prepare($queryDelete);
    $stmt->bind_param("s", $itemCD); // Bind parameter
    if ($stmt->execute()) {
        echo "Item deleted successfully.";
    } else {
        echo "Error deleting item: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>