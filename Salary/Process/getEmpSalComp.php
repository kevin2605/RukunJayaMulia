<?php

include "../DBConnection.php";

$html = '<table id="tInv" class="table" style="width:100%">
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:10%">Tipe</th>
                    <th style="width:30%">Keterangan</th>
                    <th style="width:20%">Perhitungan</th>
                    <th style="width:15%">Pendapatan</th>
                    <th style="width:15%">Pengurangan</th>
                </tr>
            </thead>
            <tbody id="tInvBody">';
                                                          
                                                      
$ctr = 0;
$pendapatan = 0;
$pengurangan = 0;
$attendance = 0;
$tPND1 = 0;
$tOvertime = 0;


$month = str_pad($_GET["periode"], 2, "0", STR_PAD_LEFT);
$kueri = "SELECT ec.ComponentCode, sc.ComponentName, sc.ComponentType, ec.ComponentValue
          FROM employeecomponent ec, salarycomponent sc
          WHERE ec.ComponentCode = sc.ComponentCode
                AND ec.NIK='" .$_GET["nik"]. "'";
$hasil = mysqli_query($conn,$kueri);
while($row = mysqli_fetch_array($hasil)){
    $ctr++;
    $html .= '<tr id="row"'.$ctr.'>
                <td style="width:5%">
                    '.$ctr.'
                    <input type="hidden" name="componentcode[]" value="'.$row["ComponentCode"].'">
                </td>
                <td style="width:10%">
                    '.$row["ComponentType"].'
                </td>
                <td style="width:30%">
                    '.$row["ComponentName"].'
                </td>
                <td style="width:20%">';
                    
                    //RUMUS SETIAP SALARY COMPONENT
                    if($row["ComponentCode"] == "PND.1"){
                        $query = "SELECT SUM(WorkingHour) AS tWorkHour,COUNT(*) AS attendance,SUM(Overtime) AS tOvertime FROM emp_attendance WHERE NIK='" .$_GET["nik"]. "' AND substr(Date,6,2)='" .$month. "'";
                        $result = mysqli_query($conn, $query);
                        $exec = mysqli_fetch_assoc($result);
                        
                        $html .= $row["ComponentValue"] . '*' . $exec["tWorkHour"]; //append di html
                        $pendapatan += $row["ComponentValue"] * $exec["tWorkHour"]; //akumulasi pendapatan
                        $tPND1 = $row["ComponentValue"] * $exec["tWorkHour"]; //untuk cetak
                        $attendance = $exec["attendance"]; //absen kedatangan
                        $tOvertime = $exec["tOvertime"]; //total overtime

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="'.$exec["tWorkHour"].'">';
                    }else if($row["ComponentCode"] == "PND.2"){
                        $html .= $row["ComponentValue"] . '*' . $attendance; //append di html
                        $pendapatan += $row["ComponentValue"] * $attendance; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="'.$attendance.'">';
                    }else if($row["ComponentCode"] == "PND.3"){
                        $html .= $row["ComponentValue"] . '*' . $attendance; //append di html
                        $pendapatan += $row["ComponentValue"] * $attendance; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="'.$attendance.'">';
                    }else if($row["ComponentCode"] == "PND.4"){
                        $html .= $row["ComponentValue"] . '*?'; //append di html
                        $pendapatan += $row["ComponentValue"] * 0; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="0">';
                    }else if($row["ComponentCode"] == "PND.5"){
                        $html .= $row["ComponentValue"] . '*?'; //append di html
                        $pendapatan += $row["ComponentValue"] * 0; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="0">';
                    }else if($row["ComponentCode"] == "PND.6"){
                        $html .= $row["ComponentValue"] . '*' . $tOvertime; //append di html
                        $pendapatan += $row["ComponentValue"] * $tOvertime; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="'.$tOvertime.'">';
                    }else if($row["ComponentCode"] == "PND.A.1"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pendapatan += $row["ComponentValue"]; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "PND.A.2"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pendapatan += $row["ComponentValue"]; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "PND.BPJSJKN.P"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pendapatan += $row["ComponentValue"]; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "PND.BPJSTK.P"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pendapatan += $row["ComponentValue"]; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "PND.J.1"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pendapatan += $row["ComponentValue"]; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "PND.J.2"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pendapatan += $row["ComponentValue"]; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "PND.J.3"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pendapatan += $row["ComponentValue"]; //akumulasi pendapatan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "POT.1"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pengurangan += $row["ComponentValue"]; //akumulasi pengurangan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "POT.BPJSJKN.K"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pengurangan += $row["ComponentValue"]; //akumulasi pengurangan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "POT.BPJSTK.K"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pengurangan += $row["ComponentValue"]; //akumulasi pengurangan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }else if($row["ComponentCode"] == "POT.BPJSTK.P"){
                        $html .= $row["ComponentValue"]; //append di html
                        $pengurangan += $row["ComponentValue"]; //akumulasi pengurangan

                        echo '<input type="hidden" name="componentvalue[]" value="'.$row["ComponentValue"].'">';
                        echo '<input type="hidden" name="multiplier[]" value="1">';
                    }
                    
    $html .= '  </td>
                <td style="width:15%">';
                    if($row["ComponentCode"] == "PND.1"){
                        $html .= number_format($tPND1, 0, ',', '.');
                    }else if($row["ComponentCode"] == "PND.2" || $row["ComponentCode"] == "PND.3"){
                        $html .= number_format($row["ComponentValue"] * $attendance, 0, ',', '.');
                    }else if($row["ComponentCode"] == "PND.4" || $row["ComponentCode"] == "PND.5"){
                        $html .= number_format(0, 0, ',', '.');
                    }else if(substr($row["ComponentCode"],0,3) == "PND"){
                        $html .= number_format($row["ComponentValue"], 0, ',', '.');
                    }
    $html .= '  </td>
                <td style="width:15%">';
                    if(substr($row["ComponentCode"],0,3) == "POT"){
                        $html .= number_format($row["ComponentValue"], 0, ',', '.');
                    }
    $html .= '  </td>
            </tr>';
}

$html .= '  
                <tr>
                    <td colspan="4" class="text-end">Total</td>
                    <td>'.number_format($pendapatan, 0, ',', '.').'</td>
                    <td>'.number_format($pengurangan, 0, ',', '.').'</td>
                </tr>
            </tbody>
          </table>';

echo $html;
?>