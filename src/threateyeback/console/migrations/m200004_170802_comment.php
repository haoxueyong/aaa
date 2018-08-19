<?php

use yii\db\Migration;

class m200004_170802_comment extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'content' => $this->string()->notNull(),
            'uid' => $this->integer()->notNull(),
            'sid' => $this->integer()->notNull(),
            'type' => $this->smallInteger()->notNull()->defaultValue(0),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%comment}}');
    }
}
