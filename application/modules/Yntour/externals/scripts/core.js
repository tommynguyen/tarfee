en4.yntour = {
	isBuilt : 0,
	hover : 0,
	tourId : 0,
	tourRows : [],
	tourTotal : 0,
	hoverIn : null,
	currentIndex : 0,
	editMode : 'disabled',
	autorun: true,
	autoclose : false,
	autoclose_time_delay : 10,
	fx : 0,
	hash : '',
	title : '',
	json : {},
	//isBuilt : false,
	auto : false,
	timer : 0,
	hidebox : false,
    initData: {},
    arrowElement: null
};
en4.yntour.scrollTo = function(ele) 
{
	if(en4.yntour.fx === 0) {
		en4.yntour.fx = new Fx.Scroll(window, {

		});
	}
	//var pos =  $(ele).getPosition();
	en4.yntour.fx.scrollToCenter(ele);
}
en4.yntour.init =  function(json){
    en4.yntour.initData = json;
}
en4.yntour.bootstrap = function() {
	if(window.parent != window) {
		return false;
	}
	var value = Cookie.read('yntourSecret');

	if(value != undefined && value == 'enabled') {
		en4.yntour.editMode = 'enabled';
	}

	en4.yntour.process(en4.yntour.initData); 
    
}
en4.yntour.poptour = function() {    
    var body = document.getElementsByTagName('body')[0].id;
	Smoothbox.open(en4.core.baseUrl + 'yntour/index/edit-tour/tour_id/' + en4.yntour.tourId + '/body/'+body+'/');
}

en4.yntour.getPath = function(e) {
   
	var ke = e;
	function getIndex(ele) {
		var len = ele.parentNode.children.length;
		var index = 0;
		for(var i = 0; i < len; ++i) {
			var e = ele.parentNode.children[i];
			if(e == ele) {
				return index;
			}
			if(e.tagName == e.tagName) {++index;
			}
		}
		return index;
	}

	var ar = [];
	var first = true;
	while(e != undefined && e.tagName && e.parentNode) {
		var cs = e.getAttribute('class');
		//var id = e.getAttribute('id');
		var tag = e.tagName;
		var str = tag;
		var found = false;
/*
		if(id != undefined && id) {
			str += '#' + id;
			found = true;
		} else 
        */
        if(cs != undefined && cs && cs != 'yntour_box_hightlight') {
			str += '.' + cs.replace(/\s+/gi, '.');
		} else if(!found && first) {
			var index = getIndex(e);
			if(index) {
				str += ':eq(' + index + ')';
			}
		}
		first = false;
		str = str.replace(/[\.\s\t\r\n]+$/gi,"");
		ar.unshift(str);
		e = e.parentNode;
		if(found) {
			break
		}

	}
	
	var path = ar.join(' ');
	var len = Sizzle(path).length;
	if(len > 1 || len == 0) {
	       
		return false;
	}
    
	return path;
}
en4.yntour.getPath2 = function(e) {
    
	var ar = [];
	var p = e.parentNode;
    
	ar.push(e.tagName);
	var str = "";
	while(p != undefined && p.tagName && p.parentNode) {
		//var id = p.getAttribute('id');
		var cs = e.getAttribute('class');
        /*
		if(id != undefined && id != null && id) {
			ar.unshift(p.tagName + '#' + id)
			break;
		} else 
        */
        if(cs != undefined && cs != null && cs) 
		{
			str = p.tagName + '.' + cs.replace(/\s+/gi, '.');
			str = str.replace(/[\.\s\t\r\n]+$/gi,"");
			ar.unshift(str)
		}
		p = p.parentNode;
	}
    
	var les = Sizzle(ar.join(' '));
    
	var index = 0;
	for(var i = 0; i < les.length; ++i) {
		if(les[i] == e) {
		  var p = ar.join(' ') + ':eq(' + index + ')';
		      
			return p;
		}++index;
	}
    
	return false;
}
en4.yntour.stepStart = function() {
	en4.yntour.hidebox = true;
	en4.yntour.startTour();
	en4.yntour.hover = 1;
}
en4.yntour.markAsRead = function() {
	if(en4.yntour.hash == '') {
		return;
	}

	var key = 'sdco' + en4.yntour.tourId;
	var val = en4.yntour.hash;
	Cookie.write(key, val, {
		'duration' : 365,
		path : en4.core.baseUrl
	});

}

