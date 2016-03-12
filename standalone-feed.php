<?php
require_once('standalone-settings.php');
$events = require('feed.php');

header('Content-type: application/javascript');
echo json_encode($events);
