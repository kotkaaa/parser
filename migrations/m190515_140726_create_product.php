<?php

use yii\db\Migration;

/**
 * Class m190515_140726_create_product
 */
class m190515_140726_create_product extends Migration
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
        echo "m190515_140726_create_product cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->createTable('product', [
            'id' => $this->primaryKey(),
            'queue_id' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'sku' => $this->string(),
            'url' => $this->string()->notNull()->defaultValue(''),
            'title' => $this->string(),
            'descr' => $this->string(),
            'fulldescr' => $this->text(),
            'brand' => $this->string(),
            'series' => $this->string(),
            'images' => $this->text()->comment('Serialized array'),
            'attributes' => $this->text()->comment('Serialized array'),
            'assortments' => $this->text()->comment('Serialized array'),
            'processed' => $this->integer(1)->unsigned()->notNull()->defaultValue(0),
            'deleted' => $this->integer(1)->unsigned()->notNull()->defaultValue(0),
        ], 'ENGINE MyISAM');
        
        $this->createIndex(
            'idx-product-queue_id',
            'product',
            'queue_id'
        );
        
        $this->createIndex(
            'idx-product-url',
            'product',
            'url'
        );
        
        $this->createIndex(
            'idx-product-deleted',
            'product',
            'deleted'
        );
        
        $this->createIndex(
            'idx-product-processed',
            'product',
            'processed'
        );
        
        echo "Table product created.\n";
    }

    public function down()
    {
        $this->dropTable('product');
        
        echo "Table product deleted.\n";
    }
}

/**
 * 
CREATE TABLE `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sku` varchar(50) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `descr` varchar(255) DEFAULT NULL,
  `fulldescr` text,
  `brand` varchar(255) DEFAULT NULL,
  `series` varchar(255) DEFAULT NULL,
  `images` text COMMENT 'Serialized array',
  `attributes` text,
  `assortments` text COMMENT 'Serialized array',
  `processed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_queue` (`queue_id`),
  KEY `idx_deleted` (`deleted`),
  KEY `udx_url` (`url`),
  KEY `idx_processed` (`processed`)
) ENGINE=MyISAM AUTO_INCREMENT=5497 DEFAULT CHARSET=utf8;
 * 
 */