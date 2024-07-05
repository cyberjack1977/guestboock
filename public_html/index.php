<?php
function get_url($http_host){
    $https = filter_input(INPUT_SERVER, "HTTPS", FILTER_SANITIZE_STRING);
    $request_uri = filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_URL);
    if($https === "On"){$url = "https";}else{$url = "http";}
    $url .= "://" . $http_host . $request_uri;
    $parse = parse_url($url);
    $path = pathinfo($parse["path"]);
    return array_merge(array("string" => $url), $parse, $path);
}
function Captcha(){
    $_SESSION['captcha'] = substr(str_shuffle('abcdefghijkmnpqrstuvwxyz23456789'), 0, 4);
    $image = imagecreatetruecolor(120, 48);
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 13, 13, 13);
    $line_color = imagecolorallocate($image, 13, 13, 13);
    $pixel_color = imagecolorallocate($image, 13, 13, 13);
    imagefilledrectangle($image, 0, 0, 120, 48, $background_color);
    for ($i = 0; $i < 5; $i++) {imageline($image, 0, rand() % 48, 120, rand() % 48, $line_color);}
    for ($i = 0; $i < 1000; $i++) {imagesetpixel($image, rand() % 120, rand() % 48, $pixel_color);}
    imagettftext($image, 24, 0, 25, 34, $text_color, __DIR__ . '/assets/font/arialmt.ttf', $_SESSION['captcha']);
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}
function get_browser_info() {
    $agent = filter_input(INPUT_SERVER, "HTTP_USER_AGENT", FILTER_SANITIZE_STRING);
    $result = array("name" => "Unknown", "version" => "Unknown");
    $browser_pattern = "/(?P<name>Edge|Opera|Chrome|Safari|Firefox)[\/ ]+(?P<version>[\d\.]+)/";
    if (preg_match($browser_pattern, $agent, $matches)) {
        $result = array("name" => $matches["name"], "version" => $matches["version"]);
    }
    return $result;
}
function add_message_GB($mysqli){
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(strip_tags(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING)), ENT_QUOTES, 'UTF-8');
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);    

    $err = 0;
    $err_msg = '';
    
    if (empty($code) || $code !== $_SESSION['captcha']) {
        $err = 1;
        $err_msg = "Проверочный код не совпадает\n";
    }
    if (empty($message)) {
        $err = 1;
        $err_msg = "Сообщение не задано.\n" . $err_msg;
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 1;
        $err_msg = "Email не задан или задан неверно.\n" . $err_msg;
    }
    if (empty($name) || !preg_match('/^[a-zA-Z0-9]+$/', $name)) {
        $err = 1;
        $err_msg = "Имя должно состоять только из латинских букв и цифр.\n" . $err_msg;
    }

    if ($err === 1) {
        $answer = json_encode(array("err" => $err, "err_msg" => $err_msg));
    }
    else{
        $browser = get_browser_info();
        $ip = filter_input(INPUT_SERVER, "REMOTE_ADDR", FILTER_SANITIZE_STRING);
        $datetime = time();
        $mysqli->begin_transaction();

        try {
            $result = $mysqli->query("SELECT id FROM account WHERE email = '$email'");
            if ($result->num_rows === 0) {
                $mysqli->query("INSERT INTO account (email) VALUES ('$email')");
            }
            $result = $mysqli->query("SELECT id FROM account WHERE email = '$email'");
            $account_id = $result->fetch_assoc()['id'];

            $result = $mysqli->query("SELECT id FROM address WHERE ip = '$ip'");
            if ($result->num_rows === 0) {
                $mysqli->query("INSERT INTO address (ip, blocked) VALUES ('$ip', 0)");
            }
            $result = $mysqli->query("SELECT id FROM address WHERE ip = '$ip'");
            $address_id = $result->fetch_assoc()['id'];

            $result = $mysqli->query("SELECT id FROM browser WHERE name = '{$browser['name']}' AND version = '{$browser['version']}'");
            if ($result->num_rows === 0) {
                $mysqli->query("INSERT INTO browser (name, version) VALUES ('{$browser['name']}', '{$browser['version']}')");
            }
            $result = $mysqli->query("SELECT id FROM browser WHERE name = '{$browser['name']}' AND version = '{$browser['version']}'");
            $browser_id = $result->fetch_assoc()['id'];

            $result = $mysqli->query("SELECT id FROM persona WHERE name = '$name'");
            if ($result->num_rows === 0) {
                $mysqli->query("INSERT INTO persona (name) VALUES ('$name')");
            }
            $result = $mysqli->query("SELECT id FROM persona WHERE name = '$name'");
            $persona_id = $result->fetch_assoc()['id'];

            $mysqli->query("INSERT INTO message (account_id, persona_id, address_id, browser_id, created, text, blocked) VALUES ($account_id, $persona_id, $address_id, $browser_id, '$datetime', '$message', 0)");

            $mysqli->commit();
        }
        catch (Exception $e) {
            $mysqli->rollback();
            $err_msg = $e->getMessage() . $err_msg;
        }
        $mysqli->close();

        $answer = json_encode(array("err" => $err, "row" => array("name" => $name, "email" => $email, "datetime" => $datetime, "message" => $message)));
    }
    return $answer;
}
function list_message_GB($mysqli, $page, $sort, $order) {
    $messages = array();
    if(!$sort){$sort = "datetime";}
    if(!$order){$order = "DESC";}
    $limit = 5;
    $offset = ($page - 1) * $limit;

    if ($sort === 'name' || $sort === 'email' || $sort === 'datetime') {
        $sql_order = "ORDER BY $sort $order";
    } else {
        $sql_order = "ORDER BY m.created DESC";
    }

    $count_sql = "SELECT COUNT(*) AS total FROM message";

    $count_result = $mysqli->query($count_sql);
    $total_rows = $count_result->fetch_assoc()['total'];
    $count_result->free();

    $total = ceil($total_rows / $limit);

    $sql = "SELECT p.name, a.email, m.created AS datetime, m.text AS message
            FROM message m
            INNER JOIN account a ON m.account_id = a.id
            INNER JOIN persona p ON m.persona_id = p.id
            $sql_order 
            LIMIT $limit OFFSET $offset";

    $result = $mysqli->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        $result->free();
    }

    return array("row" => $messages, "current" => $page, "total" => $total, "sort" => $sort, "order" => $order);
}

