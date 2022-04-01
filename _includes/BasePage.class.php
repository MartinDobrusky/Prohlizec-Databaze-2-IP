<?php

abstract class BasePage
{
    protected MustacheRunner $m;
    public string $title;

    public function __construct()
    {
        $this->m = new MustacheRunner();
    }

    public function render() : void {
        try {
            $this->setUp();

            $html = $this->header();
            $html .= $this->body();
            $html .= $this->footer();
            echo $html;

            $this->wrapUp();
            exit;
        } catch (RequestException $e) {
            $ePage = new ErrorPage($e->getCode());
            $ePage->render();
        }
        catch (Exception $e) {
            if (LocalConfig::DEBUG) {
                dump($e);
            } else {
                $ePage = new ErrorPage();
                $ePage->render();
            }
        }
    }

    protected function setUp() : void {}

    protected function header() : string
    {
        return $this->m->render("head", ["title" => $this->title]);
    }

    abstract protected function body() : string;

    protected function footer() : string
    {
        return  $this->m->render("foot");
    }

    protected function wrapUp() : void {}
}