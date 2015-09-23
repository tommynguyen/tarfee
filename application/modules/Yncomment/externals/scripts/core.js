en4.yncomment = {
	editCommentInfo : {}
};
var tempUnlike = 0;
var tempLike = 0;
en4.yncomment.yncomments = {
	loadCommentReplies : function(comment_id) {
		$$('.reply' + comment_id).setStyle('display', 'inline-block');
		$('replies_show_' + comment_id).setStyle('display', 'none');
		$('replies_hide_' + comment_id).setStyle('display', 'inline-block');
	},
	hideCommentReplies : function(comment_id) {
		$$('.reply' + comment_id).setStyle('display', 'none');
		$('replies_hide_' + comment_id).setStyle('display', 'none');
		$('replies_show_' + comment_id).setStyle('display', 'inline-block');
	},
	showReplyEditForm : function(reply_id, is_enter_submit) {
		if (document.getElementsByClassName('reply_edit')) {
			var elements = document.getElementsByClassName('reply_edit');
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = 'none';
			}
		}
		if (document.getElementsByClassName('comment_edit')) {
			var elements = document.getElementsByClassName('comment_edit');
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = 'none';
			}
		}
		
		if (document.getElementsByClassName('comment_close')) {
			var elements = document.getElementsByClassName('comment_close');
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = 'none';
			}
		}

		var elements = document.getElementsByClassName('comments_body');
		for (var i = 0; i < elements.length; i++) {
			elements[i].style.display = 'initial';
		}
		
		var elements = document.getElementsByClassName('yncomment_comments_attachment');
		for (var i = 0; i < elements.length; i++) {
			elements[i].style.display = 'block';
		}
		
		$$('.yncomment_replies_pulldown_open').each(function(item, index)
        {
            item.removeClass('yncomment_replies_pulldown_open');
        }); 
		
		if ($('activity-reply-edit-form-' + reply_id).getElementById('compose-container') == null) {
			en4.yncomment.yncomments.attachReply($('activity-reply-edit-form-' + reply_id), is_enter_submit, 'edit');
			$('activity-reply-edit-form-' + reply_id).body.value = htmlDecode(replyAttachment.editReply[reply_id].body);
		}
		
		if(!$('activity-reply-edit-form-' + reply_id).hasClass('yncommnet_click_textarea'))
		{
			$('activity-reply-edit-form-' + reply_id).addClass('yncommnet_click_textarea');
		}
		$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});

		$('activity-reply-edit-form-' + reply_id).style.display = 'block';
		$('reply_body_' + reply_id).style.display = 'none';
		if($('yncomment_comments_attachment_' + reply_id))
			$('yncomment_comments_attachment_' + reply_id).style.display = 'none';
		$('reply_edit_' + reply_id).style.display = 'block';
		$('close_edit_box-' + reply_id).style.display = 'block';
	},
	showCommentEditForm : function(comment_id, is_enter_submit) {
		if (document.getElementsByClassName('reply_edit')) {
			var elements = document.getElementsByClassName('reply_edit');
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = 'none';
			}
		}

		if (document.getElementsByClassName('comment_edit')) {
			var elements = document.getElementsByClassName('comment_edit');
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = 'none';
			}
		}
		
		if (document.getElementsByClassName('comment_close')) {
			var elements = document.getElementsByClassName('comment_close');
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = 'none';
			}
		}
		
		var elements = document.getElementsByClassName('comments_body');
		for (var i = 0; i < elements.length; i++) {
			elements[i].style.display = 'initial';
		}
		
		var elements = document.getElementsByClassName('yncomment_comments_attachment');
		for (var i = 0; i < elements.length; i++) {
			elements[i].style.display = 'block';
		}
		
		$$('.yncomment_replies_pulldown_open').each(function(item, index)
        {
            item.removeClass('yncomment_replies_pulldown_open');
        }); 

		if ($('activity-comment-edit-form-' + comment_id).getElementById('compose-container') == null) 
		{
			// make content edit
			en4.yncomment.yncomments.attachComment($('activity-comment-edit-form-' + comment_id), is_enter_submit, 'edit');
			$('activity-comment-edit-form-' + comment_id).body.value = htmlDecode(commentAttachment.editComment[comment_id].body);
		}
		if(!$('activity-comment-edit-form-' + comment_id).hasClass('yncommnet_click_textarea'))
		{
			$('activity-comment-edit-form-' + comment_id).addClass('yncommnet_click_textarea');
		}
		$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});
		
		$('activity-comment-edit-form-' + comment_id).style.display = 'block';
		$('comments_body_' + comment_id).style.display = 'none';
		if($('yncomment_comments_attachment_' + comment_id))
			$('yncomment_comments_attachment_' + comment_id).style.display = 'none';
		$('comment_edit_' + comment_id).style.display = 'block';
		$('close_edit_box-' + comment_id).style.display = 'block';
	},
	comment : function(action_id, body, extendClass, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, action, comment_id) {

		if (body.trim() == '' && formElementPhotoValue == '') {
			return;
		}
		
		var show_all_comments_value = 0;
		if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
			show_all_comments_value = show_all_comments[action_id];
		}
		
		var comment_like_box_show_value = 0;
		if ( typeof comment_like_box_show != 'undefined' && comment_like_box_show[action_id]) {
			comment_like_box_show_value = comment_like_box_show[action_id];
		}
		
		if (formElementPhotoSrc) {
			var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><div class="yncomment_comments_attachment" id="yncomment_comments_attachment"><div class="yncomment_comments_attachment_photo"><a><img src="' + formElementPhotoSrc + '" alt="" class="thumbs_photo thumb_normal item_photo_album_photo  thumb_normal"></a><div class="yncomment_comments_attachment_info"><div class="yncomment_comments_attachment_title"></div><div class="yncomment_comments_attachment_des"></div></div></div><ul class="comments_date"><li class="comments_timestamp">' + en4.ynfeed.fewSecHTML + '</li></ul></div>';
		} else {
			var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><ul class="comments_date"><li class="comments_timestamp">' + en4.ynfeed.fewSecHTML + '</li></ul></div>';
		}
		if (action == 'create') {
			if ($("feed-comment-form-open-li_" + extendClass + action_id)) {
				new Element('li', {
					'html' : CommentHTML,
				}).inject($("feed-comment-form-open-li_" + extendClass + action_id), 'before');
			} else {
				new Element('li', {
					'html' : CommentHTML,
				}).inject($('comment-likes-activity-item-' + extendClass + action_id).getElement('.comments').getElement('ul'));
			}
		}

		form_values += '&format=json';
		form_values += '&subject=' + en4.core.subject.guid;
		form_values += '&show_all_comments=' + show_all_comments_value;
		form_values += '&comment_like_box_show=' + comment_like_box_show_value;
		form_values += '&onViewPage=' + extendClass;
		form_values += '&linkEnabled=' + linkEnabled;
		form_values += '&photo_id=' + formElementPhotoValue;
		form_values += '&type=' + formElementTypeValue;
		var url;
		if (action == 'create') {
			url = en4.core.baseUrl + 'ynfeed/index/comment'; 
		} else if (action == 'edit') {
			url = en4.core.baseUrl + 'yncomment/index/comment-edit';
			$('comment-' + comment_id).innerHTML = CommentHTML;
		}
		
		en4.core.request.send(new Request.JSON({
			url : url,
			data : $merge(form_values.parseQueryString(), {
				body : body
			}),
			onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
		}), {
			'force' : true,
			'element' : $('comment-likes-activity-item-' + extendClass + action_id)
		});
	},
	reply : function(comment_id, body, extendClass, action_id, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, action) {
		if (body.trim() == '' && formElementPhotoValue == '') {
			return;
		}
		
		var show_all_comments_value = 0;
		if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
			show_all_comments_value = show_all_comments[action_id];
		}
		
		var comment_like_box_show_value = 0;
		if ( typeof comment_like_box_show != 'undefined' && comment_like_box_show[action_id]) {
			comment_like_box_show_value = show_all_comments[action_id];
		}

		if (formElementPhotoSrc) {
			var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><div class="yncomment_comments_attachment" id="yncomment_comments_attachment"><div class="yncomment_comments_attachment_photo"><a><img src="' + formElementPhotoSrc + '" alt="" class="thumbs_photo thumb_normal item_photo_album_photo  thumb_normal"></a><div class="yncomment_comments_attachment_info"><div class="yncomment_comments_attachment_title"></div><div class="yncomment_comments_attachment_des"></div></div></div><ul class="comments_date"><li class="comments_timestamp">' + en4.ynfeed.fewSecHTML + '</li></ul></div>';
		} else {
			var CommentHTML = '<div class="comments_author_photo"><a href="' + en4.user.viewer.href + '" ><img src="' + en4.user.viewer.iconUrl + '"  class="thumb_icon item_photo_user  thumb_icon"></a></div><div class="comments_info"><span class="comments_author"><a href="' + en4.user.viewer.href + '" class="" rel="user 1">' + en4.user.viewer.title + '</a></span><span class="comments_body">' + body + '</span><ul class="comments_date"><li class="comments_timestamp">' + en4.ynfeed.fewSecHTML + '</li></ul></div>';
		}
		if (action == 'create') {
			if ($("feed-reply-form-open-li_" + extendClass + comment_id)) {
				new Element('li', {
					'html' : CommentHTML,
				}).inject($("feed-reply-form-open-li_" + extendClass + comment_id), 'before');
			} else {
				new Element('li', {
					'html' : CommentHTML,
				}).inject($('comment-likes-activity-item-' + extendClass + action_id).getElement('.comments').getElement('ul'));
			}
		}
		var url;
		if (action == 'create') {
			url = en4.core.baseUrl + 'yncomment/index/reply';
		} else if (action == 'edit') {
			url = en4.core.baseUrl + 'yncomment/index/reply-edit';
			$('reply-' + comment_id).innerHTML = CommentHTML;
		}

		form_values += '&format=json';
		form_values += '&subject=' + en4.core.subject.guid;
		form_values += '&show_all_comments=' + show_all_comments_value;
		form_values += '&comment_like_box_show=' + comment_like_box_show_value;
		form_values += '&onViewPage=' + extendClass;
		form_values += '&linkEnabled=' + linkEnabled;
		form_values += '&photo_id=' + formElementPhotoValue;
		form_values += '&type=' + formElementTypeValue;
		en4.core.request.send(new Request.JSON({
			url : url,
			data : $merge(form_values.parseQueryString(), {
				body : body
			}),
		}), {
			'force' : true,
			'element' : $('comment-likes-activity-item-' + extendClass + action_id)
		});
	},
	attachComment : function(formElement, is_enter_submit, action, body) {
		var bind = this;
		var hasViewPage = formElement.get('id').indexOf('view') < 0 ? 0 : 1;
		var extendClass = '';
		if (hasViewPage) {
			extendClass = 'view-';
		}

		var composerObj = new ComposerYnActivityComment($(formElement.body.get('id')), {
			overText : true,
			lang : {
				'Post Something...' : 'Write a comment...'
			},
			allowEmptyWithAttachment : false,
			submitElement : 'submit'
		});
		
		if ( typeof action != 'undefined'
			 && action == 'edit' 
			 && commentAttachment.editComment[formElement.comment_id.value].body != '') {
			composerObj.setContent(commentAttachment.editComment[formElement.comment_id.value].body, 1);
			composerObj.focus();
		}
		formElement.store('composer', composerObj);

		composerObj.addPlugin(new ComposerYnActivityComment.Plugin.Tag({
			enabled : true,
			suggestOptions : {
				'url' : en4.core.baseUrl + 'yncomment/friends/suggest-tag/includeSelf/1',
				'postData' : {
					'format' : 'json',
					'subject' : en4.core.subject.guid,
					'taggingContent' : activityTaggingContent
				},
				'maxChoices' : 10
			},
			'suggestProto' : 'request.json'
		}));
		
		if (smiliesEnabled) {
			var emoticons_parent_icons = new Element('div', {
				'id' : 'emoticons-parent-icons_' + formElement.get('id'),
				'class' : 'yncomment_emoticons'
			}).inject($(formElement.get('id')));

			emoticons_parent_icons.innerHTML = $('emoticons-comment-icons').innerHTML;
			$('emoticons-comment-button').setAttribute('id', 'emoticons-comment-button_' + formElement.get('id'));
			$('emotion_comment_label').setAttribute('id', 'emotion_comment_label_' + formElement.get('id'));
			$('emotion_comment_symbol').setAttribute('id', 'emotion_comment_symbol_' + formElement.get('id'));
			$('emoticons-comment-board').setAttribute('id', 'emoticons-comment-board_' + formElement.get('id'));
		}
		if (photoEnabled) {
			var commentyncommentPhoto = new commentPhoto();
			commentyncommentPhoto.getPhotoContent(formElement.get('id'), {
				requestOptions : {
					'url' : requestOptionsURLYnComment
				},
				fancyUploadOptions : {
					'url' : fancyUploadOptionsURLYnComment,
					'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
				}
			});

			if (typeof action != 'undefined' 
				&& (commentAttachment.editComment[formElement.comment_id.value].attachment_type == 'album_photo' || commentAttachment.editComment[formElement.comment_id.value].attachment_type == 'advalbum_photo') 
				&& action == 'edit' 
				&& commentAttachment.editComment[formElement.comment_id.value].attachment_body != '') 
			{
				commentyncommentPhoto.activate();
				commentyncommentPhoto.doProcessResponse(commentAttachment.editComment[formElement.comment_id.value].attachment_body);
				if(commentyncommentPhoto.elements.body.getElementById('compose-photo-form-fancy-file'))
				{
					commentyncommentPhoto.elements.body.getElementById('compose-photo-form-fancy-file').style.display = 'none';
				}
			}
		}
		var formElementPhotoValue = '';
		var formElementTypeValue = '';
		var formElementPhotoSrc = '';
		
		if (is_enter_submit == 1) 
		{
			formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function(event) 
			{
				if (event.shift && event.key == 'enter') 
				{
				} 
				else if (event.key == 'enter') 
				{
					event.stop();
					if (formElement.photo_id && formElement.photo_id.value)
						formElementPhotoValue = formElement.photo_id.value;
					if (formElement.type && formElement.type.value)
						formElementTypeValue = formElement.type.value;
					if (formElement.src && formElement.src.value)
						formElementPhotoSrc = formElement.src.value;
					
					if (composerObj.getPlugin('tag'))
						composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
					
					var form_values = composerObj.getForm().toQueryString();
					form_values = form_values.replace("body=&", "");
					if ((formElementPhotoValue == '' && composerObj.getContent() == '') || formElement.retrieve('sendReq', false)) {
						return;
					}

					if ( typeof action != 'undefined' && action == 'edit') {
						bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'edit', formElement.comment_id.value);
					} else {
						bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'create', 0);
					}
					formElement.body.value = '';
					formElement.style.display = "none";
				}
			});
		}
		formElement.addEvent('submit', function(event) {
			event.stop();
			if (formElement.photo_id && formElement.photo_id.value)
				formElementPhotoValue = formElement.photo_id.value;
			if (formElement.type && formElement.type.value)
				formElementTypeValue = formElement.type.value;
			if (formElement.src && formElement.src.value)
				formElementPhotoSrc = formElement.src.value;
			if ((formElementPhotoValue == '' && composerObj.getContent() == '') || formElement.retrieve('sendReq', false)) {
				return;
			}
			
			if (composerObj.getPlugin('tag'))
				composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
			
			var form_values = composerObj.getForm().toQueryString();
			form_values = form_values.replace("body=&", "");

			if ( typeof action != 'undefined' && action == 'edit') {
				bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'edit', formElement.comment_id.value);
			} else {
				bind.comment(formElement.action_id.value, composerObj.getContent(), extendClass, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'create', 0);
			}
			formElement.body.value = '';
			formElement.style.display = "none";
		});
	},
	attachReply : function(formElement, is_enter_submit, action, body) {
		var bind = this;
		var hasViewPage = formElement.get('id').indexOf('view') < 0 ? 0 : 1;
		var extendClass = '';
		if (hasViewPage) {
			extendClass = 'view-';
		}

		var composerObj = new ComposerYnActivityComment($(formElement.body.get('id')), {
			overText : true,
			lang : {
				'Post Something...' : 'Write a reply...'
			},
			hideSubmitOnBlur : false,
			allowEmptyWithAttachment : false,
			submitElement : 'submit'
		});

		if ( typeof action != 'undefined' && action == 'edit' && replyAttachment.editReply[formElement.comment_id.value].body != '') {
			composerObj.setContent(replyAttachment.editReply[formElement.comment_id.value].body, 1);
			composerObj.focus();
		}
		formElement.store('composer', composerObj);
		composerObj.addPlugin(new ComposerYnActivityComment.Plugin.Tag({
			enabled : true,
			suggestOptions : {
				'url' : en4.core.baseUrl + 'yncomment/friends/suggest-tag/includeSelf/1',
				'postData' : {
					'format' : 'json',
					'subject' : en4.core.subject.guid,
					'taggingContent' : activityTaggingContent
				},
				'maxChoices' : 10
			},
			'suggestProto' : 'request.json'
		}));

		if (smiliesEnabled) {
			var emoticons_parent_icons = new Element('div', {
				'id' : 'emoticons-parent-icons_' + formElement.get('id'),
				'class' : 'yncomment_emoticons'
			}).inject($(formElement.get('id')));

			emoticons_parent_icons.innerHTML = $('emoticons-comment-icons').innerHTML;

			if (is_enter_submit == 0) {
			}

			$('emoticons-comment-button').setAttribute('id', 'emoticons-comment-button_' + formElement.get('id'));
			$('emotion_comment_label').setAttribute('id', 'emotion_comment_label_' + formElement.get('id'));
			$('emotion_comment_symbol').setAttribute('id', 'emotion_comment_symbol_' + formElement.get('id'));
			$('emoticons-comment-board').setAttribute('id', 'emoticons-comment-board_' + formElement.get('id'));
		}

		if (photoEnabled) {
			var commentyncommentPhoto = new commentPhoto();
			commentyncommentPhoto.getPhotoContent(formElement.get('id'), {
				requestOptions : {
					'url' : requestOptionsURLYnComment
				},
				fancyUploadOptions : {
					'url' : fancyUploadOptionsURLYnComment,
					'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
				}
			});
			if (typeof action != 'undefined' 
				&& (replyAttachment.editReply[formElement.comment_id.value].attachment_type == 'album_photo' || replyAttachment.editReply[formElement.comment_id.value].attachment_type == 'advalbum_photo')
				&& action == 'edit' 
				&& replyAttachment.editReply[formElement.comment_id.value].attachment_body != '') {
				commentyncommentPhoto.activate();
				commentyncommentPhoto.doProcessResponse(replyAttachment.editReply[formElement.comment_id.value].attachment_body);
				if(commentyncommentPhoto.elements.body.getElementById('compose-photo-form-fancy-file'))
				{
					commentyncommentPhoto.elements.body.getElementById('compose-photo-form-fancy-file').style.display = 'none';
				}
			}
		}

		var formElementPhotoValue = '';
		var formElementTypeValue = '';
		var formElementPhotoSrc = '';
		if (is_enter_submit == 1) {
			formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function(event) {
				if (event.shift && event.key == 'enter') {
				} else if (event.key == 'enter') {
					event.stop();
					if (formElement.photo_id && formElement.photo_id.value)
						formElementPhotoValue = formElement.photo_id.value;
					if (formElement.type && formElement.type.value)
						formElementTypeValue = formElement.type.value;
					if (formElement.src && formElement.src.value)
						formElementPhotoSrc = formElement.src.value;
						
					if (composerObj.getPlugin('tag'))
						composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
						
					if ((formElementPhotoValue == '' && composerObj.getContent() == '') || formElement.retrieve('sendReq', false)) {
						return;
					}
					var form_values = composerObj.getForm().toQueryString();
					form_values = form_values.replace("body=&", "");
					if ( typeof action != 'undefined' && action == 'edit') {
						bind.reply(formElement.comment_id.value, composerObj.getContent(), extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'edit');
					} else {
						bind.reply(formElement.comment_id.value, composerObj.getContent(), extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'create');
					}
					formElement.body.value = '';
					formElement.style.display = "none";
				}
			});
		}
		formElement.addEvent('submit', function(event) {
			event.stop();
			if (formElement.photo_id && formElement.photo_id.value)
				formElementPhotoValue = formElement.photo_id.value;
			if (formElement.type && formElement.type.value)
				formElementTypeValue = formElement.type.value;
			if (formElement.src && formElement.src.value)
				formElementPhotoSrc = formElement.src.value;
			
			if (composerObj.getPlugin('tag'))
				composerObj.getPlugin('tag').getComposer().fireEvent('editorSubmit');
			
			if ((formElementPhotoValue == '' && composerObj.getContent() == '') || formElement.retrieve('sendReq', false)) {
				return;
			}
			var form_values = composerObj.getForm().toQueryString();
			form_values = form_values.replace("body=&", "");

			if ( typeof action != 'undefined' && action == 'edit') {
				bind.reply(formElement.comment_id.value, formElement.body.value, extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'edit');
			} else {
				bind.reply(formElement.comment_id.value, formElement.body.value, extendClass, formElement.action_id.value, formElementPhotoValue, formElementTypeValue, formElementPhotoSrc, form_values, 'create');
			}
			formElement.body.value = '';
			formElement.style.display = "none";
		});
	},
	loadComments : function(type, id, page, order, parent_comment_id, taggingContent, showComposerOptions, pre, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {

		if ($('view_more_comments_' + parent_comment_id) && pre == 3) {
			$('view_more_comments_' + parent_comment_id).style.display = 'inline-block';
			$('view_more_comments_' + parent_comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		if ($('view_previous_comments_' + parent_comment_id) && pre == 2) {
			$('view_previous_comments_' + parent_comment_id).style.display = 'inline-block';
			$('view_previous_comments_' + parent_comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		if ($('view_later_comments_' + parent_comment_id) && pre == 1) {
			$('view_later_comments_' + parent_comment_id).style.display = 'inline-block';
			$('view_later_comments_' + parent_comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}

		en4.core.request.send(new Request.HTML({
			url : en4.core.baseUrl + 'yncomment/comment/list',
			data : {
				format : 'html',
				type : type,
				id : id,
				page : page,
				order : order,
				parent_div : 1,
				parent_comment_id : parent_comment_id,
				taggingContent : taggingContent,
				showComposerOptions : showComposerOptions,
				showAsNested : showAsNested,
				showAsLike : showAsLike,
				showDislikeUsers : showDislikeUsers,
				showLikeWithoutIcon : showLikeWithoutIcon,
				showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
				showSmilies : showSmilies,
				photoLightboxComment : photoLightboxComment,
				commentsorder : commentsorder

			},
			onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
		}), {
			'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
		});
	},
	loadcommentssortby : function(type, id, order, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
		if ($('sort' + '_' + type + '_' + id + '_' + parent_comment_id)) {
			$('sort' + '_' + type + '_' + id + '_' + parent_comment_id).style.display = 'inline-block';
			$('sort' + '_' + type + '_' + id + '_' + parent_comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		en4.core.request.send(new Request.HTML({
			url : en4.core.baseUrl + 'yncomment/comment/list',
			data : {
				format : 'html',
				type : type,
				id : id,
				order : order,
				parent_div : 1,
				parent_comment_id : parent_comment_id,
				taggingContent : taggingContent,
				showComposerOptions : showComposerOptions,
				showAsNested : showAsNested,
				showAsLike : showAsLike,
				showDislikeUsers : showDislikeUsers,
				showLikeWithoutIcon : showLikeWithoutIcon,
				showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
				showSmilies : showSmilies,
				photoLightboxComment : photoLightboxComment,
				commentsorder : commentsorder
			},
			onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
		}), {
			'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
		});
	},
	loadcommentsfilterby : function(type, id, filter, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
		if ($('filter' + '_' + type + '_' + id + '_' + parent_comment_id)) {
			$('filter' + '_' + type + '_' + id + '_' + parent_comment_id).style.display = 'inline-block';
			$('filter' + '_' + type + '_' + id + '_' + parent_comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		en4.core.request.send(new Request.HTML({
			url : en4.core.baseUrl + 'yncomment/comment/list',
			data : {
				format : 'html',
				type : type,
				id : id,
				filter : filter,
				parent_div : 1,
				parent_comment_id : parent_comment_id,
				taggingContent : taggingContent,
				showComposerOptions : showComposerOptions,
				showAsNested : showAsNested,
				showAsLike : showAsLike,
				showDislikeUsers : showDislikeUsers,
				showLikeWithoutIcon : showLikeWithoutIcon,
				showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
				showSmilies : showSmilies,
				photoLightboxComment : photoLightboxComment,
				commentsorder : commentsorder
			},
			onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
		}), {
			'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
		});
	},
	like : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if (tempLike == 0) {
			tempUnlike = tempLike = 1;
			if ($('like_comments_' + comment_id) && (option == 'child')) {
				$('like_comments_' + comment_id).style.display = 'inline-block';
				$('like_comments_' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			if ($('like_comments_' + type + '_' + id) && (option == 'parent')) {
				$('like_comments_' + type + '_' + id).style.display = 'inline-block';
				$('like_comments_' + type + '_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'yncomment/comment/like',
				data : {
					format : 'json',
					type : type,
					id : id,
					comment_id : comment_id,
					order : order,
					parent_comment_id : parent_comment_id,
					taggingContent : taggingContent,
					showComposerOptions : showComposerOptions,
					showAsNested : showAsNested,
					showAsLike : showAsLike,
					showDislikeUsers : showDislikeUsers,
					showLikeWithoutIcon : showLikeWithoutIcon,
					showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
					page : page,
					showSmilies : showSmilies,
					photoLightboxComment : photoLightboxComment,
					commentsorder : commentsorder
				},
				onComplete : function(e) {
					tempUnlike = tempLike = 0;
					$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
				}
			}), {
				'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
			});
		}
	},
	undolike : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if (tempLike == 0) {
			tempLike = tempUnlike = 1;
			if ($('like_comments_' + comment_id) && (option == 'child')) {
				$('like_comments_' + comment_id).style.display = 'inline-block';
				$('like_comments_' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			if ($('like_comments_' + type + '_' + id) && (option == 'parent')) {
				$('like_comments_' + type + '_' + id).style.display = 'inline-block';
				$('like_comments_' + type + '_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'yncomment/comment/undolike',
				data : {
					format : 'json',
					type : type,
					id : id,
					comment_id : comment_id,
					order : order,
					parent_comment_id : parent_comment_id,
					taggingContent : taggingContent,
					showComposerOptions : showComposerOptions,
					showAsNested : showAsNested,
					showAsLike : showAsLike,
					showDislikeUsers : showDislikeUsers,
					showLikeWithoutIcon : showLikeWithoutIcon,
					showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
					page : page,
					showSmilies : showSmilies,
					photoLightboxComment : photoLightboxComment,
					commentsorder : commentsorder
				},
				onComplete : function(e) {
					tempLike = tempUnlike = 0;
					$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
				}
			}), {
				'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
			});
		}
	},
	unsure : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if (tempLike == 0) {
			tempUnlike = tempLike = 1;
			if ($('unsure_comments_' + comment_id) && (option == 'child')) {
				$('unsure_comments_' + comment_id).style.display = 'inline-block';
				$('unsure_comments_' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			if ($('unsure_comments_' + type + '_' + id) && (option == 'parent')) {
				$('unsure_comments_' + type + '_' + id).style.display = 'inline-block';
				$('unsure_comments_' + type + '_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'yncomment/comment/unsure',
				data : {
					format : 'json',
					type : type,
					id : id,
					comment_id : comment_id,
					order : order,
					parent_comment_id : parent_comment_id,
					taggingContent : taggingContent,
					showComposerOptions : showComposerOptions,
					showAsNested : showAsNested,
					showAsLike : showAsLike,
					showDislikeUsers : showDislikeUsers,
					showLikeWithoutIcon : showLikeWithoutIcon,
					showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
					page : page,
					showSmilies : showSmilies,
					photoLightboxComment : photoLightboxComment,
					commentsorder : commentsorder
				},
				onComplete : function(e) {
					tempUnlike = tempLike = 0;
					$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
				}
			}), {
				'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
			});
		}
	},
	undounsure : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if (tempLike == 0) {
			tempLike = tempUnlike = 1;
			if ($('unsure_comments_' + comment_id) && (option == 'child')) {
				$('unsure_comments_' + comment_id).style.display = 'inline-block';
				$('unsure_comments_' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			if ($('unsure_comments_' + type + '_' + id) && (option == 'parent')) {
				$('unsure_comments_' + type + '_' + id).style.display = 'inline-block';
				$('unsure_comments_' + type + '_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'yncomment/comment/undounsure',
				data : {
					format : 'json',
					type : type,
					id : id,
					comment_id : comment_id,
					order : order,
					parent_comment_id : parent_comment_id,
					taggingContent : taggingContent,
					showComposerOptions : showComposerOptions,
					showAsNested : showAsNested,
					showAsLike : showAsLike,
					showDislikeUsers : showDislikeUsers,
					showLikeWithoutIcon : showLikeWithoutIcon,
					showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
					page : page,
					showSmilies : showSmilies,
					photoLightboxComment : photoLightboxComment,
					commentsorder : commentsorder
				},
				onComplete : function(e) {
					tempLike = tempUnlike = 0;
					$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
				}
			}), {
				'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
			});
		}
	},
	unlike : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if (tempUnlike == 0) {
			tempLike = tempUnlike = 1;
			if ($('unlike_comments_' + comment_id) && (option == 'child')) {
				$('unlike_comments_' + comment_id).style.display = 'inline-block';
				$('unlike_comments_' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			if ($('unlike_comments_' + type + '_' + id) && (option == 'parent')) {
				$('unlike_comments_' + type + '_' + id).style.display = 'inline-block';
				$('unlike_comments_' + type + '_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'yncomment/comment/unlike',
				data : {
					format : 'json',
					type : type,
					id : id,
					comment_id : comment_id,
					order : order,
					parent_comment_id : parent_comment_id,
					taggingContent : taggingContent,
					showComposerOptions : showComposerOptions,
					showAsNested : showAsNested,
					showAsLike : showAsLike,
					showDislikeUsers : showDislikeUsers,
					showLikeWithoutIcon : showLikeWithoutIcon,
					showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
					page : page,
					showSmilies : showSmilies,
					photoLightboxComment : photoLightboxComment,
					commentsorder : commentsorder
				},
				onComplete : function(e) {
					tempLike = tempUnlike = 0;
					$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
				}
			}), {
				'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
			});
		}
	},
	undounlike : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if (tempUnlike == 0) {
			tempLike = tempUnlike = 1;
			if ($('unlike_comments_' + comment_id) && (option == 'child')) {
				$('unlike_comments_' + comment_id).style.display = 'inline-block';
				$('unlike_comments_' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			if ($('unlike_comments_' + type + '_' + id) && (option == 'parent')) {
				$('unlike_comments_' + type + '_' + id).style.display = 'inline-block';
				$('unlike_comments_' + type + '_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'yncomment/comment/undounlike',
				data : {
					format : 'json',
					type : type,
					id : id,
					comment_id : comment_id,
					order : order,
					parent_comment_id : parent_comment_id,
					taggingContent : taggingContent,
					showComposerOptions : showComposerOptions,
					showAsNested : showAsNested,
					showAsLike : showAsLike,
					showDislikeUsers : showDislikeUsers,
					showLikeWithoutIcon : showLikeWithoutIcon,
					showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
					page : page,
					showSmilies : showSmilies,
					photoLightboxComment : photoLightboxComment,
					commentsorder : commentsorder
				},
				onComplete : function(e) {
					tempLike = tempUnlike = 0;
					$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
				}
			}), {
				'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
			});
		}
	},
	showLikes : function(type, id, order, parent_comment_id, taggingContent, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
		en4.core.request.send(new Request.HTML({
			url : en4.core.baseUrl + 'yncomment/comment/list',
			data : {
				format : 'html',
				type : type,
				id : id,
				viewAllLikes : true,
				order : order,
				parent_comment_id : parent_comment_id,
				taggingContent : taggingContent,
				showComposerOptions : showComposerOptions,
				showAsNested : showAsNested,
				showAsLike : showAsLike,
				showDislikeUsers : showDislikeUsers,
				showLikeWithoutIcon : showLikeWithoutIcon,
				showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
				showSmilies : showSmilies,
				photoLightboxComment : photoLightboxComment,
				commentsorder : commentsorder
			},
			onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
		}), {
			'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
		});
	},
	deleteComment : function(type, id, comment_id, order, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
		if ($('comment-' + comment_id)) {
			$('comment-' + comment_id).destroy();
		}
		YncommentSmoothboxClose();
		(new Request.JSON({
			url : en4.core.baseUrl + 'yncomment/comment/delete',
			data : {
				format : 'json',
				type : type,
				id : id,
				comment_id : comment_id,
				order : order,
				parent_comment_id : parent_comment_id,
				taggingContent : taggingContent,
				showComposerOptions : showComposerOptions,
				showAsNested : showAsNested,
				showAsLike : showAsLike,
				showDislikeUsers : showDislikeUsers,
				showLikeWithoutIcon : showLikeWithoutIcon,
				showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
				showSmilies : showSmilies,
				photoLightboxComment : photoLightboxComment,
				commentsorder : commentsorder
			},
			onComplete : function(e) {
				try {
					var replyCount = $$('.yncomment_replies_options span')[0];
					var m = replyCount.get('html').match(/\d+/);
					var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
					replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
					if (e.commentsCount == 0 || e.commentsCount == 1) {
						if ($("yncomment_replies_sorting"))
							$("yncomment_replies_sorting").style.display = 'none';

						if ($("yncomment_replies_li"))
							$("yncomment_replies_li").style.display = 'none';
					}
				} catch (e) {
				}
			}
		})).send();
	},
	openHideComment : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if ($('comment-' + comment_id)) {
			$('comment-' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		en4.core.request.send(new Request.JSON({
			url : en4.core.baseUrl + 'yncomment/comment/open-hide-comment',
			data : {
				format : 'json',
				type : type,
				id : id,
				comment_id : comment_id,
				order : order,
				parent_comment_id : parent_comment_id,
				taggingContent : taggingContent,
				showComposerOptions : showComposerOptions,
				showAsNested : showAsNested,
				showAsLike : showAsLike,
				showDislikeUsers : showDislikeUsers,
				showLikeWithoutIcon : showLikeWithoutIcon,
				showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
				page : page,
				showSmilies : showSmilies,
				photoLightboxComment : photoLightboxComment,
				commentsorder : commentsorder
			},
			onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
		}), {
			'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
		});
	},
	unHideComment : function(type, id, comment_id, order, parent_comment_id, option, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies, page) {
		if ($('comment-' + comment_id)) {
			$('comment-' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		en4.core.request.send(new Request.JSON({
			url : en4.core.baseUrl + 'yncomment/comment/un-hide-comment',
			data : {
				format : 'json',
				type : type,
				id : id,
				comment_id : comment_id,
				order : order,
				parent_comment_id : parent_comment_id,
				taggingContent : taggingContent,
				showComposerOptions : showComposerOptions,
				showAsNested : showAsNested,
				showAsLike : showAsLike,
				showDislikeUsers : showDislikeUsers,
				showLikeWithoutIcon : showLikeWithoutIcon,
				showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
				page : page,
				showSmilies : showSmilies,
				photoLightboxComment : photoLightboxComment,
				commentsorder : commentsorder
			},
			onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
		}), {
			'element' : $('comments' + '_' + type + '_' + id + '_' + parent_comment_id)
		});
	}
};

function hideComment(id, type) {
	if (hideReqActive)
		return;
    hideReqActive = true;
	var url = en4.core.baseUrl + 'yncomment/comment/hide-item';
	var req = new Request.JSON({
		url : url,
		data : {
			format : 'json',
			type : type,
			id : id
		},
		onComplete : function(responseJSON) {
			if ($('comment-undo-' + id)) {
				$('comment-undo-' + id).destroy();
			}
			if ($('reply-undo-' + id)) {
				$('reply-undo-' + id).destroy();
			}
			if($('comment-' + id))
			{
				$('comment-' + id).style.display = 'none';
				var innerHTML = "<li id='comment-undo-" + id + "'><div class='comment_item_hide'>" + "<i class=\"fa fa-eye-slash\"></i>" + en4.core.language.translate('This comment has been hidden.') + " <a href='javascript:void(0);' onclick='unHideComment(\"" + id + "\" , \"" + type + "\")'>" + en4.core.language.translate('Unhide') + " </a> <br /> ";
				innerHTML = innerHTML + "</div></li>";
				Elements.from(innerHTML).inject($('comment-' + id), 'after');
			}
			if($('reply-' + id))
			{
				$('reply-' + id).style.display = 'none';
				var innerHTML = "<li id='reply-undo-" + id + "'><div class='comment_item_hide'>" + "<i class=\"fa fa-eye-slash\"></i>" + en4.core.language.translate('This reply has been hidden.') + " <a href='javascript:void(0);' onclick='unHideComment(\"" + id + "\" , \"" + type + "\")'>" + en4.core.language.translate('Unhide') + " </a> <br /> ";
				innerHTML = innerHTML + "</div></li>";
				Elements.from(innerHTML).inject($('reply-' + id), 'after');
			}
			hideReqActive = false;
		}
	});
	req.send();
}

function unHideComment(id, type) {
	if (unhideReqActive)
		return;
	unhideReqActive = true;
	var url = en4.core.baseUrl + 'yncomment/comment/un-hide-item';
	var req = new Request.JSON({
		url : url,
		data : {
			format : 'json',
			type : type,
			id : id
		},
		onComplete : function(responseJSON) 
		{
			if ($('comment-undo-' + id))
				$('comment-undo-' + id).destroy();
			if ($('comment-' + id)) {
				$('comment-' + id).style.display = '';
			}
			if ($('reply-undo-' + id))
				$('reply-undo-' + id).destroy();
			if ($('reply-' + id)) {
				$('reply-' + id).style.display = '';
			}
			unhideReqActive = false;
		}
	});
	req.send();
}

function showReplyData(option, id, type, subject, hide) {
	if (option == 1) {
		if ($('yncomment_data-' + id))
			$('yncomment_data-' + id).style.display = 'none';
		if ($('comment-' + id))
			$('comment-' + id).className = "yncomment_replies_list yncomment_comments_hide";
		if ($('show_' + id))
			$('show_' + id).style.display = 'block';
		if ($('hide_' + id))
			$('hide_' + id).style.display = 'none';
		if ($('comments_' + type + '_' + subject + '_' + id))
			$('comments_' + type + '_' + subject + '_' + id).style.display = 'none';

	} else {
		if ($('yncomment_data-' + id))
			$('yncomment_data-' + id).style.display = 'block';
		if ($('show_' + id))
			$('show_' + id).style.display = 'none';
		if ($('hide_' + id))
			$('hide_' + id).style.display = 'block';
		if ($('comment-' + id))
			$('comment-' + id).className = "yncomment_replies_list";
		if ($('comments_' + type + '_' + subject + '_' + id))
			$('comments_' + type + '_' + subject + '_' + id).style.display = 'block';
	}
	if (hide) {
		$('comment-' + id).className += ' yncomment_hidden_open';
	}
}

function sortComments(order, type, id, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
	en4.yncomment.yncomments.loadcommentssortby(type, id, order, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies);
}

function filterComments(filter, type, id, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies) {
	en4.yncomment.yncomments.loadcommentsfilterby(type, id, filter, parent_comment_id, taggingContent, showComposerOptions, showAsNested, showAsLike, showDislikeUsers, showLikeWithoutIcon, showLikeWithoutIconInReplies);
}

function showReplyForm(type, id, comment_id) {

	if (document.getElementsByClassName('comments_form_yncomments_comments')) {
		var elements = document.getElementsByClassName('comments_form_yncomments_comments');
		for (var i = 0; i < elements.length; i++) {
			if (elements[i] != $('comments-form_' + type + '_' + id + '_' + comment_id)) {
				elements[i].style.display = 'none';
			}
		}
	}
	if ($('comments-form_' + type + '_' + id + '_' + comment_id).style.display == 'none') 
	{
		$('comments-form_' + type + '_' + id + '_' + comment_id).style.display = 'block';
		if (($('comments-form_' + type + '_' + id + '_' + comment_id).getElementById('compose-container')) == null) {
			makeComposer($($('comments-form_' + type + '_' + id + '_' + comment_id).body).id, type, id, comment_id, null, 'Write a reply...', 0);
			tagContentComment();
		}
	} else {
		$('comments-form_' + type + '_' + id + '_' + comment_id).style.display = 'none';
	}
	$('comments-form_' + type + '_' + id + '_' + comment_id).body.focus();
}

function showEditForm(type, id, comment_id, parent_comment_id) {
	// Expand comment
	showReplyData(0, comment_id);

	var beforeDisplay = $('yncomment_edit_comment_' + comment_id).style.display;

	// Hide all other comments edit form
	if (document.getElementsByClassName('comment_edit')) {
		var elements = document.getElementsByClassName('comment_edit');
		for (var i = 0; i < elements.length; i++) {
			elements[i].style.display = 'none';
		}
	}

	// Show content of all comments
	if (document.getElementsByClassName('yncomment_replies_comment')) {
		var elements = document.getElementsByClassName('yncomment_replies_comment');
		for (var i = 0; i < elements.length; i++) {
			elements[i].style.display = 'block';
		}
	}

	// Hide all cancel edit link
	if (document.getElementsByClassName('comment_close')) {
		var elements = document.getElementsByClassName('comment_close');
		for (var i = 0; i < elements.length; i++) {
			elements[i].style.display = 'none';
		}
	}
	
	// Hide all options
	$$('.yncomment_replies_pulldown_open').each(function(item, index)
    {
        item.removeClass('yncomment_replies_pulldown_open');
    }); 

	// Show edit form
	if ($('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display == '' || $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display == 'none' || beforeDisplay == 'none') {
		$('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display = 'block';
		$('yncomment_edit_comment_' + comment_id).style.display = 'block';
		$('yncomment_comment_data-' + comment_id).style.display = 'none';
		if ($('close_edit_box-' + comment_id)) {
			$('close_edit_box-' + comment_id).style.display = 'block';
		}
	} else {
		$('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display = 'none';
		$('yncomment_edit_comment_' + comment_id).style.display = 'none';
		$('yncomment_comment_data-' + comment_id).style.display = 'block';
		if ($('close_edit_box-' + comment_id))
			$('close_edit_box-' + comment_id).style.display = 'none';
	}

	// Check and add compose container if not exists
	if (($('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).getElementById('compose-container')) == null) {
		makeComposer($($('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).body).id, type, id, comment_id, parent_comment_id, 'Write a comment...', 1);
		tagContentComment();
		if ($('close_edit_box-' + comment_id)) {
			$('close_edit_box-' + comment_id).style.display = 'block';
		}
	}
	
	if(!$('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).hasClass('yncommnet_click_textarea'))
	{
		$('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).addClass('yncommnet_click_textarea');
	}
	
	$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});
	// Focus to edit comment body
	$('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).body.focus();
}

var makePhotoComposer = function() {

	if (composeInstanceComment.options.type)
		type = composeInstanceComment.options.type;
	composeInstanceComment.addPlugin(new ComposerYnComment.Plugin.Photo({
		title : en4.core.language.translate('Insert Photo'),
		lang : {
			'Add Photo' : en4.core.language.translate('Insert Photo'),
			'Select File' : en4.core.language.translate('Select File'),
			'cancel' : en4.core.language.translate('cancel'),
			'Loading...' : en4.core.language.translate('Loading...'),
			'Unable to upload photo. Please click cancel and try again' : en4.core.language.translate('Unable to upload photo. Please click cancel and try again')
		},
		requestOptions : {
			'url' : en4.core.baseUrl + 'yncomment/album/compose-upload/type/comment'
		},
		fancyUploadOptions : {
			'url' : en4.core.baseUrl + 'yncomment/album/compose-upload/format/json/type/comment',
			'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
		}
	}));
}
var makeLinkComposer = function() {
	composeInstanceComment.addPlugin(new ComposerYnComment.Plugin.Link({
		title : en4.core.language.translate('Insert Link'),
		lang : {
			'cancel' : en4.core.language.translate('cancel'),
			'Last' : en4.core.language.translate('Last'),
			'Next' : en4.core.language.translate('Next'),
			'Attach' : en4.core.language.translate('Attach'),
			'Loading...' : en4.core.language.translate('Loading...'),
			'Don\'t show an image' : en4.core.language.translate('Don\'t show an image'),
			'Choose Image:' : en4.core.language.translate('Choose Image:'),
			'%d of %d' : en4.core.language.translate('%d of %d')
		},
		requestOptions : {
			'url' : en4.core.baseUrl + 'yncomment/link/preview'
		}
	}));
}
function makeComposer(body, type, id, comment_id, parent_comment_id, overtext, edit) {
	if ( typeof parent_comment_id != 'undefined' && parent_comment_id != null) {
		menuElement = 'compose-containe-menu-items_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id;
		var formEle = $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id);
	} else {
		menuElement = 'compose-containe-menu-items_' + type + '_' + id + '_' + comment_id;
		var formEle = $('comments-form_' + type + '_' + id + '_' + comment_id);
	}
	var lanText = 'Write a comment...';
	if ( typeof overtext != 'undefined') {
		lanText = overtext;
	}
	// @todo integrate this into the composer
	composeInstanceComment = new ComposerYnComment(body, {
		menuElement : menuElement,
		baseHref : en4.core.baseUrl,
		lang : {
			'Post Something...' : 'Write a comment...'
		},
		type : type,
		id : id,
		parent_comment_id : comment_id,
		edit_comment_id : parent_comment_id,
		taggingContent : taggingContent,
		showComposerOptions : showComposerOptions,
		showAsNested : showAsNested,
		showAsLike : showAsLike,
		showDislikeUsers : showDislikeUsers,
		showLikeWithoutIcon : showLikeWithoutIcon,
		showLikeWithoutIconInReplies : showLikeWithoutIconInReplies,
		overText : true,
		showLangText : lanText,
		showSmilies : showSmilies,
		photoLightboxComment : photoLightboxComment,
		commentsorder : commentsorder
	});
	if (showAddPhoto == 1) {
		makePhotoComposer();
	}

	if (showAddLink == 1) {
		makeLinkComposer();
	}

	if (showSmilies == 1) {
		makeSmilies(formEle, menuElement);
	}

	if ( typeof parent_comment_id != 'undefined' && parent_comment_id != null) {
		composerContent = $('yncomment_comment_data-' + comment_id);

		composeInstanceComment.setContent(en4.yncomment.editCommentInfo[comment_id].body, edit);
		if (en4.yncomment.editCommentInfo[comment_id].attachment_type == 'album_photo' || en4.yncomment.editCommentInfo[comment_id].attachment_type == 'advalbum_photo') 
		{
			composeInstanceComment.getPlugin('photo').activate();
			composeInstanceComment.getPlugin('photo').doProcessResponse(en4.yncomment.editCommentInfo[comment_id].attachment_body);
		} else if (en4.yncomment.editCommentInfo[comment_id].attachment_type == 'core_link') {
			composeInstanceComment.getPlugin('link').activate();
			composeInstanceComment.getPlugin('link').doAttach(en4.yncomment.editCommentInfo[comment_id].attachment_body.url);
		}
	}
	$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});
}

function htmlDecode(string) 
{
	string = string.replace(/yncomment_span_open/gi, '<span').replace(/yncomment_close/gi, '>').replace(/yncomment_span_close/gi, '</span>').replace(/yncomment_quotation/gi, '"');
	string = string.replace(/[\r\n]+/ig, "</br>").trim();
	return string;
}

function makeSmilies(formEle, menuElement) {

	if (nestedCommentPressEnter == 1) {
		var emoticons_parent_icons = new Element('div', {
			'id' : 'emoticons-parent-icons_' + formEle.get('id'),
			'class' : 'yncomment_emoticons',
			'styles' : {
				'display' : 'none'
			}
		}).inject(menuElement);
		emoticons_parent_icons.inject(formEle.getElementById('compose-container'), 'after');
	} else {
		var emoticons_parent_icons = new Element('div', {
			'id' : 'emoticons-parent-icons_' + formEle.get('id'),
			'class' : 'yncomment_emoticons yncomment_inside_smile',
			'styles' : {
				'display' : 'none'
			}
		}).inject(menuElement);

		emoticons_parent_icons.inject($("composer_container_icons_" + formEle.get('action-id')));
	}

	emoticons_parent_icons.innerHTML = $('emoticons-yncomment-comment-icons').innerHTML;

	$('emoticons-yncomment-comment-button').setAttribute('id', 'emoticons-yncomment-comment-button_' + formEle.get('id'));
	$('emotion_yncomment_comment_label').setAttribute('id', 'emotion_yncomment_comment_label_' + formEle.get('id'));
	$('emotion_yncomment_comment_symbol').setAttribute('id', 'emotion_yncomment_comment_symbol_' + formEle.get('id'));
	$('emoticons-yncomment-comment-board').setAttribute('id', 'emoticons-yncomment-comment-board_' + formEle.get('id'));

}

function tagContentComment() {

	composeInstanceComment.addPlugin(new ComposerYnComment.Plugin.Nctag({
		enabled : true,
		suggestOptions : {
			'url' : en4.core.baseUrl + 'yncomment/friends/suggest-tag/includeSelf/1',
			'postData' : {
				'format' : 'json',
				'subject' : en4.core.subject.guid,
				'taggingContent' : taggingContent
			},
			'maxChoices' : 10
		},
		'suggestProto' : 'request.json'
	}));
}

en4.yncomment.ajaxTab = {
	click_elment_id : '',
	attachEvent : function(widget_id, params) {
		params.requestParams.content_id = widget_id;
		var element;

		$$('.tab_' + widget_id).each(function(el) {
			if (el.get('tag') == 'li') {
				element = el;
				return;
			}
		});
		var onloadAdd = true;
		if (element) {
			if (element.retrieve('addClickEvent', false))
				return;
			element.addEvent('click', function() {
				if (en4.yncomment.ajaxTab.click_elment_id == widget_id)
					return;
				en4.yncomment.ajaxTab.click_elment_id = widget_id;
				en4.yncomment.ajaxTab.sendReq(params);
			});
			element.store('addClickEvent', true);
			var attachOnLoadEvent = false;
			if (widget_id) {
				attachOnLoadEvent = true;
			} else {
				$$('.tabs_parent').each(function(element) {
					var addActiveTab = true;
					element.getElements('ul > li').each(function(el) {
						if (el.hasClass('active')) {
							addActiveTab = false;
							return;
						}
					});
					element.getElementById('main_tabs').getElements('li:first-child').each(function(el) {
						el.get('class').split(' ').each(function(className) {
							className = className.trim();
							if (className.match(/^tab_[0-9]+$/) && className == "tab_" + widget_id) {
								attachOnLoadEvent = true;
								if (addActiveTab || tab_content_id_sitestore == widget_id) {
									element.getElementById('main_tabs').getElements('ul > li').removeClass('active');
									el.addClass('active');
									element.getParent().getChildren('div.' + className).setStyle('display', null);
								}
								return;
							}
						});
					});
				});
			}
			if (!attachOnLoadEvent)
				return;
			onloadAdd = false;

		}

		en4.core.runonce.add(function() {
			if (onloadAdd)
				params.requestParams.onloadAdd = true;
			en4.yncomment.ajaxTab.click_elment_id = widget_id;
			en4.yncomment.ajaxTab.sendReq(params);
		});

	},
	sendReq : function(params) {
		params.responseContainer.each(function(element) {
			element.empty();
			new Element('div', {
				'class' : 'yncomment_profile_loading_image'
			}).inject(element);
		});
		var url = en4.core.baseUrl + 'widget';

		if (params.requestUrl)
			url = params.requestUrl;

		var request = new Request.HTML({
			url : url,
			data : $merge(params.requestParams, {
				format : 'html',
				subject : en4.core.subject.guid,
				is_ajax_load : true
			}),
			evalScripts : true,
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				params.responseContainer.each(function(container) {
					container.empty();
					Elements.from(responseHTML).inject(container);
					en4.core.runonce.trigger();
					Smoothbox.bind(container);
				});
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox i', openCommentOptions);
			}
		});
		request.send();
	}
};

var hideCommentEmotionIconClickEnable = false;
var hideYnCommentEmotionIconClickEnable = false;
function setCommentEmoticonsBoard(obj) { 
	var formEle = obj.getParent().getParent();
	$('emotion_comment_label_' + formEle.get('id')).innerHTML = "";
	$('emotion_comment_symbol_' + formEle.get('id')).innerHTML = "";
	hideCommentEmotionIconClickEnable = true;
	hideYnCommentEmotionIconClickEnable = true;
	var a = $('emoticons-comment-button_' + formEle.get('id'));
	a.toggleClass('emoticons_comment_active');
	a.toggleClass('');
	var el = $('emoticons-comment-board_' + formEle.get('id'));
	el.toggleClass('yncomment_comment_embox_open');
	el.toggleClass('yncomment_comment_embox_closed');
	
	// Check offset
	var global_content = obj.getParent('#global_content');
	var y_position = obj.getPosition(global_content).y;
	var p_height = global_content.getHeight();
	var c_height = obj.getChildren('.yncomment_comment_embox').getHeight();
	if(p_height - y_position < c_height)
	{
		obj.addClass('emoticons_comment_reverse');
	}
	
}

function addCommentEmotionIcon(iconCode, obj) {
	var content;
	var formEle = obj.getParent().getParent().getParent().getParent();
	var composerObj = formEle.retrieve('composer');

	content = composerObj.elements.body.get('html');
	content = content.replace(/(<br>)$/g, "");

	content = content + ' ' + iconCode;
	composerObj.setContent(content);
	composerObj.focus();
}

//hide on body click

en4.core.runonce.add(function() {
	$(document.body).addEvent('click', function(e) {
		if(e.target != $$('.pxs_next').shift())
		{
			hideCommentEmotionIconClickEvent();
		}
	});
});

function hideCommentEmotionIconClickEvent() {
	if (!hideCommentEmotionIconClickEnable && $$('.yncomment_comment_embox')) 
	{
		$$('.yncomment_comment_embox').removeClass('yncomment_comment_embox_open').addClass('yncomment_comment_embox_closed');
		$$('.adv_post_smile').removeClass('emoticons_comment_active');
	}
	hideCommentEmotionIconClickEnable = false;
}

function setCommentEmotionLabelPlate(label, symbol, obj) {
	var formEle = obj.getParent().getParent().getParent().getParent();
	$('emotion_comment_label_' + formEle.get('id')).innerHTML = label;
	$('emotion_comment_symbol_' + formEle.get('id')).innerHTML = symbol;
}

function setYnCommentEmoticonsBoard(obj) 
{
	if (composeInstanceComment)
		composeInstanceComment.focus();
	if (nestedCommentPressEnter == 1) 
	{
		var formEle = obj.getParent().getParent();
	} else 
	{
		var formEle = obj.getParent().getParent().getParent().getParent();
	}

	$('emotion_yncomment_comment_label_' + formEle.get('id')).innerHTML = "";
	$('emotion_yncomment_comment_symbol_' + formEle.get('id')).innerHTML = "";
	hideYnCommentEmotionIconClickEnable = true;
	hideCommentEmotionIconClickEnable = true;
	var a = $('emoticons-yncomment-comment-button_' + formEle.get('id'));
	a.toggleClass('emoticons_comment_active');
	a.toggleClass('');
	var el = $('emoticons-yncomment-comment-board_' + formEle.get('id'));
	el.toggleClass('yncomment_comment_embox_open');
	el.toggleClass('yncomment_comment_embox_closed');
	// Check offset
	var global_content = obj.getParent('#global_content');
	var y_position = obj.getPosition(global_content).y;
	var p_height = global_content.getHeight();
	var c_height = obj.getChildren('.yncomment_comment_embox').getHeight();
	if(p_height - y_position < c_height)
	{
		obj.addClass('emoticons_comment_reverse');
	}
}

function addYnCommentEmotionIcon(iconCode, obj) {
	var formEle = obj.getParent().getParent().getParent().getParent().getParent().getParent();
	if (formEle) {
		var input = formEle.getElementById('compose-container').getElementsByClassName('compose-content');
		if (input.length > 0) {
			input = input[0];
			var content = input.innerHTML;
			content = content.replace(/(<br>)$/g, "");
			content = content + ' ' + iconCode;
			input.innerHTML = content;
			input.focus();
		}
	}
}

en4.core.runonce.add(function() {
	$(document.body).addEvent('click', function(e) {
		if(e.target != $$('.pxs_next').shift())
		{
			hideYnCommentEmotionIconClickEvent();
		}
		
	});
});

function hideYnCommentEmotionIconClickEvent() {
	if (!hideYnCommentEmotionIconClickEnable && $$('.yncomment_comment_embox')) {
		$$('.yncomment_comment_embox').removeClass('yncomment_comment_embox_open').addClass('yncomment_comment_embox_closed');
		$$('.adv_post_smile').removeClass('emoticons_comment_active');
	}
	hideYnCommentEmotionIconClickEnable = false;
	hideCommentEmotionIconClickEnable = false;
}

function setYnCommentEmotionLabelPlate(label, symbol, obj) {

	if (nestedCommentPressEnter == 1) {
		var formEle = obj.getParent().getParent().getParent().getParent();
	} else {
		var formEle = obj.getParent().getParent().getParent().getParent().getParent().getParent();
	}
	if ($('emotion_yncomment_comment_label_' + formEle.get('id')))
		$('emotion_yncomment_comment_label_' + formEle.get('id')).innerHTML = label;
	if ($('emotion_yncomment_comment_symbol_' + formEle.get('id')))
		$('emotion_yncomment_comment_symbol_' + formEle.get('id')).innerHTML = symbol;
}

function showCommentBox(comment_box_id, body_box_id) {

	if ($(comment_box_id).getElementById('compose-container') == null) {
		en4.yncomment.yncomments.attachComment($(comment_box_id), allowQuickComment);
	} else {
		var composerObj = $(comment_box_id).retrieve('composer');
		composerObj.focus();
	}
	if ($(comment_box_id).style.display == 'none') {
		$(comment_box_id).style.display = 'block';
	} else {
		$(comment_box_id).style.display = 'none';
	}
	$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});
	$(body_box_id).focus();
}

function showReplyBox(reply_box_id, body_box_id) {

	if (document.getElementsByClassName('activity-reply-form')) {
		var elements = document.getElementsByClassName('activity-reply-form');
		for (var i = 0; i < elements.length; i++) {
			if (elements[i] != $(reply_box_id)) {
				elements[i].style.display = 'none';
			}
		}
	}

	if ($(reply_box_id).getElementById('compose-container') == null) {
		en4.yncomment.yncomments.attachReply($(reply_box_id), allowQuickReply);
	} else {
		var composerObj = $(reply_box_id).retrieve('composer');
		composerObj.focus();
	}
	if ($(reply_box_id).style.display == 'none') {
		$(reply_box_id).style.display = 'block';
	} else {
		$(reply_box_id).style.display = 'none';
	}
	$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});
	$(body_box_id).focus();
}
