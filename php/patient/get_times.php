<?php
// include "../database.php";

// if (isset($_POST['day'])) {
//     $selectedDay = $_POST['day'];
//     $sql = "SELECT app_time FROM appointment WHERE day = '$selectedDay'";
//     $timesql = "SELECT times FROM times";
//     $result = $conn->query($sql);
//     $result2 = $conn->query($timesql);

//     if ($result->num_rows > 0) {
//         // Output data of each row
//         while ($row = $result->fetch_assoc()) {
//             $conflict = false;
//              while ($time = $result2->fetch_assoc()){
//                 if($row == $time){
//                     $conflict = true;
//                     break;
//                 }
//              }
//              if(!$conflict){echo "<option value='" . $row['fullName'] . "'>" . $row['fullName'] . "</option>";}
             
//         }
//     } else {
//         echo "<option value=''>$selectedDay Full</option>";
//     }
//     $result->close();
// } else {
//     echo "<option value=''>Select a day first</option>";
// }
?>