<?php

use yii\db\Migration;

class m200003_170731_share extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%share}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'uid' => $this->integer()->notNull(),
            'username' => $this->string(),
            'tagNames' => $this->string(),
            'tagString' => $this->string(),
            'gid' => $this->integer(),
            'groupName' => $this->string(),
            'filePath' => $this->string(),
            'describe' => $this->string(),
            'pv' => $this->integer()->notNull()->defaultValue(0),
            'uv' => $this->integer()->notNull()->defaultValue(0),
            'cq' => $this->integer()->notNull()->defaultValue(0),//评论数量(Comment quantity)
            'lq' => $this->integer()->notNull()->defaultValue(0),//点赞数量(Likes quantity)
            'data' => $this->text(),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%share_tag}}', [
            'id' => $this->primaryKey(),
            'sid' => $this->integer()->notNull(),
            'tid' => $this->integer()->notNull(),
            'shareName' => $this->string()->notNull(),
            'tagName' => $this->string()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

         $this->createTable('{{%user_share}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull(),
            'sid' => $this->integer()->notNull(),
            'read' => $this->smallInteger()->notNull()->defaultValue(0),
            'liked' => $this->smallInteger()->notNull()->defaultValue(0),
            'commented' => $this->smallInteger()->notNull()->defaultValue(0),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%share_tag}}');
        $this->dropTable('{{%tag}}');
        $this->dropTable('{{%share}}');
    }
}
