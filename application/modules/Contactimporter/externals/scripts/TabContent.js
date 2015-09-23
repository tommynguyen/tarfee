//** Tab Content script v2.0- � Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
//** Updated Oct 7th, 07 to version 2.0. Contains numerous improvements:
//   -Added Auto Mode: Script auto rotates the tabs based on an interval, until a tab is explicitly selected
//   -Ability to expand/contract arbitrary DIVs on the page as the tabbed content is expanded/ contracted
//   -Ability to dynamically select a tab either based on its position within its peers, or its ID attribute (give the target tab one 1st)
//   -Ability to set where the CSS classname "selected" get assigned- either to the target tab's link ("A"), or its parent container
//** Updated Feb 18th, 08 to version 2.1: Adds a "tabinstance.cycleit(dir)" method to cycle forward or backward between tabs dynamically
//** Updated April 8th, 08 to version 2.2: Adds support for expanding a tab using a URL parameter (ie: http://mysite.com/tabcontent.htm?tabinterfaceid=0) 

////NO NEED TO EDIT BELOW////////////////////////

function ddtabcontent(tabinterfaceid){
	this.tabinterfaceid=tabinterfaceid //ID of Tab Menu main container
	this.tabs=document.getElementById(tabinterfaceid).getElementsByTagName("a") //Get all tab links within container
	this.enabletabpersistence=true
	this.hottabspositions=[] //Array to store position of tabs that have a "rel" attr defined, relative to all tab links, within container
	this.currentTabIndex=0 //Index of currently selected hot tab (tab with sub content) within hottabspositions[] array
	this.subcontentids=[] //Array to store ids of the sub contents ("rel" attr values)
	this.revcontentids=[] //Array to store ids of arbitrary contents to expand/contact as well ("rev" attr values)
	this.selectedClassTarget="link" //keyword to indicate which target element to assign "selected" CSS class ("linkparent" or "link")
}

ddtabcontent.getCookie=function(Name){ 
	var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
	if (document.cookie.match(re)) //if cookie found
		return document.cookie.match(re)[0].split("=")[1] //return its value
	return ""
}

ddtabcontent.setCookie=function(name, value){
	document.cookie = name+"="+value+";path=/" //cookie value is domain wide (path=/)
}

