<?php
// 启用错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 检查是否接收到 POST 数据
if (isset($_POST['gasId']) && isset($_POST['gasWeightEmpty']) && isset($_POST['sensorId']) && isset($_POST['worker_Id'])) {

    require_once "conn.php";
    require_once "validate.php";  // 这里引入 validate.php，直接使用其中的 validate() 函数

    $conn = mysqli_connect("database-1.cxzg4akd6dhy.ap-northeast-1.rds.amazonaws.com", "admin", "Bk+w%H86", "gasstation");
    $conn->set_charset("UTF8");

    // 验证输入数据
    $gasId = validate($_POST['gasId']);
    $gasWeightEmpty = validate($_POST['gasWeightEmpty']);
    $sensor_Id = validate($_POST['sensorId']);
    $worker_Id = validate($_POST['worker_Id']);

    // 查找工人公司ID
    $selectWorkerCompanyQuery = "SELECT `WORKER_Company_Id` FROM `worker` WHERE `WORKER_Id` = '$worker_Id'";
    $result = $conn->query($selectWorkerCompanyQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc(); 
        $workerCompany_Id = $row['WORKER_Company_Id'];

        // 插入新的瓦斯数据
        $insertQuery = "INSERT INTO gas (`GAS_Id`, `GAS_Weight_Empty`, `GAS_Company_Id`) VALUES ('$gasId', '$gasWeightEmpty','$workerCompany_Id')";
        if ($conn->query($insertQuery)) {
            // 更新 IOT 表
            $updateIOT = "UPDATE `iot` SET `Gas_Id`='$gasId',`Gas_Empty_Weight`='$gasWeightEmpty' WHERE `SENSOR_Id`='$sensor_Id'";
            if ($conn->query($updateIOT)) {
                echo "success";
            } else {
                echo "failure: " . $conn->error;
            }
        } else {
            echo "failure: " . $conn->error;
        }
    } else {
        echo "failure: Worker ID not found.";
    }
} else {
    echo "failure: Missing POST data.";
}

// 终止脚本
exit;
?>
