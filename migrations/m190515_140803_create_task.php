<?php

use yii\db\Migration;

/**
 * Class m190515_140803_create_task
 */
class m190515_140803_create_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_140803_create_task cannot be reverted.\n";

        return false;
    }

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('task', [
            'id' => $this->primaryKey(),
            'command' => "ENUM('queue', 'product')",
            'created' => $this->dateTime(),
            'started' => $this->dateTime(),
            'ended' => $this->dateTime(),
            'descr' => $this->string(),
        ], 'ENGINE MyISAM');
        
        $this->createIndex(
            'idx-task-command',
            'task',
            'command'
        );
        
        $this->createIndex(
            'idx-task-started',
            'task',
            'started'
        );
        
        $this->createIndex(
            'idx-task-ended',
            'task',
            'ended'
        );
        
        echo "Table task created.\n";
    }

    public function down()
    {
        $this->dropTable('task');
        
        echo "Table task deleted.\n";
    }
    
}

/**
 * 
CREATE TABLE `task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `command` enum('queue','product') DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `started` datetime DEFAULT NULL,
  `ended` datetime DEFAULT NULL,
  `descr` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_command` (`command`),
  KEY `idx_created` (`created`),
  KEY `idx_started` (`started`),
  KEY `idx_ended` (`ended`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * 
 */