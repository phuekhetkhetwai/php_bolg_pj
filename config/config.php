<?php

$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,];

$db = new PDO("mysql:dbhost=localhost;dbname=post","root","", $options);