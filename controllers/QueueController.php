<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Queue;
use app\models\Product;
use app\controllers\PreControllerTrait;

class QueueController extends Controller
{
    
    use PreControllerTrait;
    
    public function actionAdd()
    {
        if (Yii::$app->request->method == "POST") {
            $model = new Queue;
            $model->load(Yii::$app->request->post());
            $model->setAttribute("hash",  md5(Yii::$app->request->post("url")));
            $model->setAttribute("created", date("Y-m-d H:i:s"));
            if ($model->save()) {
                $this->asJson(["result" => "success", "message" => "New queue added!!!"]);
            } else {
                $this->asJson(["result" => "error", "message" => "Queue couldn't be saved!!!"]);
            }
        } else {
            $this->asJson(["result" => "error", "message" => "Only POST requests!!!"]);
        }
    }
    
    public function actionEdit()
    {
        if (!Yii::$app->request->isPost) {
            $this->asJson(["result" => "error", "message" => "Only POST requests!!!"]);
        }
        
        $itemID = Yii::$app->request->get('id');
        $queue  = Queue::findOne(["id" => $itemID]);
        
        if (!$queue) {
            $this->asJson(["result" => "error", "message" => "Queue not found!!!"]);
        }
        
        $queue->load(Yii::$app->request->post());
        
        if ($queue->save()) {
            $this->asJson(["result" => "success", "message" => "New queue added!!!"]);
        } else {
            $this->asJson(["result" => "error", "message" => "Queue can't be saved!!!"]);
        }
    }
    
    public function actionDelete()
    {
        $itemID = Yii::$app->request->get('id');
        $queue  = Queue::findOne(["id" => $itemID]);
        if ($queue) $queue->deleted = 1;
        if ($queue->save()) {
            $this->asJson(["result" => "success", "message" => "Entry successfully saved!"]);
        } else {
            $this->asJson(["result" => "error", "message" => "Entry not found and can't be saved!"]);
        }
    }
    
    public function actionList()
    {
        $items = Queue::find()->where(["deleted" => 0])->all();
        $this->asJson($items);
    }
    
    public function actionView()
    {
        $itemID = Yii::$app->request->get('id');
        $queue  = Queue::findOne(["id" => $itemID]);
        $this->asJson($queue);
    }
    
    public function actionCount()
    {
        $conditions = ["deleted" => 0];
        $total = Queue::find()->where($conditions)->count();
        $this->asJson(["count" => $total]);
    }
}