<?php

class Task_Actions{

    function create_database(){
        global  $wpdb;
        $table_name = $wpdb->prefix."todo";

        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $sql = "CREATE TABLE $table_name (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `task_name` TEXT NOT NULL ,
                `task_created` DATE NOT NULL ,
                `task_deadline` DATE NOT NULL ,
                `task_priority` VARCHAR(20) NOT NULL ,
                `task_status` BOOLEAN NOT NULL,
                PRIMARY KEY  (`id`)) ENGINE = InnoDB;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
                return true;   // Check if Table created
            }
        }else{
            return true;
        }
    }

    function create_task(){
        global $wpdb;

        if($this->create_database()){
           $task_name = $_POST['task-name'];
        //    $task_created = date("Y-m-d");
           $task_deadline = $_POST['task-deadline'];
           $task_priority = $_POST['task-priority'];

            $wpdb->insert(
                $wpdb->prefix."todo",
                array(
                    'task_name' => $task_name,
                    'task_created' => date("Y-m-d"),
                    'task_deadline' => $task_deadline,
                    'task_priority' => $task_priority,
                    'task_status' => 0,
                )
            );
        }
    }

    function remove_task($id){
        global  $wpdb;
        $table_name = $wpdb->prefix."todo";

        $wpdb->delete($table_name, array('id' => $id));
    }

    function set_task_state($id){
        global  $wpdb;
        $table_name = $wpdb->prefix."todo";

        $tasks = $wpdb->get_results("SELECT task_status FROM $table_name WHERE id = '$id'");

        foreach($tasks as $task){
            if($task->task_status == 0){
                $wpdb->update($table_name, array('task_status' => '1'), array('id' => $id));
            }else{
                $wpdb->update($table_name, array('task_status' => '0'), array('id' => $id));
            }
        }
    }

    function update_task(){
        global  $wpdb;

        $task_id = $_POST['task-id'];
        $task_name = $_POST['edit-task-name'];
        $task_deadline = $_POST['edit-task-deadline'];
        $task_priority = $_POST['edit-task-priority'];

        $wpdb->update(
            $wpdb->prefix."todo",
            array(
                'task_name' => $task_name,
                'task_deadline' => $task_deadline,
                'task_priority' => $task_priority,
            ),
            array('id' => $task_id)
        );
    }
}