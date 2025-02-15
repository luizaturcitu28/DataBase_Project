<!-- Fisierul corespunzator delogarii -->

<?php

session_start();

session_unset();

session_destroy();

header("Location: /BD/index.php");
exit;
?>