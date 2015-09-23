var ynfeedback = {
		
	vote: function(feedback_id, widget_id){
		en4.core.request.send(new Request.HTML({
	        url : en4.core.baseUrl + 'feedback/vote-feedback',
	        data : {
	          feedback_id : feedback_id,
	          widget_id : widget_id
	        },
	        onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) 
	    	{
	    		 if ($$("[id^=ynfeedback-item-vote-action-"+feedback_id+"]").length){
	    			 elements = $$("[id^=ynfeedback-item-vote-action-"+feedback_id+"]");
	    			 elements.each(function(e, index){
	    				 e.set('html', responseHTML);
	    			 });
	    		 }
	    	}
		}), {
	        'element' : $('ynfeedback-item-vote-action-'+feedback_id+'-'+widget_id)
		});
		
	},
	
	unvote: function(feedback_id, widget_id){
		en4.core.request.send(new Request.HTML({
	        url : en4.core.baseUrl + 'feedback/unvote-feedback',
	        data : {
	          feedback_id : feedback_id,
	          widget_id : widget_id
	        },
	        onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) 
	    	{
	    		 if ($$("[id^=ynfeedback-item-vote-action-"+feedback_id+"]").length){
	    			 elements = $$("[id^=ynfeedback-item-vote-action-"+feedback_id+"]");
	    			 elements.each(function(e, index){
	    				 e.set('html', responseHTML);
	    			 });
	    		 }
	    	}
		}), {
	        'element' : $('ynfeedback-item-vote-action-'+feedback_id+'-'+widget_id)
		});
	},
	
	attachCreateComment : function(formElement){
	    formElement.addEvent('submit', function(event){
	      event.stop();
	      if ($("poster_name"))
    	  {
	    	  if ($("poster_name").value.trim() == '')
    		  {
	    		  alert(en4.core.language.translate("Invalid Name"));
	    		  return false;
    		  }
    	  }
	      if ($("poster_email"))
    	  {
	    	  if (!ynfeedback.checkEmail($("poster_email").value))
    		  {
	    		  alert(en4.core.language.translate("Invalid Email"));
	    		  return false;
    		  }
    	  }
	      if ($("body"))
    	  {
	    	  if ($("body").value.trim() == '')
    		  {
	    		  alert(en4.core.language.translate("Invalid Comment Content"));
	    		  return false;
    		  }
    	  }
	      var form_values  = formElement.toQueryString();
	          form_values += '&format=json';
	          form_values += '&id='+formElement.identity.value;
	      en4.core.request.send(new Request.JSON({
	        url : en4.core.baseUrl + 'feedback/comment/create',
	        data : form_values
	      }), {
	        'element' : $('comments')
	      });
	    })
	},
	
	loadComments : function(type, id, page){
	    en4.core.request.send(new Request.HTML({
	      url : en4.core.baseUrl + 'feedback/comment/list',
	      data : {
	        format : 'html',
	        type : type,
	        id : id,
	        page : page
	      }
	    }), {
	      'element' : $('comments')
	    });
	}, 
	
	comment : function(type, id, body){
	    en4.core.request.send(new Request.JSON({
	      url : en4.core.baseUrl + 'feedback/comment/create',
	      data : {
	        format : 'json',
	        type : type,
	        id : id,
	        body : body
	      }
	    }), {
	      'element' : $('comments')
	    });
	},
	
	like : function(type, id, comment_id) {
	    en4.core.request.send(new Request.JSON({
	      url : en4.core.baseUrl + 'feedback/comment/like',
	      data : {
	        format : 'json',
	        type : type,
	        id : id,
	        comment_id : comment_id
	      }
	    }), {
	      'element' : $('comments')
	    });
	},

	unlike : function(type, id, comment_id) {
	    en4.core.request.send(new Request.JSON({
	      url : en4.core.baseUrl + 'feedback/comment/unlike',
	      data : {
	        format : 'json',
	        type : type,
	        id : id,
	        comment_id : comment_id
	      }
	    }), {
	      'element' : $('comments')
	    });
	},

	showLikes : function(type, id){
	    en4.core.request.send(new Request.HTML({
	      url : en4.core.baseUrl + 'feedback/comment/list',
	      data : {
	        format : 'html',
	        type : type,
	        id : id,
	        viewAllLikes : true
	      }
	    }), {
	      'element' : $('comments')
	    });
	},

	deleteComment : function(type, id, comment_id) {
	    if( !confirm(en4.core.language.translate('Are you sure you want to delete this?')) ) {
	      return;
	    }
	    (new Request.JSON({
	      url : en4.core.baseUrl + 'feedback/comment/delete',
	      data : {
	        format : 'json',
	        type : type,
	        id : id,
	        comment_id : comment_id
	      },
	      onComplete: function() {
	        if( $('comment-' + comment_id) ) {
	          $('comment-' + comment_id).destroy();
	        }
	        try {
	          var commentCount = $$('.comments_options span')[0];
	          var m = commentCount.get('html').match(/\d+/);
	          var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
	          commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
	        } catch( e ) {}
	      }
	    })).send();
	},
	
	checkEmail : function(email){
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
		if (email == '' || !re.test(email))
		{
		    return false;
		}
		return true;
	}
	
};