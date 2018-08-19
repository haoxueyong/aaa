<?php

use yii\db\Migration;

class m200009_170429_group extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%group}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string()->notNull(),
            'type' => $this->smallInteger()->notNull()->defaultValue(0),
            'pid' => $this->integer()->notNull(),
            'level' => $this->integer()->notNull()->defaultValue(0),
            'FilterList' => $this->string(),


            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%group_sensor}}', [
            'id' => $this->primaryKey(),
            'sid' => $this->integer()->notNull(),
            'gid' => $this->integer()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%group_sensor}}');
        $this->dropTable('{{%group}}');
    }
}
