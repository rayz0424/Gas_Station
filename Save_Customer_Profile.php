<?php
if(isset($_POST['id'])){
    // Include the necessary files
    require_once "conn.php";
    require_once "validate.php";
    // Call validate, pass form data as parameter and store the returned value
    $email = validate($_POST['email']);
    $name = validate($_POST['name']);
    $phone = validate($_POST['phone']);
    $houseTel = validate($_POST['houseTel']);
    $address = validate($_POST['address']);
    $company = validate($_POST['company']);
    $id = validate($_POST['id']);

    $stmt2 = $conn->prepare("SELECT COMPANY_Id FROM company WHERE COMPANY_Name=?");
    $stmt2->bind_param("s", $company);
    $stmt2->execute();
    $result = $stmt2->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $company_id = $row['COMPANY_Id'];
        $stmt = $conn->prepare("UPDATE customer SET `CUSTOMER_Name`=?, `CUSTOMER_Email`=?, `CUSTOMER_Address`=?, `CUSTOMER_HouseTelpNo`=?, `CUSTOMER_PhoneNo`=?, `COMPANY_Id`=? WHERE `CUSTOMER_Id`=?");
        $stmt->bind_param("ssssssi", $name, $email, $address, $houseTel, $phone, $company_id, $id);
        if($stmt->execute()) {
            echo "success";
        } else {
            echo "failure: " . $stmt->error;
        }
    } else {
        echo "Company does not exist in the database";
    }
}
?>