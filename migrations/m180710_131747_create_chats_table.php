<?php

use yii\db\Migration;

/**
 * Handles the creation of table `chats`.
 */
class m180710_131747_create_chats_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('chats', [
            'id' => $this->primaryKey(),
            'author_id'=>$this->integer(),
            'from_id'=>$this->integer()->notNull(),
            'for_id'=>$this->integer()->notNull(),
        ]);
         $this->createIndex('idx-id', 'chats', 'id', false);       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('chats');
    }
}
