
var Dropzone = {
    init: function() {
        //set ajax timeout
        $.ajaxSetup({timeout: 20000});
        
        //set data transfer event
        $.event.props.push('dataTransfer');
        
        //bindings
        this.setBindings();
    },
            
    setBindings: function() {
        $(document).on('drop', '#dropzone', function(e) {
            return Dropzone.handleDrop(e, $(this));
        })
        .on('dragenter', '#dropzone', function(e) {
            return Dropzone.handleDragEnter(e, $(this));
        })
        .on('dragleave', '#dropzone', function(e) {
            return Dropzone.handleDragLeave(e, $(this));
        })
        .on('dragover', function (e) {
            return Dropzone.handleDragOver(e); //chrome hack...
        });
    },

    handleDrop: function(e, element) {
        e.stopPropagation();
        e.preventDefault();

        element.removeClass('drag-enter');
        
        //get files and item data
        var files = e.dataTransfer.files,
            item_type = element.data('itemtype'),
            item_id = element.data('itemid');

        //process files
       
        this.handleFilesArray(files, item_type, item_id, element);

        return false;
    },
            
    handleDragEnter: function(e, element) {
        e.preventDefault();

        element.addClass('drag-enter');

        return false;
    },

    handleDragLeave: function(e, element) {
        e.preventDefault();

        element.removeClass('drag-enter');

        return false;
    },

    handleDragOver: function(e) {
        e.preventDefault();

        return false;
    },
            
    handleFilesArray: function(files, item_type, item_id, element) {
        //iterate through files
        $.each(files, function(index, file) {
            //add placeholder
            Dropzone.addPlaceholder(element, item_type, file.name, index);

            //create new form data object
            var form_data = new FormData();
            
            //add file
            form_data.append('item_type', item_type);
            form_data.append('item_id', item_id);
            form_data.append('files', file);

            //post
            Dropzone.postFileUpload(form_data, index, element);
        });
    },
    
    postFileUpload: function(form_data, index, element) {
        $.ajax({
                url: addHeightToUrl(GLOBALS.ajax_dir + '/dropzone_upload.php'),
                data: form_data,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data) {
                    if(data.success) {
                        createNotification(data.success, 'success');
                    }
                    if(data.callback_url) {
                        Dropzone.makeAppendRequest(data.callback_url, element);
                    }
                    if(data.error) {
                        createNotification(data.error, 'error');
                    }
                },
                error: function() {
                    createNotification('Upload failed', 'error');
                },
                complete: function() {
                    element.find('[data-index="' + index + '"]').remove();
                }
            });
    },
            
    addPlaceholder: function(element, item_type, file_name, index) {
        //first remove h1
        var header_tag = $(element).find('h1');
        if(header_tag.length != 0) {
            header_tag.remove();
        }
        
        //get markup
        var markup;
        switch (item_type) {
            case 'job_file':
                //TODO
                //replace with handlebars template...
                markup = '<div class="upload-container uploading" data-index="' + index + '"><ul><li>' + file_name + '</li></ul><div class="ajax-loader-spinner"></div></div>';
                break;
        }
        
        //append
        element.append(markup);
    },

    makeAppendRequest: function(url, target) {
        var target = $(target);

        $.ajax({
            url: addHeightToUrl(url),
            success: function(data) {
                target.append(data);
            }
        });
    }
};