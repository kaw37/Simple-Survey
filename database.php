<?php

include_once 'model/Account.php';
include_once 'model/Question.php';

/*
  $dsn = "mysql:dbname=kaw37;host=sql1.njit.edu;port=3306"; //"mysql:dbname=devTest;host=localhost;port=3306";
  $usr = "kaw37";
  $pwd = "Kawaiiasfuck0315__";
 * */

class Database {

    static function getConn() {
        $dsn = "mysql:dbname=kaw37;host=sql1.njit.edu;port=3306"; //"mysql:dbname=devTest;host=localhost;port=3306";
        $usr = "kaw37";
        $pwd = "Kawaiiasfuck0315__";
        //global Database::dsn, Database::usr, Database::pwd;
        return new PDO($dsn, $usr, $pwd);
    }

    static function login($userName, $password) {
        try {
            $dbh = Database::getConn();
            $userCol = strpos($userName, "@") > 0 ? "email" : "username";
            $sql = "SELECT * FROM users WHERE $userCol = ?";
//echo $sql. " user: $userName";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $userName, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() < 1) {
                //echo "not found";
                return 0;
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //$pwd = password_hash($password, PASSWORD_DEFAULT);
           // echo "found: ".$row['password'];
            if (!password_verify($password,$row['password'])) {
                Database::$failureMessage= "Invalid password $password != ".$row['password'];
                return null;
            }
            //  echo "user id: ". $row['user_id'];
            $user = new Account($row);
            $id = $row['user_id'];
            $_SESSION['UserID'] = $row['user_id'];
            $_SESSION['UserEmail'] = $row['email'];
            $_SESSION['UserName'] = $row['username'];
            $_SESSION['UserFullName'] = $row['lastname'] . ", " . $row['firstname'];

            $sql = "SELECT role_id FROM user_roles WHERE user_id = ?";
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 1) {
                $user->setRoles($stmt->fetchAll());
                $_SESSION['UserRoles'] = $user->toString();
            }
            $dbh = null;
            return $user;
        } catch (PDOException $ex) {
           // die( $ex);
            return 0;
        }
    }

    static function currentUser() {
        if (!isset($_SESSION['UserID'])) {
            echo "current user is null";
            return null;
        }
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $dbh = Database::getConn();
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(1, $_SESSION['UserID'], PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() < 1) {
            return null;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
     //   echo "UserID: ".$row['lastname'];
        $_SESSION['userObject'] = $row;
        return new Account($row);
    }

    static function hasRole($userId, $roleId) {
        $sql = "SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?";
        $dbh = Database::getConn();
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $roleId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }


    static $failureMessage="";
    static function getFailureMessage (){return Database::$failureMessage;}
    static function validate($userName, $firstName, $lastName, $email,  $password, $passwordConfirm) {
        if ($password != null)
        {
            if ($password != $passwordConfirm || strlen($password) < 8) {
                Database::$failureMessage="Passwords don't match or are too short.";
                return -1;
            }
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Database::$failureMessage="Invalid email";
            return -2;
        }
        if (strlen($userName) < 2) {
            Database::$failureMessage="Username is too short - it must be at least 2 characters";
            return -3;
        }
        return 1;
    }

    static function register($userName, $firstName, $lastName, $email, $password, $passwordConfirm) {
        $valid= Database::validate($userName, $firstName, $lastName, $email,  $password, $passwordConfirm);
        if ($valid<1)
        {
            //echo "not valid";
            return $valid;
        }
        try {
           // echo "adding reg $uaerName";
            $dbh = Database::getConn();

            $sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $userName, PDO::PARAM_STR);
            $stmt->bindParam(2, $email, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {// found duplicate
               // echo "User name or email already exists";
                Database::$failureMessage="Username and/or email already exists.";
                return -1;
            }
            $pwd = password_hash($password, PASSWORD_BCRYPT);
            // echo "adding Username $userName / $pwd";
            $sql = "INSERT INTO `users` (`username`, `email`, `firstname`, `lastname`, `password`) VALUES (?,?,?,?,?) ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $userName, PDO::PARAM_STR);
            $stmt->bindParam(2, $email, PDO::PARAM_STR);
            $stmt->bindParam(3, $firstName, PDO::PARAM_STR);
            $stmt->bindParam(4, $lastName, PDO::PARAM_STR);
            // $stmt->bindParam(4, $birthday, PDO::PARAM_STR);
            $stmt->bindParam(5, $pwd, PDO::PARAM_STR);
            $stmt->execute();
            $id = $dbh->lastInsertId();
          //  echo "registration id: $id";
            $dbh = null;
            return $id;
        } catch (PDOException $ex) {
            echo "PDO EX".$ex;
            return 0;
        }
    }

    static function updateUser($userName, $firstName, $lastName, $email, $password) {
       // echo "udpate user";
        if (!isset($_SESSION['UserID'])) {
            return null;
        }
        $valid= Database::validate($userName, $firstName, $lastName, $email, "", $password,$password);
        if ($valid<1)
        {
            return $valid;
        }
        try {
            $dbh = Database::getConn();
            $userId = $_SESSION['UserID'];
            $sql = "SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id <> ?";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $userName, PDO::PARAM_STR);
            $stmt->bindParam(2, $email, PDO::PARAM_STR);
            $stmt->bindParam(3, $userId, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()>0) {
                echo "dup found on $userId";
                return -1;
            }

            $pwd = password_hash($password, PASSWORD_BCRYPT);
//die( "saving $userName");
            $sql = "REPLACE INTO `users` (`user_id`, `username`, `email`, `firstname`, `lastname`, `password`) VALUES (?,?,?,?,?,?) ";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $userId, PDO::PARAM_INT);
            $stmt->bindParam(2, $userName, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->bindParam(4, $firstName, PDO::PARAM_STR);
            $stmt->bindParam(5, $lastName, PDO::PARAM_STR);
            // $stmt->bindParam(4, $birthday, PDO::PARAM_STR);
            $stmt->bindParam(6, $pwd, PDO::PARAM_STR);
            $stmt->execute();
            $dbh = null;
            return 1;
        } catch (PDOException $ex) {
            echo $ex;
            return 0;
        }
    }

    static function saveQuestion($id, $questionName, $questionBody, $questionSkills, $vote = 0) {
        $sql = "UPDATE questions SET title=?, body=?, skills=? WHERE id = ?";

//echo "question: $questionName";
        try {
            $dbh = Database::getConn();
            $stmt = $dbh->prepare($sql);

            $stmt->bindParam(1, $questionName, PDO::PARAM_STR);
            $stmt->bindParam(2, $questionBody, PDO::PARAM_STR);
            $stmt->bindParam(3, $questionSkills, PDO::PARAM_STR);
            $stmt->bindParam(4, $id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt) {
                $dbh = null;
                return 0;
            }
            $sql = "UPDATE answers SET vote = ? WHERE question_id = ? AND account_id = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $vote, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->bindParam(3, $_SESSION['UserID'], PDO::PARAM_INT);
            $stmt->execute();
            $dbh = null;
            return 1;
        } catch (PDOException $ex) {
            $dbh = null;
            echo $ex;
            return 0;
        }
    }

    static function addQuestion($email, $userId, $questionName, $questionBody, $questionSkills, $vote = 0) {

        $sql = "INSERT INTO `questions` (`owneremail`, `ownerid`, `createddate`, `title`, `body`, `skills`, `score`)        
                VALUES (?,?,SYSDATE(),?,?,?,0) ";

        echo "question: $questionName";
        try {
            $dbh = Database::getConn();
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->bindParam(2, $userId, PDO::PARAM_INT);
            $stmt->bindParam(3, $questionName, PDO::PARAM_STR);
            $stmt->bindParam(4, $questionBody, PDO::PARAM_STR);
            $stmt->bindParam(5, $questionSkills, PDO::PARAM_STR);
            $stmt->execute();

            if (!$stmt) {
                $dbh = null;
                return 0;
            }

            $id = $dbh->lastInsertId();
            ;
            $sql = "INSERT INTO `test`.`answers`
                    (`question_id`,
                    `account_id`,
                    `vote`)
                    VALUES (?,?,0)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $userId, PDO::PARAM_INT);
            $stmt->execute();
            $dbh = null;
            return $id;
        } catch (PDOException $ex) {
            $dbh = null;
            echo $ex;
            return 0;
        }
    }

    static function getQuestion($id) {
        try {
            $dbh = Database::getConn();
            $sql = "SELECT * FROM questions WHERE id = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            if (!$stmt) {
                $dbh = null;
                return 0;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $question = new Question($row);

            $dbh = null;
            return $question;
        } catch (PDOException $ex) {
            $dbh = null;
            echo $ex;
            return 0;
        }
    }

    static function getUserVote($userId, $questionId) {
        try {
            $dbh = Database::getConn();
            $sql = "SELECT vote FROM answers WHERE question_id = ? AND account_id = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $questionId, PDO::PARAM_INT);
            $stmt->bindParam(2, $userId, PDO::PARAM_INT);
            $stmt->execute();
            if (!$stmt) {
                $dbh = null;
                return 0;
            }
            $vote = 0;

            if ($stmt->rowCount()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $vote = $row['vote'];
            }
            $dbh = null;
            return $vote;
        } catch (PDOException $ex) {
            $dbh = null;
            echo $ex;
            return 0;
        }
    }

    static function delQuestion($id) {
        $sql = "DELETE FROM questions WHERE id = ?";

        try {
            $dbh = Database::getConn();
            $stmt = $dbh->prepare($sql);

            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt) {
                $dbh = null;
                return 0;
            }
            $dbh = null;
            return 1;
        } catch (PDOException $ex) {
            $dbh = null;
            echo $ex;
            return 0;
        }
    }

    static function checkDateFormat($date) {
// match the format of the date
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {

// check whether the date is valid or not
            if (checkdate($parts[2], $parts[3], $parts[1])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

?>