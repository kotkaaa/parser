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

class QueueController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function beforeAction($action)
    {
        // ...set `$this->enableCsrfValidation` here based on some conditions...
        // call parent method that will check CSRF if such property is true.
        if (Yii::$app->request->method == "POST") {
            # code...
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    } 
    
    public function actionAdd()
    {
        if (Yii::$app->request->method == "POST") {
//            $logger = new \yii\log\Logger;
//            $logger->log($_POST, \yii\log\Logger::LEVEL_ERROR);
            $model = new \app\models\Queue;
            $model->setAttribute("title", Yii::$app->request->post("title"));
            $model->setAttribute("url",   Yii::$app->request->post("url"));
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
    
    public function actionDelete()
    {
        $itemID = Yii::$app->request->get('id');
        $queue  = \app\models\Queue::findOne(["id" => $itemID]);
        if ($queue) $queue->deleted = 1;
        if ($queue->save()) {
            $this->asJson(["result" => "success", "message" => "Entry successfully saved!"]);
        } else $this->asJson(["result" => "error", "message" => "Entry not found and can't be saved!"]);
    }
    
    public function actionList()
    {
        $items = \app\models\Queue::find()->where(["deleted" => 0])->all();
        $this->asJson($items);
    }
    
    public function actionView()
    {
        $itemID = Yii::$app->request->get('id');
        $queue  = \app\models\Queue::findOne(["id" => $itemID]);
        $this->asJson($queue);
    }
    
    public function actionCount()
    {
        $conditions = ["deleted" => 0];
        $total = \app\models\Queue::find()->where($conditions)->count();
        $this->asJson(["count" => $total]);
    }
}