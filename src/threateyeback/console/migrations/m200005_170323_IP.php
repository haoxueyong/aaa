<?php

use yii\db\Migration;
use yii\db\Query;


class m200005_170323_IP extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%IP}}', [
            'id' => $this->primaryKey(),
            'IP' => $this->string()->notNull(),
            'IPType' => $this->smallInteger()->notNull()->defaultValue(0),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createTable('{{%IP_alert}}', [
            'id' => $this->primaryKey(),
            'IPID' => $this->integer()->notNull(),
            'AlertID' => $this->integer()->notNull(),
            'EventID' => $this->bigInteger()->notNull(),
            'IsSolveBy3rd' => $this->smallInteger()->notNull()->defaultValue(0),
            'Detail' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);



        $sql = 'CREATE VIEW `IPEX` AS 
        SELECT 

            CONCAT("IP_",IP_alert.id) AS id,
            IP_alert.id AS rid,

            
            "IP" AS Type,
            
            IP_alert.status,
            IP_alert.Detail,

            NULL AS MD5, 
            NULL AS SHA256,
            IP.IP,
            NULL AS URL,
            IP.IP AS Label,


            alert.AlertID, 
            alert.Point, 
            alert.SensorID, 
            alert.AlertType, 
            alert.SrcType, 
            alert.IsSolveBy3rd, 
            alert.`Timestamp`, 
            alert.updated_at, 
            alert.created_at, 
            sensor.ComputerName, 
            sensor.OSType, 
            sensor.OSTypeShort, 
            sensor.IP AS SensorIP

        FROM IP_alert
            INNER JOIN alert ON alert.id = IP_alert.AlertID
            INNER JOIN sensor ON sensor.SensorID = alert.SensorID
            INNER JOIN IP ON IP.id = IP_alert.IPID';
        $command=$this->db->createCommand($sql);
        $rowCount=$command->execute();

    }

    public function down()
    {
        $this->db->createCommand('drop VIEW IPEX;')->execute();
        $this->dropTable('{{%IP_alert}}');
        $this->dropTable('{{%IP}}');
    }
}

