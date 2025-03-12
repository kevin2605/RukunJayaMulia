<?php
require '../vendor/autoload.php';

include "../DBConnection.php";


// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$validext = array("xls","xlsx");
$uploaddir = "../Tools/uploads/";
$creator = $_COOKIE["UserID"];
$errmsg = "Extenstion salah!";

//IMPORT DARI FINGER KE MAXI
if (isset($_POST["btnImport"])) {
    if($_FILES['input_file']['name'] == ""){
        header("Location:../Tools/import-attendance.php?status=no-file");
    }else{
        //PROCESS UPLOAD
        $filename = $_FILES['input_file']['name'];
        $tmpname = $_FILES['input_file']['tmp_name'];

        //CHECK FILE EXTENSION
        $ext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        if(in_array($ext,$validext)){
            //VALID

            $newfile = basename($filename);
            if(file_exists($uploaddir.$newfile)){
                logAction($conn, $creator, 'Create', 'gagal import data absensi karyawan. File duplicate!', 0, $filename);
                header("Location:../Tools/import-attendance.php?status=duplicate&error-data=" . $filename);
            }else{
                try{
                    move_uploaded_file($tmpname,$uploaddir.$newfile); //UPLOAD KE FOLDER
                    
                    //IF ZipArchive is not found, go to php folder enable extension=zip
                    
    
                    //GET SHIFT WORKING HOUR
                    $arrWorkHour = [];
                    $queryWH = "SELECT * FROM setting_working_hour";
                    $resultWH = mysqli_query($conn, $queryWH);
                    while ($rowWH = mysqli_fetch_array($resultWH)) {
                        array_push($arrWorkHour,$rowWH["CheckIn"],$rowWH["CheckOut"]);
                    }
                    $tmpShift1 = strtotime($arrWorkHour[0]) . "<br>";
                    $tmpShift2 = strtotime($arrWorkHour[2]) . "<br>";
                    $tmpShift3 = strtotime($arrWorkHour[4]) . "<br>";
    
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $spreadsheet = $reader->load($uploaddir.$newfile);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $data = $worksheet->toArray();
                    
                    //FORMAT 2
                    $readTime = "NO";
                    foreach($data as $row){
                        //GET MONTH
                        if(substr($row[0],0,7) == "Periode"){
                            $year = substr($row[2],0,4);
                            $month = substr($row[2],5,2);
                            continue;
                        }

                        //GET NIK
                        if(substr($row[0],0,4) == "No :"){
                            $NIK = $row[2];
                            $readTime = "YES";
                            continue;
                        }

                        //TIME ROW
                        if($readTime == "YES"){
                            for($i = 0; $i < count($row); $i++){
                                $timeIn = substr($row[$i],0,5);
                                $timeOut = substr($row[$i],6,5);
                                $date = $i + 1;

                                if($timeIn != "" && $timeOut != ""){
                                    $tmpIn = strtotime($timeIn);
                                    $tmpOut = strtotime($timeOut);

                                    //calculate working hour
                                    $workingHour = floor(($tmpOut - $tmpIn) / 3600);
                                    if($workingHour > 7 || $workingHour <= -14){
                                        $workingHour = 7;
                                    }else if($workingHour >= 5){
                                        $workingHour = 5;
                                    }else{
                                        $workingHour = 0;
                                    }
                                    
                                    //CALCULATE WORKING HOUR AND OVERTIME
                                    if(($tmpShift1 - 3600) <= $tmpIn && $tmpShift1 > $tmpIn){
                                        $tmpLimit = strtotime($arrWorkHour[1]);
                                        //calculate overtime
                                        $tmpOT = floor(($tmpOut - $tmpLimit) / 3600);
                                        if($tmpOT > 2){
                                            $overtime = 2;
                                        }else if($tmpOT > 0){
                                            $overtime = $tmpOT;
                                        }else{
                                            $overtime = 0;
                                        }
                                        $indate = $year . "-" . $month . "-" . $date;
                                        $query = "INSERT INTO `emp_attendance`(`NIK`, `Date`, `CheckIn`, `CheckOut`, `WorkingHour`, `Overtime`)
                                                VALUES ('$NIK','$indate','$timeIn','$timeOut','$workingHour','$overtime')";
                                        $result = mysqli_query($conn, $query);
                                    }else if(($tmpShift2 - 3600) <= $tmpIn && $tmpShift2 > $tmpIn){
                                        $tmpLimit = strtotime($arrWorkHour[3]);
                                        //calculate overtime
                                        $tmpOT = floor(($tmpOut - $tmpLimit) / 3600);
                                        if($tmpOT == 0){
                                            $overtime = 0;
                                        }else if($tmpOT > -23){
                                            $overtime = 2;
                                        }else if($tmpOT > -24){
                                            $overtime = 1;
                                        }else{
                                            $overtime = 0;
                                        }
                                        $indate = $year . "-" . $month . "-" . $date;
                                        $query = "INSERT INTO `emp_attendance`(`NIK`, `Date`, `CheckIn`, `CheckOut`, `WorkingHour`, `Overtime`)
                                                VALUES ('$NIK','$indate','$timeIn','$timeOut','$workingHour','$overtime')";
                                        $result = mysqli_query($conn, $query);
                                    }else if(($tmpShift3 - 3600) <= $tmpIn && $tmpShift3 > $tmpIn){
                                        $tmpLimit = strtotime($arrWorkHour[5]);
                                        //calculate overtime
                                        $tmpOT = floor(($tmpOut - $tmpLimit) / 3600);
                                        if($tmpOT > 2){
                                            $overtime = 2;
                                        }else if($tmpOT > 0){
                                            $overtime = $tmpOT;
                                        }else{
                                            $overtime = 0;
                                        }
                                        $indate = $year . "-" . $month . "-" . $date;
                                        $query = "INSERT INTO `emp_attendance`(`NIK`, `Date`, `CheckIn`, `CheckOut`, `WorkingHour`, `Overtime`)
                                                VALUES ('$NIK','$indate','$timeIn','$timeOut','$workingHour','$overtime')";
                                        $result = mysqli_query($conn, $query);
                                    }  
                                }
                            }
                            $readTime = "NO";
                        }
                    }
                    logAction($conn, $creator, 'Create', 'berhasil import data absensi karyawan.', 0, $filename);
                    header("Location:../Tools/import-attendance.php?status=success&success-data=" . $filename);
                }catch(Exception $e){
                    die("Error! NIK tidak terdaftar dalam sistem! Silahkan periksa data absensi kembali.");
                }
            }
        }else{
            //NOT VALID
            logAction($conn, $creator, 'Create', 'gagal upload file dengan extension xls/xlsx.', 1, $errmsg);
            header("Location:../Tools/import-attendance.php?status=error-ext");
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