<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "queue".
 *
 * @property int $id
 * @property string $title
 * @property string $url
 * @property string $hash
 * @property int $cnt
 * @property int $processed
 * @property int $deleted
 * @property string $created
 * @property string $modified
 */
class Queue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'queue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cnt', 'processed', 'deleted'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['title', 'url', 'hash'], 'string', 'max' => 255],
            [['hash'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'url' => 'Url',
            'hash' => 'Hash',
            'cnt' => 'Cnt',
            'processed' => 'Processed',
            'deleted' => 'Deleted',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
}
