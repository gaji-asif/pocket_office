
var Chat = {
    endpoint: '/workflow/includes/ajax/chat.php',
    wrapper: null,
    interval: 8000,
    intervalId: null,
    
    init: function() {
        Chat.wrapper = $('#assure-chat');
        //if chat wrapper exists, get new messages
        if (Chat.wrapper.length !== 0) {
            Chat.startIntervals();
//            Chat.toggleUserList();
        }
    },
    
    fetchAndRenderUserList: function() {
        this.getUsers(this.renderUserList);
    },
    
    post: function(userId, messageText) {
        var payload = { id: userId, text: messageText };
        Data.post(this.endpoint, payload, this.renderMessages);
    },
    
    markRead: function(userId, callback) {
        var payload = { action: 'update', id: userId };
        Data.post(this.endpoint, payload, callback);
    },
    
    getAll: function() {
        Data.get(this.endpoint);
    },
    
    get: function(userId, callback) {
        var payload = { id: userId };
        Data.get(this.endpoint, payload, callback);
    },
    
    getNumNew: function() {
        var payload = { action: 'new' };
        Data.get(this.endpoint, payload);
    },
    
    getUsers: function(callback) {
        var payload = { action: 'users' };
        Data.get(this.endpoint, payload, callback);
    },
            
    toggleUserList: function() {
        var el = $('#user-list'),
            badge = $('#assure-chat > .btn > span.badge');
        
        badge.html('');
        el.toggleClass('hidden');
        this.positionChatWindows();
    },
            
    userListIsVisible: function() {
        var el = $('#user-list');
        return !el.hasClass('hidden');
    },
    
    openChat: function(userId) {
        var that = this;
        
        //first, make sure we have room...
        if (!this.canAddOneMoreChat()) {
            //TODO
            //error message
            return;
        }
        
        //remove count badge
        this.removeUserBadgeCount(userId);
        
        this.get(userId, this.renderChat);
        this.markRead(userId, function() {
            that.stopIntervals();
            that.startIntervals();
        });
    },
            
    chatExists: function(userId) {
        return $('.chat[data-user-id="' + userId + '"]').length !== 0;
    },
            
    positionChatWindows: function() {
        var that = this,
            els = this.getOpenChats();
        els.each(function(i){
            $(this).css('right', String(that.getChatWindowRightOffest(i)) + 'px');
        });
    },
            
    getOpenChats: function() {
        return $('#assure-chat').find('.chat:visible');
    },
            
    getChatWindowRightOffest: function(i) {
        return i * 210 + 47;
    },
            
    canAddOneMoreChat: function() {
        var els = this.getOpenChats(),
            appFrameWidth = $('#app-iframe').width();

        return this.getChatWindowRightOffest(els.length + 1) < appFrameWidth;
    },
            
    renderUserList: function(data) {
        var el = $('#user-list > ul');
        
        //update new message count
        Chat.renderNewMessageNotification(data);
        
        //highlight open chats
        Chat.highlightOpenNewChats(data);
        
        //merge
        var mergedData = {
            users: _.merge(data.results.users, data.results.usersNewMessages)
        };
        el.html(Handlebars.renderTemplate('chat-user-list', mergedData));
    },
            
    renderChat: function(data) {
        if (Chat.chatExists(data.results.user_id)) {
            Chat.refreshChat(data);
            return;
        }

        var el = $('#assure-chat');
        el.append(Handlebars.renderTemplate('chat', data.results));
        
        //get newly created chat and scroll
        var el = $('.chat[data-user-id="' + data.results.user_id + '"] ul');
        el.scrollTop(el[0].scrollHeight);

        //focus on input
        $('.chat[data-user-id="' + data.results.user_id + '"]').find('input').focus();
        
        Chat.positionChatWindows();
    },
            
    refreshChat: function(data) {
        var el = $('.chat[data-user-id="' + data.results.user_id + '"] ul');
        
        //render
        el.html(Handlebars.renderTemplate('chat-message-list', data.results));
        
        //scroll
        el.scrollTop(el[0].scrollHeight);

        //focus on input
        $('.chat[data-user-id="' + data.results.user_id + '"]').find('input').focus();
    },
            
    renderMessages: function(data) {
        var el = $('.chat[data-user-id="' + data.results.user_id + '"] ul');
        el.html(Handlebars.renderTemplate('chat-message-list', data.results));
        el.scrollTop(el[0].scrollHeight);
    },
            
    renderNewMessageNotification: function(data) {
        var el = $('#assure-chat > .btn > span.badge'),
            userList = $('#user-list'),
            newMessages = 0;
        
        //if user list is open, don't show badge
        if(userList.length && userList.is(':visible')) {
            el.html('');
            return;
        }
        
        if (data.results && data.results.usersNewMessages) {
            //only set count to show messages that aren't already open
            _.each(data.results.usersNewMessages, function(message, userId) {
                if(!$('.chat[data-user-id="' + userId + '"]').length) {
                    newMessages++;
                }
            })
            
            if(newMessages > 0) {
                el.text(data.results.new).addClass('end');
            } else {
                el.html('');
            }
        }
    },
            
    highlightOpenNewChats: function(data) {
        if (!data.results || !data.results.usersNewMessages) {
            return;
        }
        
        _.each(data.results.usersNewMessages, function(value) {
            var el = $('.chat[data-user-id="' + value.user_id + '"]');
            if (el.length !== 0) {
                Chat.openChat(value.user_id);
            }
        });
    },
    
    closeChat: function(userId) {
        $('.chat[data-user-id="' + userId + '"]').remove();
        Chat.positionChatWindows();
    },
            
    stopIntervals: function() {
        clearInterval(this.intervalId);
    },
    
    startIntervals: function() {
        Chat.intervalId = setInterval(function() {
                Chat.fetchAndRenderUserList();
        }, Chat.interval);
    },
            
    removeUserBadgeCount: function(userId) {
        //remove count badge
        $('a[rel="open-chat"][data-user-id="' + userId + '"]').siblings('span.badge').remove();
    }
};

$(document).ready(Chat.init);
