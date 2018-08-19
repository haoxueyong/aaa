<?php

use yii\db\Migration;
use yii\db\Query;


class m200004_170323_file extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'MD5' => $this->string()->notNull(),
            'SHA256' => $this->string(),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createTable('{{%file_alert}}', [
            'id' => $this->primaryKey(),
            'FileID' => $this->integer()->notNull(),
            'AlertID' => $this->integer()->notNull(),
            'EventID' => $this->bigInteger()->notNull(),
            'FilePath' => $this->string()->notNull(),
            'Level' => $this->string()->notNull(),
            'IsSolveBy3rd' => $this->smallInteger()->notNull()->defaultValue(0),
            'Detail' => $this->text(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);



        $sql = 'CREATE VIEW `fileEX` AS 
        SELECT 

            CONCAT("File_",file_alert.id) AS id,
            file_alert.id AS rid,

            "File" AS Type,
            
            file_alert.status,
            file_alert.Detail,

            file.MD5, 
            file.SHA256, 
            NULL AS IP,
            NULL AS URL,
            file_alert.FilePath AS Label,

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

        FROM file_alert 
            INNER JOIN alert ON alert.id = file_alert.AlertID
            INNER JOIN sensor ON sensor.SensorID = alert.SensorID
            INNER JOIN file ON file.id = file_alert.FileID';
        $command=$this->db->createCommand($sql);
        $rowCount=$command->execute();
    }

    public function down()
    {
        $this->db->createCommand('drop VIEW fileEX;')->execute();
        $this->dropTable('{{%file_alert}}');
        $this->dropTable('{{%file}}');
    }
}

