<?php

require_once "../_includes/bootstrap.inc.php";

final class DeleteRoom extends BaseDBPage{

    const STATE_REPORT_RESULT = 3;
    const STATE_DELETE_REQUESTED = 4;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private int $state;
    private int $result;

    public function __construct()
    {
        parent::__construct();
        $this->title = "Room delete";
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getState();

        if ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                $this->title = "Room deleted";
            } else {
                $this->title = "Room deletion failed";
            }
            return;
        }

        if ($this->state === self::STATE_DELETE_REQUESTED) {
            $roomId = filter_input(INPUT_POST, "room_id", FILTER_VALIDATE_INT);
            if ($roomId){
                if (RoomModel::deleteById($roomId)) {
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    $this->redirect(self::RESULT_FAIL);
                }
            } else {
                throw new RequestException(400);
            }

        }
    }

    protected function body(): string {
        if ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                return $this->m->render("reportSuccess", ["data"=>"Room deleted successfully", "where"=>"room list"]);
            } else {
                return $this->m->render("reportFail", ["data"=>"Room deletion failed. Please contact adiministrator or try again later.", "where"=>"room list"]);
            }
        }
        return "";
    }

    private function getState() : void {
        $result = filter_input(INPUT_GET, "result", FILTER_VALIDATE_INT);

        if ($result === self::RESULT_SUCCESS) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_SUCCESS;
            return;
        } elseif ($result === self::RESULT_FAIL) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_FAIL;
            return;
        }

        $this->state = self::STATE_DELETE_REQUESTED;
    }

    private function redirect(int $result) : void {
        $location = strtok($_SERVER['REQUEST_URI'], '?');

        header("Location: {$location}?result={$result}");
        exit;
    }
}

(new DeleteRoom())->render();
