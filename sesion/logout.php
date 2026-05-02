<?php
session_start();
session_destroy();
header("Location: ../sesion/login.php");
exit();
