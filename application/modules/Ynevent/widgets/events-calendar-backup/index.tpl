<link type="text/css" href="application/modules/Ynevent/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<div id="datepicker">
</div>
<script type="text/javascript">      
    var dates = []; 
    function highlightDays(date) {        
        for (var i = 0; i < dates.length; i++) {           
            if (date - dates[i].day == 0) {                  
                return [true, 'ui-state-active',dates[i].event_count +" event(s)" ]; 
            }
        }
        return [true];
    }    
	    
    function change(year, month, inst)
    {     
        
        new Request.JSON({
            url: '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'widget', 'action' => 'event-calendar'), 'default', true); ?>',
            method: 'post',
            data : {
                format: 'json',
                'month': month,
                'year' : year
            },
                   
            onComplete: function(abc)
            {  				
                dates = [];                 
                for(var i=0 ; i < abc.eventDates.length; i++)
                {                    
                    dates[i]={
                        'day': Date.parse(abc.eventDates[i].day),
                        'event_count': abc.eventDates[i].event_count
                    }                    
                }                
                jQuery('#datepicker').datepicker('refresh');                          
            }
        }).send();         
    }
        
    function selectedDay(date, inst) {
        window.setTimeout(function () {
            //Get the clicked cell classes
            var classes = inst.dpDiv.find('.ui-datepicker-current-day a').parent().attr("class").split(" ");
						
            //loop through classes, read the ID=x class and extract 'x', then redirect to desired page
            for (var i in classes) {
                //alert(classes[i]);
                if (classes[i]== 'ui-state-active') { location.href = "<?php echo $this->url(array('action' => 'browse'), 'event_general') ?>"+"?selected_day=" + date }
            }
        }, 0);				
    }    
   
	<?php  if (count($this->eventDates)) :?>
		<?php foreach ($this->eventDates as $index => $date) : ?>
				dates[<?php echo $index ?>] = {
					'day' : Date.parse('<?php echo $date['day'] ?>'),
					'event_count' : <?php echo $date['event_count'] ?>
				}									 
		<?php endforeach; ?>
	<?php endif; ?>         

    var ynEventCalendar= {        
        currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
        monthNames: ['<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>', 
            '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>', 
            '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
        ],
        monthNamesShort: ['<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
        ],
        dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',            
        ],
        dayNamesShort: ['<?php echo $this->translate('Su') ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
            ],
        dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
            ],        
        firstDay: 0,
        //isRTL:yneventIsRightToLeft,
        isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
        showMonthAfterYear: false,
        yearSuffix: ''
	};
	
    // Datepicker 
    jQuery.datepicker.setDefaults(ynEventCalendar);     
    jQuery('#datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        inline: true, 
        beforeShowDay: highlightDays,
        onSelect: selectedDay,
        onChangeMonthYear: change,
        firstDay: 1
    });  
</script>