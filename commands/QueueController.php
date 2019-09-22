<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Queue;
use app\models\Task;
use app\models\Product;
use darkdrim\simplehtmldom\SimpleHTMLDom as SHD;
use app\commands\PreControllerTrait;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class QueueController extends Controller
{
    
    use PreControllerTrait;
    
    public function actionProcess()
    {
        $queue = Queue::findOne(["processed" => 0]);
        if ($queue and self::processQueue($queue, $queue->url)) {
            $queue->setAttribute("processed", 1);
            $queue->save();
        }
        // close task
        $task = Task::findBySql("SELECT * FROM `task` WHERE `command`=:command AND `ended` IS NULL", ["command"=>"queue"])->one();
        if ($task) {
            $task->setAttribute("ended", date("Y-m-d H:i:s"));
            $task->save();
            echo "Task is done!!!\n";
        }
    }
    
    private static function processQueue(Queue $queue, $url)
    {
        $html_source = SHD::file_get_html($url);
        $page_link_next = $html_source->find(".ty-pagination__right-arrow", 0);
        if ($page_link_next) {
            $page_link_next = $page_link_next->href;
        }
        if ($html_source) {
            $list = $html_source->find(".grid-list", 0);
            if ($list) {
                $products = $list->find(".ty-column4");
                foreach ($products as $product_item) {
                    $list = $product_item->find(".ty-grid-list__image", 0);
                    if (!$list) continue;
                    $a = $list->find("a", 0);
                    if ($a) {
                        $product = Product::findOne(["url" => $a->href]);
                        if (!$product) {
                            $product = new Product();
                            $product->setAttribute("queue_id", $queue->id);
                            $product->setAttribute("url", $a->href);
                            $product->save();
                        } 
                        unset($a);
                        unset($list);
                        unset($product);
                    }
                } 
                unset($products);
                unset($list);
            } 
            $html_source->clear();
        } 
        unset($html_source);
        if ($page_link_next) {
            self::processQueue($queue, $page_link_next);
        } return true;
    }
}
