(function() {// START NAMESPACE
	var $ = 'id' in document ? document.id : window.$;
	ComposerYnComment = new Class({
		Implements : [Events, Options],
		elements : {},
		plugins : {},
		options : {
			lang : {},
			overText : true,
			allowEmptyWithoutAttachment : false,
			allowEmptyWithAttachment : true,
			hideSubmitOnBlur : true,
			submitElement : false,
			useContentEditable : true,
			type : '',
			id : 0,
			parent_comment_id : 0,
			comment_id : 0,
			taggingContent : '',
			showComposerOptions : '',
			showAsNested : 1,
			showAsLike : 1,
			showDislikeUsers : 1,
			showLikeWithoutIcon : 1,
			showLikeWithoutIconInReplies : 1,
			showLangText : 'Write a comment...',
			showSmilies : 1,
			photoLightboxComment : 0,
			commentsorder : 1
		},
		initialize : function(element, options) {
			this.setOptions(options);
			this.elements = new Hash(this.elements);
			this.plugins = new Hash(this.plugins);
			this.elements.textarea = $(element);
			this.elements.textarea.store('Composer');
			this.attach();
			this.getTray();
			this.getMenu();
			this.pluginReady = false;
			this.getForm().addEvent('submit', function(e) {
				this.fireEvent('editorSubmit');
				if (this.pluginReady) {
					if (!this.options.allowEmptyWithAttachment && this.getContent() == '') {
						e.stop();
						return;
					}
				} else {
					if (!this.options.allowEmptyWithoutAttachment && this.getContent() == '') {
						e.stop();
						return;
					}
				}
				this.saveContent();

				e.stop();

				if (this.getForm().comment_id) {
					comment_id = this.getForm().comment_id.value;
					parent_comment_id = this.options.edit_comment_id;
					url = en4.core.baseUrl + 'yncomment/comment/update';
				} else {
					comment_id = this.options.parent_comment_id;
					parent_comment_id = this.options.parent_comment_id;
					url = en4.core.baseUrl + 'yncomment/comment/create';
				}

				if (this.getForm().comment_id) {
					if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id))
						this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id).style.display = 'none';
				} else {
					if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id))
						this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id).style.display = 'none';
				}

				var divEl = new Element('div', {
					'class' : 'yncomment_replies_post_loading',
					'html' : '<img width = "16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading">',
					'id' : 'yncomment_comment_image_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id,
					'styles' : {
						'display' : 'inline-block'
					}
				});

				if (this.getForm().comment_id) {
					if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id))
						divEl.inject(this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id), 'after');
				} else {
					if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id))
						divEl.inject(this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id), 'after');
				}
				var form_values = this.getForm().toQueryString();
				form_values += '&format=json';
				form_values += '&id=' + this.getForm().identity.value;
				form_values += '&taggingContent=' + this.options.taggingContent;
				form_values += '&showComposerOptions=' + this.options.showComposerOptions;
				form_values += '&showAsNested=' + this.options.showAsNested;
				form_values += '&showAsLike=' + this.options.showAsLike;
				form_values += '&showDislikeUsers=' + this.options.showDislikeUsers;
				form_values += '&showLikeWithoutIcon=' + this.options.showLikeWithoutIcon;
				form_values += '&showLikeWithoutIconInReplies=' + this.options.showLikeWithoutIconInReplies;
				form_values += '&showSmilies=' + this.options.showSmilies;
				form_values += '&photoLightboxComment=' + photoLightboxComment;
				form_values += '&commentsorder=' + commentsorder;

				en4.core.request.send(new Request.JSON({
					url : url,
					data : form_values,
					type : this.options.type,
					id : this.options.id,
					onComplete : function(e) 
					{
						if (parent_comment_id == 0)
							return;
						if($('comment-' + parent_comment_id))
						{
							var parent_content = $('comment-' + parent_comment_id).getChildren('div.yncomment_replies_content');
							parent_content.addClass('yncomment_content_hasChild');
						}
						try {
							var replyCount = $$('.yncomment_replies_options span')[0];
							var m = replyCount.get('html').match(/\d+/);
							replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
						} catch (e) {
						}
					}
				}), {
					'element' : $('comments' + '_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id)
				});

			}.bind(this));

		},
		getMenu : function() {
			if (!$type(this.elements.menu)) {
				this.elements.menu = $try( function() {
					return $(this.options.menuElement);
				}.bind(this));

				if (!$type(this.elements.menu)) {
					this.elements.menu = new Element('div', {
						'id' : 'submit',
						'class' : 'compose-menu'
					}).inject(this.getForm(), 'after');
				}
			}
			return this.elements.menu;
		},
		getTray : function() {
			if (!$type(this.elements.tray)) {
				this.elements.tray = $try( function() {
					return $(this.options.trayElement);
				}.bind(this));

				if (!$type(this.elements.tray)) {
					this.elements.tray = new Element('div', {
						'id' : 'compose-tray',
						'class' : 'compose-tray',
						'styles' : {
							'display' : 'block'
						}
					}).inject(this.getForm());

				}
			}
			return this.elements.tray;
		},
		getInputArea : function() {
			if (!$type(this.elements.inputarea)) {
				var form = this.elements.textarea.getParent('form');
				this.elements.inputarea = new Element('div', {
					'styles' : {
						'display' : 'none'
					}
				}).inject(form);
			}
			return this.elements.inputarea;
		},
		getForm : function() {
			return this.elements.textarea.getParent('form');
		},
		// Editor

		attach : function() {
			var size = this.elements.textarea.getSize();

			// Modify textarea
			this.elements.textarea.addClass('compose-textarea').setStyle('display', 'none');

			// Create container
			this.elements.container = new Element('div', {
				'id' : 'compose-container',
				'class' : 'compose-container',
				'styles' : {
				}
			});
			this.elements.container.wraps(this.elements.textarea);

			// Create body
			var supportsContentEditable = this._supportsContentEditable();

			if (supportsContentEditable) {
				this.elements.body = new Element('div', {
					'class' : 'compose-content',
					'styles' : {
						'display' : 'block'
					},
					'events' : {
						'keypress' : function(event) {
							if (event.key == 'a' && event.control) {
								// FF only
								if (Browser.Engine.gecko) {
									fix_gecko_select_all_contenteditable_bug(this, event);
								}
							}
						}
					}
				}).inject(this.elements.textarea, 'before');
			} else {
				this.elements.body = this.elements.textarea;
			}

			// Attach blur event
			var self = this;
			this.elements.body.addEvent('blur', function(e) {
				var curVal;
				if (supportsContentEditable) {
					curVal = this.get('html').replace(/\s/, '').replace(/<[^<>]+?>/ig, '');
				} else {
					curVal = this.get('value').replace(/\s/, '').replace(/<[^<>]+?>/ig, '');
				}
				if ('' == curVal) {
					if (!Browser.Engine.trident) {
						if (supportsContentEditable) {
							this.set('html', '<br />');
						} else {
							this.set('value', '');
						}
					}
					if (self.options.hideSubmitOnBlur) {
						(function() {
							if (!self.hasActivePlugin()) {
							}
						}).delay(250);
					}
				}
			});
			if (self.options.hideSubmitOnBlur) {
				this.getMenu().setStyle('display', 'none');
				this.elements.body.addEvent('focus', function(e) 
				{
					// add class yncommnet_click_textarea
					if(!this.hasClass("yncommnet_click_textarea"))
					{
						this.addClass("yncommnet_click_textarea");
					}
					$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});
					self.getMenu().setStyle('display', '');
					if (self.getMenu().parentNode.getElementsByClassName('yncomment_emoticons')) {
						var ele = self.getMenu().parentNode.getElementsByClassName('yncomment_emoticons');
						if ( typeof ele[0] != 'undefined')
							ele[0].style.display = 'block';
					}
				});
			}

			if (supportsContentEditable) {
				$(this.elements.body);
				this.elements.body.contentEditable = true;
				this.elements.body.designMode = 'On';
				['MouseUp', 'MouseDown', 'ContextMenu', 'Click', 'Dblclick', 'KeyPress', 'KeyUp', 'KeyDown'].each( function(eventName) {
					var method = (this['editor' + eventName] ||
					function() {
					}).bind(this);
					this.elements.body.addEvent(eventName.toLowerCase(), method);
				}.bind(this));
				this.setContent(this.elements.textarea.value);
				this.selection = new ComposerYnComment.Selection(this.elements.body);
			} else {
				this.elements.textarea.setStyle('display', '');
			}

			if (this.options.overText) {
				new ComposerYnComment.OverText(this.elements.body, $merge({
					textOverride : en4.core.language.translate(this._lang(this.options.showLangText)),
					poll : true,
					isPlainText : !supportsContentEditable,
					positionOptions : {
						position : (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
						edge : (en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft'),
						offset : {
							x : (en4.orientation == 'rtl' ? -4 : 4),
							y : 2
						}
					}
				}, this.options.overTextOptions));
			}
			this.fireEvent('attach', this);
		},
		detach : function() {
			this.saveContent();
			this.textarea.setStyle('display', '').removeClass('compose-textarea').inject(this.container, 'before');
			this.container.dispose();
			this.fireEvent('detach', this);
			return this;
		},
		focus : function() 
		{
			// needs the delay to get focus working
			(function() {
				this.elements.body.focus();
				this.fireEvent('focus', this);
			}).bind(this).delay(10);
			return this;
		},
		// Content

		getContent : function() {
			if (this._supportsContentEditable()) {
				return this.cleanup(this.elements.body.get('html'));
			} else {
				return this.cleanup(this.elements.body.get('value'));
			}
		},
		setContent : function(newContent, edit) {
			if (this._supportsContentEditable()) {
				if (!newContent.trim() && !Browser.Engine.trident)
					newContent = '<br />';
				if ( typeof edit != 'undefined' && edit == 1) 
				{
					newContent = htmlDecode(newContent);
				}
				this.elements.body.set('html', newContent);
			} else {
				this.elements.body.set('value', newContent);
			}
			return this;
		},
		saveContent : function() {
			if (this._supportsContentEditable()) {
				this.elements.textarea.set('value', this.getContent());
			}
			return this;
		},
		cleanup : function(html) {
			// @todo
			return html.replace(/<(br|p|div)[^<>]*?>/ig, "\r\n").replace(/<[^<>]+?>/ig, '').replace(/(\r\n?|\n){3,}/ig, "\n\n").trim();
		},
		// Plugins

		addPlugin : function(plugin) {
			var key = plugin.getName();
			this.plugins.set(key, plugin);
			plugin.setComposer(this);
			return this;
		},
		addPlugins : function(plugins) {
			plugins.each( function(plugin) {
				this.addPlugin(plugin);
			}.bind(this));
		},
		getPlugin : function(name) {
			return this.plugins.get(name);
		},
		activate : function(name) {
			this.deactivate();
			this.getMenu().setStyle();
			this.plugins.get(name).activate();
		},
		deactivate : function() {
			this.plugins.each(function(plugin) {
				plugin.deactivate();

				if ( typeof en4.yncomment.editCommentInfo[composeInstanceComment.options.parent_comment_id] != 'undefined' && en4.yncomment.editCommentInfo[composeInstanceComment.options.parent_comment_id].attachment_type != '') {
					en4.yncomment.editCommentInfo[composeInstanceComment.options.parent_comment_id].attachment_type = '';
					en4.yncomment.editCommentInfo[composeInstanceComment.options.parent_comment_id].attachment_body = '';
				}

				if ( typeof composeInstanceComment.getForm().comment_id != 'undefined' && composeInstanceComment.getForm().comment_id.value) {
					if ($('close_edit_box-' + composeInstanceComment.getForm().comment_id.value))
						$('close_edit_box-' + composeInstanceComment.getForm().comment_id.value).style.display = 'block';
				}

			});
			this.getTray().empty();
		},
		signalPluginReady : function(state) {
			this.pluginReady = state;
		},
		hasActivePlugin : function() {
			var active = false;
			this.plugins.each(function(plugin) {
				active = active || plugin.active;
			});
			return active;
		},
		// Key events

		editorMouseUp : function(e) {
			this.fireEvent('editorMouseUp', e);
		},
		editorMouseDown : function(e) {
			this.fireEvent('editorMouseDown', e);
		},
		editorContextMenu : function(e) {
			this.fireEvent('editorContextMenu', e);
		},
		editorClick : function(e) 
		{
			$$('.swiff-uploader-box').each(function(e){e.title = en4.core.language.translate('Attach a Photo');});
			// make images selectable and draggable in Safari
			if (Browser.Engine.webkit) 
			{
				var el = e.target;
				if (el.get('tag') == 'img') {
					this.selection.selectNode(el);
				}
			}
			this.fireEvent('editorClick', e);
		},
		editorDoubleClick : function(e) {
			this.fireEvent('editorDoubleClick', e);
		},
		editorKeyPress : function(e) {
			this.keyListener(e);
			this.fireEvent('editorKeyPress', e);
		},
		editorKeyUp : function(e) {
			if (e.key == 'esc') {
				if (this.getForm().comment_id) {
					comment_id = this.getForm().comment_id.value;
					if ($('close_edit_box-' + comment_id))
						$('close_edit_box-' + comment_id).style.display = 'none';
					if ($('yncomment_edit_comment_' + comment_id))
						$('yncomment_edit_comment_' + comment_id).style.display = 'none';
					if ($('yncomment_comment_data-' + comment_id))
						$('yncomment_comment_data-' + comment_id).style.display = 'block';

					if ($('comments-form_' + this.options.type + '_' + this.options.id + '_' + this.options.edit_comment_id + '_' + comment_id))
						$('comments-form_' + this.options.type + '_' + this.options.id + '_' + this.options.edit_comment_id + '_' + comment_id).style.display = 'none';
    
			       	if($('comments_'+ type +'_'+ this.options.id +'_' + comment_id))
			            $('comments_'+ type +'_'+ this.options.id +'_' + comment_id).style.display = 'block';
				}
			}
			this.fireEvent('editorKeyUp', e);
		},
		editorKeyDown : function(e) {
			if (e.key == 'enter') {
				if (this.getForm().comment_id) {
					comment_id = this.getForm().comment_id.value;
					parent_comment_id = this.options.edit_comment_id;
					url = en4.core.baseUrl + 'yncomment/comment/update';
				} else {
					comment_id = this.options.parent_comment_id;
					parent_comment_id = this.options.parent_comment_id;
					url = en4.core.baseUrl + 'yncomment/comment/create';
				}

				if (this.getForm().getElementsByClassName('yncomment_emoticons')) {
					var ele = this.getForm().getElementsByClassName('yncomment_emoticons');
					if ( typeof ele[0] != 'undefined' && this.getContent() != '')
						ele[0].style.display = 'none';
				}

				if (nestedCommentPressEnter == 1) {

					if (this.pluginReady) {
						if (!this.options.allowEmptyWithAttachment && this.getContent() == '') {
							e.stop();
							return;
						}
					} else {
						if (!this.options.allowEmptyWithoutAttachment && this.getContent() == '') {
							e.stop();
							return;
						}
					}

					e.preventDefault();

					if (composeInstanceComment.getPlugin('tag').getYnCommentTagsFromComposer().toQueryString() != '')
						composeInstanceComment.getPlugin('tag').getComposer().fireEvent('editorSubmit');

					if ($('yncomment_comment_image_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id))
						$('yncomment_comment_image_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id).destroy();

					if (this.getForm().comment_id) {
						if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id))
							this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id).style.display = 'none';
					} else {
						if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id))
							this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id).style.display = 'none';
					}

					var divEl = new Element('div', {
						'class' : 'yncomment_replies_post_loading',
						'html' : '<img width = "16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading">',
						'id' : 'yncomment_comment_image_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id,
						'styles' : {
							'display' : 'inline-block'
						}
					});

					if (this.getForm().comment_id) {
						if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id))
							divEl.inject(this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id + '_' + comment_id), 'after');
					} else {
						if (this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id))
							divEl.inject(this.getForm().getElementById('compose-containe-menu-items_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id), 'after');
					}

					this.getForm().body.value = this.getContent();
					var form_values = this.getForm().toQueryString();
					form_values += '&format=json';
					form_values += '&id=' + this.getForm().identity.value;
					form_values += '&taggingContent=' + this.options.taggingContent;
					form_values += '&showComposerOptions=' + this.options.showComposerOptions;
					form_values += '&showAsNested=' + this.options.showAsNested;
					form_values += '&showAsLike=' + this.options.showAsLike;
					form_values += '&showDislikeUsers=' + this.options.showDislikeUsers;
					form_values += '&showLikeWithoutIcon=' + this.options.showLikeWithoutIcon;
					form_values += '&showLikeWithoutIconInReplies=' + this.options.showLikeWithoutIconInReplies;
					form_values += '&showSmilies=' + this.options.showSmilies;
					form_values += '&photoLightboxComment=' + photoLightboxComment;
					form_values += '&commentsorder=' + commentsorder;

					en4.core.request.send(new Request.JSON({
						url : url,
						data : form_values,
						type : this.options.type,
						id : this.options.id,
						onComplete : function(e) {
							if (parent_comment_id == 0)
								return;
							if($('comment-' + parent_comment_id))
							{
								var parent_content = $('comment-' + parent_comment_id).getChildren('div.yncomment_replies_content');
								parent_content.addClass('yncomment_content_hasChild');
							}
							if ($('yncomment_edit_comment_' + comment_id))
								$('yncomment_edit_comment_' + comment_id).style.display = 'none';
							if ($('yncomment_comment_data-' + comment_id))
								$('yncomment_comment_data-' + comment_id).style.display = 'block';
							if ($('yncomment_comment_image_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id))
								$('yncomment_comment_image_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id).style.display = 'none';

							try {
								var replyCount = $$('.yncomment_replies_options span')[0];
								var m = replyCount.get('html').match(/\d+/);
								replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
							} catch (e) {
							}
						}
					}), {
						'element' : $('comments' + '_' + this.options.type + '_' + this.options.id + '_' + parent_comment_id)
					});
					return false;
				}
			}
			this.fireEvent('editorKeyDown', e);
		},
		keyListener : function(e) {

		},
		_lang : function() {
			try {
				if (arguments.length < 1) {
					return '';
				}

				var string = arguments[0];
				if ($type(this.options.lang) && $type(this.options.lang[string])) {
					string = this.options.lang[string];
				}

				if (arguments.length <= 1) {
					return string;
				}

				var args = new Array();
				for (var i = 1,
				    l = arguments.length; i < l; i++) {
					args.push(arguments[i]);
				}

				return string.vsprintf(args);
			} catch (e) {
				alert(e);
			}
		},
		_supportsContentEditable : function() {
			if ('useContentEditable' in this.options && this.options.useContentEditable) {
				return true;
			} else {
				return false;
			}
		}
	});

	ComposerYnComment.Selection = new Class({
		initialize : function(win) {
			this.win = win;
		},
		getSelection : function() {
			//this.win.focus();
			return window.getSelection();
		},
		getRange : function() {
			var s = this.getSelection();

			if (!s)
				return null;

			try {
				return s.rangeCount > 0 ? s.getRangeAt(0) : (s.createRange ? s.createRange() : null);
			} catch (e) {
				// IE bug when used in frameset
				return document.body.createTextRange();
			}
		},
		setRange : function(range) {
			if (range.select) {
				$try(function() {
					range.select();
				});
			} else {
				var s = this.getSelection();
				if (s.addRange) {
					s.removeAllRanges();
					s.addRange(range);
				}
			}
		},
		selectNode : function(node, collapse) {
			var r = this.getRange();
			var s = this.getSelection();

			if (r.moveToElementText) {
				$try(function() {
					r.moveToElementText(node);
					r.select();
				});
			} else if (s.addRange) {
				collapse ? r.selectNodeContents(node) : r.selectNode(node);
				s.removeAllRanges();
				s.addRange(r);
			} else {
				s.setBaseAndExtent(node, 0, node, 1);
			}

			return node;
		},
		isCollapsed : function() {
			var r = this.getRange();
			if (r.item)
				return false;
			return r.boundingWidth == 0 || this.getSelection().isCollapsed;
		},
		collapse : function(toStart) {
			var r = this.getRange();
			var s = this.getSelection();

			if (r.select) {
				r.collapse(toStart);
				r.select();
			} else {
				toStart ? s.collapseToStart() : s.collapseToEnd();
			}
		},
		getContent : function() {
			var r = this.getRange();
			var body = new Element('body');

			if (this.isCollapsed())
				return '';

			if (r.cloneContents) {
				body.appendChild(r.cloneContents());
			} else if ($defined(r.item) || $defined(r.htmlText)) {
				body.set('html', r.item ? r.item(0).outerHTML : r.htmlText);
			} else {
				body.set('html', r.toString());
			}

			var content = body.get('html');
			return content;
		},
		getText : function() {
			var r = this.getRange();
			var s = this.getSelection();

			return this.isCollapsed() ? '' : r.text || s.toString();
		},
		getNode : function() {
			var r = this.getRange();

			if (!Browser.Engine.trident) {
				var el = null;

				if (r) {
					el = r.commonAncestorContainer;

					// Handle selection a image or other control like element such as anchors
					if (!r.collapsed)
						if (r.startContainer == r.endContainer)
							if (r.startOffset - r.endOffset < 2)
								if (r.startContainer.hasChildNodes())
									el = r.startContainer.childNodes[r.startOffset];

					while ($type(el) != 'element')
					el = el.parentNode;
				}

				return $(el);
			}

			return $(r.item ? r.item(0) : r.parentElement());
		},
		insertContent : function(content) {
			var r = this.getRange();

			if (r.insertNode) {
				r.deleteContents();
				r.insertNode(r.createContextualFragment(content));
			} else {
				// Handle text and control range
				(r.pasteHTML) ? r.pasteHTML(content) : r.item(0).outerHTML = content;
			}
		}
	});

	ComposerYnComment.OverText = new Class({
		Extends : OverText,
		test : function() {
			var v;
			if (!$type(this.options.isPlainText) || !this.options.isPlainText) {
				v = this.element.get('html').replace(/\s+/, '').replace(/<br.*?>/, '');
			} else {
				v = !this.parent();
			}
			return !v;
		},
		hide : function(suppressFocus, force) {
			if (this.text && (this.text.isDisplayed() && (!this.element.get('disabled') || force))) {
				this.text.hide();
				this.fireEvent('textHide', [this.text, this.element]);
				this.pollingPaused = true;
				try {
					this.element.fireEvent('focus');
					this.element.focus();

					if (composeInstanceComment.getContent()) {
						(function() {
							var range = document.createRange();
							var sel = window.getSelection();
							range.setStart(this.element, 1);
							range.collapse(true);
							sel.removeAllRanges();
							sel.addRange(range);
							this.element.focus();
						}).bind(this).delay(10);

					}

				} catch (e) {
				} //IE barfs if you call focus on hidden elements
			}
			return this;
		}
	})

	ComposerYnComment.Plugin = {};

	ComposerYnComment.Plugin.Interface = new Class({
		Implements : [Options, Events],
		name : 'interface',
		active : false,
		composer : false,
		options : {
			loadingImage : en4.core.staticBaseUrl + 'application/modules/Yncomment/externals/images/loading.gif'
		},
		elements : {},
		persistentElements : ['activator', 'loadingImage'],
		params : {},
		initialize : function(options) {
			this.params = new Hash();
			this.elements = new Hash();
			this.reset();
			this.setOptions(options);
		},
		getName : function() {
			return this.name;
		},
		setComposer : function(composer) {
			this.composer = composer;
			this.attach();
			return this;
		},
		getComposer : function() {
			if (!this.composer)
				throw "No composer defined";
			return this.composer;
		},
		attach : function() {
			this.reset();
		},
		detach : function() {
			this.reset();
			if (this.elements.activator) {
				this.elements.activator.destroy();
				this.elements.erase('menu');
			}
		},
		reset : function() {
			this.elements.each( function(element, key) {
				if ($type(element) == 'element' && !this.persistentElements.contains(key)) {
					element.destroy();
					this.elements.erase(key);
				}
			}.bind(this));
			this.params = new Hash();
			this.elements = new Hash();
		},
		activate : function() {
			if (this.active)
				return;
			this.active = true;

			this.reset();

			this.getComposer().getTray().setStyle('display', '');
			this.getComposer().getMenu().setStyle('display', 'none');
			var submitButtonEl = $(this.getComposer().options.submitElement);
			if (submitButtonEl) {
				submitButtonEl.setStyle('display', 'none');
			}

			this.getComposer().getMenu().setStyle('', '');

			this.getComposer().getMenu().getElements('.compose-activator').each(function(element) {
				element.setStyle('display', 'none');
			});

			switch ($type(this.options.loadingImage)) {
			case 'element':
				break;
			case 'string':
				this.elements.loadingImage = new Asset.image(this.options.loadingImage, {
					'id' : 'yncomment-compose-' + this.getName() + '-loading-image',
					'class' : 'compose-loading-image'
				});
				break;
			default:
				this.elements.loadingImage = new Asset.image('loading.gif', {
					'id' : 'yncomment-compose-' + this.getName() + '-loading-image',
					'class' : 'compose-loading-image'
				});
				break;
			}
		},
		deactivate : function() {
			if (!this.active)
				return;
			this.active = false;
			this.reset();
			this.getComposer().getTray().setStyle('display', '');
			this.getComposer().getMenu().setStyle('display', '');
			var submitButtonEl = $(this.getComposer().options.submitElement);
			if (submitButtonEl) {
				submitButtonEl.setStyle('display', '');
			}
			this.getComposer().getMenu().getElements('.compose-activator').each(function(element) {
				element.setStyle('display', '');
			});

			this.getComposer().getMenu().set('style', '');
			this.getComposer().signalPluginReady(false);
		},
		ready : function() {
			this.getComposer().signalPluginReady(true);
			this.getComposer().getMenu().setStyle('display', '');

			var submitEl = $(this.getComposer().options.submitElement);
			if (submitEl) {
				submitEl.setStyle('display', '');
			}
		},
		// Utility

		makeActivator : function() {
			if (!this.elements.activator) {
				this.elements.activator = new Element('a', {
					'id' : 'yncomment-comment-compose-' + this.getName() + '-activator',
					'class' : 'compose-activator buttonlink',
					'href' : 'javascript:void(0);',
					'html' : this._lang(this.options.title),
					'title' : this._lang(this.options.title),
					'events' : {
						'click' : this.activate.bind(this)
					}
				}).inject(this.getComposer().getMenu());

				if (nestedCommentPressEnter == 0 || nestedCommentPressEnter == '') {
					this.elements.activator.inject($("composer_container_icons_" + this.getComposer().getForm().get('action-id')));
				}
				return false;

			}
		},
		makeMenu : function() {
			if (!this.elements.menu) {
				var tray = this.getComposer().getTray();
				this.elements.menu = new Element('div', {
					'id' : 'yncomment-compose-' + this.getName() + '-menu',
					'class' : 'compose-menu'
				}).inject(tray);

				this.elements.menuTitle = new Element('span', {
					'html' : this._lang(this.options.title) + ' ('
				}).inject(this.elements.menu);

				this.elements.menuClose = new Element('a', {
					'href' : 'javascript:void(0);',
					'html' : this._lang('cancel'),
					'events' : {
						'click' : function(e) {
							e.stop();
							this.getComposer().deactivate();
						}.bind(this)
					}
				}).inject(this.elements.menuTitle);
				this.elements.menuTitle.appendText(')');
			}
		},
		makeBody : function() {
			if (!this.elements.body) {
				var tray = this.getComposer().getTray();
				this.elements.body = new Element('div', {
					'id' : 'yncomment-compose-' + this.getName() + '-body',
					'class' : 'compose-body'
				}).inject(tray);
			}
		},
		makeLoading : function(action) {
			if (!this.elements.loading) {
				if (action == 'empty') {
					this.elements.body.empty();
				} else if (action == 'hide') {
					this.elements.body.getChildren().each(function(element) {
						element.setStyle('display', 'none');
					});
				} else if (action == 'invisible') {
					this.elements.body.getChildren().each(function(element) {
						element.setStyle('height', '0px').setStyle('visibility', 'hidden');
					});
				}

				this.elements.loading = new Element('div', {
					'id' : 'yncomment-compose-' + this.getName() + '-loading',
					'class' : 'compose-loading'
				}).inject(this.elements.body);

				var image = this.elements.loadingImage || (new Element('img', {
					'id' : 'yncomment-compose-' + this.getName() + '-loading-image',
					'class' : 'compose-loading-image'
				}));

				image.inject(this.elements.loading);

				new Element('span', {
					'html' : this._lang('Loading...')
				}).inject(this.elements.loading);
			}
		},
		makeError : function(message, action) {
			if (!$type(action))
				action = 'empty';
			message = message || 'An error has occurred';
			message = this._lang(message);

			this.elements.error = new Element('div', {
				'id' : 'yncomment-compose-' + this.getName() + '-error',
				'class' : 'compose-error',
				'html' : message
			}).inject(this.elements.body);
		},
		makeFormInputs : function(data) {
			this.ready();
			this.getComposer().getInputArea().empty();
			data.type = this.getName();
			$H(data).each( function(value, key) {
				this.setFormInputValue(key, value);
			}.bind(this));
		},
		setFormInputValue : function(key, value) {
			var elName = 'attachmentForm' + key.capitalize();
			if (!this.elements.has(elName)) {
				this.elements.set(elName, new Element('input', {
					'type' : 'hidden',
					'name' : 'attachment[' + key + ']',
					'value' : value || ''
				}).inject(this.getComposer().getInputArea()));
			}
			this.elements.get(elName).value = value;
		},
		_lang : function() {
			try {
				if (arguments.length < 1) {
					return '';
				}
				var string = arguments[0];
				if ($type(this.options.lang) && $type(this.options.lang[string])) {
					string = this.options.lang[string];
				}

				if (arguments.length <= 1) {
					return string;
				}

				var args = new Array();
				for (var i = 1,
				    l = arguments.length; i < l; i++) {
					args.push(arguments[i]);
				}

				return string.vsprintf(args);
			} catch (e) {
				alert(e);
			}
		}
	});

})();
// END NAMESPACE