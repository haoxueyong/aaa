<?php

use yii\db\Migration;

class m180815_101328_white_list extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%white_list}}', [
            'id' => $this->primaryKey(),
            'indicator' => $this->string()->notNull()->comment('指标'),
            'alert_type' => $this->string()->notNull()->comment('告警类型'),
            'create_time' => $this->string()->notNull()->comment('创建时间'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%white_list}}');
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
