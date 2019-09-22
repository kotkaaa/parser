<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\commands;

use app\models\Task;

/**
 *
 * @author oleksandr
 */
trait PreControllerTrait {
    //put your code here
    
    private function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }
    
    public function beforeAction($action)
    {
        $task = Task::findBySql("SELECT * FROM `task` WHERE `command`=:command AND (DATE_ADD(`started`, INTERVAL 1 HOUR) > NOW()) AND `ended` IS NULL", ["command"=>"queue"])->one();
        if ($task) {
            echo "You have an undone task!!!\n";
            return false;
        } else {
            $task = new Task;
            $task->setAttributes([
                "command" => "queue",
                "created" => date("Y-m-d H:i:s"),
                "started" => date("Y-m-d H:i:s"),
            ]);
            $task->save();
            echo "New task was added and started!!!\n";
        }
        return parent::beforeAction($action);
    }
}
