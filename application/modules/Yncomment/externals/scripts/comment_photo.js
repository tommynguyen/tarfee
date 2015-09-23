
var commentAttachment = { editComment:{}};
var replyAttachment = { editReply:{}};
var commentPhoto = new Class({
  //Extends : Composer.Plugin.Interface,
  options : {
    title : 'Add Photo',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {}
  },

  initialize : function(){
  },      
  activate: function() {
  },
  getPhotoContent: function(element, options) {
    this.params = new Hash();
    this.elements = new Hash();
    this.reset();
    
    this.elements.textarea = $(element);
    this.makeMenu(0);
    this.makeBody();
    this.pluginReady = true;
    var fullUrl = options.fancyUploadOptions.url;
    this.elements.form = new Element('form', {
                'id': 'compose-photo-form',
                'class': 'compose-form',
                'method': 'post',
                'action': fullUrl,
                'enctype': 'multipart/form-data'
            }).inject(this.elements.body);

    this.elements.formInput = new Element('input', {
        'id': 'compose-photo-form-input',
        'class': 'compose-form-input',
        'type': 'file',
        'name': 'Filedata',
         'styles' : {
            'display' : 'none',
            'visibility' : 'hidden'
          },
        'events': {
            'change': this.doRequest.bind(this)
        }
    }).inject(this.elements.form);
    this.elements.formFancyContainer = new Element('div', {
      'styles' : {
        'display' : 'none',
        'visibility' : 'hidden'
      }
    }).inject(this.elements.body);
    
    this.elements.preview_image = new Element('div', {
      'id' : 'yncomment_preview_image',
      'styles' : {
        'display' : 'none'
      }
    }).inject(this.elements.body);

    // This is the browse button
    this.elements.formFancyFile = new Element('a', {
      'href' : 'javascript:void(0);',
      'id' : 'compose-photo-form-fancy-file',
      'class' : 'buttonlink',
      'html' : '<i class="fa fa-picture-o"></i>',
      'title' : this._lang('Attach a Photo')
    }).inject(this.elements.formFancyContainer);
    
    // This is the status
      this.elements.formFancyStatus = new Element('div', {
        'html' : 
'<div style="display:none;">\n\
  <div class="demo-status-overall" id="demo-status-overall" style="display:none;">\n\
    <div class="overall-title"></div>\n\
    <img src="" class="progress overall-progress" />\n\
  </div>\n\
  <div class="demo-status-current" id="demo-status-current" style="display:none;">\n\
    <div class="current-title"></div>\n\
    <img src="" class="progress current-progress" />\n\
  </div>\n\
  <div class="current-text"></div>\n\
</div>'
      }).inject(this.elements.formFancyContainer);
      
      // This is the list
      this.elements.formFancyList = new Element('div', {
        'styles' : {
          'display' : 'none'
        }
      }).inject(this.elements.formFancyContainer);

      var self = this;
      var opts = $merge({
        policyFile : ('https:' == document.location.protocol ? 'https://' : 'http://')
            + document.location.host
            + en4.core.baseUrl + 'cross-domain',
        url : fullUrl,
        appendCookieData: true,
        multiple : false,
        typeFilter: {
          'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
        },
        target : this.elements.formFancyFile,
        // Events
        onLoad : function() {
          self.elements.formFancyContainer.setStyle('display', '');
          self.elements.formFancyContainer.setStyle('visibility', 'visible');
          self.elements.form.destroy();
          this.target.addEvents({
                  click: function() {
                          return false;
                  },
                  mouseenter: function() {
                          this.addClass('hover');
                  },
                  mouseleave: function() {
                          this.removeClass('hover');
                          this.blur();
                  },
                  mousedown: function() {
                          this.focus();
                  }
          });
        },
        onSelectSuccess : function() {
          self.makeLoading('invisible');
          this.start();
        },
        onFileSuccess : function(file, response) {
          var json = new Hash(JSON.decode(response, true) || {});
          self.doProcessResponse(json);
        }
      }, options.fancyUploadOptions);

      try {
        this.elements.formFancyUpload = new FancyUpload2(this.elements.formFancyStatus, this.elements.formFancyList, opts);
      } catch( e ) {
      }
  },          
  doRequest : function() {
    this.elements.iframe = new IFrame({
      'name' : 'composePhotoFrame',
      'src' : 'javascript:false;',
      'styles' : {
        'display' : 'none'
      },
      'events' : {
        'load' : function() {
          this.doProcessResponse(window._composePhotoResponse);
          window._composePhotoResponse = false;
        }.bind(this)
      }
    }).inject(this.elements.body);

    window._composePhotoResponse = false;
    this.elements.form.set('target', 'composePhotoFrame');

    // Submit and then destroy form
    this.elements.form.submit();
    this.elements.form.destroy();

    // Start loading screen
    this.makeLoading();
  },

  doProcessResponse : function(responseJSON) {
  	
    // An error occurred
    if( ($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.photo_id)) != 'number' ) {
      this.elements.loading.destroy();
      this.elements.body.empty();
      if(responseJSON.error == 'Invalid data'){
        this.makeError(this._lang('The image you tried to upload exceeds the maximum file size.'), 'empty');
      }
      else{
        this.makeError(this._lang('Unable to upload photo.'), 'empty');
      }
      return;
      //throw "unable to upload image";
    }
    
    // Success
    this.params.set('rawParams', responseJSON);
    this.params.set('photo_id', responseJSON.photo_id);
    this.params.set('src', responseJSON.src);
    this.elements.preview = Asset.image(responseJSON.src, {
      'id' : 'compose-photo-preview-image',
      'class' : 'compose-preview-image',
      'onload' : this.doImageLoaded.bind(this)
    });
  },

  doImageLoaded : function() {
      
    //compose-photo-error
    if($('compose-photo-error')){
      $('compose-photo-error').destroy();
    }
    if(this.elements.formFancyFile)
    	this.elements.formFancyFile.hide();
    if( this.elements.loading ) this.elements.loading.destroy();
    this.elements.preview.erase('width');
    this.elements.preview.erase('height');
    this.elements.preview_image.setStyle('display', 'block');
    this.elements.preview.inject(this.elements.preview_image);
    this.makeFormInputs({
        photo_id: this.params.photo_id,
        type: 'photo',
        src: this.params.src
      });
      this.makeMenu(1);
  },

	  makeFormInputs : function(data) {    
      $H(data).each(function(value, key) {
        this.setFormInputValue(key, value);
      }.bind(this));
    },
    //MAKE CHEKIN HIDDEN INPUT AND SET VALUE INTO COMPOSER FORM
    setFormInputValue : function(key, value) {
      if($(key)) 
      $(key).destroy();						
			this.elements.hiddenElement=new Element('input', {
				'type' : 'hidden',
				'id': key,
				'name' : 'attachment[' + key + ']',
				'value' : value || ''
			});
			this.elements.hiddenElement.inject(this.elements.textarea);			
    },
   _lang : function() {
    try {
      if( arguments.length < 1 ) {
        return '';
      }

      var string = arguments[0];
      if( $type(this.options.lang) && $type(this.options.lang[string]) ) {
        string = this.options.lang[string];
      }

      if( arguments.length <= 1 ) {
        return string;
      }

      var args = new Array();
      for( var i = 1, l = arguments.length; i < l; i++ ) {
        args.push(arguments[i]);
      }

      return string.vsprintf(args);
    } catch( e ) {
      alert(e);
    }
  },
  makeLoading : function(action) {
    if( !this.elements.loading ) {
      if( action == 'empty' ) {
        this.elements.body.empty();
      } else if( action == 'hide' ) {
        this.elements.body.getChildren().each(function(element){ element.setStyle('display', 'none')});
      } else if( action == 'invisible' ) {
        this.elements.body.getChildren().each(function(element){ 
            if(element.get('id') != 'yncomment_preview_image') {
              element.setStyle('height', '0px').setStyle('visibility', 'hidden');
            } else {
              //element.setStyle('display', 'block');
            }}
        );
      }

      this.elements.loading = new Element('div', {
        'id' : 'compose-yncomment-photo-loading',
        'class' : 'compose-loading mtop5'
      }).inject(this.elements.body);

      var image = this.elements.loadingImage || (new Element('img', {
        'id' : 'compose-yncomment-photo-loading-image',
        'class' : 'compose-loading-image',
        'src': en4.core.staticBaseUrl + 'application/modules/Yncomment/externals/images/loading.gif'
      }));
      
      image.inject(this.elements.loading);

      new Element('span', {
        'html' : this._lang('Loading...')
      }).inject(this.elements.loading);
    }
  },
  makeMenu : function(test) {
      if(test == 0)
          return;
    if( !this.elements.menu ) {
      var tray = this.getTray();

      this.elements.menu = new Element('div', {
        'id' : 'compose-yncomment-comment-photo-menu',
        'class' : 'compose-menu'
      }).inject(tray);

      this.elements.menuTitle = new Element('span', {
        //'html' : this._lang(this.options.title) + ' ('
      }).inject(this.elements.menu);

      this.elements.menuClose = new Element('a', {
        'href' : 'javascript:void(0);',
				'class' : 'yncomment_icon_cross buttonlink',
        'events' : {
          'click' : function(e) {
            e.stop();
            this.elements.tray.destroy();
            if($('photo_id'))
            	$('photo_id').destroy();
            if($('type'))
            	$('type').destroy();
            if($('src'))
            	$('src').destroy();
            this.getPhotoContent(this.elements.textarea, {requestOptions : {
          'url'  : requestOptionsURLYnComment
        },
        fancyUploadOptions : {
          'url'  : fancyUploadOptionsURLYnComment,
          'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
        }});
          }.bind(this)
        }
      }).inject(this.elements.menuTitle);
    }
    
  },
  emptyTray : function() {
    if($('compose-photo-preview-image'))
        $('compose-photo-preview-image').destroy();
    $('compose-yncomment-comment-photo-body').style.display = 'block';

    $('compose-yncomment-comment-photo-body').erase('height');
  },
  getTray : function() {
    if( !$type(this.elements.tray) ) {
      this.elements.tray = $try(function(){
        return $(this.options.trayElement);
      }.bind(this));

      if( !$type(this.elements.tray) ) {
        //if($('compose-tray'))  
            //$('compose-tray').destroy();
        this.elements.tray =  new Element('div',{
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
  makeBody : function() {
    if( !this.elements.body ) {
      var tray = this.getTray();
      this.elements.body = new Element('div', {
        'id' : 'compose-yncomment-comment-photo-body',
        'class' : 'compose-body'
      }).inject(tray);
    }
  },        
  getForm : function() {
    return this.elements.textarea;
  },
  signalPluginReady : function(state) {
    this.pluginReady = state;
  },
  ready : function() {
    this.signalPluginReady(true);
  },
  reset : function() {
    this.elements.each(function(element, key) {
      if( $type(element) == 'element' && !this.persistentElements.contains(key) ) {
        element.destroy();
        this.elements.erase(key);
      }
    }.bind(this));
    this.params = new Hash();
    this.elements = new Hash();
  },
  makeError: function(message, action) {
    if (!$type(action))
        action = 'empty';
    message = message || 'An error has occurred';
    message = this._lang(message);

    this.elements.error = new Element('div', {
        'id': 'yncomment-compose-photo-error',
        'class': 'compose-error',
        'html': message
    }).inject(this.elements.body);
    }         
});