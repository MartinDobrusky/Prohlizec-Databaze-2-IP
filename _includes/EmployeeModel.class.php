<?php

class EmployeeModel
{
    public ?int $employee_id;
    public string $name = "";
    public string $surname = "";
    public ?string $job = "";
    public string $wage = "";
    public ?int $room = null;
    public ?array $keys;

    private array $validationErrors = [];

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct()
    {
    }

    public function insert() : bool {

        $sql = "INSERT INTO employee (name, surname, job, wage, room) VALUES (:name, :surname, :job, :wage, :room)";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);

        $stmt->execute();

        $sql2 = "SELECT `employee_id` FROM `employee` WHERE name=:name AND surname=:surname";

        $stmt2 = DB::getConnection()->prepare($sql2);
        $stmt2->bindParam(':name', $this->name);
        $stmt2->bindParam(':surname', $this->surname);

        $stmt2->execute();

        $emp_id = $stmt2->fetch();

        foreach ($this->keys as $value) {
            $stmt3 = DB::getConnection()->prepare("INSERT INTO `key` (employee, room) VALUES (:employee_id, :room_id)");
            $stmt3->bindParam(':employee_id', $emp_id->employee_id);
            $stmt3->bindParam(':room_id', $value);

            $stmt3->execute();
        }

        return true;
    }

    public function update() : bool
    {
        $sql = "UPDATE employee SET name=:name, surname=:surname, job=:job, wage=:wage, room=:room WHERE employee_id=:employee_id";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':employee_id', $this->employee_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);


        $stmt2 = DB::getConnection()->prepare("DELETE FROM `key` WHERE `employee`=:employee_id");
        $stmt2->bindParam(':employee_id', $this->employee_id);
        $stmt2->execute();

        foreach ($this->keys as $value) {
            $stmt3 = DB::getConnection()->prepare("INSERT INTO `key` (employee, room) VALUES (:employee_id, :room_id)");
            $stmt3->bindParam(':employee_id', $this->employee_id);
            $stmt3->bindParam(':room_id', $value);
            $stmt3->execute();
        }

        return $stmt->execute();
    }

    public static function getById($roomId) : ?self
    {
        $stmt = DB::getConnection()->prepare("SELECT * FROM `employee` WHERE `employee_id`=:employee_id");
        $stmt->bindParam(':employee_id', $roomId);
        $stmt->execute();

        $record = $stmt->fetch();

        if (!$record)
            return null;

        $model = new self();
        $model->employee_id = $record->employee_id;
        $model->name = $record->name;
        $model->surname = $record->surname;
        $model->job = $record->job;
        $model->wage = $record->wage;
        $model->room = $record->room;
        return $model;
    }

    public static function getAll($orderBy = "name", $orderDir = "ASC") : PDOStatement
    {
        $stmt = DB::getConnection()->prepare("SELECT *, e.name AS empName FROM employee AS e INNER JOIN room ON e.room = room.room_id");
        $stmt->execute();
        return $stmt;
    }

    public static function getAllFromRoom(int $roomId, $orderBy = "name", $orderDir = "ASC") : PDOStatement
    {
        $stmt = DB::getConnection()->prepare("SELECT * FROM `employee` WHERE `room`=$roomId ORDER BY `{$orderBy}` {$orderDir}");
        $stmt->execute();
        return $stmt;
    }

    public static function getAllWithKey(int $roomId, $orderBy = "name", $orderDir = "ASC") : PDOStatement
    {
        $stmt = DB::getConnection()->prepare("SELECT * FROM `key` AS k INNER JOIN employee AS e ON(k.employee=e.employee_id) WHERE k.room=$roomId ORDER BY `{$orderBy}` {$orderDir}");
        $stmt->execute();

        return $stmt;
    }

    public static function getAllKeys(int $employeeId) : array
    {
        $stmt = DB::getConnection()->prepare("SELECT * FROM `key` WHERE employee=$employeeId");
        $stmt->execute();

        $stmt2 = DB::getConnection()->prepare("SELECT * FROM room");
        $stmt2->execute();

        $stmta = $stmt->fetchAll();
        $stmtb = $stmt2->fetchAll();

        $stack = array();


        foreach ($stmta as $val1) {
            foreach ($stmtb as $val2) {
                if ($val2->room_id == $val1->room){
                    $stack[] = $val2->name;
                }
            }
        }

        return $stack;
    }

    public static function avgSalary(int $roomId) : float
    {
        $stmt = DB::getConnection()->prepare("SELECT wage FROM `employee` WHERE `room`=$roomId");
        $stmt->execute();
        $salarySum = 0;
        $num = 0;

        foreach ($stmt as $value) {
            $salarySum += $value->wage;
            $num++;
        }

        if($num > 0) {
            $avgSalary = $salarySum / $num;
        }else {
            $avgSalary = 0;
        }

        return $avgSalary;
    }

    public static function getRoom(int $employeeId) : PDOStatement
    {
        $stmt = DB::getConnection()->prepare("SELECT * FROM `employee` AS e INNER JOIN room AS r ON(e.room=r.room_id) WHERE e.employee_id=$employeeId");
        $stmt->execute();

        return $stmt;
    }

    public static function deleteById(int $employee_id) : bool
    {
        $sql = "DELETE FROM employee WHERE employee_id=:employee_id";

        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':employee_id', $employee_id);

        $sql2 = "DELETE FROM `key` WHERE employee=:employee_id";

        $stmt2 = DB::getConnection()->prepare($sql2);
        $stmt2->bindParam(':employee_id', $employee_id);
        $stmt2->execute();

        return $stmt->execute();
    }

    public function delete() : bool
    {
        return self::deleteById($this->employee_id);
    }

    public static function getFromPost() : self {
        $employeeObj = new EmployeeModel();

        $employeeObj->employee_id = filter_input(INPUT_POST, "employee_id", FILTER_VALIDATE_INT);
        $employeeObj->name = filter_input(INPUT_POST, "name");
        $employeeObj->surname = filter_input(INPUT_POST, "surname");
        $employeeObj->job = filter_input(INPUT_POST, "job");
        $employeeObj->wage = filter_input(INPUT_POST, "wage");
        $employeeObj->room = filter_input(INPUT_POST, "room");
        $employeeObj->keys = $_POST["keys"];

        return $employeeObj;
    }

    public function validate() : bool {
        $isOk = true;
        $errors = [];

        if (!$this->name){
            $isOk = false;
            $errors["name"] = "Employee name cannot be empty";
        }

        if (!$this->surname){
            $isOk = false;
            $errors["surname"] = "Employee surname cannot be empty";
        }
        if ($this->job === ""){
            $this->job = null;
        }

        $this->validationErrors = $errors;
        return $isOk;
    }
}
