<?php

use yii\db\Migration;

class m180426_163319_device_log extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%device_log}}', [
            'id' => $this->primaryKey(),
            'device_id' => $this->string()->comment('安全设备id'),
            'host' => $this->string()->comment('安全设备IP'),
            'info' => $this->string()->comment('日志信息'),
            'status' => $this->smallInteger()->notNull()->comment('预留字段'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            ], $tableOptions);
    }

    public function down() {
        echo "m180426_163319_device_log cannot be reverted.\n";

        return false;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
