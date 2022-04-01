<?php

class ErrorPage extends BasePage
{
    private int $httpErrorCode;

    public function __construct(int $httpErrorCode = 500)
    {
        parent::__construct();
        $this->httpErrorCode = $httpErrorCode;
        $this->title = "Error {$this->httpErrorCode}";
    }

    protected function setUp(): void
    {
        http_response_code($this->httpErrorCode);
        parent::setUp();
    }

    protected function body(): string
    {
        switch ($this->httpErrorCode) {
            case 400:
                return "<h1>Error 400: Bad request</h1>";
            case 404:
                return "<h1>Error 404: Not found</h1>";
            case 500:
                return "<h1>Error 500: internal server error</h1>";
            default:
                return "<h1>Error 400</h1>";
        }
    }
}