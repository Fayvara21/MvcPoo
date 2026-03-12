<?php

require_once '../core/Database.php';

class Appro
{
    private $db;

    public static function create($taskId,$pn,$nb,$designation,$of,$location,$plane,$oe)
    {
		$db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO appro (TaskID,pn,nb,designation,of,location,plane,oe)
            VALUES (?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $taskId,$pn,$nb,$designation,$of,$location,$plane,$oe
        ]);
    }
}
