<?php
session_start();
require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Změna hesla";
        $this->loggedUser = $_SESSION["userName"];
    }

    protected function body(): string
    {
        if(isset($_POST['submit']))
        {
            $userName = $_POST["name"];
            $oldPassword = $_POST["oldPassword"];
            $password = $_POST["password"];

            $sql = "SELECT * FROM `employee`";

            $stmt = DB::getConnection()->prepare($sql);
            $stmt->execute();

            foreach ($stmt as $value) {
                if ($userName == $value->login && password_verify($oldPassword, $value->password)) {
                    $passHash = password_hash($password, PASSWORD_BCRYPT);

                    $sql = "UPDATE employee SET password=:password WHERE employee_id=$value->employee_id";

                    $stmt = DB::getConnection()->prepare($sql);
                    $stmt->bindParam(':password', $passHash);

                    $stmt->execute();

                    header("Location: ../index.php");
                }
            }
        }

        if ($_SESSION["loggedIn"] == true) {
            return $this->m->render(
                "passChange"
            );
        }else {
            return $this->m->render(
                "login"
            );
        }
    }
}

(new Page())->render();