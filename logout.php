<?php
session_start();
session_destroy();
header("Location: /traceabilitydev/login.php");
exit();
