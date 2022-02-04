var UI = {
    
    generatePagination: function(options) {
        //remove pagination
        $('div.pagination').remove();
        
        options.pageLower = (options.page * options.numPerPage) + 1;
        options.pageUpper = (options.page * options.numPerPage) + options.numPerPage;
        options.pageUpper = options.pageUpper > options.totalRows ? options.totalRows : options.pageUpper;
        options.displayPage = options.page + 1;
        
        options.afterTarget.after(Handlebars.renderTemplate('pagination', options));
    },
            
    atWho: function(el) {
        el.atwho({
            at: "@", 
            data: '/includes/ajax/get_users_for_mention.php',
            limit: 10,
            tpl: "<li data-value='${atwho-at}${username}'>${name} (${username})</li>",
        });
    }
    
};