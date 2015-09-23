<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _jsAdmin.tpl 8268 2011-01-19 01:44:19Z john $
 * @author     John
 */
?>

<script type="text/javascript">

  var fieldType = '<?php echo $this->fieldType ?>';
  var topLevelFieldId = '<?php echo sprintf('%d', $this->topLevelFieldId) ?>';
  var topLevelOptionId = '<?php echo sprintf('%d', $this->topLevelOptionId) ?>';
  var logging = true;
  var sortablesInstance;
  var urls = {
    option : {
      create : '<?php echo $this->url(array('action' => 'option-create')) ?>',
      edit : '<?php echo $this->url(array('action' => 'option-edit')) ?>',
      remove : '<?php echo $this->url(array('action' => 'option-delete')) ?>'
    },
    field : {
      create : '<?php echo $this->url(array('action' => 'field-create')) ?>',
      edit : '<?php echo $this->url(array('action' => 'field-edit')) ?>',
      remove : '<?php echo $this->url(array('action' => 'field-delete')) ?>'
    },
    map : {
      create : '<?php echo $this->url(array('action' => 'map-create')) ?>',
      remove : '<?php echo $this->url(array('action' => 'map-delete')) ?>'
    },
    type : {
      create : '<?php echo $this->url(array('action' => 'type-create')) ?>',
      edit : '<?php echo $this->url(array('action' => 'type-edit')) ?>',
      remove : '<?php echo $this->url(array('action' => 'type-delete')) ?>'
    },
    heading : {
      create : '<?php echo $this->url(array('action' => 'heading-create')) ?>',
      edit : '<?php echo $this->url(array('action' => 'heading-edit')) ?>',
      remove : '<?php echo $this->url(array('action' => 'heading-delete')) ?>'
    },
    order : '<?php echo $this->url(array('action' => 'order')) ?>',
    index : '<?php echo $this->url(array('action' => 'index')) ?>'
  };

  window.addEvent('domready', function() {
    registerEvents();
  });

  // Register all events
  var registerEvents = function() {

    // Attach change profile type
    if( $('profileType') ) {
      $('profileType').removeEvents().addEvent('change', uiChangeProfileType);
    }

    // Attach create field (top level)
    $$('.admin_fields_options_addquestion').removeEvents().addEvent('click', uiSmoothTopFieldCreate);

    // Attach create heading (top level)
    $$('.admin_fields_options_addheading').removeEvents().addEvent('click', uiSmoothTopHeadingCreate);

    // Attach create option (top level)
    $$('.admin_fields_options_addtype').removeEvents().addEvent('click', uiSmoothTopOptionCreate);

    // Attach edit option (top Level)
    $$('.admin_fields_options_renametype').removeEvents().addEvent('click', uiSmoothTopOptionEdit);

    // Attach delete option (top level)
    $$('.admin_fields_options_deletetype').removeEvents().addEvent('click', uiSmoothTopOptionDelete);


    // Attach options activator
    $$('.field_extraoptions > a').removeEvents().addEvent('click', uiToggleOptions);

    // Attach create options input
    $$('.field_extraoptions_add > input').removeEvents().addEvent('keypress', uiTextOptionCreate);

    // Attach edit options activator
    $$('.field_extraoptions_choices_options > a:first-child').removeEvents().addEvent('click', uiSmoothOptionEdit);

    // Attach delete options activator
    $$('.field_extraoptions_choices_options > a + a').removeEvents().addEvent('click', uiConfirmOptionDelete);

    // Attach toggle dependent fields
    $$('.field_option_select > span + span').removeEvents().addEvent('click', uiToggleOptionDepFields);
    $$('.dep_hide_field_link').removeEvents().addEvent('click', uiToggleOptionDepFields);

    // Attach create field in option
    $$('.dep_add_field_link').removeEvents().addEvent('click', uiSmoothCreateField);

    // Attach edit field
    $$('.field > .item_options > a:first-child').removeEvents().addEvent('click', uiSmoothEditField);

    // Attach delete field
    $$('.field > .item_options > a + a').removeEvents().addEvent('click', uiConfirmDeleteField);

    // Attach heading edit
    $$('.heading > .item_options > a:first-child').removeEvents().addEvent('click', uiSmoothEditHeading);

    // Attach heading edit
    $$('.heading > .item_options > a:last-child').removeEvents().addEvent('click', uiConfirmDeleteField);


    // Attach over text
    $$('.field_extraoptions_add input').each(function(el){ new OverText(el); });


    // Attach sortables
    if( !sortablesInstance ) {
      sortablesInstance = new Sortables($$('.admin_fields').concat($$('.field_extraoptions_choices')), {
        clone: true,
        constrain: true,
        handle : '.item_handle',
        onComplete : showSaveOrderButton
      });
    } else {
      // @todo make sure this doesn't add existing ones twice
      sortablesInstance.removeLists(sortablesInstance.lists);
      sortablesInstance.addLists($$('.admin_fields').concat($$('.field_extraoptions_choices')));
    }
  }

  // Read the parent-option-child identifiers
  var readIdentifiers = function(string, throwException) {
    var m;

    // Find in ID
    m = string.match(/([0-9]+)_([0-9]+)_([0-9]+)(_([0-9]+))?/);
    if( $type(m) && $type(m[2]) ) {
      var dat = new Hash({
        parent_id : m[1],
        option_id : m[2],
        child_id : m[3]
      });
      if( $type(m[5]) ) {
        dat.set('suboption_id', m[5]);
      }
      return dat;
    }

    // Find in CLASS
    m = string.match(/parent_([0-9]+).+option_([0-9]+).+child_([0-9]+)/);
    if( $type(m) && $type(m[2]) ) {
      return new Hash({
        parent_id : m[1],
        option_id : m[2],
        child_id : m[3]
      });
    }

    // Not found
    if( !$type(throwException) || throwException ) {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("Unable to find identifiers in text:")) ?> ' + string;
    } else {
      return false;
    }
  }

  var consoleLog = function() {
    //if( logging && typeof(console) != 'undefined' && console != null ) {
    if( logging ) {
      //if( typeof(console) !== 'undefined' && console != null ) {
      //  console.log(arguments);
        //console.log.apply(null, arguments);
      //}
    }
  }

  var genericUpdateKeys = function(htmlArr) {
    consoleLog(htmlArr);
    $H(htmlArr).each(function(html, key) {
      var oldEl = $('admin_field_' + key);
      var newEl = Elements.from(html)[0];
      if( oldEl && !newEl ) { // Remove
        consoleLog('remove', key);
        oldEl.destroy();
      } else if( oldEl && newEl ) { // Replace
        consoleLog('replace', key);
        newEl.replaces(oldEl);
      } else if( !oldEl && newEl ) { // Add
        consoleLog('add', key);
        // This could cause future replaces
        var ids = readIdentifiers(key);
        if( ids.option_id == topLevelOptionId ) {
          var targetEl = $$('.admin_fields')[0];
          if( !targetEl ) {
            //throw '<?php echo $this->string()->escapeJavascript($this->translate("could not find target element")) ?>';
          } else {
            newEl.inject(targetEl, 'bottom');
          }
        } else {
          var selector =
            '.admin_field_dependent_field_wrapper_' + ids.option_id +
            ' .admin_fields';
          var targetEl = $$(selector)[0];
          if( !targetEl ) {
            //throw '<?php echo $this->string()->escapeJavascript($this->translate("could not find target element")) ?>';
          } else {
            newEl.inject(targetEl, 'bottom');
          }
        }
      }
    });
    registerEvents();
  }

  var showSaveOrderButton = function() {
    //$$('.admin_fields_options_saveorder')[0].setStyle('display', '').removeEvents().addEvent('click', function() {
      saveOrder();
    //});
  }

  var saveOrder = function() {
    $$('.admin_fields_options_saveorder')[0].setStyle('display', 'none');

    // Generate order structure
    var fieldOrder = [];
    var optionOrder = [];


    // Options order
    $$('.field_option_select').each(function(el) {
      var ids = readIdentifiers(el.get('id'));
      optionOrder.push(ids.getClean());
    });

    // Send request
    var request = new Request.JSON({
      'url' : urls.order,
      'data' : {
        'fieldType' : fieldType,
        'format' : 'json',
        'fieldOrder' : fieldOrder,
        'optionOrder' : optionOrder
      },
      onSuccess : function(responseJSON, responseHTML) {
        //alert('Order saved!');
      }
    });

    request.send();
  }

  /* --------------------------- OPTION - GENERAL --------------------------- */

  var uiToggleOptions = function(spec, forceClose) {
    if( $type(spec) == 'event' ) {
      element = $(spec.target);
    } else if( $type(spec) == 'element' ) {
      element = spec;
    } else {
      throw '<?php echo $this->string()->escapeJavascript($this->translate("cannot toggle, no event or element")) ?>';
    }
    element = element.getParent('.admin_field').getElement('.field_extraoptions');
    var targetState = !element.hasClass('active');
    if( $type(forceClose) && !forceClose ) targetState = false;
    !targetState ? element.removeClass('active') : element.addClass('active');
    OverText.update();
  }

  var uiToggleOptionDepFields = function(event) {
    element = $(event.target);
    element = element.getParent('.field_option_select') || element.getParent('.admin_field_dependent_field_wrapper');
    var ids = readIdentifiers(element.get('id'));
    var wrapper = element.getParent('.admin_field').getElement('.admin_field_dependent_field_wrapper_' + ids.suboption_id);
    var hadClass = wrapper.hasClass('active');
    $$('.admin_field_dependent_field_wrapper').removeClass('active');
    hadClass ? wrapper.removeClass('active') : wrapper.addClass('active');
    uiToggleOptions(element, false);

    // Make sure parents stay open
    var tmpEl = element;
    while( null != (tmpEl = tmpEl.getParent('.admin_field_dependent_field_wrapper')) ) {
      tmpEl.addClass('active');
    }
  }

  var uiChangeProfileType = function(event) {
    var option_id = $(event.target).value;
    var url = new URI(window.location);
    url.setData({option_id:option_id});
    window.location = url;
  }


</script>



<h2><?php echo $this->translate("Percent Profile Info Completed")?></h2>

<br />


<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("Define the weight for each profile field to calculate the percentage of profile information completed. 0 means they are not included in calculating.")?>
  </p>
<br />
<div class="admin_fields_type">
  <h3><?php echo $this->translate("Editing Profile Type:")?></h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<ul class="admin_fields">
  <?php foreach( $this->secondLevelMaps as $map ): ?>
    <?php echo $this->adminProfileWeightMeta($map,$this->type) ?>
  <?php endforeach; ?>
</ul>

<br />

<h2>
    <a class='smoothbox icon_edit' href='<?php echo $this->url(array('module'=>'profile-completeness', 'controller'=>'manage','action' => 'edit','option_id'=>$this->option_id),'admin_default',true); ?>'>
        <?php echo $this->translate("Edit the weight of profile fields") ?>
    </a>
</h2>


