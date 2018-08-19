<?php

use yii\db\Migration;

class m180723_091620_protocol_flow_statistics extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%protocol_flow_statistics}}', [
            'id' => $this->primaryKey(),
            'pro_type' => $this->string()->notNull()->comment('协议类型'),
            'statistics_time' => $this->integer()->notNull()->comment('统计时间'),
            'flow' => $this->float()->comment('流量'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%protocol_flow_statistics}}');
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
