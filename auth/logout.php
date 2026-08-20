<?php
session_start();
session_regenerate_id(true);
session_destroy();
header("Location: " . BASE_PATH . "pages/index.php");
exit;
