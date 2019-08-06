<?php

use yii\db\Migration;

/**
 * Class m190515_140749_create_queue
 */
class m190515_140749_create_queue extends Migration
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
        echo "m190515_140749_create_queue cannot be reverted.\n";

        return false;
    }

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('queue', [
            'id' => $this->primaryKey(),
            'url' => $this->string()->notNull()->defaultValue(''),
            'title' => $this->string(),
            'hash' => $this->string(),
            'cnt' => $this->integer(),
            'processed' => $this->integer(1)->unsigned()->notNull()->defaultValue(0),
            'deleted' => $this->integer(1)->unsigned()->notNull()->defaultValue(0),
            'created' => $this->dateTime(),
            'modified' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ], 'ENGINE MyISAM');
        
        $this->createIndex(
            'idx-queue-processed',
            'queue',
            'processed'
        );
        
        $this->createIndex(
            'idx-queue-deleted',
            'queue',
            'deleted'
        );
        
        $this->createIndex(
            'idx-queue-created',
            'queue',
            'created'
        );
        
        echo "Table queue created.\n";
    }

    public function down()
    {
        $this->dropTable('queue');
        
        echo "Table queue deleted.\n";
    }
    
}

/**
 * 
CREATE TABLE `queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `hash` varchar(255) NOT NULL DEFAULT '',
  `cnt` int(11) unsigned NOT NULL DEFAULT '0',
  `processed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_processed` (`processed`),
  KEY `idx_deleted` (`deleted`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
 * 
 */