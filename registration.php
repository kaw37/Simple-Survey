<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="registrationphp.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Montserrat' >
    <script src="registration.js"></script>
</head>
<body>
<div class="container">
<form method="post" action="index.php"onSubmit="return validateForm(this)">
        <input type="hidden" name="action" value="register"/>
    <h1>Registration Form</h1><br><br>
    <div class="firstAndLastNameField">
        <input type='text' name='userName' value="<?php echo $row->getUsername()?>" placeholder="User name"/> <br>
    </div>
    <div class="firstAndLastNameField">
        <input type='text' name='firstName' value="<?php echo $row->getFirstname()?>" placeholder="First Name"/> <br>
    </div>
    <div class="firstAndLastNameField">
        <input type='text' name='lastName' placeholder="Last Name"
               value="<?php echo $row->getLastname()?>"/>
    </div>
    <div class="emailAndPasswordField">
        <input type='text' name='email' placeholder="Email" value="<?php echo $row->getEmail()?>"/>
    </div>
    <div class="emailAndPasswordField">
        <input type='password' name='password' placeholder="Password"/>
    </div><br><br>
    <div id="formButtons">
        <input type='submit' value='Register' class="button inner"/><br>
        <a href="index.php?action=dashboard" class="button inner">Back</a>
    </div>
</form>
</div>
</body>
</html>