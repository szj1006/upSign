<?php
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','root');
define('DB_NAME','signin');
$M = new mysql();
$M->connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
