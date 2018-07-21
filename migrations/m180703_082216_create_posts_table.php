<?php

use yii\db\Migration;

/**
 * Handles the creation of table `posts`.
 */
class m180703_082216_create_posts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('posts', [
            'id' => $this->primaryKey(),
            'author_id'=>$this->integer(),
            'post_user_id'=>$this->integer()->notNull(),
            'post_type'=>$this->string()->defaultValue('post'),
            'post_wall_id'=>$this->integer(),
            'post_group_id'=>$this->integer(),
            'post_like_count'=>$this->integer(),
            'post_like_users'=>$this->json(),
            'post_attachments'=>$this->json(),
            'post_content'=>$this->string(),
            'post_community'=>$this->integer(),
            'post_poll'=>$this->integer(),
            'post_poll_title'=>$this->string(),
            'post_poll_all_voted'=>$this->integer(),
            'post_comment_count'=>$this->integer(),
            'post_created_date'=>$this->date()->defaultValue(date('Y-m-d H:i:s')),
            'post_updated_date'=>$this->date()->defaultValue(date('Y-m-d H:i:s'))
        ]);

        $this->createIndex('idx-post_user_id', 'posts', 'post_user_id', false);

        $this->createIndex('idx-post_type', 'posts', 'post_type', false);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('posts');
    }
}
