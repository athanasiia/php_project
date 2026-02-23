<?php
namespace app\controllers;

class AppController
{
    public function index() : void
    {
        require VIEWS_PATH . "/users/new.php";
    }
}