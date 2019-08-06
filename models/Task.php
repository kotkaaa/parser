<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property string $command
 * @property string $created
 * @property string $started
 * @property string $ended
 * @property string $descr
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['command'], 'string'],
            [['created', 'started', 'ended'], 'safe'],
            [['descr'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'command' => 'Command',
            'created' => 'Created',
            'started' => 'Started',
            'ended' => 'Ended',
            'descr' => 'Descr',
        ];
    }
}
