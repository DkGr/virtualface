<?php
session_start();

/**
 *
 * @author padman
 */
require __DIR__ . '/../vendor/jacwright/RestServer/source/Jacwright/RestServer/RestServer.php';
require 'WSController.php';
require 'UserController.php';
require 'PostController.php';
require 'CommentController.php';
require 'NotificationController.php';

spl_autoload_register();
$server = new \Jacwright\RestServer\RestServer('debug');
$server->addClass('WSController');
$server->addClass('UserController');
$server->addClass('PostController');
$server->addClass('CommentController');
$server->addClass('NotificationController');
$server->handle();