en4.yntour.hasRead = function() {
	if(en4.yntour.hash == '') {
		return false;
	}

	var key = 'sdco' + en4.yntour.tourId;
	var val = en4.yntour.hash;
	var cookieVal = Cookie.read(key);
	if(cookieVal != undefined && cookieVal != null && cookieVal == val) {
		return true;
	}
	return false;

}

en4.yntour.expand = function(){
    this.style.display = 'none';
    $(yntour_control).style.display = 'block';
}

en4.yntour.collapse = function(){   
    this.style.display = 'none';    
    $(yntour_collapse).style.display = 'block';    
}
en4.yntour.process = function(json) {

	en4.yntour.tourId = json.id;
	en4.yntour.tourTotal = json.total;
	en4.yntour.hash = json.hash;
	en4.yntour.autoclose = json.autoclose;
	en4.yntour.autoclose_time_delay = json.autoclose_time_delay;
	en4.yntour.json = json;
	en4.yntour.title = json.title;

	var overlay = document.createElement('DIV');
	overlay.setAttribute('id', 'yntour_overlay');
	var body = Sizzle('body')[0];
	body.appendChild(overlay);
	$(overlay).addEvent('click',en4.yntour.go.close);

	if(en4.yntour.editMode == 'enabled') {
	  
		var html = '<div style="postion:relative;"><p id="activatetourtitle">Tour Guide Plugin</p><p><a id="yntour_editour" href="javascript:en4.yntour.poptour()">Edit Tour Guide</a></p><div id="yn_collapse_btn"></div></div>';
		html.replace('{close}',en4.core.language.translate('close'))
		.replace('{prev}',en4.core.language.translate('previous'))
		.replace('{next}',en4.core.language.translate('next'))
        .replace('{title}',en4.core.language.translate('title'))
		.replace('{replay}',en4.core.language.translate('replay'));
		
		var e = document.createElement('div');
		e.className = 'yntour_control';
		e.id = 'yntour_control';       
        e.style.display = 'none';
        
		var body = Sizzle('body')[0];
		body.appendChild(e);
        $(e).addEvent('click',en4.yntour.collapse);
		e.innerHTML = html;  
        
        var collapse = document.createElement('div');
        collapse.setAttribute('id','yntour_collapse');        
        body.appendChild(collapse);
        $(collapse).addEvent('click',en4.yntour.expand);             
        
	}

	if(en4.yntour.editMode != 'enabled') {
		if(!json.id || !json.total || !json.hash) {

			return;
		}

		var key = 'sdco' + json.id;
		var val = Cookie.read(key);

		if(val != undefined && val != null && val == json.hash) {
		  
			return;
		}
	}

	if(json.id && en4.yntour.editMode == 'enabled') {
		var c = document.getElementById('yntour_control');
		if(c) {
			var a = document.createElement('a');
			a.setAttribute('href', 'javascript:en4.yntour.stepStart()');
			c.appendChild(a);
			a.innerHTML = "Add step";
		}
	}

	if((json.autoplay && json.id && json.total && !en4.yntour.hasRead())) {
		//en4.yntour.startTour();
        window.setTimeout('en4.yntour.startTour()', 5 * 1000);       
                
	} 
    
    //window.setInterval('en4.yntour.lighten()',1000);

}
en4.yntour.showOverlay = function() {
	var e = document.getElementById('yntour_overlay');
	if(e != undefined && e != null) {
		var body = Sizzle('body')[0];
		var size = window.getSize();
		e.style.width = size.x + 'px';
		e.style.height = size.y + 'px';
		e.style.display = 'block';
	}
}
en4.yntour.hideOverlay = function() {
	var e = document.getElementById('yntour_overlay');
	if(e != undefined && e != null) {
		e.style.display = 'none';
	}
}
en4.yntour.startTour = function() {
	en4.yntour.buildTour();
	en4.yntour.markAsRead();
	en4.yntour.go.first();     
    /**************************************************************************************/
    
    $(document).addEvent('keydown',function(evt){        
        
            var key = evt.key;
            switch(key){
                case 'up':  en4.yntour.auto = false;
                            if($('yntour_overlay').style.display == 'block')
                                en4.yntour.go.next();
                    break;
                case 'down': en4.yntour.auto = false;
                            if($('yntour_overlay').style.display == 'block')
        			         en4.yntour.go.prev();
                    break;
                case 'right': en4.yntour.auto = false;
                            if($('yntour_overlay').style.display == 'block')
        			         en4.yntour.go.next();
                    break;
                case 'left': en4.yntour.auto = false;
                            if($('yntour_overlay').style.display == 'block')
        			         en4.yntour.go.prev();
                    break;
                case 'esc' :    en4.yntour.auto = false;
                            if($('yntour_overlay').style.display == 'block')
        			         en4.yntour.go.close();
                    break;     
            }    
            
    }); 
     
       
/**************************************************************************************/  
}
en4.yntour.makePage = function(span, index) {
	$(span).addEvent('click', function() {
		en4.yntour.go.page(index);
	});
}
en4.yntour.buildTour = function() {

	if(en4.yntour.isBuilt == 0) {
		var body = document.body;		
		en4.yntour.isBuilt = true;
		var rows = [];
		var json = en4.yntour.json;
		en4.yntour.auto = json.autoplay;
		if(!json.total || !json.hash || !json.id) {
			return;
		}
		var index = 0;
		for( i = 0; i < json.total; ++i) {
			var row = json.rows[i];
			var p = row.dompath;
			var ele = Sizzle(p);
			
			if(ele == null || ele == undefined || !ele.length) {
				continue;
			}			
			ele = ele[0];
			
			row.element = ele;
			++index;
			
			var span = en4.yntour.createIndex($(ele).getPosition(), row, index);
			row.span = span;
            
			body.appendChild(span);
			en4.yntour.makePage(span, index - 1);
            
			rows.push(row);
		}

		en4.yntour.tourRows = rows;
		en4.yntour.tourTotal = rows.length;
		en4.yntour.auto = json.autoplay;
	}

	var len = en4.yntour.tourRows.length;
	for(var i = 0; i < len; ++i) {
		var row = en4.yntour.tourRows[i];
       
		//$(row.element).addClass('yntour_box_hightlight');
		row.span.style.display = 'block';
    	$(row.span).position({
			relativeTo : row.element,
			position : 'upperLeft',
			edge : 'upperRight'
		});
    
        if (row.top_position !=0 || row.left_position !=0) //tuan
        {
        	if (i >= 9) {
        		$(row.span).style.paddingLeft = "5px";
        		$(row.span).style.paddingRight = "11px";
        		$(row.span).style.fontSize = "34px";
        	}
        	$(row.span).style.top = parseInt($(row.span).style.top,10) + row.top_position + "px";
            $(row.span).style.left = parseInt($(row.span).style.left,10) + row.left_position + "px";
        }
        
	}
	en4.yntour.currentIndex = 0;
}
en4.yntour.createIndex = function(pos, row, index) {
	var span = document.createElement('span');
	    
    span.style.left =  pos.x + parseInt(row.left_position)+ 'px';
	span.style.top = pos.y + parseInt(row.top_position)+ 'px';
          
	span.className = 'yntour_indexs';
	span.innerHTML = index;
	return span;
}

