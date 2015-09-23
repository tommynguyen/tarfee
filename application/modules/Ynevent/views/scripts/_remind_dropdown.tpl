<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
$timeRemindings = array(
    '-1' => 'None',
    '0' => '0 minutes',
    '5' => '5 minutes',
    '10' => '10 minutes',
    '15' => '15 minutes',
    '30' => '30 minutes',
    '60' => '1 hour',
    '120' => '2 hours',
    '180' => '3 hours',
    '240' => '4 hours',
    '300' => '5 hours',
    '360' => '6 hours',
    '420' => '7 hours',
    '480' => '8 hours',
    '540' => '9 hours',
    '600' => '10 hours',
    '660' => '11 hours',
    '720' => '12 hours',
    '780' => '18 hours',
    '1440' => '1 day',
    '2880' => '2 days',
    '4320' => '3 days',
    '5760' => '4 days',
    '10080' => '1 week',
    '20160' => '2 weeks',
    );
?>
<span style="color:#5F93B4; font-size: .8em; font-weight: bold"><?php echo $this->translate("Remind me before")?></span>
<select id="<?php echo $this->event->getIdentity() ?>">
     <?php
     $table = Engine_Api::_()->getDbTable('remind', "ynevent");
     $remind = $table->getRemindRow($this->event->getIdentity(),$this->viewer()->getIdentity());
     ?>
     <?php foreach ($timeRemindings as $key => $reminding) : ?>
          <option value="<?php echo $key ?>" 
          <?php       
          if (count($remind)>0 && $remind->remain_time == $key) :
               ?>
                       selected="selected"
     <?php endif ?>
                  >

          <?php echo $this->translate($reminding); ?></option>
<?php endforeach; ?>
</select>