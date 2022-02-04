var CONFIG = {
	API: {
		METHOD: {
			notes: 'get_notes'
		}
	},
    APP: {
        debug: true
    },
    BOOLEANS: {},
	DATA: {
		global_data_type: 'json'
	},
    DIMENSIONS: {},
    DURATIONS: {},
    EASING: {},
    PATHS: {},
    SELECTORS: {},
    TEMPLATES: {},
	HISTORY: {},
	preSelectElements: function() {
		CONFIG.SELECTORS.body = $('body');
	}
};