window.addEvent('domready', function()
{
	$(document).addEvent('mouseover', function(evt) {
		if(!en4.yntour.hover) {
			return;
		}
		var ele = evt.target;
		$(ele).addClass('yntour_box_hightlight');
		en4.yntour.hoverIn = ele;
	});
	
	$(document).addEvent('mouseout', function(evt) {
		if(!en4.yntour.hover) {
			return;
		}
		var ele = evt.target;
		$(ele).removeClass('yntour_box_hightlight');
	
	});
	
	$(document).addEvent('click', function(evt) {
		if(!en4.yntour.hover) {
			return;
		}
		en4.yntour.hover = 0;
		// process somethign else.
		var ele = evt.target;
		$(ele).addClass('yntour_box_hightlight');
		en4.yntour.hoverIn = ele;
		en4.yntour.newStep(ele);
		evt.stop();
		evt.stopPropagation();
		return false;
	});
});

en4.yntour.newStep = function(ele) {
	en4.yntour.hidebox = false;
	var len = 0,found =  false, path =  '', orgPos = $(ele).getPosition(), newPos = null;
	$i =  100;
    
    while(--$i > 0 && found == false && typeof ele != 'undefined' && ele.tagName != 'BODY') {
    	$(ele).removeClass('yntour_box_hightlight');
		path = en4.yntour.getPath(ele);
		  
	    if(path == false) {
			path = en4.yntour.getPath2(ele);
		}
		if(path != false){	
		  len = Sizzle(path).length;
        }
        
        if(len == 1){
            found = true;
        }else{
			ele = ele.parentNode; 
        }
	}
	
	if(found){
		newPos =  $(ele).getPosition();
		var offsetX =  orgPos.x -  newPos.x;
		var offsetY = orgPos.y - newPos.y;
		
		$(ele).addClass('yntour_box_hightlight');
		// fix issue related with active tab.
		
		path = path.replace('.is_active','');
		var url = en4.core.baseUrl + 'yntour/index/edit-step/tour_id/' + en4.yntour.tourId + '/x/'+offsetX+'/y/'+offsetY+'/dompath/' + encodeURIComponent(path);
	   	Smoothbox.open(url);
	}
	
	return;
}
en4.yntour.guideStart = function() {

}
en4.yntour.getBox = function() {
	var id = 'id_yntour_showbox';
	var e = document.getElementById(id);    
	if(e == undefined || e == null) {
	  
		var html = '';
		e = document.createElement('div');
		e.id = 'id_yntour_showbox';
		e.style.position = 'absolute';
        var html = '\x3Cdiv class=\"yntourbox_overlay\"\x3E\n\t\x3Cdiv class=\"yntourbox_inner\"\x3E\n\t\t\x3Ctable id=\"yntour_table\"\x3E\n\t\t\t\x3Ctr height=\"11px\"\x3E\n\t\t\t\t\x3Ctd width=\"10px\"\x3E\n\t\t\t\t\t\x3Cdiv class=\"yntour_side yntour_side_1\"\x3E\&nbsp;\x3C\x2Fdiv\x3E\n\t\t\t\t\x3C\x2Ftd\x3E\n\t\t\t\t\x3Ctd class=\"yntour_side yntour_side_2\"\x3E\&nbsp;\x3C\x2Ftd\x3E\n\t\t\t\t\x3Ctd width=\"10px\"\x3E\n\t\t\t\t\t\x3Cdiv class=\"yntour_side yntour_side_3\"\x3E\&nbsp;\x3C\x2Fdiv\x3E\n\t\t\t\t\x3C\x2Ftd\x3E\n\t\t\t\x3C\x2Ftr\x3E\n\t\t\t\x3Ctr\x3E\n\t\t\t\t\x3Ctd width=\"10px\" class=\"yntour_side yntour_side_4\"\x3E\&nbsp;\x3C\x2Ftd\x3E\n\t\t\t\t\x3Ctd class=\"yntour_side yntour_side_5\"\x3E\n\t\t\t\t\t\x3Cdiv id=\"id_yntour_box_title\" title=\"{title}\"\x3E\x3C\x2Fdiv\x3E\n\t\t\t\t\t\x3Cdiv id=\"id_yntour_box_content\"\x3E\x3C\x2Fdiv\x3E\n\t\t\t\t\x3C\x2Ftd\x3E\n\t\t\t\t\x3Ctd width=\"10px\" class=\"yntour_side yntour_side_6\"\x3E\&nbsp;\x3C\x2Ftd\x3E\n\t\t\t\x3C\x2Ftr\x3E\n\t\t\t\x3Ctr height=\"36px\"\x3E\n\t\t\t\t\x3Ctd width=\"10px\"\x3E\n\t\t\t\t\t\x3Cdiv class=\"yntour_side yntour_side_7\"\x3E\&nbsp;\x3C\x2Fdiv\x3E\n\t\t\t\t\x3C\x2Ftd\x3E\n\t\t\t\t\x3Ctd class=\"yntour_side yntour_side_8\"\x3E\n\t\t\t\t\t\x3Cdiv id=\"id_yntour_pag\"\x3E\n\t\t\t\t\t\t\x3Cspan id=\"id_yntour_pag_index\"\x3E\x3C\x2Fspan\x3E\n\t\t\t\t\t\t\x3Cdiv\x3E\n\t\t\t\t\t\t\t\x3Cspan id=\"id_yntour_pag_prev\" title=\"{prev}\"\x3E\x3C\x2Fspan\x3E\n\t\t\t\t\t\t\t\x3Cspan id=\"id_yntour_pag_next\" title=\"{next}\"\x3E\t\x3C\x2Fspan\x3E \n\t\t\t\t\t\t\t\x3Cspan id=\"id_yntour_pag_first\" title=\"{replay}\"\x3E\x3C\x2Fspan\x3E\n\t\t\t\t\t\t\x3C\x2Fdiv\x3E\n\t\t\t\t\t\x3C\x2Fdiv\x3E\n\t\t\t\t\x3C\x2Ftd\x3E\n\t\t\t\t\x3Ctd\x3E\n\t\t\t\t\t\x3Cdiv class=\"yntour_side yntour_side_9\"\x3E\&nbsp;\x3C\x2Fdiv\x3E\n\t\t\t\t\x3C\x2Ftd\x3E\n\t\t\t\x3C\x2Ftr\x3E\t\n\t\t\x3C\x2Ftable\x3E\n\t\x3C\x2Fdiv\x3E\n\t\x3Cdiv id=\"id_yntour_pag_close\" title=\"{close}\"\x3E\x3C\x2Fdiv\x3E\n\t\x3Cdiv id=\"id_yntour_arrow\" class=\"yntour_arrow\"\x3E\n\t\t\x3Cdiv\x3E\&nbsp;\x3C\x2Fdiv\x3E\n\t\x3C\x2Fdiv\x3E\n\x3C\x2Fdiv\x3E';
        
        html = html.replace('{prev}',en4.core.language.translate('prev'))
		.replace('{next}',en4.core.language.translate('next'))
		.replace('{replay}',en4.core.language.translate('replay'))
        .replace('{title}',en4.core.language.translate('title'))
        ;
        
        e.innerHTML = unescape(html);
		var bd = document.getElementsByTagName('body')[0];
		bd.appendChild(e);
		$('id_yntour_pag_prev').addEvent('click', function() {
			en4.yntour.auto = false;
			en4.yntour.go.prev();
		});
		$('id_yntour_pag_next').addEvent('click', function() {
			en4.yntour.auto = false;
			en4.yntour.go.next();
		});
		$('id_yntour_pag_first').addEvent('click', function() {
			en4.yntour.auto = en4.yntour.json.autoplay;
			en4.yntour.go.first();
		});
		$('id_yntour_pag_close').addEvent('click', function() {
			en4.yntour.auto = false;
			en4.yntour.go.close();
		});
		//$('id_yntour_pag_last').addEvent('click',en4.yntour.go.last);
		en4.yntour.arrowElement = $('id_yntour_arrow');
                 
	}
   
	e.style.display = 'none';
	return e;
}
en4.yntour.setBoxTitle = function(title) {
	var e = document.getElementById("id_yntour_box_title");
	e.innerHTML = title;
}
en4.yntour.setBoxContent = function(content, w, h) {
	var e = document.getElementById("id_yntour_box_content");
	e.style.width = w + 'px';
	e.style.height = h;
	e.innerHTML = content;
	e.style.display = "block";
}

