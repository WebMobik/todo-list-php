<?php
$connection = mysqli_connect('localhost', 'root', '', 'todo');
$select_db = mysqli_select_db($connection, 'todo');
