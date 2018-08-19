<?php

use yii\db\Migration;

class m180516_061001_safety_score extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%safety_score}}', [
            'id' => $this->primaryKey(),
            'statistics_time' => $this->string()->notNull()->comment('统计时间'),
            'alert_count' => $this->integer()->notNull()->defaultValue(0)->comment('当日告警数量'),
            'score' => $this->smallInteger()->notNull()->defaultValue(85)->comment('安全评分'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%safety_score}}');
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
