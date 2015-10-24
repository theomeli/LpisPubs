<?php
$link = mysql_connect("webpagesdb.it.auth.gr","theomeli","password"); 

if (!$link) {
    echo '<p>Error connecting to the database <br>';  
    echo 'Please try again.</p>';
    exit(); 
}

$result = mysql_select_db("lpisPubs");

if (!$result) {
    echo '<p>Error selecting database table <br>';  
    echo 'Please try again.</p>';
    exit(); 
}

?>