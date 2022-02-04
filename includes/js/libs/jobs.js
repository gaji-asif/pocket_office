var Jobs = {
    
    setBindings: function() {
        $(document).on('click', '[rel="add-auto-create-task"]', this.addAutoCreateTask);
    },
            
    addAutoCreateTask: function(e) {
        e.preventDefault();
        
        var val = $('select[name="task_types"]').val(),
            option = $('select[name="task_types"]').find('[value="' + val + '"]'),
            data = {
                taskTypeId: val,
                color: option.data('color'),
                name: option.text()
        };
        
        if($('ul#auto-create-tasks').find('[value="' + val + '"]').length) { return; }
        
        $('ul#auto-create-tasks').append(Handlebars.renderTemplate('auto-create-task', data));
    }
    
};