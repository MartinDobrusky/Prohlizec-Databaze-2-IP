<?php

class MustacheRunner
{
    private Mustache_Engine $engine;

    public function __construct()
    {
        $this->engine = new Mustache_Engine([
            'entity_flags' => ENT_QUOTES,
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . "/../templates")
        ]);
    }

    public function render($templateName, $context = []) : string
    {
        return $this->engine->loadTemplate($templateName)->render($context);
    }

}