<?php

use yii\db\Migration;

/**
 * Handles the creation of table `friends`.
 */
class m180704_115639_create_friends_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('friends', [
            'id' => $this->primaryKey(),
            'author_id'=>$this->integer(),
            'user_id' => $this->integer()->notNull(),
            'friend_id'=>$this->integer()->notNull(),
            'subscription'=>$this->integer()->defaultValue(0),
            'added_date'=>$this->timestamp(),
        ]);

        $this->createIndex('idx-user_id', 'friends', 'user_id', false);       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('friends');
    }
}
