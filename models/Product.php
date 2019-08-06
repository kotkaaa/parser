<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $queue_id
 * @property string $sku
 * @property string $url
 * @property string $title
 * @property string $descr
 * @property string $fulldescr
 * @property string $brand
 * @property string $series
 * @property string $images Serialized array
 * @property string $attributes Serialized array
 * @property string $assortments Serialized array
 * @property int $deleted
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['queue_id', 'processed', 'deleted'], 'integer'],
            [['fulldescr', 'images', 'attributes', 'assortments'], 'string'],
            [['url', 'title', 'descr', 'brand', 'series'], 'string', 'max' => 255],
            [['sku'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'queue_id' => 'Queue ID',
            'sku' => 'Sku',
            'url' => 'Url',
            'title' => 'Title',
            'descr' => 'Descr',
            'fulldescr' => 'Fulldescr',
            'brand' => 'Brand',
            'series' => 'Series',
            'images' => 'Serialized array',
            'attributes' => 'Serialized array',
            'assortments' => 'Serialized array',
            'processed' => 'Processed',
            'deleted' => 'Deleted',
        ];
    }
}