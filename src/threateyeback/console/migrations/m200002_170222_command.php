<?php

use yii\db\Migration;

class m200002_170222_command extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%command}}', [
            'id' => $this->primaryKey(),
            'Type' => $this->String(1)->notNull(),
            'SensorID' => $this->bigInteger(),
            'CommandID' => $this->string(),
            'data' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%command}}');
    }
}

