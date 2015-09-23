(function($) { 
    $.fn.myplugin = function (options) {
        var defaults = {};
        var options = $.extend(defaults, options);

        $(this).jtable({
            title: '',
            actions: { 
                listAction: en4.core.baseUrl + 'admin/socialloft/ajax/open-my-plugin'
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
                key_info: {
                    title: 'Key License Information',
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
                    list: false
                },
                packages: {
                    title: 'Verify',
                    create: false,
                    edit: false
                }
            },
            recordsLoaded:function(){
                //$Behavior.socialLOFTicon();
                $( ".loft-verify" ).buttonVerify();
            }
        });
        
    }
})(jQuery)


