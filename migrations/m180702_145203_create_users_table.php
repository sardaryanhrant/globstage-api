<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m180702_145203_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'author_id'=>$this->integer()->unique(),
            'user_name'=>$this->string()->notNull(),
            'user_last_name'=>$this->string()->notNull(),
            'user_email'=>$this->string()->notNull()->unique(),
            'user_photo'=>$this->string(),
            'user_friends'=>$this->json()->defaultValue(json_encode([])),
            'user_friends_num'=>$this->integer(),
            'user_status'=>$this->string(),
            'user_location'=>$this->string(),
            'user_date_of_birth'=>$this->timestamp(),
            // 'user_activities'=>$this->json(),
            'user_marital_status'=>$this->string(),
            'user_gender'=>$this->string(),
            // 'user_rate'=>$this->integer(),
            'user_password'=>$this->string()->notNull(),
            'user_chat_list'=>$this->json()->defaultValue(json_encode([])),
            'user_interests'=>$this->json()->defaultValue(json_encode([])),
            'user_contact'=>$this->json()->defaultValue(json_encode([])),
            'user_country'=>$this->string(),
            'user_city'=>$this->string(),
            'user_blacklist'=>$this->json()->defaultValue(json_encode([])),
            'user_announced'=>$this->json()->defaultValue(json_encode([])),
            'user_privacy'=>$this->json()->defaultValue(json_encode([])),
            'user_last_visit'=>$this->timestamp(),
            'user_subscriptions'=>$this->json()->defaultValue(json_encode([])),
            'user_active'=>$this->boolean(),
            'user_guests'=>$this->json()->defaultValue(json_encode([])),
            'user_speed'=>$this->integer(),
            'user_permissions'=>$this->json()->defaultValue(json_encode([]))
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }
}
