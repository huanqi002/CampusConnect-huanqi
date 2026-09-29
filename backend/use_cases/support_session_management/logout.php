<?php
require __DIR__ . '/../../general/config.php';
session_destroy();
header('Location: select_user.php');
exit;
