<?php

use yii\db\Migration;

class m180730_090036_system_state extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%system_state}}', [
            'id' => $this->primaryKey()->unsigned(),
            'statistics_time' => $this->string()->notNull()->comment('统计时间'),
            'dev_ip' => $this->string()->notNull()->comment('设备IP'),
            'dev_type' => $this->smallInteger()->notNull()->comment('设备类型，1探针，2引擎，3探针+引擎'),
            'cpu' => $this->float()->notNull()->comment('CPU'),
            'mem' => $this->float()->notNull()->comment('内存'),
            'disk' => $this->float()->notNull()->comment('磁盘'),
            'flow' => $this->float()->notNull()->comment('流量'),
            'status' => $this->smallInteger()->notNull()->comment('设备状态，0离线，1在线'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%system_state}}');
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
