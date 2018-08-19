<?php

use yii\db\Migration;

class m180719_075804_report extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%report}}', [
            'id' => $this->primaryKey(),
            'report_name' => $this->string()->notNull()->comment('报表名称'),
            'create_time' => $this->integer()->notNull()->defaultValue(0)->comment('生成时间'),
            'stime' => $this->bigInteger()->notNull()->defaultValue(0)->unsigned()->comment('开始时间'),
            'etime' => $this->bigInteger()->notNull()->defaultValue(0)->unsigned()->comment('结束时间'),
            'report_type' => $this->string()->notNull()->comment('报表类型'),
            'perday_ip' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('受害主机IP（每天）'),
            'url_top10' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('恶意URL TOP10'),
            'ip_top10' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('恶意IP TOP10'),
            'hash_top10' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('恶意文件 TOP10'),
            'host_top50' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('受害主机TOP50'),
            'extortion_software' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('勒索软件攻击'),
            'phishing' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('钓鱼攻击'),
            'botc_c' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('僵尸网络访问'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%report}}');
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
