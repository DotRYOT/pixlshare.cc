<?php
session_start();

require "../../../backend/_include.php";

if (!isset($_GET['key']) || !isset($_GET['uuid']) || empty($_GET['key']) || empty($_GET['uuid'])) {
  $Error = generateErrorUrl("Missing Keys!");
  redirectTo("/account/forgot/$Error");
}

$key = filter_user_input($_GET['key'], 'int');

$UUID = filter_user_input($_GET['uuid'], 'string');
