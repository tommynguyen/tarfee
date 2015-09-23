(function($) { 
    $.fn.loftplugin = function (options) {
        var defaults = {};
        var options = $.extend(defaults, options);

        $(this).jtable({
            title: '',
            actions: { 
                listAction: en4.core.baseUrl + 'admin/socialloft/ajax/open-loft-plugin'
            //  createAction: '/GettingStarted/CreatePerson',
            //  updateAction: '/GettingStarted/UpdatePerson',
            //  deleteAction: '/GettingStarted/DeletePerson'
            },
            fields: {
                id: {
                    key: true,
                    create: false,
                    edit: false,
                    list: false
                },
                name: {
                    title: 'Products',
                    width: '30%'
                },
                des: {
                    title: 'Description',
                    width: '30%'
                },
                version: {
                    title: 'Version',              
                    create: false,
                    edit: false
                },
                intalled: {
                    title: 'Installed',
                    create: false,
                    edit: false,
                    list: true
                },
                packages: {
                    title: 'Package',
                    create: false,
                    edit: false,
                    list : false
                },
                price:{
                    title: 'Price'
                }
            },
            recordsLoaded:function(){
                $.ajaxCall("admin/socialloft/ajax/installed-plugin");
                socialLOFTicon();
            }
        });

        
    }

})(jQuery)

