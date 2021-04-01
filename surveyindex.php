
<?php
$email="";
$msg="<h3>Welcome! What would you like to do?</h3>";
$userId=0;

if (isset ($_SESSION['UserID']))
{
    
    $userId= ($_SESSION['UserID']);
    $name=$_SESSION['UserFullName'];
    $msg="<h3>Welcome back, $name!<br>What would you like to do?</h3>";
}else
    header("Location: login.html");
?>

<html>
<head>
    <title>Main Menu</title>
</head>

<body>
<div class="indexphpContent">
<?php
    echo $msg;
    echo "<h4>Your Questions:</h4>";
    try{
        include_once ("database.php");

       $dbh = new Database();
       if (isset($_GET['toggle']))
           $stmt = $dbh->getAllQuestions($userId);
       else
           $stmt = $dbh->getUserQuestions($userId);
       
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<p><a href='index.php?action=question&id=".$row['id']."'>". $row['title']. "</a></p>";

        }
        $dbh=null;
    }catch (Exception $e){
        echo $e;
    }

   // $dbh->close()
?>
    <div class="formButtons">
        <a href="index.php?action=addQuestion" target="_self" class="button inner">Add a Question</a> &nbsp;
        <a href="index.php?toggle=All" target="_self" class="button inner">All Questions</a> &nbsp;  
        <a href="index.php" target="_self" class="button inner">My Questions</a> &nbsp;          
        <a href="logout.php" target="_self" class="button inner">Log Out</a>
    </div>

</div>
</body>
</html>