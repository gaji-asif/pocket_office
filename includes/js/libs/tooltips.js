var Tooltips = {
    
    cachedTooltips: {},
    
    setBindings: function(selector) {
        selector = selector ? selector : '[tooltip]';
        
        $(document).on('mouseenter', '[tooltip]', Tooltips.show);
        $(document).on('mouseleave', '[tooltip]', Tooltips.hide);
        $(document).on('click', '[tooltip]', Tooltips.hide);
    },
    
    show: function(e) {
        var el = $(this),
            toolTip = $('<div class="assure-tooltip" />').html('<div class="ajax-loader-spinner"></div>'),
            xCoord = e.pageX,
            yCoord = e.pageY;
        
        //determine coords
        var finalCoords = {
            top: String(yCoord) + 'px',
            left: xCoord + 'px'
        };
        var windowWidth = $(window).width(),
            windowHeight = $(window).height();
        if((windowWidth - xCoord) < (windowWidth / 2)) {
            finalCoords.left = 'auto';
            finalCoords.right = (windowWidth - xCoord) + 'px';
        }
        if((windowHeight - yCoord) < (windowHeight / 2)) {
            finalCoords.top = 'auto';
            finalCoords.bottom = (windowHeight - yCoord) + 'px';
        }
        toolTip.css(finalCoords).appendTo('body');
        toolTip.fadeIn(100);

        //set title
        Tooltips.getTitle(el, toolTip);
    },
    
    hide: function() {
        $('.assure-tooltip').remove();
    },
    
    getTitle: function(el, toolTip) {
        var title = el.attr('title'),
            type = el.data('type'),
            id = el.data('id'),
            url = el.attr('href');
        
        if(url && url.indexOf('javascript') === -1 && url.indexOf('/docs') === -1) {
            var pieces = this.parseUrl(url);
            if(pieces) {
                id = pieces.id;
                type = pieces.type;
            }
        }
        
        //set tooltip
        if(type && id) {
            this.fetch(type, id, toolTip);
        } else if (el.data('title')) {
            toolTip.html(el.data('title'));
        } else if (title) {
            el.removeAttr('title');
            el.data('title', title);
            toolTip.html(el.data('title'));
        } else {
            toolTip.remove();
        }
    },
            
    fetch: function(type, id, toolTip) {
        //try to get cached version
        var cachedTooltip = this.cachedTooltips[type + id];
        
        if(cachedTooltip) {
            toolTip.html(cachedTooltip);
        }
        
        $.ajax({
            url: GLOBALS.ajax_dir + '/get_tooltip.php?type=' + type + '&id=' + id,
            success: function(data) {
                if(data && data.length) {
                    Tooltips.cachedTooltips[type + id] = data;
                    toolTip.html(data);
                } else {
                    toolTip.remove();
                }
            }
        });
    },
    
    parseUrl: function(url) {
        if(!url || !url.length) {
            return false;
        }
        
        var uri = URI(url),
            filename = uri.filename();
    
        return {
            type: _.first(filename.split('.')).singularize(),
            id: getQueryStringParam(uri.query(), 'id')
        };
    }
    
};
