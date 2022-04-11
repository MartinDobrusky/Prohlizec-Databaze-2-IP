<?php
session_start();
require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Room";
        $this->loggedUser = $_SESSION["userName"];
    }

    protected function body(): string
    {
        $roomId = filter_input(INPUT_GET, "room_id", FILTER_VALIDATE_INT);

        if ($_SESSION["loggedIn"]) {
            return $this->m->render(
                "roomDetail",
                ["room" => RoomModel::getById($roomId), "employee" => EmployeeModel::getAllFromRoom($roomId), "salary" => EmployeeModel::avgSalary($roomId), "key" => EmployeeModel::getAllWithKey($roomId)]
            );
        }else {
            header("Location: ../index.php");
            return "";
        }
    }
}

(new Page())->render();