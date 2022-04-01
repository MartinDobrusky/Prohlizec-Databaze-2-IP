<?php
session_start();
require_once "./_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Prohlížeč databáze";
    }

    protected function body(): string
    {
        if(isset($_POST['submit']))
        {
            $userName = $_POST["name"];
            $password = $_POST["password"];

            $sql = "SELECT * FROM `employee`";

            $stmt = DB::getConnection()->prepare($sql);
            $stmt->execute();

            foreach ($stmt as $value) {
                if ($userName == $value->login && password_verify($password, $value->password)) {
                    $_SESSION["userName"] = $userName;
                    $_SESSION["password"] = $password;
                    $_SESSION["loggedIn"] = true;
                    $_SESSION["isAdmin"] = $value->admin;
                }
            }
        }

        if ($_SESSION["loggedIn"] == true) {
            return $this->m->render(
                "index"
            );
        }else {
            return $this->m->render(
                "login"
            );
        }
    }
}

(new Page())->render();