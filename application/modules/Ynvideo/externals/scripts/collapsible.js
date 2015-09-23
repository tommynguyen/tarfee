/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
window.addEvent('domready', function() {
   $$('.ynvideo-category-sub-category').set('styles', {
        display : 'none'
    });
    
    $$('.ynvideo-category-collapse-control').addEvent('click', function(event) {
        var row = this.getParent('tr');
        var rowSubCategories = row.getAllNext('tr');
        if (this.hasClass('ynvideo-category-collapsed')) {  
            this.removeClass('ynvideo-category-collapsed');
            this.addClass('ynvideo-category-no-collapsed')
            for(var i = 0; i < rowSubCategories.length; i++) {
                if (!rowSubCategories[i].hasClass('ynvideo-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'table-row'
                    });
                }
            }
        } else {
            this.removeClass('ynvideo-category-no-collapsed');
            this.addClass('ynvideo-category-collapsed');
            for(var i = 0; i < rowSubCategories.length; i++) {
                if (!rowSubCategories[i].hasClass('ynvideo-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});