
/* $Id: core.js 5969 2010-09-4 01:54:02Z thuan $ */
   
en4.questionanswer = {
	  
	Trim : function(sString){
		while (sString.substring(0,1) == ' ')
		{
			sString = sString.substring(1, sString.length);
		}
		while (sString.substring(sString.length-1, sString.length) == ' ')
		{
			sString = sString.substring(0,sString.length-1);
		}
		return sString;
	},
	
	addSlashes : function (str) {
		str=str.replace(/\\/g,'\\\\');
		str=str.replace(/\'/g,'\\\'');
		str=str.replace(/\"/g,'\\"');
		str=str.replace(/\0/g,'\\0');
		return str;
	},
			
	isEmpty : function(Object){	
		var str = this.Trim(Object.value);
		if (str == null || str == "" || str == " ") return true;
		return false;
	},

	showhide : function(id, page, user_id, category){
		if(document.getElementById(id).style.display=='none') {
			document.getElementById(id).style.display='block';		
			this.stop();
		} 
		else{
			document.getElementById(id).style.display='none';	
			this.start(page, user_id, category);
		}
	},
	
	changetab : function(activeTab, inactiveTab1, inactiveTab2){	
		document.getElementById(activeTab).className="qa_tab_active";
		$('qid').value = "0";
		if(document.getElementById(inactiveTab1) != null)
		{
			document.getElementById(inactiveTab1).className="qa_tab_inactive";
		}
		if(document.getElementById(inactiveTab2) != null)
		{
			document.getElementById(inactiveTab2).className="qa_tab_inactive";
		}
		document.getElementById('answerqa_tab').style.display = "none";
		document.getElementById('normal').style.display = 'block';
		document.getElementById('extra').style.display = 'none';
		
		if(activeTab == "qa_tab")
		{			
			this.restart(0,0,1,'');
			document.getElementById('postQuestion').style.display = 'block';
			document.getElementById('btnSearch').style.display = 'none';
			$('q_mess').value = "";
			$('search').value = "";
			if($('user_id').value == "0")
			{
				document.getElementById('q_mess').disabled = true;
			}
		}
		else if(activeTab == "myqa_tab")
		{
			$('q_user_id').value= $('user_id').value;
			var user_id = $('q_user_id').value;
			this.restart(0,user_id,1,'');
			document.getElementById('postQuestion').style.display = 'block';
			document.getElementById('btnSearch').style.display = 'none';
			$('q_mess').value = "";
			$('search').value = "";
			if($('user_id').value == "0")
			{
				document.getElementById('q_mess').disabled = true;
			}
		}	
		else
		{
			document.getElementById('postQuestion').style.display = 'none';
			document.getElementById('btnSearch').style.display = 'block';
			document.getElementById('q_mess').disabled = false;
			$('q_mess').value = "";
			this.stop();
			$('tab_content').innerHTML = "<br />";
		}
	},
	
	searchQuestion : function()
	{
		$('qid').value = "0";
		var search = $('q_mess');
		this.stop();		
		search = this.addSlashes(search.value);
		$('search').value = search;
		this.start(0, 0, 1, search, 0);
	},
			
	imposeMaxLength : function(Event, Object, MaxLen){
		return (Object.value.length <= MaxLen)||(Event.keyCode == 8 ||Event.keyCode==46||(Event.keyCode>=35&&Event.keyCode<=40))
	},

	doSubstring : function(Object, MaxLen){
		if(Object.value.length > MaxLen)
		{
			alert("Your text is too long, the maximum allowed length is " + MaxLen + " characters ");
			Object.value = Object.value.substring(0, MaxLen);
			return;
		}
	},
	
	
	showAnswerBox : function(obj, id)
	{
		var dim = this.GetTopLeft(obj);
		var answer_box = $('answer_box');
		var question = $('a_question');
		if(obj.offsetParent){
			answer_box.style.left = (dim.Left-180)+"px";
			answer_box.style.top = (dim.Top-120)+"px";
			answer_box.style.display = "block";
		}
		question.value=id;		
	},
		
	GetTopLeft : function(elm){		
		var x, y = 0;
		
		//set x to elm’s offsetLeft
		x = elm.offsetLeft;
			
		//set y to elm’s offsetTop
		y = elm.offsetTop;
			
		//set elm to its offsetParent
		elm = elm.offsetParent;
			
		//use while loop to check if elm is null
		// if not then add current elm’s offsetLeft to x
		//offsetTop to y and set elm to its offsetParent			
		while(elm != null)
		{		
			x = parseInt(x) + parseInt(elm.offsetLeft);
			y = parseInt(y) + parseInt(elm.offsetTop);
			elm = elm.offsetParent;
		}

		//here is interesting thing
		//it return Object with two properties
		//Top and Left			
		return {Top:y, Left: x};		
	},
		
	closeAnswerBox : function(){
		var answer_box = $('answer_box');
		var question = $('a_question');
		var mess = $('a_mess');
		answer_box.style.display = "none";
		question.value='';	
		mess.value='';	
	},
	
	postQuestion : function() {
		$('qid').value = "0";
		var mess = $('q_mess');
        var user_id = $('q_user_id');
        var category = $('q_category');
        //check if value is blank
        if(this.isEmpty(mess)) {
            alert("Please enter your text...");
            return false;
        }
        //post Question
        var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'questionanswer/postquestion',
            'data' : {
                'mess' : mess.value,            
                'category' : category.value                                   
            },
            'onComplete':function(responseObject)
            {
                if( typeof(responseObject)!="object")
                {
                     //alert('ERR');
                }
                else
                {                                
                    if(responseObject.result == "success")
                    {                        
                        mess.value=''; //clear text after submiting
                        en4.questionanswer.closeAnswerBox();
                        en4.questionanswer.stop();
                        en4.questionanswer.start("1");
                    }
                    else
                    {                        
                        alert(responseObject.message);
                    }            
                }
            }
        });
        request.send();
	},
    
    postAnswer : function(extraBox){
		if(extraBox == 1)
			var mess = $('a_mess1');
		else
			var mess = $('a_mess');
        var user_id = $('a_user_id');
        var question = $('a_question');
        var page = $('currentPage');   
        var search = $('search'); 
        //check if value is blank
        if(this.isEmpty(mess)) {
            alert("Please enter your text...");
            return false;
        }    
            
        var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'questionanswer/postanswer',
            'data' : {
                'mess' : mess.value,
                'user_id' : user_id.value,
                'question_id' : question.value            
            },
            'onComplete':function(responseObject)
            {
                if( typeof(responseObject)!="object")
                {
                  //alert('ERR');
                }
                else
                {                                    
                    if(responseObject.result == "success")
                    {                       	      
                    	mess.value = "";
                    	en4.questionanswer.restart(page.value);                    	
                    }
                    else
                    {
                        alert(responseObject.message);
                    }            
                }
            }
        });
        request.send();
    },
    
    voteQuestion : function(id)
    {
		var page = $('currentPage');   
        var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'questionanswer/votequestion',
            'data' : {                
                'question_id' : id            
            },
            'onComplete':function(responseObject)
            {
                if( typeof(responseObject)!="object")
                {
                  //alert('ERR');
                }
                else
                {                                    
                    if(responseObject.result == "success")
                    {   
                    	en4.questionanswer.restart(page.value);
                    }
                    else
                    {
                        alert(responseObject.message);
                    }            
                }
            }
        });
        request.send();
    },
    
        
    start : function(page, user_id, category, search, quesid)
    {
        var pageObj = $('currentPage');
        //check page
        //if page <= 0, get current page from hidden field    
        if((!page || page <= 0) && pageObj != null)
        {
            var page = pageObj.value;
        }
            
        var userObj = $('q_user_id');        
        //check myqa_tab active        
        if(!user_id && userObj && ($('myqa_tab') != null && $('myqa_tab').className == "qa_tab_active" ))
        {
            var user_id = $('q_user_id').value;
        }
        
		var searchObj = $('search');
        //check search_tab active
        if(search != "" && searchObj && ($('searchqa_tab') != null && $('searchqa_tab').className == "qa_tab_active" ))
        {
            var search = $('search').value;
        }
		else
			var  search = "";
        
        var qidObj = $('qid');		
        //check special question id
        if(!quesid && qidObj)
        {
            var quesid = $('qid').value;
        }
		else if(quesid == "" || isNaN(quesid))
			var quesid = "0";
		        
        this.getThreads(page, user_id, category, search, quesid);        
        mainThread = setInterval ( "en4.questionanswer.getThreads("+page+", "+user_id+", "+category+", '"+search+"', '"+quesid+"');", 50000 );
    },
    
    stop : function()
    {
    	clearInterval(mainThread);
    },
    
    restart : function(page, user_id, category, search, qid)
    {    	
    	this.stop();
        this.closeAnswerBox();
        this.start(page, user_id, category, search, qid);
        var pageObj = $('currentPage');
        if(pageObj != null){
            pageObj.value = page;
        }
    },
    
    getThreads : function(page, user_id, category, search, qid)
    {       
    	if(user_id == "" || typeof(user_id) == "undefined") user_id = 0;
        if(page == "" || typeof(page) == "undefined") page = 0;
        if(category == "" || category == 0 || typeof(category) == "undefined") category = 1;
        if(search == "" || typeof(search) == "undefined" || search == "undefined") search = "";
        if(qid == "" || typeof(qid) == "undefined" || qid == "undefined") qid = 0;
        
        if(parseInt(qid) > 0)
        {        	        	
        	document.getElementById('answerqa_tab').style.display = "block";
        
        	if(document.getElementById('searchqa_tab') != null)
        	{
        		document.getElementById('searchqa_tab').className="qa_tab_inactive";
        	}        	    		
    		if(document.getElementById('myqa_tab') != null)
    		{
    			document.getElementById('myqa_tab').className="qa_tab_inactive";
    		}
    		if(document.getElementById('qa_tab') != null)
    		{
    			document.getElementById('qa_tab').className="qa_tab_inactive";
    		}
    					
			document.getElementById('normal').style.display = 'none';
			document.getElementById('extra').style.display = 'block';
			$('a_question').value = qid;
        }
		
        var request = new Request.JSON({
            'method' : 'post',
            'contentType' : "charset=UTF-8",
            'url' : en4.core.baseUrl + 'questionanswer/list',
            'data' : {            
                'user_id' : user_id,
                'category' : category,
                'search' : search,
                'page' : page,
                'qid' : qid
            },
            'onComplete':function(responseObject)
            {
                var content = '';
                var paging = '';
                var tab_content = $('tab_content');
                if( typeof(responseObject)!="object")
                {
                  //alert('ERR');
                }
                else
                {         
                	if(responseObject.result == "norecord")
                	{
                		tab_content.innerHTML = "<p>"+responseObject.message+"</p><p>&nbsp;</p>";                		
                	}
                	else
                	{						
	                    //get paging bar
	                    paging += '<div class="qa_paging">';
	                    if(responseObject['page_info'].maxpage >1)
	                    {
	                        //button previous
	                        if(responseObject['page_info'].p !=1)
	                            paging +='<img src="application/modules/Questionanswer/externals/images/qa_left_page.png" alt="" onclick="en4.questionanswer.restart('+(parseInt(responseObject['page_info'].p)-parseInt(1))+','+user_id+', '+category+', \''+search+'\');"/>';
	                        else
	                            paging +='<img src="application/modules/Questionanswer/externals/images/qa_left_page_disabled.png" alt="" />';
	                        var i = 1;
	                        var viewpage = 10;
	                        if(parseInt(responseObject['page_info'].p) == parseInt(responseObject['page_info'].maxpage))
	                        {
								if(parseInt(responseObject['page_info'].maxpage) > 10)
								{
									i = parseInt(responseObject['page_info'].maxpage) - 9;
								}
								else
								{
									i = 1;
								}	                        	
	                        	viewpage = parseInt(responseObject['page_info'].maxpage);
	                        }
	                        else if(parseInt(responseObject['page_info'].p)>= 10 && parseInt(responseObject['page_info'].p) + 10 >= parseInt(responseObject['page_info'].maxpage))
	                        {
	                        	i = parseInt(responseObject['page_info'].maxpage) - 10;
	                        	viewpage = parseInt(responseObject['page_info'].maxpage);
	                        }
	                        else if(parseInt(responseObject['page_info'].p)>= 10)
	                        {	                        
	                        	i = parseInt(responseObject['page_info'].p);
	                        	viewpage = 10 + parseInt(responseObject['page_info'].p);
	                        }
	                        
	                        
	                        //paging number
	                        for(i; i<=responseObject['page_info'].maxpage && i<= viewpage; i++)
	                        {
	                            if(i == responseObject['page_info'].p)
	                                paging += '<a href="javascript:void(0)"><span style="color:#FF0000">'+i+'</span></a>';
	                            else
	                                paging += '<a href="javascript:void(0)"><span onclick="en4.questionanswer.restart('+i+','+user_id+', '+category+', \''+search+'\')">'+i+'</span></a>';
	                        }
	                        //button next
	                        if (responseObject['page_info'].p != responseObject['page_info'].maxpage)
	                            paging +='<img src="application/modules/Questionanswer/externals/images/qa_right_page.png" alt="" onclick="en4.questionanswer.restart('+(parseInt(responseObject['page_info'].p)+parseInt(1))+','+user_id+', '+category+', \''+search+'\');"  />';
	                        else
	                            paging +='<img src="application/modules/Questionanswer/externals/images/qa_right_page_disabled.png" alt="" />';        
	                            
	                    }
	                    paging +='</div>';
	                    
	                    //get threads info
	                    for(i=0; i<responseObject['threads_info'].length; i++)
	                    {       
	                        //get question info                        
	                        content +='<div class="qa_line">'+
	                                    '<div style="float:left;">' + responseObject['threads_info'][i].question.user_photo +
	                                    '<div class="qa_au_in">';
	                                    if(responseObject['threads_info'][i].question.is_allowed == "1")
	                                    	content += '<a href="javascript:void(0)"><img title="Like" id="vote'+responseObject['threads_info'][i].question.question_id+'" src="application/modules/Questionanswer/externals/images/vote.jpg" class="fr" alt="Like me" border="0" onclick="en4.questionanswer.voteQuestion('+responseObject['threads_info'][i].question.question_id+')" /></a>';
	                        content +=      '<a href="javascript:void(0)"><img id="answer'+responseObject['threads_info'][i].question.question_id+'" src="application/modules/Questionanswer/externals/images/Q&A-box_19.png" onmouseover="this.src=\'application/modules/Questionanswer/externals/images/Q&A-box_19b.png\'" onmouseout="this.src=\'application/modules/Questionanswer/externals/images/Q&A-box_19.png\'" class="fr" alt="" border="0" onclick="en4.questionanswer.showAnswerBox(this, '+responseObject['threads_info'][i].question.question_id+')" /></a>'+
	                                        '<a class="smoothbox" href="javascript:void(0)" onclick="openPopup(\'' + en4.core.baseUrl + 'qa/addreport?qid='+responseObject['threads_info'][i].question.question_id+'\');" ><img src="application/modules/Questionanswer/externals/images/Q&A-box_17.png" class="fr" alt="" border="0" /></a>'+
	                                        '<a href="'+en4.core.baseUrl+'profile/'+responseObject['threads_info'][i].question.username+'">'+responseObject['threads_info'][i].question.displayname+'</a> ('+responseObject['threads_info'][i].question.date_created+')'+
	                                       '</div>'+                                    
	                                    '<div><span class="qa_label">&nbsp;Q:&nbsp;</span><span  class="qa_content">' +  responseObject['threads_info'][i].question.content + '</span></div></div>'+
	                                       '<div class="space-line"></div>'+
	                                '</div>';
	                        //get answers info                    
	                        for(j=0; j<responseObject['threads_info'][i].answers_list.length; j++)
	                        {                    
	                            if(j==3) content += '<div id="answer_line'+responseObject['threads_info'][i].question.question_id+'" style="display:none">';
	                            content += '<div class="qa_line">'+
	                            				responseObject['threads_info'][i].answers_list[j].user_photo +
	                                            '<span class="qa_label_r">&nbsp;A:&nbsp;</span><span  class="answer_content">'+ responseObject['threads_info'][i].answers_list[j].content + '</span>'+
	                                            '<div class="space-line"></div>'+
	                                        '</div>';
	                        }
	                        if(j>3) {
	                            content += '</div>';                    
	                            //get number of replies
	                            content +='<div class="qa_total_reply"><a href="javascript:void(0)" onclick="en4.questionanswer.showhide(\'answer_line'+responseObject['threads_info'][i].question.question_id+'\','+responseObject['page_info'].p+','+user_id+','+category+');"><span class="qa_reply">'+responseObject['threads_info'][i].question.answers+'</span> Answers</a>|';
	                            content +='<span class="qa_reply">'+responseObject['threads_info'][i].question.likes+'</span> Likes</div>';
	                        }
	                        else{
	                        	content +='<div class="qa_total_reply" style="float:right;"><a href="javascript:void(0)"><span class="qa_reply">'+responseObject['threads_info'][i].question.answers+'</span> Answers</a>|';
	                        	content +='<span>'+responseObject['threads_info'][i].question.likes+'</span> Likes</div><br />';
	                        }
						}
	                    content += paging;
	                    tab_content.innerHTML= "<br /><br />" + content;
                	}
                }
            }
             });
        request.send();
    }
};