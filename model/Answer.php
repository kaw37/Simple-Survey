<?php

class Answer {

    private $id;
    private $questionId;
    private $accountId;
    private $vote;
    private $userName;

    public function __construct($row = null) {
        $this->id = $row['id'];
        $this->questionId = $row['question_id'];
        $this->accountId = $row['account_id'];
        $this->vote = $row['vote'];
        if (isset($row['lname']))
        {
            $this->userName = $row['lname'].", ".$row['fname'];
        }
    }
    function toString()
    {
        return "{AccountId: $this->accountId, vote: $this->vote}";
    }
    function getUserName(){
        return $this->userName;
    }
    function upVote()
    {
        $this->vote++;
    }
    function downVote()
    {
        $this->vote--;
    }
    function getId() {
        return $this->id;
    }

    function getQuestionId() {
        return $this->questionId;
    }

    function getAccountId() {
        return $this->accountId;
    }

    function getVote() {
        return $this->vote;
    }

    function setId($id): void {
        $this->id = $id;
    }

    function setQuestionId($questionId): void {
        $this->questionId = $questionId;
    }

    function setAccountId($accountId): void {
        $this->accountId = $accountId;
    }

    function setVote($vote): void {
        $this->vote = $vote;
    }


}

?>