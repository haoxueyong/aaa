<?php

use yii\db\Migration;

class m200001_170627_alert extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%alert}}', [
            'id' => $this->primaryKey(),
            'alert_id' => $this->bigInteger()->notNull()->comment('告警ID'),
            'src_ip' => $this->string()->comment('源ip'),
            'dest_ip' => $this->string()->comment('目的ip'),
            'alert_type' => $this->string()->comment('告警类型'),
            'indicator' => $this->string()->comment('指标'),
            'category' => $this->string()->comment('威胁描述'),
            'threat' => $this->string()->comment('描述（只有当文件类告警是才会有值）'),
            'application' => $this->string()->comment('应用(协议)'),
            'degree' => $this->string()->comment('风险等级'),
            'detect_engine' => $this->string()->comment('检测引擎'),
            'alert_description' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->comment('告警描述'),
            'alert_time' => $this->integer()->comment('告警时间'),
            'status' => $this->smallInteger()->notNull()->comment('0未确认，1已确认，2已处理'),
            'processing_person' => $this->string()->comment('处理人'),
            'device_id_111' => $this->string()->comment('预留字段'),
            'session_111' => $this->string()->comment('预留字段'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%alert}}');
    }

}
