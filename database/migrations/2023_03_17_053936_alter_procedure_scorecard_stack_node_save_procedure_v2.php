<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = "DROP PROCEDURE IF EXISTS `scorecardStackNodeSaveProcedureV1`;
		CREATE PROCEDURE `scorecardStackNodeSaveProcedureV1`(IN `ssid` BIGINT(15)) NOT DETERMINISTIC CONTAINS SQL SQL SECURITY DEFINER BEGIN

        DECLARE SSDATA LONGTEXT;
        DECLARE NodesData LONGTEXT;
        DECLARE NodesDataLenth BIGINT DEFAULT 0;
        DECLARE Counter BIGINT DEFAULT 0;
        DECLARE item_arr_element LONGTEXT;
        DECLARE node_dt LONGTEXT;
        DECLARE node_id  VARCHAR(255);
        DECLARE auto_assign_color SMALLINT DEFAULT 0;
        DECLARE assigned_color   VARCHAR(255);
        DECLARE assigned_to  LONGTEXT DEFAULT 0;
        DECLARE goal_achieve_in_number  BIGINT DEFAULT 0;
        DECLARE reminder SMALLINT DEFAULT 0;
        DECLARE node_data LONGTEXT;
        DECLARE created_by_resp BIGINT DEFAULT 0;
        DECLARE node_data_type LONGTEXT DEFAULT NULL;
   
        SELECT scorecard_data,created_by INTO SSDATA,created_by_resp FROM scorecard_stack WHERE id=ssid;

        IF SSDATA IS NOT NULL AND JSON_UNQUOTE (JSON_EXTRACT (SSDATA, '$.nodes')) != ' ' OR JSON_UNQUOTE (JSON_EXTRACT (SSDATA, '$.nodes')) IS NOT NULL THEN

            SET NodesData = JSON_UNQUOTE (JSON_EXTRACT (SSDATA, '$.nodes'));
            SET NodesDataLenth = JSON_LENGTH (NodesData, '$') ;
            

            # Delete Old Nodes Data If Exist Bcs. Multiple records saved to upsert not possible

            SET @sqlssnddlt = CONCAT('DELETE FROM  `scorecard_stack_nodes`  where scorecard_stack_id = ',ssid,'');
            PREPARE stmt FROM @sqlssnddlt;
            EXECUTE stmt;

            WHILE (Counter < NodesDataLenth) DO

                SET item_arr_element = JSON_EXTRACT (NodesData,CONCAT('$[', Counter, ']'));
                SET node_dt = JSON_UNQUOTE (JSON_EXTRACT (item_arr_element,'$.data'));
                
                IF node_dt IS NOT NULL THEN                
	
                    SET node_id = JSON_UNQUOTE (JSON_EXTRACT (item_arr_element,'$.id'));
                    SET node_data_type = JSON_UNQUOTE (JSON_EXTRACT (node_dt,'$.type'));
                    SET reminder = IF(JSON_UNQUOTE(JSON_EXTRACT(node_dt,'$.reminder')) IS NULL, '0',JSON_UNQUOTE (JSON_EXTRACT (node_dt, '$.reminder')));
		
		    IF (JSON_UNQUOTE(JSON_EXTRACT(node_dt,'$.assigned_to')) IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(node_dt,'$.assigned_to')) = 'null')	THEN
			SET assigned_to = 'NULL';
		    ELSE
			SET assigned_to = JSON_UNQUOTE(JSON_EXTRACT(node_dt,'$.assigned_to'));
		    END IF;				
                    
                    SET goal_achieve_in_number = IF(JSON_UNQUOTE(JSON_EXTRACT(node_dt,'$.goal_achieve_in_number')) IS NULL, '0',JSON_UNQUOTE (JSON_EXTRACT (node_dt, '$.goal_achieve_in_number')));
                    SET node_id = IF(JSON_UNQUOTE(JSON_EXTRACT(item_arr_element,'$.id')) IS NULL, 'NULL',CONCAT('\"',JSON_UNQUOTE (JSON_EXTRACT (item_arr_element, '$.id')),'\"'));
                    SET assigned_color = IF(JSON_UNQUOTE(JSON_EXTRACT(node_dt,'$.assigned_color')) IS NULL, 'NULL',CONCAT('\"',JSON_UNQUOTE (JSON_EXTRACT (node_dt, '$.assigned_color')),'\"'));
                    SET auto_assign_color = IF(JSON_UNQUOTE(JSON_EXTRACT(node_dt,'$.assigned_color')) IS NULL, 0,1);
                    SET node_data =  IF(node_dt IS NULL, 'NULL',CONCAT(\"'\",node_dt,\"'\"));
                    
                    IF node_data_type = 'metricBox' THEN
                    
                        SET @sqlsssave = CONCAT('INSERT INTO `scorecard_stack_nodes` (`id`, `scorecard_stack_id`, `node_id`, `node_data`, `auto_assign_color`, `assigned_color`, `assigned_to`, `goal_achieve_in_number`, `reminder`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`, `created_at`, `updated_at`)
                        VALUES (NULL,',ssid,',',node_id,',',node_data,',',auto_assign_color,',',assigned_color,',',assigned_to,',',goal_achieve_in_number,',',reminder,',',created_by_resp,',NULL,NULL,NULL,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)');

                        PREPARE stmt FROM @sqlsssave;
                        EXECUTE stmt;

                    END IF;

                END IF;

                SET Counter = Counter + 1;

            END WHILE;

            SELECT \"Inserted \";

        END IF;

        END";
        \DB::unprepared($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $query = "DROP PROCEDURE IF EXISTS `scorecardStackNodeSaveProcedureV1`";
        \DB::unprepared($query);
    }
};
