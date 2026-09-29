<?php
require 'config.php';
session_destroy();
header('Location: select_user.php');
exit;