session_start();
$DOCUMENT_ROOT = filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING);
$SORT = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);
$ORDER = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING);
include $DOCUMENT_ROOT . "/source/mysqli_config.php";
include $DOCUMENT_ROOT . "/source/mysqli_connect.php";

$GB = array();
$GB["url"] = get_url(filter_input(INPUT_SERVER, "HTTP_HOST", FILTER_SANITIZE_URL));
$GB["tpl"]["path"] = "/templates/";
$GB["name"] = "Гостевая книга";

$GB["tpl"]["file"] = "pageGB.php";
$GB["header"] = "Список сообщений";
$GB["title"] = "Список сообщений &mdash; " . $GB["name"];
$GB["description"] = "Здесь вы можете увидеть сообщения пользователей, а также оставить свое сообщение.";


if($GB["url"]["path"] === "/"){
    $GB["message"] = list_message_GB($mysqli, 1, $SORT, $ORDER);
}
elseif($GB["url"]["path"] === "/message.html"){
    $GB["message"] = list_message_GB($mysqli, 1, "", "");
    $GB["tpl"]["file"] = "messageGB.php";
}
elseif((integer)$GB["url"]["filename"] > 0){
    $GB["message"] = list_message_GB($mysqli, $GB["url"]["filename"], $SORT, $ORDER);
}
elseif($GB["url"]["path"] === "/form.html"){
    $GB["tpl"]["file"] = "formGB.php";
}
elseif($GB["url"]["path"] === "/add.html"){
    header('Content-Type: application/json');
    echo add_message_GB($mysqli);
    exit();
}
elseif($GB["url"]["path"] === "/captcha.png"){
    Captcha();
    exit();
}
else{
    header('HTTP/1.1 404 Not Found');
    $GB["tpl"]["file"] = "404.php";
    $GB["header"] = "Страница не найдена";
    $GB["title"] = $GB["name"] . " &mdash; " . "Страница не найдена";
    $GB["description"] = "Ошибка 404";
}

include $DOCUMENT_ROOT . $GB["tpl"]["path"] . $GB["tpl"]["file"];

//echo '<pre>';print_r($GB);echo '</pre>';
