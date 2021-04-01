<?php session_start(); ?>
<html class="en">
<head>
    <link rel="stylesheet" href="index.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Montserrat' >
    <script src="registration.js"></script>
</head>
<body>
<div class="container">
<?php

//echo "index";

session_start();

require("database.php");
$method = 1;
$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
   // echo "method two";
    $method = 2;
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        //$action = 'list_products';
        if (isset($_SESSION['UserID'])) {

            include("dashboard.php");
        } else {
            include("login.html");
        }
    }
}

if ($action == "login") {
    $email = filter_input(INPUT_POST, 'email');
    $password = filter_input(INPUT_POST, 'password');
    if ($email && $password) {
        $dbh = new Database();
        $user = $dbh->login($email, $password);
        $_SESSION['userObject'] = $user;
        if ($user == null) {
            die("Email and/or password does not match.");
        }
        $dbh = null;
        include("dashboard.php");
    } else {
        include("login.html");
    }
} else if ($action=="logout"){
    session_destroy();
    echo "You have been logged out.";
    include("login.html");
} else if ($action == "dashboard") {
    if ($method == 2) {
        if (isset($_SESSION['UserID'])) {

//echo "user id: ".$_SESSION['UserID'];
            include("dashboard.php");
            return;
        } else {
            include("login.html");
            return;
        }
    }
}
else if ($action == "register") {
  //  echo "register";
    if ($method == 2) {
     //   echo "get";
        if (isset($_SESSION['UserID'])) {
         //  echo "user id: ".$_SESSION['UserID'];
            $dbh = new Database();
            $row = $dbh->currentUser();
            include("registration.php");
            return;
        } else {
            //echo "new";
            include("registration.html");
            return;
        }
    }

    extract($_POST);
   // echo "$firstName, $lastName, $email, $password";
    if (!$firstName || !$lastName || !$email || !$password) {
        //echo "missing data";
        include("registration.html");
    } else {
        $dbh = new Database();
        if (isset($_SESSION['UserID'])) {
            //echo "updating";
            $id = $dbh->updateUser($userName, $firstName, $lastName, $email, $password);
        }else{
           // echo "inserting";
            $id = $dbh->register($userName, $firstName, $lastName, $email, $password, $passwordConfirm);
            if ($id>0)
            {
                $_SESSION['UserID']= $id;
            }
        }
        if ($id < 1) {
            die($dbh->getFailureMessage());
        }

        //echo "logged in successfully";
        include("dashboard.php");
    }
}


?>
</div>
</body>
</html>