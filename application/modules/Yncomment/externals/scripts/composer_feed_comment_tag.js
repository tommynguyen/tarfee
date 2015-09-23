ComposerYnActivityComment.Plugin.Tag = new Class({
    Extends: ComposerYnActivityComment.Plugin.Interface,
    name: 'tag',
    options: {
        'enabled': false,
        requestOptions: {},
        'suggestOptions': {
            'minLength': 1,
            'maxChoices': 100,
            'delay': 250,
            'selectMode': 'pick',
            'multiple': false,
            'filterSubset': true,
            'tokenFormat': 'object',
            'tokenValueKey': 'label',
            'injectChoice': $empty,
            'onPush': $empty,
            'prefetchOnInit': true,
            'alwaysOpen': false,
            'ignoreKeys': true,
            'stopKeysEvent': false
        }
    },
    initialize: function(options) {
        this.params = new Hash(this.params);
        this.parent(options);
    },
    suggest: false,
    attach: function() {
        this.parent();
        // Key Events
        var self = this;

        this.getComposer().addEvent((1) ? 'editorKeyDown' : 'editorKeyPress',
                function(event) {
                    if (self.suggest && self.suggest.visible && event) {
                        self.suggest.onCommand(event);
                        if (self.suggest.stopKeysEvent) {
                            event.stop();
                            return;
                        }
                    }
                    self.monitor.bind(self)(event);
                }
        );
        this.getComposer().addEvent('editorClick', this.monitor.bind(this));

        // Submit
        this.getComposer().addEvent('editorSubmit', this.submit.bind(this));
        return this;
    },
    detach: function() {
        if (!this.options.enabled)
            return;
        this.parent();
        this.getComposer().removeEvent('editorKeyPress', this.monitor.bind(this));
        this.getComposer().removeEvent('editorClick', this.monitor.bind(this));
        this.getComposer().removeEvent('editorSubmit', this.submit.bind(this));
        if (this.interval)
            $clear(this.interval);
        return this;
    },
    activate: $empty,
    deactivate: $empty,
    poll: function() {

    },
    monitor: function(e) {
        // seems like we have to do this stupid delay or otherwise the last key
        // doesn't get in the content
        (function() {
            var selection = this.getComposer().selection;
            var range = selection.getRange();
            if (!range)
                return;
            var start = 0;
            if (window.getSelection) {
                start = range.startOffset;
            } else if (document.selection) {
                var range_t = document.selection.createRange();
                if (range_t == null || range_t['text'] == null) {
                } else {
                    var textlength = selection.win.innerText.length;
                    range.moveStart("character", -textlength);
                    start = range.text.length;
                }

            }

            var content = null;
            if (range.startContainer) {
                content = range.startContainer.data;
            } else if (selection.win) {
                content = selection.win.innerText;
            }
            if (!content)
                return;

            this.filterYnCommentTagsFromComposer();

            var currentIndex = false;
            content = content.substring(0, start);
            currentIndex = content.lastIndexOf('@');
            if (currentIndex == -1 || currentIndex >= start) {
                this.endSuggest();
                return;
            }
            // Get the current at segment
            var segment = content.substring(currentIndex + 1, start);

			if (segment.length < 1) {
				this.endSuggest();
                return;
			}
			
            this.doSuggest(segment);

        }).delay(250, this);
    },
    doSuggest: function(text) {
        this.currentText = text;
        var tagged = '';
        this.getComposer().elements.body.getElements('.yncomment_composer_tag').each(function(tag) 
        {
           tagged += tag.get('rel') + ',';
        });
        this.tagged = tagged;
        var suggest = this.getSuggest();
        var input = this.getHiddenInput();
        input.set('value', text);
        input.value = text;
        suggest.prefetch();
    },
    endSuggest: function() {
        this.currentText = '';
        this.tagged = '';
        this.positions = {};
        if (this.suggest) {
            this.getSuggest().destroy();
            delete this.suggest;
        }
    },
    getHiddenInput: function() {
        if (!this.hiddenInput) {
            this.hiddenInput = new Element('input', {
                'type': 'text',
                'styles': {
                    'display': 'none'
                }
            }).inject(document.body);
        }
        return this.hiddenInput;
    },
    getSuggest: function() {
        if (!this.suggest) {
            var width = this.getComposer().elements.body.getSize().x;
            this.choices = new Element('ul', {
                'class': 'tag-autosuggest yncomment-autosuggest',
                'styles': {
                    'width': (width - 2) + 'px'
                }
            }).inject(this.getComposer().elements.body, 'after');
            
            // Check offset
			var global_content = this.choices.getParent('#global_content');
			var y_position = this.choices.getPosition(global_content).y;
			var p_height = global_content.getHeight();
			var c_height = 375;
			if(p_height - y_position < c_height)
			{
				this.choices.addClass('users_suggest_reverse');
			}

            var self = this;
            var options = $merge(this.options.suggestOptions, {
                'customChoices': this.choices,
                'injectChoice': function(token) {
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        'html': token.photo || '',
                        'id': token.guid
                    });
                    var divEl = new Element('div', {
                        'html': this.markQueryValue(token.label) + '<span class = "yc_tag_' + token.type + '"></span>',
                        'class': 'autocompleter-choice'
                    });
                    if (token.type != 'user') {
                    	if(token.type == 'Group')
                    		token.type = 'Club';
                        new Element('div', {
                            'html': token.type
                        }).inject(divEl);
                    }
                    divEl.inject(choice);
                    new Element('input', {
                        'type': 'hidden',
                        'value': JSON.encode(token)
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                },
                'onChoiceSelect': function(choice) {
                    var data = JSON.decode(choice.getElement('input').value);
                    var composer = self.getComposer();
                    var body = composer.elements.body;

                    var content = null;
                    if (composer._supportsContentEditable()) {
                        content = composer.elements.body.get('html');
                    } else {
                        content = composer.elements.body.get('value');
                    }

                    var replaceString = '@' + self.currentText;
                    var newString = '<span class="yncomment_composer_tag" rel="' + data.guid + '" rev="' + data.label + '" >' + data.label + '</span>&nbsp;';

                    content = content.replace(/\<span\>\<\/span\>/ig, '');
                    content = content.replace(new RegExp(replaceString, 'i'), newString);
                    composer.setContent(content);

                    var tagElement = self.getYnCommentTagElementFromComposer(data.label);

                    if (window.getSelection) {  // all browsers, except IE before version 9         
                        composer.selection.getSelection().collapse(tagElement.nextSibling, 1);
                    } else if (document.selection) {  // Internet Explorer before version 9            
                        composer.selection.getRange().moveToElementText(tagElement);
                    }

                },
                'emptyChoices': function() {
                    this.fireEvent('onHide', [this.element, this.choices]);
                },
                'onCommand': function(e) {
                    // This code is copy to Autocompleter JS amd hack minor for check that stop the key event
                    if (e && e.key && !e.shift) {
                        switch (e.key) {
                            case 'enter':
                                e.stop();
                                if (!this.selected) {
                                    if (!this.options.customChoices) {
                                        // @todo support multiple
                                        this.element.value = '';
                                    }
                                    return true;
                                }
                                if (this.selected && this.visible) {
                                    this.stopKeysEvent = true;
                                    this.choiceSelect(this.selected);
                                    return !!(this.options.autoSubmit);
                                }
                                break;
                            case 'up':
                            case 'down':
                                var value = this.element.value;
                                if (!this.prefetch() && this.queryValue !== null) {
                                    this.stopKeysEvent = true;
                                    var up = (e.key == 'up');
                                    if (this.selected)
                                        this.selected.removeClass('autocompleter-selected');
                                    if (!(this.selected)[
                                            ((up) ? 'getPrevious' : 'getNext')
                                    ](this.options.choicesMatch)) {
                                        this.selected = null;
                                    }

                                    this.choiceOver(
                                            (this.selected || this.choices)[
                                            (this.selected) ? ((up) ? 'getPrevious' : 'getNext') : ((up) ? 'getLast' : 'getFirst')
                                    ](this.options.choicesMatch), true);
                                    this.element.value = value;
                                }
                                return false;
                            case 'esc':
                                this.stopKeysEvent = true;
                                this.hideChoices(true);
                                if (!this.options.customChoices)
                                    this.element.value = '';
                                break;
                            case 'tab':
                                this.stopKeysEvent = true;
                                if (this.selected && this.visible) {
                                    this.choiceSelect(this.selected);
                                    return !!(this.options.autoSubmit);
                                } else {
                                    this.hideChoices(true);
                                    if (!this.options.customChoices)
                                        this.element.value = '';
                                    break;
                                }
                            default :
                                this.stopKeysEvent = false;
                        }
                    }

                }
            });

            if (this.options.suggestProto == 'local') {
                this.suggest = new Autocompleter.Local(this.getHiddenInput(), this.options.suggestParam, options);
            } else if (this.options.suggestProto == 'request.json') 
            {
            	var suggestUrl = this.options.suggestOptions.url;
            	if(this.tagged)
            	{
            		suggestUrl = suggestUrl + '/tagged/' + this.tagged;
            	}
                this.suggest = new Autocompleter.Request.JSON(this.getHiddenInput(), suggestUrl, options);
            }
            if (this.suggest && !this.suggest.options.alwaysOpen) {
                this.getComposer().elements.body
                        .addEvent('blur', this.suggestHideChoices.create({
                    bind: this
                }));
            }
        }

        return this.suggest;
    },
    suggestHideChoices: function() {
        var suggest = this.suggest;
        if (suggest) {
            suggest.hideChoices(true);
            if (!suggest.options.customChoices)
                suggest.element.value = '';
        }
    },
    filterYnCommentTagsFromComposer: function()
    {
        var body = this.getComposer().elements.body;
        body.getElements('.yncomment_composer_tag').each(function(tag) {
            if (tag.get('text') != tag.get('rev')) {
                tag.removeClass('yncomment_composer_tag');
            }
        });
    },
    getYnCommentTagElementFromComposer: function(text) {
        var body = this.getComposer().elements.body;
        var element = null;
        body.getElements('.yncomment_composer_tag').each(function(tag) {
             if (tag.get('text') == tag.get('rev') && tag.get('text') == text.replace(/&shy;/ig, '­').replace(/&#039;/ig, '\'')) {
                element = tag;
            }
        });
        return element;
    },
    submit: function() {
        this.makeFormInputs({
            tag: this.getYnCommentTagsFromComposer().toQueryString()
        });
    },
    // get the tags which are in composer
    getYnCommentTagsFromComposer: function()
    {
        // filter the tags which are uncorrect formate or lable 
        this.filterYnCommentTagsFromComposer();
        var composerTags = new Hash();
        var body = this.getComposer().elements.body;

        body.getElements('.yncomment_composer_tag').each(function(tag) {
            composerTags[tag.get('rel')] = tag.get('text');
        });
        return composerTags;
    },
    makeFormInputs: function(data) {
        $H(data).each(function(value, key) {
            this.setFormInputValue(key, value);
        }.bind(this));
    },
    // make tag hidden input and set value into composer form
    setFormInputValue: function(key, value) {
        var elName = 'yncommentComposerForm' + key.capitalize();
        var composerObj = this.getComposer();
        if (composerObj.elements.has(elName))
            composerObj.elements.get(elName).destroy();
        composerObj.elements.set(elName, new Element('input', {
            'type': 'hidden',
            'name': 'composer[' + key + ']',
            'value': value || ''
        }).inject(composerObj.getInputArea()));
        
        composerObj.elements.get(elName).value = value;
    }
});