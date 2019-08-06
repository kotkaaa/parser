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
use app\models\Product;
use darkdrim\simplehtmldom\SimpleHTMLDom as SHD;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ProductController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    private function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }
    
    public function beforeAction($action)
    {
        $task = \app\models\Task::findBySql("SELECT * FROM `task` WHERE `command`=:command AND (DATE_ADD(`started`, INTERVAL 1 HOUR) > NOW()) AND `ended` IS NULL", ["command"=>"queue"])->one();
        if ($task) {
            echo "You have an undone task!!!\n";
            return false;
        } else {
            $task = new \app\models\Task;
            $task->setAttributes([
                "command" => "product",
                "created" => date("Y-m-d H:i:s"),
                "started" => date("Y-m-d H:i:s"),
            ]);
            $task->save();
            echo "New task was added and started!!!\n";
        }
        return parent::beforeAction($action);
    }
    
    public function actionProcess()
    {
        $products = \app\models\Product::find()->where(["processed"=>0, "deleted"=>0])->limit(50)->all();
        if ($products) {
            foreach ($products as $product) {
                self::processProduct($product);
                sleep(1);
            }
        }
        $task = \app\models\Task::findBySql("SELECT * FROM `task` WHERE `command`=:command AND (DATE_ADD(`started`, INTERVAL 5 MINUTE) > NOW()) AND `ended` IS NULL", ["command"=>"product"])->one();
        if ($task) {
            $task->setAttribute("ended", date("Y-m-d H:i:s"));
            $task->save();
            echo "Task is done!!!\n";
        }
    }
    
    private static function processProduct(\app\models\Product $product)
    {
        $html_source = SHD::file_get_html($product->url);
        if ($html_source) {
            $metadata = $html_source->find("div[itemtype=\"http://schema.org/Product\"]", 0);
            // get data from metadata
            if ($metadata) {
                // sku
                $sku = $metadata->find("meta[itemprop=\"sku\"]", 0);
                if ($sku) $product->sku = trim($sku->content);
                unset($sku);
                // title
                $name = $metadata->find("meta[itemprop=\"name\"]", 0);
                if ($name) $product->title = trim($name->content);
                unset($name);
                // fulldescr
                $description = $metadata->find("meta[itemprop=\"description\"]", 0);
                if ($description) $product->fulldescr = trim($description->content);
                unset($description);
            } unset($metadata);
            // get data from html
            if (empty($product->sku)) {
                $sku = $html_source->find(".ty-product-block__sku", 0);
                if ($sku) {
                    $sku = $sku->find(".ty-control-group__item", 0);
                    if ($sku) $product->sku = trim($sku->plaintext);
                } unset($sku);
            }
            // title
            if (empty($product->title)) {
                $title = $html_source->find(".ty-product-block-title", 0);
                if ($title) {
                    $product->title = trim($title->plaintext);
                } unset($title);
            }
            // fulldescr
            if (empty($product->fulldescr)) {
                $description = $html_source->find("#content_description", 0);
                if ($description) {
                    $description = $description->children(0);
                    if ($description) {
                        $product->fulldescr = $description->innertext;
                    }
                } unset($description);
            }
            // images
            $images = $html_source->find(".ty-product-img", 0);
            if ($images) {
                $images = $images->find("a[id^=\"det_img_link_\"]");
                $arImages = [];
                if ($images) {
                    foreach ($images as $image) {
                        $arImages[] = $image->href;
                    }
                }
                if (!empty($arImages)) {
                    $product->images = json_encode($arImages);
                }
                unset($arImages);
                unset($images);
            }
            // attributes
            $attributes = $html_source->find(".content-features", 0);
            if ($attributes) {
                $arrAttributes = [];
                foreach ($attributes->find(".ty-product-feature") as $i=>$attribute) {
                    // brand (first in list)
                    if ($i==0) {
                        $valueItem = $attribute->find(".ty-product-feature__value", 0);
                        if ($valueItem) {
                            $product->brand = trim($valueItem->plaintext);
                            unset($valueItem);
                        } 
                    } 
                    // skip category
                    elseif ($i==1) continue;
                    // attibutes
                    else {
                        $key = $attribute->find(".ty-product-feature__label", 0);
                        $key = $key ? rtrim(trim($key->plaintext), ":") : "";
                        $val = [];
                        foreach ($attribute->find(".ty-product-feature__multiple-item") as $valueItem) {
                            $val[] = trim($valueItem->plaintext);
                        }
                        if (!empty($key) and !empty($val)) {
                            $arrAttributes[$key] = $val;
                        }
                    }
                }
                $product->attributes = !empty($arrAttributes) ? json_encode($arrAttributes) : "";
            }
            // assortments
            $assortments = $html_source->find(".ty-product-block__option", 0);
            if ($assortments) {
                $arrAssortments = [];
                $prototype = [
                    "type_id" => 1,
                    "sku" => "",
                    "value" => "",
                    "color" => "",
                    "thumbnail" => "",
                ];
                $select = $assortments->find("select", 0);
                $colors = $assortments->find(".ty-product-variant-image", 0);
                $options = $assortments->find(".ty-product-options__item", 0);
                if ($colors) {
                    $prototype["type_id"] = 2;
                }
                if ($select) {
                    foreach ($select->find("option") as $option) {
                        $assortment = $prototype;
                        $val = trim($option->value);
                        $txt = trim($option->plaintext);
                        // set assortment sku
                        $assortment["sku"] = $val;
                        // set assortment color preview
                        if ($colors) {
                            // set assortment color value
                            $assortment["color"] = $txt;
                            // set assortment thumbnail
                            foreach ($colors->find(".ty-pict") as $color) {
                                $color_id = trim($color->id);
                                $src = trim($color->src);
                                if (!empty($src) and preg_match("/_$val$/", $color_id)) {
                                    $assortment["thumbnail"] = $src;
                                    break;
                                }
                            }
                        } else {
                            // set assortment value
                            $assortment["value"] = $txt;
                        }
                        // add assortment
                        $arrAssortments[] = $assortment;
                    }
                }
                elseif ($options) {
                    foreach ($options->find(".option-items") as $option) {
                        $assortment = $prototype;
                        $radio = $option->find("input", 0);
                        $val = trim($radio->value);
                        $txt = trim($option->plaintext);
                        // set assortment sku
                        $assortment["sku"] = $val;
                        // set assortment color preview
                        if ($colors) {
                            // set assortment color value
                            $assortment["color"] = $txt;
                            // set assortment thumbnail
                            foreach ($colors->find(".ty-pict") as $color) {
                                $color_id = trim($color->id);
                                $src = trim($color->src);
                                if (!empty($src) and preg_match("/_$val$/", $color_id)) {
                                    $assortment["thumbnail"] = $src;
                                    break;
                                }
                            }
                        } else {
                            // set assortment value
                            $assortment["value"] = $txt;
                        }
                        // add assortment
                        $arrAssortments[] = $assortment;
                    }
                }
                // stringify array
                $product->assortments = !empty($arrAssortments) ? json_encode($arrAssortments) : "";
            }
            // mark product as processed
            $product->setAttribute("processed", 1);
            // free memory
            $html_source->clear();
        } $product->save();
    }
}
