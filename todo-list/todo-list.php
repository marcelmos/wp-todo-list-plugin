<?php
/**
* Plugin Name: ToDo List
* Description: ToDo List for your WordPress admin panel. Plan your future posts and actions on your page. You can create new task, edit task, set as done, and remove.
* Version: 1.0
* Author: Marcel Moś
* Author URI: http://defaultweb.epizy.com
*/

include_once(__DIR__ . '/task-actions.php');

class ToDo_List {
    public function __construct() {
        add_action('admin_menu', array($this, 'create_plugin_settings_page'));
    }

    public function create_plugin_settings_page() {

        add_menu_page(
            'ToDo',
            'ToDo List',
            'edit_posts',
            'cg_settings',
            array($this, 'plugin_page_content'),
            'dashicons-admin-post',
            100
        );

        wp_enqueue_script( 'todo-script', plugins_url( '/js/app.js', __FILE__ ));
        wp_enqueue_style( 'todo-style', plugins_url( '/css/style.css', __FILE__ ));
    }

    public function plugin_page_content() {
        global $wpdb;

        $table_name = $wpdb->prefix."todo";

        ?>
        <div class="wrap">
            <h2>ToDo List</h2>

            <div class="task-list">
                <button class="task add-new" name="addNewTask" onClick='displayModal("add-task")'>
                    <section class="task-add">
                        <h2 class="btn-task-add">+</h2>
                        <h3 class="btn-task-add">New Task</h3>
                    </section>
                </button>

                <?php
                $tasks = $wpdb->get_results("SELECT * FROM $table_name");

                if($tasks){
                    foreach($tasks as $task){
                        echo "<div class='task ".(($task->task_status == 0) ? "" : "task-done")."' data-id='$task->id'>
                                <div class='task-top priority-".strtolower($task->task_priority)."'>
                                    <div class='task-priority'>".($task->task_priority == "None" ? "" : $task->task_priority)."</div>
                                    <div class='task-actions'>

                                            ".(($task->task_status == 0) ? '<button id="task-id-value" class="btn-task" onClick="displayModal(null, '.$task->id.')" value="$task->id" title="Edit"><span class="dashicons dashicons-edit"></span></button>' : "")."

                                        <form method='post' action=''>
                                            <button class='btn-task' name='del_task' value='$task->id' title='Remove'><span class='dashicons dashicons-trash'></span></button>
                                        </form>
                                    </div>
                                </div>
                                <section class='task-content'>
                                    <p class='task-name'>$task->task_name</p>
                                    <div class='task-date'>
                                        <p><b>Created:</b><br> $task->task_created</p>
                                        <p><b>Deadline:</b><br> "
                                        .(($task->task_deadline == "0000-00-00") ? "Not set" : $task->task_deadline)."</p>
                                    </div>
                                    <form method='post' action=''>
                                        ".(($task->task_status == 0) ? "<button class='button button-primary' name='action_btn' value='$task->id'>Set As Done</button>" : "<button class='button' name='action_btn' value='$task->id'>Set ToDo</button>")."
                                    </form>
                                </section>
                            </div>";
                    }
                }else{
                    echo "
                            <div class='task'>
                                <div class='task-top'>
                                    :(
                                </div>
                                <section class='task-content'>
                                    <p class='task-name'>
                                        Ohhh no... Look's like you don't had any task ToDo for now... :/<br>
                                        But still you can create new one.
                                    </p>
                                </section>
                            </div>";
                }
                ?>
            </div>

            <!-- MODAL ADD NEW TASK -->
            <div id="add-task" class="modal" aria-hidden="true">
                <h3>New Task</h3>
                <form method="post" action="">
                    <label>
                        <p class="label-text">Task Name:</p>
                        <input type="text" class="task-name" name="task-name" required placeholder="Plan for today is...">
                    </label><br>

                    <div class="justify-content">
                        <label>
                            <p class="label-text">Task Deadline:</p>
                            <input type="date" name="task-deadline">
                        </label>

                        <label>
                            <p class="label-text">Priority level:</p>
                            <select name="task-priority">
                                <option>None</option>
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                                <option>Important</option>
                            </select>
                        </label>
                    </div>
                    <div class="form-btn">
                        <input class="button button-primary" type="submit" name="submit_new_task" value="Add Task">
                        <input class="button" type="reset" onclick="displayModal('add-task')" value="Close">
                    </div>
                </form>
            </div><!-- MODAL ADD NEW TASK END -->

            <!-- MODAL EDIT TASK -->
            <div id="edit-task" class="modal" aria-hidden="true">
                <h3>Edit Task</h3>
                <form method="post" action="">
                    <!-- HIDDEN INPUT WHICH CONTAIN TASK ID -->
                    <input type="hidden" id="task-id" name="task-id" value="" />
                    <label>
                        <p class="label-text">Task Name:</p>
                        <input type="text" class="task-name" name="edit-task-name" max-length="255" required placeholder="New task name...">
                    </label><br>

                    <div class="justify-content">
                        <label>
                            <p class="label-text">Task Deadline:</p>
                            <input type="date" name="edit-task-deadline">
                        </label>

                        <label>
                            <p class="label-text">Priority level:</p>
                            <select name="edit-task-priority">
                                <option>None</option>
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                                <option>Important</option>
                            </select>
                        </label>
                    </div>
                    <div class="form-btn">
                        <input class="button button-primary" type="submit" name="submit_edit_task" value="Edit Task">
                        <input class="button" type="reset" onclick="displayModal()" value="Close">
                    </div>
                </form>
            </div><!-- MODAL EDIT TASK END -->

        </div>
        <?php
    }


}

$ToDo_List = new ToDo_List();
$Task_Actions = new Task_Actions();

if(isset($_POST['submit_new_task'])){
    $Task_Actions->create_task();
}

if(isset($_POST['del_task'])){
    $Task_Actions->remove_task($_POST['del_task']);
}

if(isset($_POST['action_btn'])){
    $Task_Actions->set_task_state($_POST['action_btn']);
}

if(isset($_POST['submit_edit_task'])){
    $Task_Actions->update_task();
}
