<?php
$email="junk@test.com";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
           echo $emailErr;
            //return -2;
        }
?>