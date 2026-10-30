<?php
session_start();
session_unset();
session_destroy();
header("Location: http://localhost/site_management/public/users.php");
exit();
?>