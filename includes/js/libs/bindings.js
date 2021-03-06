

var Bindings = {

    setAllBindings: function() {

        //bind to modal close button click

        $(document).on('click', '.btn-close-modal', function(){

            if($(this).hasClass('reload-parent')) {

                parent.window.location.reload();

            } else if(!$(this).hasClass('no-close')) {

                parent.deleteOverlay();

            }

        });

        

        $(document).on('click', '[rel="close-me"]', function(e){

            e.preventDefault();

            parent.deleteOverlay();

        });



        //bind to bookmark link click

        $(document).on('click', '.bookmark-link', function(){

            bookmarkJob($(this).data('job-id'));



            if($(this).hasClass('active')) {

                $(this).html('Bookmark');

            } else {

                $(this).html('Remove Bookmark');

            }

            $(this).toggleClass('active');



            return false;

        });



        //bind to onchange-set-meta class

        $(document).on('change', '.onchange-set-meta', setMeta);



        //bind to click rel star-color-picker

        $(document).on('change', '[rel="star-color-picker"]', function(){

            $('#' + $(this).data('preview-id')).css('color', $(this).val());

        });



        //bind to click rel print-page

        $(document).on('click', '[rel="print-page"]', function(e){

            //prevent default action

            e.preventDefault();



            //get href

            var href = $(this).attr('href');



            //print

            printPage(href);

        });



        //bind to click rel delete-job-upload

        $(document).on('click', '[rel="delete-job-upload"]', function(e){

            //prevent default action

            e.preventDefault();



            deleteJobUpload($(this));

        });



        //bind to click rel show-forgot-password

        $(document).on('click', '[rel="show-forgot-password"]', function(e){

            //prevent default action

            e.preventDefault();



            $('#login-container').fadeOut(function(){

                $('#forgot-password-container').fadeIn();

            });

        });



        //bind to click rel hide-forgot-password

        $(document).on('click', '[rel="hide-forgot-password"]', function(e){

            //prevent default action

            e.preventDefault();



            $('#forgot-password-container').fadeOut(function(){

                $('#login-container').fadeIn();

            });

        });



        //bind to click rel submit-forgot-password

        $(document).on('click', '[rel="submit-forgot-password"]', function(e){

            //prevent default action

            e.preventDefault();



            //get variables

            var email = $('#input-email-forgot').val();



            //make url

            var request_url = GLOBALS.ajax_dir + '/lost_password.php?e=' + email;



            //send

            $.ajax({

                url: addHeightToUrl(request_url),

                dataType: 'json',

                success: function(data){

                    $('#forgot-password-result').text(data.message);

                }

            });

        });



        $(document).on('click', '[rel="select-job-salesman"]', function(e){

            //prevent default action

            e.preventDefault();



            //get salesman id

            var salesman_id = $(this).data('salesman-id');



            //if that value exists, select

            if($("#salesman-picklist option[value='" + salesman_id + "']").length) {

                $('#salesman-picklist').val(salesman_id);

            }

            else {

                createNotification('User not available to select', 'error');

            }

        });



        $(document).on('click', '[rel="delete-journal"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var journal_id = $(this).data('journal-id');



            if($.inArray(journal_id, GLOBALS.journals_being_processed) == -1 && confirm("Are you sure?")) {

                GLOBALS.journals_being_processed.push(journal_id);

                $.getJSON(GLOBALS.ajax_dir + '/delete_journal.php?journalid=' + journal_id, function(data){

                    if(data.error) {

                        createNotification(data.error, 'error');

                    }

                    else if(data.success) {

                        removeJournalAndRestyleList(journal_id);

                    }

                }).fail(function(){

                    createNotification('Operation failed', 'error');

                }).always(function(){

                    GLOBALS.journals_being_processed.splice(GLOBALS.journals_being_processed.indexOf(journal_id), 1);

                });

            }

        });

		

		$(document).on('click', '[rel="delete-journal-inbox"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var journal_id = $(this).data('journal-id');

			



            if($.inArray(journal_id, GLOBALS.journals_being_processed) == -1 && confirm("Are you sure you would like to delete?")) {

                GLOBALS.journals_being_processed.push(journal_id);

				//alert(data);

                $.getJSON(GLOBALS.ajax_dir + '/delete_journal_inbox.php?journalid=' + journal_id, function(data){

                    if(data.error) {

				

                        createNotification(data.error, 'error');

                    }

                    else if(data.success) {

						

                        removeJournalAndRestyleListDashboard(journal_id);

                    }

                }).fail(function(){

                    createNotification('Operation failed', 'error');

                }).always(function(){

                    GLOBALS.journals_being_processed.splice(GLOBALS.journals_being_processed.indexOf(journal_id), 1);

                });

            }

        });

		

		$(document).on('click', '[rel="delete-journal-user"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var journal_id = $(this).data('user-id');

			//alert(journal_id);



            if($.inArray(journal_id, GLOBALS.journals_being_processed) == -1 ) {

                GLOBALS.journals_being_processed.push(journal_id);

				//alert(data);

                $.getJSON(GLOBALS.ajax_dir + '/delete_user_journal.php?userid=' + journal_id, function(data){

                    if(data.error) {

				

                        createNotification(data.error, 'error');

                    }

                    else if(data.success) {

						

                        removeuserJournal(journal_id);

                    }

                }).fail(function(){

                    createNotification('Operation failed', 'error');

                }).always(function(){

                    GLOBALS.journals_being_processed.splice(GLOBALS.journals_being_processed.indexOf(journal_id), 1);

                });

            }

        });

		

		





        $(document).on('click', '[rel="delete-upload"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var upload_id = $(this).data('upload-id');



            if($.inArray(upload_id, GLOBALS.uploads_being_processed) == -1 && confirm("Are you sure?"))

            {

                GLOBALS.uploads_being_processed.push(upload_id);

                $.getJSON(GLOBALS.ajax_dir + '/delete_upload.php?uploadid=' + upload_id, function(data){

                    //consol.log(data);

                    if(data.error)

                    {

                        createNotification(data.error, 'error');

                    }

                    else if(data.success)

                    {

                        removeJobUpload(upload_id);
                         $("#upload-"+upload_id).remove();
                         //alert(1);

                    }

                }).fail(function(){

                    createNotification('Operation failed', 'error');

                }).always(function(){

                    GLOBALS.uploads_being_processed.splice(GLOBALS.uploads_being_processed.indexOf(upload_id), 1);
                   

                });

            }

        });



        $(document).on('click', '[rel="delete-subscriber"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var subscriber_id = $(this).data('subscriber-id');



            if($.inArray(subscriber_id, GLOBALS.subscribers_being_processed) == -1 && confirm("Are you sure?"))

            {

                GLOBALS.subscribers_being_processed.push(subscriber_id);
                $.getJSON(GLOBALS.ajax_dir + '/delete_subscriber.php?subscriberid=' + subscriber_id, function(data){

                    if(data.error)

                    {

                        createNotification(data.error, 'error');

                    }

                    else if(data.success)

                    {
                        removeJobSubscriber(subscriber_id);

                    }

                }).fail(function(){

                    createNotification('Operation failed', 'error');

                }).always(function(){

                    GLOBALS.subscribers_being_processed.splice(GLOBALS.subscribers_being_processed.indexOf(subscriber_id), 1);

                });

            }

        });



        $(document).on('click', '[rel="delete-material-sheet"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var sheet_id = $(this).data('sheet-id');



            if($.inArray(sheet_id, GLOBALS.sheets_being_processed) == -1 && confirm("Are you sure?"))

            {

                GLOBALS.sheets_being_processed.push(sheet_id);

                $.getJSON(GLOBALS.ajax_dir + '/delete_material_sheet.php?sheetid=' + sheet_id, function(data){

                    if(data.error)

                    {

                        createNotification(data.error, 'error');

                    }

                    else if(data.success)

                    {

                        removeJobMaterialSheet(sheet_id);

                    }

                }).fail(function(){

                    createNotification('Operation failed', 'error');

                }).always(function(){

                    GLOBALS.sheets_being_processed.splice(GLOBALS.sheets_being_processed.indexOf(sheet_id), 1);

                });

            }

        });



        $(document).on('click', '[rel="delete-browsing-history-item"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var element = $(this),

                script = element.data('script'),

                id = element.data('id');



            deleteBrowsingHistoryItem(element);



            $.ajax(addHeightToUrl(GLOBALS.ajax_dir + '/delete_browsing_history_item.php?id=' + id + '&script=' + script));

        });



        $(document).on('click', '[rel="open-modal"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var script = $(this).data('script');

            

            //apply overlay

            applyOverlay(script);

        });

		

		$(document).on('click', '[rel="open-modal-insurance"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var script = $(this).data('script');

            

            //apply overlay

            applyOverlay(script);

        });



        $(document).on('click', '[rel="switch-job-tab"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var tab = $(this).data('tab');



            if(!$(this).hasClass("active"))

            {

                switchJobTab(tab);

            }

        });



        $(document).on('submit', '[rel="filter-list-form"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var action = $(this).attr('action');

            var destination = $(this).data('destination');



            //make request

            filterList(action, destination);

        });



        $(document).on('click', '[rel="filter-list-btn"]', function(e){

            //prevent default action

            e.preventDefault();



            //submit filter list form

            $('[rel="filter-list-form"]').trigger('submit');

        });



        $(document).on('click', '[rel="reset-list-btn"]', function(e){

            //prevent default action

            e.preventDefault();



            //reset all filters and load list

            resetFilterListInputs();

            $('[rel="filter-list-form"]').trigger('submit');

        });



        $(document).on('click', '[rel="make-request"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var action = $(this).data('action'),

                destination = $(this).data('destination'),

                confirmMsg = $(this).data('confirm');



            //make request

            if(!confirmMsg || (confirmMsg && confirm(confirmMsg))) {

                Request.make(action, destination, true, true);

            }

        });



        $(document).on('click', '[rel="change-window-location"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var url = $(this).data('url'),

                confirmMsg = $(this).data('confirm');



            if(!confirmMsg || (confirmMsg && confirm(confirmMsg))) {

                window.location.href = url;

            }

        });



        $(document).on('click', '[rel="change-frame-location"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var url = $(this).data('url');



            setFrameLocation(url);

        });





        $(document).on('click', '[rel="edit-user-stage-advance-access"]', function(e){

            //disable checkbox

            $(this).attr('disabled', true);



            //get data

            var user_id = $(this).data('user-id');

            var stage_id = $(this).data('stage-id');



            //build request url

            var url = GLOBALS.ajax_dir + '/get_userstageadvancement.php?id=' + user_id + '&stageid=' + stage_id;



            //make request

            Request.make(url, 'user-stage-advancement-container', false, true);

        });



        $(document).on('click', '[rel="edit-user-stage-notification"]', function(e){

            //disable checkbox

            $(this).attr('disabled', true);



            //get data

            var action = $(this).data('action');

            var user_id = $(this).data('user-id');

            var stage_num = $(this).data('stage-num');

            var checked = ($(this).attr('checked') ? '' : 'checked');



            //build request url

            var url = GLOBALS.ajax_dir + '/edit_stagenotifications.php?id=' + user_id + '&num=' + stage_num + '&action=' + action + '&checked=' + checked;



            //make request

            Request.make(url, 'stagenotificationscontainer', null, true);

        });
        
        $(document).on('click', '[rel="edit-user-checklist-job-access"]', function(e){
            //disable checkbox
            $(this).attr('disabled', true);

            //get data
            var user_id = $(this).data('user-id');
            var chcklistjob_id = $(this).data('chcklistjob-id');
            //build request url
            var url = GLOBALS.ajax_dir + '/get_user_chcklistjob.php?id=' + user_id + '&chcklistjob_id=' + chcklistjob_id;

            //make request
            Request.make(url, 'user-checklist-job-container', false, true);
        });
        
        $(document).on('click', '[rel="toggle-get-user-chcklistjob"]', function(e){
          var checklist_name = $(this).data('checklist-name');
          var clist=document.getElementsByName(checklist_name);
          var chkListId = $(this).data('chklist-id');
          var checkAll = '';
          if (this.checked)
          {
            checkAll = 1;
          }
          else
          {
              checkAll = 0;
          }
          var all_checked = 0;
          var user_id = '';
          for (var i = 0; i < clist.length; ++i) 
          { 
            $(clist[i]).attr('disabled', true);
            user_id = $(clist[i]).data('user-id');
            
		  }
		  var url = GLOBALS.ajax_dir + '/get_user_chcklistjob.php?id=' + user_id + '&chkListId=' + chkListId + '&chckAll=' + checkAll ;
		  //var url = GLOBALS.ajax_dir + '/get_user_chcklistjob.php?id=' + user_id + '&chcklistjob_id=' + 1;
          Request.make(url, 'user-checklist-job-container', false, true);
          
          for (var i = 0; i < clist.length; ++i) 
          { 
            $(clist[i]).attr('disabled', false);
            
		  }
        });

        $(document).on('click', '[rel="edit-user-todolist-job-access"]', function(e){
            //disable checkbox
            $(this).attr('disabled', true);

            //get data
            var user_id = $(this).data('user-id');
            var todolistjob_id = $(this).data('todolistjob-id');
            //build request url
            var url = GLOBALS.ajax_dir + '/get_user_todolistjob.php?id=' + user_id + '&todolistjob_id=' + todolistjob_id;

            //make request
            Request.make(url, 'user-todolist-job-container', false, true);
        });


        $(document).on('click', '[rel="stop-watching"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var conversation_id = $(this).data('conversation-id');

            var type = $(this).data('type');



            if($.inArray(conversation_id, GLOBALS.watches_being_processed) === -1 && confirm("Are you sure?"))

            {

                GLOBALS.watches_being_processed.push(conversation_id);

                $.getJSON(GLOBALS.ajax_dir + '/stop_watching.php?conversationid=' + conversation_id + '&type=' + type, function(data){

                    if(data.error)

                    {

                        createNotification(data.error, 'error');

                    }

                    else if(data.success)

                    {

                        stopWatching(conversation_id, type);

                    }

                }).fail(function(){

                    createNotification('Operation failed', 'error');

                }).always(function(){

                    GLOBALS.watches_being_processed.splice(GLOBALS.watches_being_processed.indexOf(conversation_id), 1);

                });

            }

        });



        $(document).on('click', '[rel="show-quick-settings"]', function(e){

            //prevent default action

            e.preventDefault();



            //get data

            var job_id = $(this).data('job-id');



            //show quick settings

            showQuickSettings(job_id);

        });

        

        $(document).on('click', '[rel="toggle-user-list"]', function() {

            Chat.toggleUserList();

        }).on('click', '[rel="open-chat"]', function(e) {

            e.preventDefault();

            

            if(!$('#assure-chat').length) {

                parent.Chat.openChat($(this).data('user-id'));

                return;

            }

            

            Chat.openChat($(this).data('user-id'));

        }).on('click', '[rel="close-chat"]', function() {

            Chat.closeChat($(this).closest('.chat').data('user-id'));

        }).on('keypress', '.chat input', function(e) {

            //enter

            if(e.keyCode === 13) {

                Chat.post($(this).closest('.chat').data('user-id'), $(this).val());

                $(this).val('');

                return false;

            }

        }).on('focus', '.chat input', function(e) {

            Chat.removeUserBadgeCount($(this).closest('.chat').data('user-id'));

        }).on('click', '.chat input', function(e) {

            Chat.removeUserBadgeCount($(this).closest('.chat').data('user-id'));

        });

        

        $(document).on('click', '[rel="send-credentials"]', function() {

            var url = GLOBALS.ajax_dir + '/send_credentials.php?id=' + $(this).data('user-id');

            Request.make(url, 'view-action-info', false, true, function() {

                clearInfoContainer('#view-action-info');

            });

        });

        

        $(document).on('mouseenter', '[rel="load-browsing-history"]', loadBrowsingHistory);

        $(document).on('mouseenter', '[rel="load-new-jobs"]', loadNewJobs);

        $(document).on('mouseenter', '[rel="load-bookmarks"]', loadBookmarks);

                

        //trigger filter/sort on change of any list filter input

        $(document).on('change', '.list-filter-input:not(#search)', function() {

            $('[rel="filter-list-form"]').trigger('submit');

        });
        
        //trigger filter/sort on change of any Email list filter input
        
        $(document).on('click', '[rel="email-filter-list-btn"]', function(e){
            //prevent default action
            e.preventDefault();
            //submit filter list form
            
            $('[rel="email-filter-list-btn"]').removeClass('active_email');
            var active_tab = $(this).val().toLowerCase();
            if(active_tab=='sent')
            $("#send_filter").addClass('active_email');
            else
            $("#"+active_tab+"_filter").addClass('active_email');
            
            $("#prev_folder").val($("#email_folder").val());
            $("#email_folder").val($(this).val());
            
            if($("#email_folder").val()!=$("#prev_folder").val())
            $('[rel="filter-email-list-form"]').trigger('submit');
        });
        
        $(document).on('keyup', '#emailsearch.list-filter-input', _.debounce(function() {
            $("#prev_folder").val($("#email_folder").val());
            $('[rel="filter-email-list-form"]').trigger('submit');
        }, 500));
        

        $(document).on('submit', '[rel="filter-email-list-form"]', function(e){
            //prevent default action
            e.preventDefault();
            //get data
            var action = $(this).attr('action');
            var destination = $(this).data('destination');
            //make request
            filterList(action, destination);
        });
        
        

        

        $(document).on('click', '[rel="next-stage"]', function(e) {

            //prevent default action

            e.preventDefault();

            

            var jobId = $(this).data('job-id');

            

            if(!jobId) {

                return false;

            }

            

            if(confirm('Are you sure you want to move to next stage?')) {

                nextStage(jobId);

            }

        });

        

        //remove journal recipient

        $(document).on('click', '[rel="remove-journal-recipient"]', deleteRecipient);

        

        

        $(document).on('click', '[rel="confirm-order"]', function() {

            confirmOrder($(this).data('sheet-id'), $(this).data('job-id'));

        });

        $(document).on('click', '[rel="undo-confirm-order"]', function() {

            unconfirmOrder($(this).data('sheet-id'), $(this).data('job-id'));

        });

        

        $(document).on('click', '[rel="mark-paid"]', function() {

            markTaskPaid($(this).data('task-id'), $(this).data('job-id'));

        });

        $(document).on('click', '[rel="undo-mark-paid"]', function() {

            markTaskUnpaid($(this).data('task-id'), $(this).data('job-id'));

        });

        

        $(document).on('click', '[rel="toggle-sidebar-width"]', function() {

            $('#sidebar').toggleClass('minimized');

            $(window).trigger('resize');

        });

        

        //pagination

        $(document).on('click', '[rel="pagination-previous"]', function(e) {

            e.preventDefault();

            if(_.isUndefined(window.previousPage)) { return; }

            Functions.executeCallback(previousPage);

        });

        $(document).on('click', '[rel="pagination-next"]', function(e) {

            e.preventDefault();

            if(_.isUndefined(window.nextPage)) { return; }

            Functions.executeCallback(nextPage);

        });

        $(document).on('click', '[rel="pagination-first"]', function(e) {

            e.preventDefault();

            if(_.isUndefined(window.firstPage)) { return; }

            Functions.executeCallback(firstPage);

        });

        $(document).on('click', '[rel="pagination-last"]', function(e) {

            e.preventDefault();

            if(_.isUndefined(window.lastPage)) { return; }

            Functions.executeCallback(lastPage);

        });

        

        $(document).on('keyup', '#search.list-filter-input', _.debounce(function() {

            $('[rel="filter-list-form"]').trigger('submit');

        }, 500));

        

        Search.init();

        

        $(document).on('click', '[rel="del-my-li"]', function(e) {

            e.preventDefault();

            $(this).closest('li').remove();

        });

        

        $(document).on('change', '.pikaday', function() {

            var $this = $(this),

                val = $this.val(),

                defaultVal = $this.data('default');

                

            //set default value if found and is valid

            if(!val.length || !moment(val, 'YYYY-MM-DD').isValid()) {

                var m = moment(defaultVal);

                if(defaultVal && m.isValid()) {

                    $this.val(m.format('YYYY-MM-DD'));

                }

            }

        });

        

        $(document).on('click', '[rel="change-parent-location"]', function() {

           parent.window.location = $(this).data('url');

        });

        

//        UI.atWho($('[rel="mention"]'));

    },

            

    setupUI: function() {

        $('select.tss-multi').tssMultiSelectSearch();

        $('select.tss-select').tssSelectSearch();

        $('.pikaday').pikaday();

    }

};

