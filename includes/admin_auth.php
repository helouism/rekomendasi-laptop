<?php

function requireAdmin()
{
    if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {

        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login");
        exit();
    }
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin()
{
    if (!isset($_SESSION['username'])) {

        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login");
        exit();
    }
}