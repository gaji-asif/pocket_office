<?php
include 'includes/common_lib.php';
$this_page = new pageinfo('reports.php');
pageSecure($this_page->source);

echo ViewUtil::loadView('doc-head');
echo $this_page->getHeader(TRUE);

//get filters
$filterMaps = ReportUtil::getMap();
$filterMapKeys = array_keys($filterMaps);
?>
<div class="filters-container">
    <ul class="tabs">
<?php
foreach($filterMapKeys as $filterMapKey) {
?>
        <li data-tab="<?=$filterMapKey?>-filters"><?=ucwords($filterMapKey)?></li>
<?php
}
?>
        <li data-tab="saved-reports">Saved</li>
    </ul>
<?php
foreach($filterMapKeys as $filterMapKey) {
//    continue;
?>
    <div class="tab-content" id="<?=$filterMapKey?>-filters"><?=ViewUtil::loadView('report-filters', array('filterMapKey' => $filterMapKey))?></div>
<?php
}
?>
    <div class="tab-content" id="saved-reports"></div>
    <div class="toggle" title="Toggle filters" tooltip>
        <i class="icon-filter"></i>
    </div>
</div>
<div id="report-output"></div>
</body>
<script>
    $(function() {
        $('ul.tabs').each(function() {
            var active, content, tabLinks = $(this).find('li');

            //set active as first tab
            active = $(tabLinks[0]);
            active.addClass('active');
            content = $('#' + active.data('tab'));

            //hide other tabs
            tabLinks.not(active).each(function () {
                $('#' + $(this).data('tab')).hide();
            });

            //bindings
            tabLinks.on('click', function(e) {
                active.removeClass('active');
                content.hide();

                active = $(this);
                active.addClass('active');
                content = $('#' + active.data('tab'));
                content.show();

                e.preventDefault();
            });
        });
        
        $('div.control-binary').each(function() {
            var $this = $(this),
                buttons = $(this).find('div'),
                input = $this.find('input[type="hidden"]');
            
            buttons.click(function() {
                input.val($(this).hasClass('off') ? 0 : 1);
                $this.change();
            });
            
            $this.change(handleChange).change();
            
            function off() {
                $this.removeClass('on').addClass('off');
            };
            function on() {
                $this.removeClass('off').addClass('on');
            };
            function handleChange() {
                var value = input.val();
                if(value == 1) {
                    on();
                } else {
                    off();
                }
            }
        });

        $(document).on('click', '.filters-container > .toggle', function() {
            $(this).parent().toggleClass('open');
        });

        $(document).on('click', '[rel="report-save"], [rel="report-copy"]', function(e) {
            var $this = $(this),
                errors = false,
                $form = $(this).closest('form.report-filter'),
                query = $form.serialize();

            //check for report name and columns
            $form.find('[name="report_name"]').removeClass('error');
            $form.find('[name="columns[]"]').next().removeClass('error');
            if(!$form.find('[name="report_name"]').val() || !$form.find('[name="report_name"]').val().length) {
                $form.find('[name="report_name"]').addClass('error');
                errors = true;
            }
            if(!$form.find('[name="columns[]"]').val() || !$form.find('[name="columns[]"]').val().length) {
                $form.find('[name="columns[]"]').next().addClass('error');
                errors = true;
            }

            if(errors) { return false; }
            
            var url = '<?=AJAX_DIR?>/save_report.php?' + query;
            url = $this.data('copy') ? url + '&copy=1' : url;
            $.getJSON(url, function(data){
                if(data.errors.length) {
                    createNotification(data.errors, 'error');
                } else if(data.success.length) {
                    createNotification(data.success, 'success');
                    var nameValuePairs = data.results.query,
                        table = nameValuePairs.table;
                    fillInFilters($('#' + table + '-filters'), nameValuePairs);
                }
            }).fail(function(){
                createNotification('Operation failed', 'error');
            });

            e.preventDefault();
        });

        $(document).on('click', '[rel="report-csv"]', function(e) {
            var $form = $(this).closest('form.report-filter');

            //check for columns
            $form.find('[name="columns[]"]').next().removeClass('error');
            if(!$form.find('[name="columns[]"]').val()) {
                $form.find('[name="columns[]"]').next().addClass('error');
                return false;
            }

            window.location = '<?=AJAX_DIR?>/generate_report.php?csv=1&' + $form.serialize();

            e.preventDefault();
        });

        $(document).on('click', '[rel="report-view"]', function(e) {
            var $form = $(this).closest('form.report-filter');

            //check for columns
            $form.find('[name="columns[]"]').next().removeClass('error');
            if(!$form.find('[name="columns[]"]').val()) {
                $form.find('[name="columns[]"]').next().addClass('error');
                return false;
            }

            console.log($form.serialize());
            Request.make('<?=AJAX_DIR?>/generate_report.php?' + $form.serialize(), 'report-output', true, true);
            $('.filters-container').removeClass('open');

            e.preventDefault();
        });

        $(document).on('click', '[rel="load-saved-report"]', function(e) {
            e.preventDefault();
            
            var nameValuePairs = $(this).data('query'),
                table = nameValuePairs.table;
            switchToSavedTab(table);
            fillInFilters($('#' + table + '-filters'), nameValuePairs);
            Request.make('<?=AJAX_DIR?>/generate_report.php?' + $.param($(this).data('query')), 'report-output', true, true);
        });
        
        $(document).on('click', '[data-tab="saved-reports"]', function() {
            getSavedReportsList();
        });
        
        $(document).on('click', '[rel="report-clear-filters"]', function() {
            fillInFilters($(this).closest('.tab-content'), []);
        });
        
        $(document).on('click', '[rel="report-delete"]', function(e) {
            if(!confirm('Are you sure?')) { return false; }
            
            var $this = $(this),
                savedReportId = $this.data('id');
            
            $.getJSON('<?=AJAX_DIR?>/delete_report.php?saved_report_id=' + $this.data('id'), function(data){
                if(data.errors.length) {
                    createNotification(data.errors, 'error');
                } else if(data.success.length) {
                    getSavedReportsList();
                    createNotification(data.success, 'success');
                    var $tab = $('[name="saved_report_id"][value="' + savedReportId + '"]').closest('.tab-content');
                    if($tab.length) {
                        fillInFilters($tab, []);
                    }
                }
            }).fail(function(){
                createNotification('Operation failed', 'error');
            });
            e.preventDefault();
        });
    });
    
    function getSavedReportsList() {
        $('#saved-reports').load('<?=AJAX_DIR?>/get_saved_reports.php');
    }
    
    function fillInFilters($tab, nameValuePairs) {
        //get filters
        var $filters = $tab.find('select, input');

        $filters.each(function() {
            var $this = $(this),
                name = String($this.attr('name')).replace('[]', ''),
                value = nameValuePairs[name];
            
            //ignore table.
            if(name === 'table') { return; }
            
            if(!value) {
                if($this.hasClass('tss-multi')) {
                    $this.val([]).change();
                    return;
                } else {
                    $this.val('').removeAttr('checked').change();
                    return;
                }
            }
            
            //checkbox
            if($this.is(':checkbox')) {
                $this.attr('checked', true);
            }
            //everything else
            else {
                $this.val(value).change();
            }
        });
        
        if(nameValuePairs.saved_report_id) {
            $tab.find('[rel="report-copy"]').removeClass('hidden');
        } else {
            $tab.find('[rel="report-copy"]').addClass('hidden');
        }
    }
    
    function switchToSavedTab(table) {
        $('[data-tab="' + table + '-filters"]').click();
    }
</script>
</html>