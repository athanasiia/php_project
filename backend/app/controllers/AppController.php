<?php
namespace app\controllers;

class AppController
{
    public function index() {
        require VIEWS_PATH . "/users/new.php";
    }
}