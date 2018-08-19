<?php

use yii\db\Migration;

class m180726_030018_risk_asset_statistics extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%risk_asset_statistics}}', [
            'id' => $this->primaryKey()->unsigned(),
            'asset_ip' => $this->string()->notNull()->comment('告警IP'),
            'high_count' => $this->integer()->notNull()->unsigned()->comment('高风险数量'),
            'medium_count' => $this->integer()->notNull()->unsigned()->comment('中风险数量'),
            'low_count' => $this->integer()->notNull()->unsigned()->comment('低风险数量'),
            'count' => $this->integer()->notNull()->unsigned()->comment('告警系数'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%risk_asset_statistics}}');
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
