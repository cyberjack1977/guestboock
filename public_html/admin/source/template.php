<?php
header('Content-Type: text/html');
$ADMIN = $_POST["data"];
include '../templates/'.$_POST["file"];
