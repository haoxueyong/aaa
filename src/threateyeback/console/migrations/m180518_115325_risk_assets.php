<?php

use yii\db\Migration;

class m180518_115325_risk_assets extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%risk_assets}}', [
            'id' => $this->primaryKey(),
            'statistics_time' => $this->string()->notNull()->comment('统计时间'),
            'alert_details' => $this->string()->notNull()->comment('告警情况'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%risk_assets}}');
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
