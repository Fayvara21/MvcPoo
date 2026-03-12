<?php

require_once '../core/Database.php';

class Retour
{
    
    public static function create($taskId,$pn,$nb,$sn,$certif)
    {
		$db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO retour (TaskID,PN,nb,sn,certif)
            VALUES (?,?,?,?,?)
        ");

        $stmt->execute([
            $taskId,$pn,$nb,$sn,$certif
        ]);
    }
}
