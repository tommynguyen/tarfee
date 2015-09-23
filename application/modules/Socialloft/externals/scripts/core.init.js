var socialLOFTplugin = "";
var stories = 200;
var ProgressBarIsEnable = false;

jQuery(document).ready(function() {
	jQuery.fn.__tabs = jQuery.fn.tabs;
	jQuery.fn.tabs = function (a, b, c, d, e, f) {
		var base = location.href.replace(/#.*jQuery/, '');
		jQuery('ul>li>a[href^="#"]', this).each(function () {
			var href = jQuery(this).attr('href');
			jQuery(this).attr('href', base + href);
		});
		jQuery(this).__tabs(a, b, c, d, e, f);
	};
	
	
	jQuery.fn.ajaxCall = function(sCall, sExtra, bNoForm, sType)
	{	
		if (empty(sType))
		{
			sType = 'POST';
		}
		
		var sUrl = en4.core.baseUrl;
		
		var sParams='';
		if (sExtra)
		{
			sParams += '&' + ltrim(sExtra, '&');
		}
		console.log(sParams);
		
		oCacheAjaxRequest = jQuery.ajax(
		{
				type: sType,
			  	url: sUrl + sCall,//getParam('sJsStatic') + "ajax.php",
			  	dataType: "script",	
				data: sParams			
			}
		);
		return oCacheAjaxRequest;
	}

	jQuery.ajaxCall = function(sCall, sExtra, sType)
	{
	    return jQuery.fn.ajaxCall(sCall, sExtra, true, sType);
	}
	
    jQuery("#tabs").tabsLoft();
});

function ltrim( str, charlist ) { 
    charlist = !charlist ? ' \s\xA0' : (charlist+'').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
    var re = new RegExp('^[' + charlist + ']+', 'g');
    return (str+'').replace(re, '');
}

var socialLOFTicon = function(){
    jQuery( "div.loft-icon-yes" ).buttonYes();
    jQuery( "div.loft-icon-no" ).buttonNo();
    jQuery( "div.loft-icon-key-info" ).buttonKeyInfo();
    jQuery( ".loft-cart" ).buttonCart();
    jQuery( ".loft-verify" ).buttonVerify();
}

function empty(mixed_var) {
    var key;
    
    if (mixed_var === ""
        || mixed_var === 0
        || mixed_var === "0"
        || mixed_var === null
        || mixed_var === false
        || mixed_var === undefined
        || trim(mixed_var) == ""
    ){
        return true;
    }
    if (typeof mixed_var == 'object') {
        for (key in mixed_var) {
            if (typeof mixed_var[key] !== 'function' ) {
              return false;
            }
        }
        return true;
    }
    return false;
}


function message_verify(result){
    jQuery("#loft-dialog-form-verify" ).dialog( "close" );
    console.log(result);
     
    jQuery('#loft-dialog-message').show().html(result);
    jQuery('#loft-dialog-message').dialog("open");
}
    
function ajaxCall(sCall){
    return '';
}

(function($) { 
    jQuery.fn.buttonYes = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        
        jQuery( this ).button({
            icons: {
                primary: " ui-icon-check "
            },
            text: false
        });
    }

    jQuery.fn.buttonNo = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        
        jQuery( this ).button({
            icons: {
                primary: " ui-icon-close "
            },
            text: false
        });
    }
 
    jQuery.fn.buttonKeyInfo = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        
        jQuery( this ).button({
            icons: {
                primary: " ui-icon-info "
            },
            text: false
        });
    }
   
    jQuery.fn.buttonCart = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        
        jQuery( this ).button({
            icons: {
                primary: " ui-icon-cart "
            },
            text: false
        });
    }
  
    jQuery.fn.buttonVerify = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        
        jQuery( this).button({
            icons: {
                primary: " ui-icon-transferthick-e-w "
            },
            text: false
        }).click(function(){
            console.log(jQuery(this));
            socialLOFTplugin = jQuery(this).attr("name");
            jQuery("#loft-product").val(jQuery(this).attr("name"));
            //jQuery("#loft-dialog-form-verify" ).dialog( "open" );
            jQuery.ajaxCall("admin/socialloft/ajax/validate-license", "product="+jQuery("#loft-product").val() );
            
        });
    }
    
    
    jQuery.fn.dialogVerify = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        
        var name = jQuery( "#loft-license" ),
        email = jQuery( "#email" ),
        password = jQuery( "#password" ),
        allFields = jQuery( [] ).add( name ).add( email ).add( password ),
        tips = jQuery( ".validateTips" );
 
        function updateTips( t ) {
            tips
            .text( t )
            .addClass( "ui-state-highlight" );
            setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
            }, 500 );
        }
 
        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                updateTips( "Length of " + n + " must be  " +
                    min + "." );
                return false;
            } else {
                return true;
            }
        }
 
        function checkRegexp( o, regexp, n ) {
            if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                updateTips( n );
                return false;
            } else {
                return true;
            }
        }
        function updateProgressBar(x) {
            if (x == 1) {
                ProgressBarIsEnable = true;
                jQuery('#loft-progressbar').show();
                jQuery('#loft-progressbar').progressbar({
                    value : 0
                }); 
            }
            if (!ProgressBarIsEnable){
                return;
            }           
            if (x <= stories) {
                jQuery('#loft-progressbar').progressbar('value', parseInt(x / stories * 100));
                setTimeout(function() {
                    updateProgressBar(x + 1)
                }, 200);
            }
        }
        
        jQuery( this ).dialog({
            autoOpen: false,
            height: 300,
            width: 550,
            modal: true,
            resizable: false,
            buttons: {
                "OK": function() {
                    var bValid = true;
                    allFields.removeClass( "ui-state-error" );
 
                    bValid = bValid && checkLength( name, "License Key", 29, 29 );
                    //bValid = true;
                    if ( bValid ) {
                        updateProgressBar(1);
                        sParams = jQuery('#loft-form').serialize()+ '&global_ajax_message=true';

                        jQuery.ajaxCall("admin/socialloft/ajax/verify-license", sParams );
                    //jQuery(this).dialog( "close" );
                    }
                },
                Cancel: function() {
                    jQuery( this ).dialog( "close" );
                }
            },
            close: function() {
                allFields.val( "" ).removeClass( "ui-state-error" );
                ProgressBarIsEnable = false;
                jQuery('#loft-myplugin').jtable('load');
                   
            },
            open: function(){
                jQuery('#loft-progressbar').hide();
                ProgressBarIsEnable = false;
            }
        });
    }
    
    jQuery.fn.dialogMessage = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        
        jQuery( this).dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                Ok: function() {
                    jQuery( this ).dialog( "close" );
                },
                Reset : function(){
                    jQuery( "#loft-dialog-form-verify" ).dialog("open");
                    jQuery( this ).dialog( "close" );
                }
            }
        });
    }


    jQuery.fn.tabsLoft = function (options) {
        var defaults = {};
        var options = jQuery.extend(defaults, options);
        jQuery( "#loft-dialog-form-verify" ).dialogVerify(); 
        jQuery( "#loft-dialog-message" ).dialogMessage();
        jQuery('#loft-socialplugin').loftplugin();
        jQuery('#loft-myplugin').myplugin();        
        jQuery( this ).tabs({
        	activate: function( event, ui ) {
       	
                var name_tab =  ui.newTab.find('a:first').attr('rel'); ; //ui.tab.find('a:first').attr('href');                
                switch(name_tab)
                {      
                    case 'tabs-socialloft-plugin':
                        jQuery('#loft-socialplugin').jtable('load');   
                        break;
                    case 'tabs-my-plugin':
                        //jQuery.ajaxCall("socialloft.openMyPlugin");
                        jQuery('#loft-myplugin').jtable('load');
                        break;
                    case 'tabs-verify-plugin':
                        
                        break;
                    case 'tabs-socialloft-support':
                       
                        break;
                }
            }
       
        });
        jQuery('#loft-main').show();
        jQuery('#loft-socialplugin').jtable('load');
    }
    
    
    
})(jQuery)


    /*(function( $ ){

        $.fn.loftCore = function() {};
    })( jQuery );*/