<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>

<?php if ($this->viewer_id): ?>
     <script type="text/javascript">
          en4.core.runonce.add(function(){
               $$('#rsvp_options input[type=radio]').addEvent('click', function(){
                    var option_id = this.get('value');
                    $('ynevent_radio_' + option_id).className = 'ynevent_radio_loading';
                    new Request.JSON({
                         url: '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $this->subject()->getGuid()), 'default', true); ?>',
                         method: 'post',
                         data : {
                              format: 'json',
                              'event_id': <?php echo $this->subject()->event_id ?>,
                              'option_id' : option_id
                         },
              
                         onComplete: function(responseJSON, responseText)
                         {
                                  
                              refreshEventStats();
                     
                              $('ynevent_radio_' + option_id).className = 'ynevent_radio';
                              $$('#rsvp_options input').each(function(radio){
                                   if (radio.type == 'radio') {
                                        radio.style.display = null;
                                        radio.blur();
                                   }
                              });
                              if (responseJSON.error) {
                                   alert(responseJSON.error);
                              } else {
							     <?php if (!$this->canChangeVote): ?>
							            $$('.poll_radio input').set('disabled', true);
							     <?php endif ?>
                              }
                         }
                    }).send();
               });
          });

         var refreshEventStats = function() 
         {
		        new Request.HTML({
		         method: 'post',
		        url: '<?php echo $this->url(Array('module'=>'core', 'controller'=>'widget', 'action'=>'index','name' => 'ynevent.profile-info'), 'default', true);?>',
		        data: {
		            format: 'html',
		            subject : en4.core.subject.guid
		        },
		        onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) 
		        {
		            document.getElementsByClassName('layout_ynevent_profile_info')[0].innerHTML = responseHTML;
		        }
		     }).send();

    	}
     </script>

     <h3>
          <?php echo $this->translate('Your RSVP'); ?>
     </h3>
     <form class="ynevent_rsvp_form" action="<?php echo $this->url() ?>" method="post" onsubmit="return false;">
          <div class="ynevents_rsvp" id="rsvp_options">
               <div class="ynevent_radio" id="ynevent_radio_2">
                    <input id="rsvp_option_2" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 2): ?>checked="true"<?php endif; ?> value="2" /><?php echo $this->translate('Attending'); ?>
               </div>
               <div class="ynevent_radio" id="ynevent_radio_1">
                    <input id="rsvp_option_1" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 1): ?>checked="true"<?php endif; ?> value="1" /><?php echo $this->translate('Maybe Attending'); ?>
               </div>
               <div class="ynevent_radio" id="ynevent_radio_0">
                    <input id="rsvp_option_0" type="radio" class="rsvp_option" name="rsvp_options" <?php if ($this->rsvp == 0): ?>checked="true"<?php endif; ?> value="0" /><?php echo $this->translate('Not Attending'); ?>
               </div>
          </div>
     </form>

<?php endif; ?>
