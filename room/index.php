<?php
session_start();

require_once "../_includes/bootstrap.inc.php";

final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Room listing";
    }

    protected function body(): string
    {
        if ($_SESSION["loggedIn"]) {
            return $this->m->render(
                "roomList",
                ["rooms" => RoomModel::getAll(), "roomDetailName" => "roomDetail.php", "admin" => $_SESSION["isAdmin"]]
            );
        }else {
            header("Location: ../index.php");
            return "";
        }
    }
}

(new Page())->render();
