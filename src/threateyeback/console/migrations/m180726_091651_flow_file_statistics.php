<?php

use yii\db\Migration;

class m180726_091651_flow_file_statistics extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%flow_file_statistics}}', [
            'id' => $this->primaryKey()->unsigned(),
            'statistics_time' => $this->string()->notNull()->comment('统计时间'),
            'flow' => $this->float()->notNull()->comment('流量'),
            'file_count' => $this->bigInteger()->notNull()->unsigned()->comment('文件数量'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%flow_file_statistics}}');
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
