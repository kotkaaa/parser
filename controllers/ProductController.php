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

class ProductController extends Controller
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
//            $logger = new \yii\log\Logger;
//            $logger->log($_POST, \yii\log\Logger::LEVEL_INFO);
            # code...
            $this->enableCsrfValidation = 0;
        } 
        return parent::beforeAction($action);
    }

    public function actionList()
    {
        $queue_id = Yii::$app->request->get('queue_id');
        $offset   = Yii::$app->request->get('offset');
        $limit    = Yii::$app->request->get('limit');
        $filters  = Yii::$app->request->post('filters', []);
        $conditions = [
            "processed" => 1, 
            "deleted" => 0
        ];
        $wheretitle = [];
        $wherebrand = [];
        // select from specifiс queue
        if ($queue_id) {
            $conditions["queue_id"] = $queue_id;
        }
        // add filters
        if (!empty($filters)) {
            if (!empty($filters["title"])) {
                $wheretitle = ['like', 'title', $filters['title']];
            }
            if (!empty($filters["brand"])) {
                $wherebrand = ['like', 'brand', $filters["brand"]];
            }
        }
        $products = \app\models\Product::find()
                    ->where($conditions)
                    ->andWhere($wheretitle)
                    ->andWhere($wherebrand)
                    ->offset($offset)->limit($limit)
                    ->all();
        if (!empty($products)) {
            foreach ($products as &$product) {
                $product->images      = !empty($product->images)      ? json_decode($product->images)      : [];
                $product->assortments = !empty($product->assortments) ? json_decode($product->assortments) : [];
                $product->attributes  = !empty($product->attributes)  ? json_decode($product->attributes)  : [];
            }
        }
        $this->asJson($products);
    }
    
    public function actionCount()
    {
        $queue_id   = Yii::$app->request->get('queue_id');
        $filters    = Yii::$app->request->post('filters', []);
        $conditions = [
            "processed" => 1, 
            "deleted" => 0
        ];
        $wheretitle = [];
        $wherebrand = [];
        // select from specifiс queue
        if ($queue_id) {
            $conditions["queue_id"] = $queue_id;
        }
        // add filters
        if (!empty($filters)) {
            if (!empty($filters["title"])) {
                $wheretitle = ['like', 'title', $filters['title']];
            }
            if (!empty($filters["brand"])) {
                $wherebrand = ['like', 'brand', $filters["brand"]];
            }
        }
        $total = \app\models\Product::find()
                ->where($conditions)
                ->andWhere($wheretitle)
                ->andWhere($wherebrand)
                ->count();
        $this->asJson(["count" => $total]);
    }
    
    public function actionView()
    {
        $itemID  = Yii::$app->request->get('id');
        $product = \app\models\Product::findOne(["id" => $itemID]);
        $this->asJson($product);
    }
    
    public function actionDelete()
    {
        $itemID  = Yii::$app->request->get('id');
        $product = \app\models\Product::findOne(["id" => $itemID]);
        if ($product) $product->deleted = 1;
        if ($product->save()) {
            $this->asJson(["result" => "success", "message" => "Entry successfully saved!"]);
        } else $this->asJson(["result" => "error", "message" => "Entry not found and can't be saved!"]);
    }
}
