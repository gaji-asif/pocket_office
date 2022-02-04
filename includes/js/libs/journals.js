var Journals = {
    
    input: null,
    
    init: function() {
        Journals.setBindings();
    },
            
    setBindings: function() {
        $(document).on('click', '[rel="journal-quick-add"]', this.post);
//        $(document).on('keyup', '.journal-quick-add textarea', 'ctrl+return', this.shortcutPost);
    },
    
    post: function() {
        var el = $(this),
            val = $('.journal-quick-add textarea').val(),
            recipients = $('.journal-quick-add [name="recipients[]"]').val(),
            url = GLOBALS.ajax_dir + '/quick_add_journal.php';
        
        if(!val.length) {
            return;
        }
        
        $('.journal-quick-add textarea').val('');
        $('.journal-quick-add [name="recipients[]"]').trigger('clearall');
        
        $.post(
            url,
            {
                id: el.data('job-id'),
                journal: val,
                recipients: recipients
            },
            Journals.handlePostCallback
        );
    },
    
    handlePostCallback: function(data) {
        var resetData = data.replace('<pre>', '') ;
        resetData = resetData.replace('</pre>', '') ;
        $('.journal-quick-add').after(resetData);
    },
            
    handleKeyup: function(e) {
        $('[rel="journal-quick-add"]').click();
        return false;
    }
    
};

$(Journals.init);