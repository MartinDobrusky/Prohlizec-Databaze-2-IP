<?php
class RoomModel
{
    public ?int $room_id;
    public string $name = "";
    public ?int $no = null;
    public ?int $phone = null;

    private array $validationErrors = [];

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct()
    {
    }

    public function insert() : bool {

        $sql = "INSERT INTO room (name, no, phone) VALUES (:name, :no, :phone)";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':no', $this->no);
        $stmt->bindParam(':phone', $this->phone);

        return $stmt->execute();
    }

    public function update() : bool
    {
        $sql = "UPDATE room SET name=:name, no=:no, phone=:phone WHERE room_id=:room_id";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':room_id', $this->room_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':no', $this->no);
        $stmt->bindParam(':phone', $this->phone);

        return $stmt->execute();
    }

    public static function getById($roomId) : ?self
    {
        $stmt = DB::getConnection()->prepare("SELECT *, r.name AS roomName FROM `room` AS r WHERE `room_id`=:room_id");
        $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();

        $record = $stmt->fetch();

        if (!$record)
            return null;

        $model = new self();
        $model->room_id = $record->room_id;
        $model->name = $record->name;
        $model->no = $record->no;
        $model->phone = $record->phone;
        return $model;
    }

    public static function getAll($orderBy = "name", $orderDir = "ASC") : PDOStatement
    {
        $stmt = DB::getConnection()->prepare("SELECT * FROM `room` ORDER BY `{$orderBy}` {$orderDir}");
        $stmt->execute();
        return $stmt;
    }

    public static function deleteById(int $room_id) : int
    {
        $canDelete = true;

        $sql4 = "SELECT * FROM `employee`";

        $stmt4 = DB::getConnection()->prepare($sql4);
        $stmt4->execute();

        foreach ($stmt4 as $value) {
            if ($value->room == $room_id) {
                $canDelete = false;
            }
        }

        if ($canDelete) {
            $sql = "DELETE FROM room WHERE room_id=:room_id";

            $stmt = DB::getConnection()->prepare($sql);
            $stmt->bindParam(':room_id', $room_id);

            $sql3 = "DELETE FROM `key` WHERE room=:room_id";

            $stmt3 = DB::getConnection()->prepare($sql3);
            $stmt3->bindParam(':room_id', $room_id);
            $stmt3->execute();

            return $stmt->execute();
        }else {
            return 3;
        }
    }

    public static function getFromPost() : self {
        $room = new RoomModel();

        $room->room_id = filter_input(INPUT_POST, "room_id", FILTER_VALIDATE_INT);
        $room->name = filter_input(INPUT_POST, "name");
        $room->no = filter_input(INPUT_POST, "no");
        $room->phone = filter_input(INPUT_POST, "phone");

        return $room;
    }

    public function validate() : bool {
        $isOk = true;
        $errors = [];

        if (!$this->name){
            $isOk = false;
            $errors["name"] = "Room name cannot be empty";
        }

        if (!$this->no){
            $isOk = false;
            $errors["no"] = "Room number cannot be empty";
        }

        $this->validationErrors = $errors;
        return $isOk;
    }
}
