<?php
session_start();
class LoginModel
{
    public ?string $name = "";
    public ?bool $loggedIn= false;
    public ?bool $adminLoggedIn= false;

    const STATE_DATA_SENT = 1;
    private int $state;

    private array $validationErrors = [];

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct()
    {
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getState();

        if ($this->state === self::STATE_DATA_SENT) {
            echo "Ano";
        } else {

        }
    }

    private function getState() : void {
        $action = filter_input(INPUT_POST, "action");
        if ($action === "update") {
            $this->state = self::STATE_DATA_SENT;
            return;
        }

        $this->state = self::STATE_FORM_REQUESTED;
    }

    private function redirect(int $result) : void {
        $location = strtok($_SERVER['REQUEST_URI'], '?');

        header("Location: {$location}?{$result}");
        exit;
    }

    public static function getLogin() : self
    {
        return $login;
    }
}