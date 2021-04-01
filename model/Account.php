<?php

class Account {

    private $id;
    private $username;
    private $email;
    private $lname;
    private $fname;
    private $birthday;
    private $password;
    private $roles;
    public function __construct($row = null) {
        $this->id = $row['user_id'];
        $this->username = $row['username'];
        $this->email = $row['email'];
        $this->fname = $row['firstname'];
        $this->lname = $row['lastname'];
     //   echo " new account: ". $this->lname;
        //$this->birthday = $row['birthday'];
    }
    function getBirthday() {
        return $this->birthday;
    }

    function getPassword() {
        return $this->password;
    }

    function setBirthday($birthday): void {
        $this->birthday = $birthday;
    }

    function setPassword($password): void {
        $this->password = $password;
    }

        function getId() {
        return $this->id;
    }

    function getEmail() {
        return $this->email;
    }
    function getUsername() {
        return $this->username;
    }

    function setUsername($userName) {
        $this->userName=$userName;
    }
    function getLastname() {
        return $this->lname;
    }

    function getFirstname() {
        return $this->fname;
    }

    function setId($id): void {
        $this->id = $id;
    }

    function setEmail($email): void {
        $this->email = $email;
    }

    function setLastname($lname): void {
        $this->lname = $lname;
    }

    function setFirstname($fname): void {
        $this->fname = $fname;
    }
    function setRoles ($roles){
        
    }
    function toString ()
    {
        $str = "{userId: ".$this->id. ", userName: ". $this->username;
        $roleList="";
        foreach($this->roles as $role) {
            $roleList .= ($roleList==""?"":",") . $role;
        }
        $str .= ",roles: [$roleList]}";
        
        return $str;
    }
}

?>
