<?php
class db
{
    public function connect()
    {
        $host = "127.0.0.1";
        $user = "root";
        $pass = "Vikas@219";
        $dbname = "codechef_comments";

        //connect database using php pdo wrapper 
        // $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Create a MySQLi connection
        $mysqli = new mysqli($host, $user, $pass, $dbname);

        // Check the connection
        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }
        return $mysqli;
    }
}

