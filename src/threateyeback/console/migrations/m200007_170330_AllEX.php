<?php

use yii\db\Migration;
use yii\db\Query;


class m200007_170330_AllEX extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $sql = 'CREATE VIEW `AllEX` AS 
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
            sensor.IP AS SensorIP,
            sensor.Domain AS SensorDomain

        FROM file_alert 
            INNER JOIN alert ON alert.id = file_alert.AlertID
            INNER JOIN sensor ON sensor.SensorID = alert.SensorID
            INNER JOIN file ON file.id = file_alert.FileID
        UNION ALL
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
            sensor.IP AS SensorIP,
            sensor.Domain AS SensorDomain

        FROM IP_alert
            INNER JOIN alert ON alert.id = IP_alert.AlertID
            INNER JOIN sensor ON sensor.SensorID = alert.SensorID
            INNER JOIN IP ON IP.id = IP_alert.IPID
        UNION ALL
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
            sensor.IP AS SensorIP,
            sensor.Domain AS SensorDomain

        FROM URL_alert
            INNER JOIN alert ON alert.id = URL_alert.AlertID 
            INNER JOIN sensor ON sensor.SensorID = alert.SensorID
            INNER JOIN URL ON URL.id = URL_alert.URLID

        UNION ALL
        SELECT 

            CONCAT("Beh_",alert.id) AS id,
            alert.id AS rid,
            
            "Beh" AS Type,

            alert.status,
            "" AS Detail,

            NULL AS MD5, 
            NULL AS SHA256,
            NULL AS IP,
            NULL AS URL,
            alert.Label,
            
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
            sensor.IP AS SensorIP,
            sensor.Domain AS SensorDomain
            
        FROM alert 
            INNER JOIN sensor ON sensor.SensorID = alert.SensorID
            WHERE alert.AlertType > 3';
        $command=$this->db->createCommand($sql);
        $rowCount=$command->execute();

    }

    public function down()
    {
        $this->db->createCommand('drop VIEW AllEX;')->execute();
    }
}

