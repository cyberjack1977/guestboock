<?php
$PASS = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
include '../../source/mysqli_config.php';
include '../../source/mysqli_connect.php';
if($PASS === 'parol'){
    $ADMIN["error"]["status"] = 0;
    $sql = "SELECT m.id, p.name, a.email, m.created AS datetime, m.text AS message, ad.ip, b.name AS browser, b.version 
            FROM message m
            INNER JOIN account a ON m.account_id = a.id
            INNER JOIN persona p ON m.persona_id = p.id
            INNER JOIN address ad ON m.address_id = ad.id 
            INNER JOIN browser b ON m.browser_id = b.id 
            ORDER BY m.created DESC";

    $result = $mysqli->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        $result->free();
    }
}
else{
    $ADMIN["error"]["status"] = 1;
    $ADMIN["error"]["message"] = "В доступе отказано, пароль неправильеный.";
}
$ADMIN["list"] = $list;
header('Content-Type: application/json');
echo json_encode($ADMIN);
