<?php

include_once 'Answer.php';

class Question {

    private $email;
    private $id;
    private $accountId;
    private $questionName;
    private $questionBody;
    private $questionSkills;
    private $answers;
    private $userVote;

    public function __construct($row = null) {
        $this->id = $row['id'];
        $this->email = $row['owneremail'];
        $this->userId = $row['ownerid'];
        $this->questionName = $row['title'];
        $this->questionBody = $row['body'];
        $this->questionSkills = $row['skills'];
    }

    function toString() {
        return "{id: $this->id, title: '$this->questionName', skills: '$this->questionSkills', body: '$this->questionSkills'}";
    }

    function newAnswer($dbh, $vote) {
        $sql = "INSERT INTO `answers` (question_id, account_id, vote)        
                VALUES (?,?,SYSDATE(),?,?,?,0) ";

        //echo "question: $questionName";
        try {
            //$dbh = getConn();
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->accountId, PDO::PARAM_INT);
            $stmt->bindParam(3, $vote, PDO::PARAM_INT);

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

    function getId() {
        return $this->id;
    }

    function setUserVote($vote) {
        $this->userVote = $vote;
    }

    function getUserVote() {
        return $this->userVote;
    }

    function setAnswers($db, $userFlag = false) {
        $this->answers = array();
        try {
            $sql = "SELECT a.*, u.lname, u.fname FROM answers a JOIN accounts u ON a.account_id = u.id WHERE question_id = ? ";
            if ($userFlag) {
                $sql .= " AND account_id = ?";
            }

            $dbh = $db::getConn();
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
            if ($userFlag) {
                $stmt->bindParam(2, $this->accountId, PDO::PARAM_INT);
            }
            //  echo $sql . $this->id;
            $stmt->execute();

            if (!$stmt) {
                $dbh = null;
                return 0;
            }

            //  array_push($stack, "apple", "raspberry");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $answer = new Answer($row);

                array_push($this->answers, $answer);
            }

            return $this->answers;
        } catch (PDOException $ex) {
            $dbh = null;
            echo $ex;
            return 0;
        }
    }

    function getAnswers() {
        return $this->answers;
    }

    function getEmail() {
        return $this->email;
    }

    function getUserId() {
        return $this->userId;
    }

    function getQuestionTitle() {
        return $this->questionName;
    }

    function getQuestionBody() {
        return $this->questionBody;
    }

    function getQuestionSkills() {
        return $this->questionSkills;
    }

    function setEmail($email): void {
        $this->email = $email;
    }

    function setUserId($userId): void {
        $this->userId = $userId;
    }

    function setQuestionName($questionName): void {
        $this->questionName = $questionName;
    }

    function setQuestionBody($questionBody): void {
        $this->questionBody = $questionBody;
    }

    function setQuestionSkills($questionSkills): void {
        $this->questionSkills = $questionSkills;
    }

}

?>
