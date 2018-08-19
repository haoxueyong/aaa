<?php

use yii\db\Migration;

class m180801_032508_ioc_scanning extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ioc_scanning}}', [
            'id' => $this->primaryKey()->unsigned(),
            'upload_file_name' => $this->string()->notNull()->comment('用户上传的文件的名字'),
            'create_percent' => $this->smallInteger()->comment('创建进度'),
            'create_status' => $this->smallInteger()->notNull()->comment('创建状态，0未完成，1完成'),
            'create_time' => $this->string()->notNull()->comment('生成扫描结果文件的时间'),
            'download_file_name' => $this->string()->notNull()->comment('生成的结果文件的文件名'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%ioc_scanning}}');
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
