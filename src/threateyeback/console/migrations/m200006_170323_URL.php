<?php

use yii\db\Migration;
use yii\db\Query;


class m200006_170323_URL extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%URL}}', [
            'id' => $this->primaryKey(),
            'URL' => $this->string()->notNull(),
            'URLType' => $this->smallInteger()->notNull()->defaultValue(0),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createTable('{{%URL_alert}}', [
            'id' => $this->primaryKey(),
            'URLID' => $this->integer()->notNull(),
            'AlertID' => $this->integer()->notNull(),
            'EventID' => $this->bigInteger()->notNull(),
            'IsSolveBy3rd' => $this->smallInteger()->notNull()->defaultValue(0),
            'Detail' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);



        $sql = 'CREATE VIEW `URLEX` AS 
        SELECT 

            CONCAT("URL_",URL_alert.id) AS id,
            URL_alert.id AS rid,

            
            "URL" AS Type,

            URL_alert.status,
            URL_alert.Detail,
            
            NULL AS MD5, 
            NULL AS SHA256,
            NULL AS IP,
            URL.URL,
            URL.URL AS Label,

            
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

        FROM URL_alert
            INNER JOIN alert ON alert.id = URL_alert.AlertID 
            INNER JOIN sensor ON sensor.SensorID = alert.SensorID
            INNER JOIN URL ON URL.id = URL_alert.URLID';
        $command=$this->db->createCommand($sql);
        $rowCount=$command->execute();

    }

    public function down()
    {
        $this->db->createCommand('drop VIEW URLEX;')->execute();
        $this->dropTable('{{%URL_alert}}');
        $this->dropTable('{{%URL}}');
    }
}

