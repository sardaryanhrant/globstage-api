<?php

use yii\db\Migration;

/**
 * Handles the creation of table `groups`.
 */
class m180719_072414_create_groups_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('groups', [
            'id' => $this->primaryKey(),
            'author_id'=>$this->integer(),
            'group_name'=>$this->string()->notNull(),
            'group_author'=>$this->integer()->notNull(),
            'group_coords'=>$this->string(),
            'group_followers'=>$this->json()->defaultValue(json_encode([])),
            'group_created_date'=>$this->timestamp()->defaultValue(date('Y-m-d H:i:s')),
            'group_updated_date'=>$this->timestamp()->defaultValue(date('Y-m-d H:i:s')),
            'group_videos'=>$this->json()->defaultValue(json_encode([])),
            'group_audios'=>$this->json()->defaultValue(json_encode([])),
            'group_description'=>$this->string(),
            'group_address'=>$this->string(),
            'group_website'=>$this->string(),
            'group_privacy'=>$this->json()->defaultValue(json_encode(['comment_included'=>false, 'discussiond_included'=>false, 'full_background'=>false])),
            'group_background'=>$this->integer()
        ]);

        $this->createIndex('idx-group_author', 'groups', 'group_author', false);  
        $this->addForeignKey('fk-group_author', 'groups', 'group_author', 'users', 'id');     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('groups');
    }
}
