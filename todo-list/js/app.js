function displayModal(data, taskId){
    const addModal = document.querySelector('#add-task');
    const editModal = document.querySelector('#edit-task');
    // const hiddenInput = document.que rySelector('#task-id');
    let modal;
    let taskEditId;

    if(data == 'add-task'){
        modal = addModal;
        console.log("Add modal");
    }else{
        modal = editModal;
        taskEditId = document.querySelector('#task-id-value').value;
        document.querySelector('#task-id').value = taskId;
        console.log(taskEditId);
    }


    if(modal.getAttribute('aria-hidden') === 'true'){
        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
    }else{
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    }
}