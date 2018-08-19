<?php

use yii\db\Migration;

class m200005_170930_groupNode extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%group_node}}', [
            'id' => $this->primaryKey(),

            'name' => $this->string(),
            'nodes' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),

        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%group_node}}');
    }
}
