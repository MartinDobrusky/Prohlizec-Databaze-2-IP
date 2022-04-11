<?php
session_start();
require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Employee";
        $this->loggedUser = $_SESSION["userName"];
    }

    protected function body(): string
    {
        $employeeId = filter_input(INPUT_GET, "employee_id", FILTER_VALIDATE_INT);

        if($_SESSION["loggedIn"]) {
            return $this->m->render(
                "employeeDetail",
                ["employee" => EmployeeModel::getById($employeeId), "room" => EmployeeModel::getRoom($employeeId),"keys" => EmployeeModel::getAllKeys($employeeId)]
            );
        }else {
            header("Location: ../index.php");
            return "";
        }
    }
}

(new Page())->render();