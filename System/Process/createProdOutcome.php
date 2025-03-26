<?php

include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Ambil data dari POST
$spk = explode(" | ", $_POST["spk"])[0];
$group = explode(" - ", $_POST["group"])[0];
$mesin = explode(" - ", $_POST["mesin"])[0];
$produk = explode(" - ", $_POST["produk"])[0];
$quantity = str_replace('.', '', $_POST["quantity"]);
$tweight = str_replace('.', '', $_POST["tweight"]);
$workhour = $_POST["workhour"];
$creator = $_COOKIE["UserID"] ?? 'unknown'; // Menggunakan cookie untuk creator
$datetime = date('Y-m-d H:i:s');

// Query untuk memasukkan data ke tabel productionresulthistory
$query = "INSERT INTO `productionresulthistory`(`ProductionOrderID`, `CreatedOn`, `GroupCD`, `MachineCD`, `ProductCD`, `WorkingHour`, `ProdOutcome`, `TotalWeight`) 
          VALUES ('$spk','$datetime','$group','$mesin','$produk','$workhour','$quantity','$tweight')";
$result = mysqli_query($conn, $query);

// Update production order
$queryU = "UPDATE productionorder SET QtyProduced=QtyProduced+" . $quantity . " WHERE ProductionOrderID='" . $spk . "'";
$resultU = mysqli_query($conn, $queryU);

// Check if QtyOrder = QtyProduced
$queryc = "SELECT * FROM productionorder WHERE ProductionOrderID='" . $spk . "'";
$resultc = mysqli_query($conn, $queryc);
$rowc = mysqli_fetch_assoc($resultc);
if($rowc["QtyOrder"] == $rowc["QtyProduced"]){
    $queryU = "UPDATE productionorder SET Status='1' WHERE ProductionOrderID='" . $spk . "'";
    $resultU = mysqli_query($conn, $queryU);
}

//MENGURANGI LEDGER BAHAN
if($group == "BPPK"){
    $queryA = "SELECT * FROM mat_ppk_ledger ORDER BY 1 DESC LIMIT 1";
    $resultA = mysqli_query($conn, $queryA);
    $rowA = mysqli_fetch_assoc($resultA);

    $remaining = $rowA["RemainingAmount"];
    $newAmount = $remaining - $tweight; //flowout bahan keluar dari ledger masuk stok

    $query = "INSERT INTO `mat_ppk_ledger`(`CreatedOn`, `ReferenceKey`, `MaterialCD`, `Quantity`, `UnitCD`, `InVsOut`, `RemainingAmount`) 
              VALUES ('$datetime','$spk','-','$tweight','GRAM','1','$newAmount')";
    $result = mysqli_query($conn, $query);
}else if($group == "BPPH"){
    $queryB = "SELECT * FROM mat_pph_ledger ORDER BY 1 DESC LIMIT 1";
    $resultB = mysqli_query($conn, $queryB);
    $rowB = mysqli_fetch_assoc($resultB);

    $remaining = $rowB["RemainingAmount"];
    $newAmount = $remaining - $tweight; //flowout bahan keluar dari ledger masuk stok

    $query = "INSERT INTO `mat_pph_ledger`(`CreatedOn`, `ReferenceKey`, `MaterialCD`, `Quantity`, `UnitCD`, `InVsOut`, `RemainingAmount`) 
              VALUES ('$datetime','$spk','-','$tweight','GRAM','1','$newAmount')";
    $result = mysqli_query($conn, $query);
}else if($group == "BPETK"){
    $queryC = "SELECT * FROM mat_petk_ledger ORDER BY 1 DESC LIMIT 1";
    $resultC = mysqli_query($conn, $queryC);
    $rowC = mysqli_fetch_assoc($resultC);

    $remaining = $rowC["RemainingAmount"];
    $newAmount = $remaining - $tweight; //flowout bahan keluar dari ledger masuk stok

    $query = "INSERT INTO `mat_pph_ledger`(`CreatedOn`, `ReferenceKey`, `MaterialCD`, `Quantity`, `UnitCD`, `InVsOut`, `RemainingAmount`) 
              VALUES ('$datetime','$spk','-','$tweight','GRAM','1','$newAmount')";
    $result = mysqli_query($conn, $query);
}

// Log aktivitas ke tabel systemlog
logAction($conn, $creator, 'Update', 'menambahkan hasil produksi', ($result && $resultU) ? 0 : 1, $spk);

// Arahkan ke halaman dengan status sukses atau error
if ($result && $resultU) {
    header("Location:../Production/production-outcome.php?status=success");
} else {
    header("Location:../Production/production-outcome.php?status=error");
}

function logAction($conn, $userID, $actionDone, $actionMSG, $actionStatus, $recordID)
{
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO systemlog (Timestamp, UserID, ActionDone, ActionMSG, ActionStatus, RecordID) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $timestamp, $userID, $actionDone, $actionMSG, $actionStatus, $recordID);
        if (!$stmt->execute()) {
            error_log("Log action failed: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
}

?>