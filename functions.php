<?php

function check_login($con) {
    if(isset($_SESSION['Employer_ID'])) {
        $id = $_SESSION['Employer_ID'];
        $query = "SELECT * FROM employers WHERE Employer_ID = '$id' LIMIT 1";

        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0) {
            $employer_data = mysqli_fetch_assoc($result);
            return $employer_data;
        }
    }

    // Redirect to login
    header("Location: employer_login.php");
    die;
}

function random_num($length) {
    $text = "";
    if($length < 5) {
        $length = 5;
    }

    $len = rand(4, $length);

    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9);
    }

    return $text;
}
?>