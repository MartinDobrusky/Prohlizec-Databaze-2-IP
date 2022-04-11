<?php
session_start();
require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Employee listing";
        $this->loggedUser = $_SESSION["userName"];
    }

    protected function body(): string
    {
        if ($_SESSION["loggedIn"]) {
            return $this->m->render(
                "employeeList",
                ["rooms" => EmployeeModel::getAll(), "employeeDetailName" => "employeeDetail.php", "isAdmin" => $_SESSION["isAdmin"]]
            );
        }else {
            header("Location: ../index.php");
            return "";
        }
    }
}

(new Page())->render();
