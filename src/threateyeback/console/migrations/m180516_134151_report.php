<?php

use yii\db\Migration;

class m180516_134151_report extends Migration {

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
            'alarm_count' => $this->integer()->defaultValue(0)->comment('告警总数'),
            'risk_dev_count' => $this->integer()->defaultValue(0)->comment('风险资产数量'),
            'safety_score' => $this->integer()->defaultValue(85)->comment('安全评分'),
            'risk_assets' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('风险资产TOP10'),
            'threat_level' => $this->string()->comment('威胁等级及数量'),
            'threat_type' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('威胁类型及数量'),
            'last_alert' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('最新告警TOP10'),
            'device_logs' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('安全设备运行状况（日志）'),
            'device_send_logs' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('每个安全设备发送日志量'),
            'risk_device' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('产生告警安全设备及告警数量统计'),
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
