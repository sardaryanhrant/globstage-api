<?php

use yii\db\Migration;

/**
 * Handles the creation of table `files`.
 */
class m180716_143214_create_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'author_id'=>$this->integer(),
            'type'=> $this->string()->notNull(),
            'created'=>$this->timestamp()->notNull()->defaultValue(date('Y-m-d H:i:s')),
            'name'=>$this->string()->notNull(),
            'path'=>$this->string()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('files');
    }
}
