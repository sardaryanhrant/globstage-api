<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comments`.
 */
class m180719_115432_create_comments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('comments', [
            'id' => $this->primaryKey(),
            'author_id'=>$this->integer(),
            'comment_user_id'=> $this->integer()->notNull(),
            'comment_post_id'=>$this->integer()->notNull(),
            'comment_content'=>$this->string()->notNull(),
            'comment_created_date'=>$this->timestamp()->defaultValue(date('Y-m-d H:i:s')),
            'comment_updated_date'=>$this->timestamp()->defaultValue(date('Y-m-d H:i:s')),
        ]);

        $this->createIndex('idx-comment_post_id', 'comments', 'comment_post_id', false);  
        $this->addForeignKey('fk-comment_post_id', 'comments', 'comment_post_id', 'posts', 'id');     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('comments');
    }
}
