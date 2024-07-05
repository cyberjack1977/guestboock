<?php
$PASS = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
include '../../source/mysqli_config.php';
include '../../source/mysqli_connect.php';
if($PASS === 'parol'){
    $ADMIN["error"]["status"] = 0;
    $sql = "DELETE FROM message WHERE id = '{$id}' LIMIT 1";
    $result = $mysqli->query($sql);
}
else{
    $ADMIN["error"]["status"] = 1;
    $ADMIN["error"]["message"] = "В доступе отказано, пароль неправильеный.";
}
$ADMIN["list"] = $list;
header('Content-Type: application/json');
echo json_encode($ADMIN);