en4.yntour.checkPag = function(step) {
	var index = en4.yntour.currentIndex + step;
	if(index < 0) {
		index = 0;
	} else if(index >= en4.yntour.tourTotal) {
		index = en4.yntour.tourTotal - 1;
	}
	en4.yntour.currentIndex = index;

}

en4.yntour.setBoxIndex = function(step) {    
	var e = document.getElementById("id_yntour_pag_index");
	if(e != null && e != undefined) {
		e.innerHTML = step + '/' + en4.yntour.tourTotal;
	}
}

en4.yntour.go = {
	page : function(i) {
		en4.yntour.currentIndex = i;
		en4.yntour.checkPag(0);
		en4.yntour.showbox();
	},
	next : function() {
		en4.yntour.checkPag(+1);
		en4.yntour.showbox();
	},
	prev : function() {
		en4.yntour.checkPag(-1);
		en4.yntour.showbox();
	},
	first : function() {
		en4.yntour.checkPag(-1000);
		en4.yntour.showbox();
	},
	last : function() {
		en4.yntour.checkPag(1000);
		en4.yntour.showbox();
	},
	close : function() {
		en4.yntour.killTimeout();
		en4.yntour.hideOverlay();
		var e = document.getElementById('id_yntour_showbox');
		e.style.display = 'none';
		for(var i = 0; i < en4.yntour.tourTotal; ++i) {
			var row = en4.yntour.tourRows[i];
			var e = row.element;
			$(e).removeClass('yntour_box_hightlight');
			var span = row.span;
			span.style.display = 'none';
		}
		en4.yntour.killTimeout();
	}
};
en4.yntour.showbox = function() {

	if(en4.yntour.hidebox) {
		return;
	}
	en4.yntour.showOverlay();
	en4.yntour.killTimeout();
	var index = en4.yntour.currentIndex;
	if(index < 0 || index >= en4.yntour.tourTotal) {
		return;
	}
	var row = en4.yntour.tourRows[index];
	var e = en4.yntour.getBox();

	en4.yntour.setBoxTitle(en4.yntour.title);
	en4.yntour.setBoxContent(row.body, row.width, row.height);
	en4.yntour.setBoxIndex(index + 1);
	
	// caculate position & offset
	
	var x  = parseInt(row.span.style.left);// + parseInt(row.left_position);
	var y  = parseInt(row.span.style.top);//  + parseInt(row.top_position);
	var arrowX = 0;
	var arrowY = 0;
	
    var arrowElement = en4.yntour.arrowElement;
	var dir  =  row.position;
	
	dir = dir.toLowerCase();
    	
	switch(dir){
		case 'top':
			x -= parseInt(parseInt(row.width)/2) - 20 ;
            y -= 0;
           
            arrowElement.style.left = '46%';
            arrowElement.style.right = 'auto';            
            arrowElement.style.top = 'auto';
            arrowElement.style.bottom = '2px';
			break;
		case 'bottom':
			x -= parseInt(parseInt(row.width)/2) - 25;
            y += 63;
           
            arrowElement.style.right  = 'auto';
            arrowElement.style.left = '44%';            
            arrowElement.style.bottom = 'auto';
            arrowElement.style.top =  '-8px';
			break;
		case 'left':
			//x  -=  row.left_position + 10;
            x = x - 8;
            y  -= 10;                        
            arrowElement.style.left = 'auto';
            arrowElement.style.right = '-8px';
            arrowElement.style.top =  '30px';
            arrowElement.style.bottom = 'auto';
			break;
		case 'right':
			//x -= parseInt(row.left_position/2) - 60 ;
            x = x + 60;
            y  -= 10;                    
            arrowElement.style.right = 'auto';
            arrowElement.style.left = '-8px';
            arrowElement.style.top =  '30px';
            arrowElement.style.bottom = 'auto';            	
			break;
		default:
            x -= parseInt(parseInt(row.width)/2) - 20 ;
            y -= 0;
           
            arrowElement.style.left = '46%';
            arrowElement.style.right = '-8px';            
            arrowElement.style.top = 'auto';
            arrowElement.style.bottom = '2px';
			break;
					
	}
	e.removeAttribute('class');
    e.setAttribute("class", 'yntourbox_'+dir);
    //e.addClass('yntourbox_'+dir);
    //e.erase('class').addClass('yntourbox_'+dir);     
    //e.removeProperty('class').addClass('yntourbox_'+dir);
    //e.style.className = 'yntourbox_'+dir;
    
	e.style.left = x + 'px';
	e.style.top  = y + 'px';

	$$('.yntour_indexs').removeClass('active');
	$(row.span).addClass('active');
	en4.yntour.scrollTo($(row.span));

	var index = en4.yntour.currentIndex;

	var prev = $('id_yntour_pag_prev');

	if(prev != undefined) {
		prev.addClass('disable');
	}
	var next = $('id_yntour_pag_next');
	if(next != undefined) {
		next.addClass('disable');
	}
	var first = $('id_yntour_pag_first');
	if(first != undefined) {
		first.addClass('disable');
	}

	// enable next button
	if(index < en4.yntour.tourTotal - 1) {
		if(next != undefined) {
			next.removeClass('disable');
		}
	}

	// enable previous
	if(index > 0 && en4.yntour.tourTotal > 1) {
		if(prev != undefined) {
			prev.removeClass('disable');
		}

	}
	// enable replay
	if(index > 0 && en4.yntour.tourTotal > 1) {
		if(first != undefined) {
			first.removeClass('disable');
		}
	}

	e.style.display = 'block';
	if(en4.yntour.autorun) {
		en4.yntour.setTimeout(row.time_delay);
	}
}
en4.yntour.setTimeout = function(delay) {
	if(en4.yntour.auto && en4.yntour.currentIndex < en4.yntour.tourTotal - 1) {
		en4.yntour.timer = window.setTimeout('en4.yntour.go.next()', parseInt(delay) * 1000);
	}
	if(en4.yntour.auto && en4.yntour.autoclose && en4.yntour.currentIndex == en4.yntour.tourTotal - 1) {
		window.setTimeout('en4.yntour.close()', parseInt(en4.yntour.autoclose_time_delay) * 1000);
	}
}
en4.yntour.killTimeout = function() {
	if(en4.yntour.timer) {
		try {
		    clearTimeout(en4.yntour.timer);  
			window.killTimeout(en4.yntour.timer);
			en4.yntour.timer = 0;
		} catch(e) {
			en4.yntour.timer = 0;
		}
	}
}

document.addEvent('domready',function(){
	function k(){
		en4.yntour.bootstrap();
	}	
	window.setTimeout(k,3000);
});