ddtabcontent.prototype={

	expandit:function(tabid_or_position){ //PUBLIC function to select a tab either by its ID or position(int) within its peers
		this.cancelautorun() //stop auto cycling of tabs (if running)
		var tabref=""
		try{
			if (typeof tabid_or_position=="string" && document.getElementById(tabid_or_position).getAttribute("rel")) //if specified tab contains "rel" attr
				tabref=document.getElementById(tabid_or_position)
			else if (parseInt(tabid_or_position)!=NaN && this.tabs[tabid_or_position].getAttribute("rel")) //if specified tab contains "rel" attr
				tabref=this.tabs[tabid_or_position]
		}
		catch(err){alert("Invalid Tab ID or position entered!")}
		if (tabref!="") //if a valid tab is found based on function parameter
			this.expandtab(tabref) //expand this tab
	},

	cycleit:function(dir, autorun){ //PUBLIC function to move foward or backwards through each hot tab (tabinstance.cycleit('foward/back') )
		if (dir=="next"){
			var currentTabIndex=(this.currentTabIndex<this.hottabspositions.length-1)? this.currentTabIndex+1 : 0
		}
		else if (dir=="prev"){
			var currentTabIndex=(this.currentTabIndex>0)? this.currentTabIndex-1 : this.hottabspositions.length-1
		}
		if (typeof autorun=="undefined") //if cycleit() is being called by user, versus autorun() function
			this.cancelautorun() //stop auto cycling of tabs (if running)
		this.expandtab(this.tabs[this.hottabspositions[currentTabIndex]])
	},

	setpersist:function(bool){ //PUBLIC function to toggle persistence feature
			this.enabletabpersistence=bool
	},

	setselectedClassTarget:function(objstr){ //PUBLIC function to set which target element to assign "selected" CSS class ("linkparent" or "link")
		this.selectedClassTarget=objstr || "link"
	},

	getselectedClassTarget:function(tabref){ //Returns target element to assign "selected" CSS class to
		return (this.selectedClassTarget==("linkparent".toLowerCase()))? tabref.parentNode : tabref
	},

	urlparamselect:function(tabinterfaceid){
		var result=window.location.search.match(new RegExp(tabinterfaceid+"=(\\d+)", "i")) //check for "?tabinterfaceid=2" in URL
		return (result==null)? null : parseInt(RegExp.$1) //returns null or index, where index (int) is the selected tab's index
	},

	expandtab:function(tabref)
	{
		var subcontentid=tabref.getAttribute("rel") //Get id of subcontent to expand
		//Get "rev" attr as a string of IDs in the format ",john,george,trey,etc," to easily search through
		var associatedrevids=(tabref.getAttribute("rev"))? ","+tabref.getAttribute("rev").replace(/\s+/, "")+"," : ""
		this.expandsubcontent(subcontentid)
		this.expandrevcontent(associatedrevids)
		for (var i=0; i<this.tabs.length; i++){ //Loop through all tabs, and assign only the selected tab the CSS class "selected"
			this.getselectedClassTarget(this.tabs[i]).className=(this.tabs[i].getAttribute("rel")==subcontentid)? "selected" : ""
		}
		if (this.enabletabpersistence) //if persistence enabled, save selected tab position(int) relative to its peers
			ddtabcontent.setCookie(this.tabinterfaceid, tabref.tabposition)
		this.setcurrenttabindex(tabref.tabposition) //remember position of selected tab within hottabspositions[] array
	},

	expandsubcontent:function(subcontentid)
	{
		for (var i = 1; i < this.subcontentids.length - 1; i++)
		{
			var subcontent=document.getElementById(this.subcontentids[i]) //cache current subcontent obj (in for loop)
			subcontent.style.display = (subcontent.id == subcontentid)? "block" : "none" //"show" or hide sub content based on matching id attr value
		}
	},

	expandrevcontent:function(associatedrevids){
		var allrevids=this.revcontentids
		for (var i=0; i<allrevids.length; i++){ //Loop through rev attributes for all tabs in this tab interface
			//if any values stored within associatedrevids matches one within allrevids, expand that DIV, otherwise, contract it
			document.getElementById(allrevids[i]).style.display=(associatedrevids.indexOf(","+allrevids[i]+",")!=-1)? "block" : "none"
		}
	},

	setcurrenttabindex:function(tabposition){ //store current position of tab (within hottabspositions[] array)
		for (var i=0; i < this.hottabspositions.length; i++){
			if (tabposition==this.hottabspositions[i]){
				this.currentTabIndex=i
				break
			}
		}
	},

	autorun:function(){ //function to auto cycle through and select tabs based on a set interval
		this.cycleit('next', true)
	},

	cancelautorun:function(){
		if (typeof this.autoruntimer!="undefined")
			clearInterval(this.autoruntimer)
	},

	init:function(automodeperiod){
		var persistedtab=ddtabcontent.getCookie(this.tabinterfaceid) //get position of persisted tab (applicable if persistence is enabled)
		var selectedtab=-1 //Currently selected tab index (-1 meaning none)
		var selectedtabfromurl=this.urlparamselect(this.tabinterfaceid) //returns null or index from: tabcontent.htm?tabinterfaceid=index
		this.automodeperiod=automodeperiod || 0
		var total =  this.tabs.length;
		for (var i = 0; i < this.tabs.length; i++)
		{
			this.tabs[i].tabposition = i //remember position of tab relative to its peers
			if (this.tabs[i].getAttribute("rel"))
			{
				var tabinstance=this
				this.hottabspositions[this.hottabspositions.length]=i //store position of "hot" tab ("rel" attr defined) relative to its peers
				this.subcontentids[this.subcontentids.length]=this.tabs[i].getAttribute("rel") //store id of sub content ("rel" attr value)
				this.tabs[i].onclick=function()
				{
					if(this.id != 0 && this.id != total-1)
					{
	          			tabinstance.expandtab(this)
						tabinstance.cancelautorun() //stop auto cycling of tabs (if running)
						//set active for current tab head
				           var page_heads = document.getElementById('contactimporter_page_list').getChildren();
				           for(i=0;i<page_heads.length;i++)
				           {
				             if( page_heads[i].hasClass('selected'))
				              {
				                page_heads[i].removeClass('selected');
				              }
				           }
				           
				           var tab_head_selected = document.getElementsByClassName('selected')[0];
				           if(tab_head_selected)
				           {
				             tab_head_selected.parentNode.addClass('selected');
				           }
				           var sub_page = 5;
				           if((total - 2 - parseInt(this.id)) < 5)
				           {
				           		sub_page = total - 2 - parseInt(this.id);
				           }
							var end_page = parseInt(this.id) + sub_page;
							if(this.id > 5 && total - 2 > 10)
							{
								for(var l = 1; l <= total - 1; l ++)
								{
									document.getElementById(l).parentNode.style.display = 'none';
								}
								
								for(var j = 1; j < 10 - (end_page - parseInt(this.id)); j ++)
								{
									document.getElementById(parseInt(this.id) - j).parentNode.style.display = 'block';
								}
								for(var k = parseInt(this.id); k <= end_page; k ++)
								{
									document.getElementById(k).parentNode.style.display = 'block';
								}
							}
							if(this.id <= 5 && total - 1 > 10)
							{
								var max = 10;
								if(total - 1 > 10)
								{
									max = 10;
								}
								else
								{
									max = total - 1;
								}
								for(var j = 1; j <= max; j ++)
								{
									document.getElementById(j).parentNode.style.display = 'block';
								}
								for(var k = max + 1; k <= total - 1; k ++)
								{
									document.getElementById(k).parentNode.style.display = 'none';
								}
							}
							if(this.id > 1)
							{
								document.getElementById('0').parentNode.style.display = 'block';
							}
							else
							{
								document.getElementById('0').parentNode.style.display = 'none';
							}
							if(this.id == total - 2)
							{
								document.getElementById(total - 1).parentNode.style.display = 'none';
							}
							else
							{
								document.getElementById(total - 1).parentNode.style.display = 'block';
							}
				          return false
					}
					else
					{
						var page_selected = document.getElementsByClassName('selected')[0].children[0];
						var page_id = parseInt(page_selected.id);
						if(this.id == 0)
						{
							//Previous
							page_id = page_id - 1;
						}
						else
						{
							//Next
							page_id = page_id + 1;
						}
						var obj = document.getElementById(page_id);
						tabinstance.expandtab(obj)
						tabinstance.cancelautorun() //stop auto cycling of tabs (if running)
						//set active for current tab head
				           var page_heads = document.getElementById('contactimporter_page_list').getChildren();
				           for(i=0;i<page_heads.length;i++)
				           {
				             if( page_heads[i].hasClass('selected'))
				              {
				                page_heads[i].removeClass('selected');
				              }
				           }
				           obj.parentNode.addClass('selected');
				           var sub_page = 5;
				           if((total - 2 - page_id) < 5)
				           {
				           		sub_page = total - 2 - page_id;
				           }
							var end_page = page_id + sub_page;
							if(page_id > 5 && total - 2 > 10)
							{
								for(var l = 1; l <= total - 1; l ++)
								{
									document.getElementById(l).parentNode.style.display = 'none';
								}
								
								for(var j = 1; j < 10 - (end_page - page_id); j ++)
								{
									document.getElementById(page_id - j).parentNode.style.display = 'block';
								}
								for(var k = page_id; k <= end_page; k ++)
								{
									document.getElementById(k).parentNode.style.display = 'block';
								}
							}
							if(page_id <= 5 && total - 1 > 10)
							{
								var max = 10;
								if(total - 1 > 10)
								{
									max = 10;
								}
								else
								{
									max = total - 1;
								}
								for(var j = 1; j <= max; j ++)
								{
									document.getElementById(j).parentNode.style.display = 'block';
								}
								for(var k = max + 1; k <= total - 1; k ++)
								{
									document.getElementById(k).parentNode.style.display = 'none';
								}
							}
							if(page_id > 1)
							{
								document.getElementById('0').parentNode.style.display = 'block';
							}
							else
							{
								document.getElementById('0').parentNode.style.display = 'none';
							}
							if(page_id == total - 2)
							{
								document.getElementById(total - 1).parentNode.style.display = 'none';
							}
							else
							{
								document.getElementById(total - 1).parentNode.style.display = 'block';
							}
				          return false
					}
				}
				
				if(i != 0 && i != total - 1)
				{
					if (this.tabs[i].getAttribute("rev"))
					{ //if "rev" attr defined, store each value within "rev" as an array element
						this.revcontentids=this.revcontentids.concat(this.tabs[i].getAttribute("rev").split(/\s*,\s*/))
					}
					if (selectedtabfromurl==i || this.enabletabpersistence && selectedtab==-1 && parseInt(persistedtab)==i || !this.enabletabpersistence && selectedtab==-1 && this.getselectedClassTarget(this.tabs[i]).className=="selected")
					{
						selectedtab=i //Selected tab index, if found
					}
				}
			}
		} //END for loop
		if (selectedtab!=-1) //if a valid default selected tab index is found
			this.expandtab(this.tabs[selectedtab]) //expand selected tab (either from URL parameter, persistent feature, or class="selected" class)
		else //if no valid default selected index found
			this.expandtab(this.tabs[this.hottabspositions[1]]) //Just select first tab that contains a "rel" attr
		if (parseInt(this.automodeperiod)>500 && this.hottabspositions.length>1){
			this.autoruntimer=setInterval(function(){tabinstance.autorun()}, this.automodeperiod)
		}
	} //END int() function

} //END Prototype assignment