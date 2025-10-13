<?php
session_start();

if (isset($_SESSION["user"]["UUID"])) {
  header("Location: ./home/");
  exit;
} else {
  header("Location: ./welcome/");
  exit;
}