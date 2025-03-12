<?php
require '../vendor/autoload.php';

include "../DBConnection.php";


// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$validext = array("xls","xlsx");
$uploaddir = "../Pajak/uploads/";
$creator = $_COOKIE["UserID"];
$errmsg = "Extenstion salah!";

//IMPORT DARI CORETAX KE MAXI
if (isset($_POST["btnImport"])) {
    if($_FILES['input_file']['name'] == ""){
        header("Location:../Pajak/import.php?status=no-file");
    }else{
        //PROCESS UPLOAD
        $filename = $_FILES['input_file']['name'];
        $tmpname = $_FILES['input_file']['tmp_name'];

        //CHECK FILE EXTENSION
        $ext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        if(in_array($ext,$validext)){
            //VALID

            $newfile = basename($filename);
            try{
                move_uploaded_file($tmpname,$uploaddir.$newfile); //UPLOAD KE FOLDER
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spreadsheet = $reader->load($uploaddir.$newfile);
                /*
                IF ZipArchive is not found, go to php folder enable extension=zip
                */
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                unset($data[0]); //REMOVE HEADER
                $updateddata = 0;
                foreach($data as $row){
                    if($row[3] != "" && $row[13] != ""){
                        $query = "UPDATE invoiceheader SET TaxInvoiceNumber='".$row[3]."', TaxInvoiceDate='".$row[4]."' WHERE InvoiceID='".$row[13]."'";
                        $result = mysqli_query($conn, $query);
                        if($result == 1){
                            $updateddata++;
                        }
                    }
                }
                logAction($conn, $creator, 'Update', 'berhasil memperbarui data faktur penjualan.', 0, $updateddata . " data up to date!");
                header("Location:../Pajak/import.php?status=success&success-data=" . $updateddata);
            }catch(Exception $e){
                echo $e;
            }
        }else{
            //NOT VALID
            logAction($conn, $creator, 'Create', 'gagal upload file dengan extension xls/xlsx.', 1, $errmsg);
            header("Location:../Pajak/import.php?status=error-ext");
        }
    }
}


function logAction($conn, $userID, $actionDone, $actionMSG, $actionStatus, $recordID)
{
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO systemlog (Timestamp, UserID, ActionDone, ActionMSG, ActionStatus, RecordID) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $timestamp, $userID, $actionDone, $actionMSG, $actionStatus, $recordID);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
}
?>