<?php

use yii\db\Migration;

class m180524_082206_alert_statistics extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%alert_statistics}}', [
            'id' => $this->primaryKey(),
            'statistics_time' => $this->string()->notNull()->comment('统计时间'),
            'alert_count' => $this->string()->notNull()->comment('告警数量'),
            'alert_count_details' => $this->string()->notNull()->comment('告警数量详情'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%alert_statistics}}');
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
