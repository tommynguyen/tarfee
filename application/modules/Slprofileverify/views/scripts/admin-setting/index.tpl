<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<table id="table_mapping" class='admin_table'>
    <thead>
        <tr>
            <th><?php echo $this->translate("Verified User Group") ?></th>
            <th><?php echo $this->translate("Unverified User Group") ?></th>
            <th><?php echo $this->translate("Actions") ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->aGroupVerified as $key => $iIdGroup): ?>
            <tr>
                <td>
                    <?php echo $this->item('authorization_level', $iIdGroup)->getTitle(); ?>
                    <input type="hidden" name="member_verified[]" value="<?php echo $iIdGroup; ?>"/>
                </td>
                <td>
                    <?php echo $this->item('authorization_level', $this->aGroupUnverified[$key])->getTitle(); ?>
                    <input type="hidden" name="member_unverified[]" value="<?php echo $this->aGroupUnverified[$key]; ?>"/>
                </td>
                <td>
                    <a href="javascript:void(0)" onclick="removeGroup(this);"><?php echo $this->translate("Remove") ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr id="add-group">
            <td>
                <select id="verified-group">
                    <option value="">select</option>
                    <?php foreach ($this->aMemberVerified as $iLevelId => $sName): ?>
                        <option value="<?php echo $iLevelId; ?>"><?php echo $sName; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select id="unverified-group">
                    <option value="">select</option>
                    <?php foreach ($this->aMemberUnverified as $iLevelId => $sName): ?>
                        <option value="<?php echo $iLevelId; ?>"><?php echo $sName; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><button onclick="addGroup();" type="button"><?php echo $this->translate("Add") ?></button></td>
        </tr>
    </tbody>
</table>

<script type="text/javascript">
    window.addEvent('domready', function() {
        new Element('img', {
            src: "<?php echo $this->src_img; ?>"
        }).inject($('badge-element'));
        $('member_mapping-element').empty();
        $('table_mapping').inject($('member_mapping-element'));
        $('member_verified_remove').destroy();
        $('member_unverified_remove').destroy();
    });

    function addGroup() {
        var elVerified = $('verified-group');
        var elUnverified = $('unverified-group');
        if (elVerified.value !== "" && elUnverified.value !== "") {
            var tdOne = "<td>" + elVerified.getSelected().get('text') + "<input type='hidden' value='" + elVerified.value + "' name='member_verified[]'/> </td>";
            var tdTwo = "<td>" + elUnverified.getSelected().get('text') + "<input type='hidden' value='" + elUnverified.value + "' name='member_unverified[]'/> </td>";
            var aRow = new Element('tr', {
                'html': tdOne + tdTwo + "<td><a href='javascript:void(0);' onclick='removeGroup(this)'><?php echo $this->translate("Remove") ?></a></td>"
            });
            aRow.inject($('table_mapping').getElementById('add-group'), 'before');
            elVerified.getSelected().destroy();
            elUnverified.getSelected().destroy();
        }
    }

    function removeGroup(el) {
        var elParent = el.getParent('tr');
        var verifiedText = elParent.getChildren('td')[0].get('text');
        var verifiedValue = elParent.getChildren('td')[0].getChildren('input').get('value');
        var unverifiedText = elParent.getChildren('td')[1].get('text');
        var unverifiedValue = elParent.getChildren('td')[1].getChildren('input').get('value');

        new Element('option', {
            'value': verifiedValue,
            'text': verifiedText
        }).inject($('verified-group'));

        new Element('option', {
            'value': unverifiedValue,
            'text': unverifiedText
        }).inject($('unverified-group'));

        elParent.destroy();
    }
</script>