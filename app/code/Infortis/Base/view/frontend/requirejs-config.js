var config = {
    paths: {
        'smartheader': 'Infortis_Base/js/smartheader',
        'stickyheader': 'Infortis_Base/js/stickyheader',
        'qtycontrol': 'Infortis_Base/js/qtycontrol',
        'expandingsearch': 'Infortis_Base/js/expandingsearch'
    },
    shim: {
        'smartheader': {
            deps: ['jquery', 'mage/dropdown', 'enquire']
        },
        'stickyheader': {
            deps: ['jquery', 'jquery-ui-modules/effect', 'enquire']
        },
        'qtycontrol': {
            deps: ['jquery', 'jquery-ui-modules/core']
        },
        'expandingsearch': {
            deps: ['jquery', 'jquery-ui-modules/core']
        }
    }
};
