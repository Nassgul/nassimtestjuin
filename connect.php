<?php
require_once 'config.php';
$mysqli = mysqli_connect(DB_SERVER,DB_USER,DB_MDP,DB_NAME);
mysqli_set_charset($mysqli, "utf8");