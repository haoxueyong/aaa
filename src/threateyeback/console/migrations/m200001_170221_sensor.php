<?php

use yii\db\Migration;

class m200001_170221_sensor extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sensor}}', [
            'id' => $this->primaryKey(),
            'SensorID' => $this->bigInteger()->unique(),
            'ComputerName' => $this->string()->notNull(),
            'SensorVersion' => $this->string()->notNull(),
            'OSType' => $this->string()->notNull(),
            'OSTypeShort' => $this->string()->notNull(),
            'ProfileVersion' => $this->string()->notNull(),
            'IP' => $this->string()->notNull(),
            'Domain' => $this->string()->notNull(),
            'Timestamp' => $this->bigInteger()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'isolate' => $this->smallInteger()->notNull()->defaultValue(0),
            'pause' => $this->smallInteger()->notNull()->defaultValue(0),
            'work' => $this->smallInteger()->notNull()->defaultValue(0),
            'scan' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%sensorVersion}}', [
            'id' => $this->primaryKey(),
            'Version' => $this->string()->notNull(),
            'FileName' => $this->string()->notNull(),
            'Path' => $this->string()->notNull(),
            'MD5' => $this->string()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%sensorVersion}}');
        $this->dropTable('{{%sensor}}');
    }
}
