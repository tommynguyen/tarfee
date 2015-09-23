<div class="ynfeed_popup">
	<div class="ynfeed_popup_top">		
		<div class="ynfeed_popup_des">
			<b>
	          	<?php echo $this->translate("Create a new list for viewing updates by choosing content items and friends below.") ?>
			</b>
		</div>
	</div>
	
	<div class="ynfeed_popup_options">
		<input type="hidden"  name="page" id='page' value="1"/>  
		<div id="list_title-element" class="ynfeed_popup_options_left">      
			<input type='text' class='text suggested' name='title' id='list_title' onkeypress="javascript:if(document.getElementById('validation_title')){ document.getElementById('list_title-element').removeChild(document.getElementById('validation_title'));}" size='50' maxlength='100' alt='<?php echo $this->translate('Enter List Title...') ?>' />
		</div>
	  <div class="ynfeed_popup_options_right">
	    <input type='text' class='ynfeed_popup_searchbox suggested' name='search' id='field_search' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' onkeyup="getContentItem(1)" />
	  </div> 
		<div class="ynfeed_popup_options_middle">
        <b><?php echo $this->translate("Choose :") ?></b>
	    <select name="resource_type" id="resource_type" onchange="getContentItem(1);">
	      <?php foreach ($this->customTypeLists as $list): ?>
	      	<option value="<?php echo $list->resource_type ?>" >  <?php echo $this->translate($list->resource_title); ?></option>
	      <?php endforeach; ?>
	    </select> 
	  	<b>&nbsp;<?php echo sprintf($this->translate("Selected (%s)"),'<span id="selected_count">0</span>')
?></b>
	  </div>   
	</div>
	
	<div class="ynfeed_popup_content">
		<div class="ynfeed_popup_content_inner">
			<div id="resource_loading" class="ynfeed_item_list_popup_loader">
				<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Ynfeed/externals/images/loading.gif' alt="Loading" />
			</div>
 			<div id="resource_items_content"></div>
 		</div>
 	</div>		
 
	<div class="popup_btm">
		<div id="check_error"></div>
		<form method="post" action="" id="form_custom_list">
			<input type="hidden"  name="selected_resources" id='selected_resources' />
			<input type="hidden"  name="title" id='title' />
			<div class="ynfeed_feed_popup_bottom" id="buttons-element" style="float: right">
				<button type='button' onClick='submitListForm()'><?php echo sprintf($this->translate('Create list with %s items'),'<span id="item_count">0</span>'); ?> </button> 
				<?php echo $this -> translate("or")?>
				<?php $session = new Zend_Session_Namespace('mobile');
						$isMobile = $session -> mobile;?>
				<a href="javascript:void(0);" onclick="<?php if($isMobile) echo 'history.go(-1); return false;'; else echo 'javascript:parent.Smoothbox.close()'?>"><?php echo $this->translate("cancel"); ?></a>    
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
  var list=new Array();
  var pagenationContent=null;
  var resource_type_temp=null;
  var getContentItem = function(type)
  { 
    if(resource_type_temp != document.getElementById('resource_type').value)
    {
      document.getElementById('field_search').value='';
      document.getElementById('page').value = 1;
    }
    resource_type_temp =document.getElementById('resource_type').value;
    var url = '<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'custom-list', 'action' => 'get-content-items'), 'default', true) ?>';

    if(document.getElementById('page').value == 1)
    {      
    	document.getElementById('resource_items_content').innerHTML="";
    	document.getElementById('resource_loading').style.display = '';
    }
    var request = new Request.HTML({
      url : url,
      data : {
        format : 'html',
        'resource_type' : document.getElementById('resource_type').value,
        'search':document.getElementById('field_search').value,
        'page':document.getElementById('page').value
      },
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('resource_loading').style.display = 'none';       
        if(document.getElementById('page').value ==1)
        {       
         document.getElementById('resource_items_content').innerHTML="";       
        }
        else
        {
          if(document.getElementById('view_more_sea'))
         	document.getElementById('view_more_sea').destroy();       
        }
        if(type == 0)
       		Elements.from(responseHTML).inject(document.getElementById('resource_items_content'));
       	else
       		document.getElementById('resource_items_content').innerHTML = responseHTML;       
        setSelecetedItems();
      }
    });
    request.send();

  }
  en4.core.runonce.add(function() 
  {
    if(document.getElementById('list_title'))
    {
      new OverText(document.getElementById('list_title'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

  if(document.getElementById('field_search'))
  {
    new OverText(document.getElementById('field_search'), {
      poll: true,
      pollInterval: 500,
      positionOptions: {
        position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        offset: {
          x: ( en4.orientation == 'rtl' ? -4 : 4 ),
          y: 2
        }
      }
    });
}
getContentItem(1);
});

function getNextPage()
{
  document.getElementById('page').value=parseInt(document.getElementById('page').value)+1;
  getContentItem(0);
  if(document.getElementById('view_more_sea'))
  {
      document.getElementById('view_more_link').style.display ='none';
      document.getElementById('view_more_loding').style.display ='';  
  }
}
function setContentInList(element,resource_type, resource_id)
{
  var index=resource_type+"-"+resource_id; 
  var checkelement=document.getElementById(index);
  if(checkelement.value==0){
   // pushinto list  
   list.push(index);
   element.addClass('selected');
   checkelement.value=1;
  }else{
   // pop from list
   for(var i=0; i<list.length;i++ )
      {
        if(list[i]==index) 
          list.splice(i,1); 
      }
      checkelement.value=0;
      element.removeClass('selected');
  } 
  document.getElementById("selected_count").innerHTML= document.getElementById("item_count").innerHTML=list.length;
  document.getElementById("selected_resources").value=list;
}

function setSelecetedItems()
{ 
  for(var i=0; i<list.length;i++ )
  { 
    if(document.getElementById(list[i])){
      document.getElementById(list[i]).value=1;
     var element= document.getElementById('contener_'+list[i]);
      element.addClass('selected');
    }

  }      

}
function submitListForm(){
  document.getElementById("title").value=document.getElementById("list_title").value;
   
  if (document.getElementById("title").value=="")
    {
      if(!document.getElementById('validation_title')){
        var div_campaign_name = document.getElementById("list_title-element");
        var myElement = new Element("p");
        myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Please enter a List Title.")) ?>';
        myElement.addClass("aaf_feed_error");
        myElement.id = "validation_title";
        div_campaign_name.appendChild(myElement);
      }
      validationFlage=1;
    }else{
    document.getElementById("form_custom_list").submit();
    }
}
</script>