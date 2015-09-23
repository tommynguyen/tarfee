<?php if ($this->countAgent < $this->maxAgent) : ?>
	<div class="ynevent_block">
	    <form action="<?php echo $this->url(array('controller' => 'agent', 'action' => 'create'), 'event_extended') ?>">
	        <button type="submit"><?php echo $this->translate("Create new agent") ?></button> 
	    </form>
    </div>
<?php endif; ?>

<?php if (count($this->paginator)): ?>
    <div id="formborder">
        <div class="table_scroll">
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class='ynevent_agent_table'>
            	<thead>
                        <tr>
                            <th class="table_th"><?php echo $this->translate('Agent Name'); ?></th>
                    		<th class="table_th"><?php echo $this->translate('Creation Date'); ?></th>
                    		<th class="table_th"><?php echo $this->translate('Options'); ?></th>
                        </tr>
                </thead>
				<tbody>
                <?php foreach ($this->paginator as $agent) :
                    ?>
                    <tr>
                        <td class="table_td"><?php echo $agent['title']
                    ?></td>

                        <td class="table_td"><?php echo $agent['creation_date'] ?></td>
                        <td class="table_td">
                            <?php
                            echo $this->htmlLink($this->url(array('id' => $agent['agent_id'], "controller" => "agent", "action" => "edit"), 'event_extended'), $this->translate("Edit"));
                            ?>
                            |
                            <?php
                            echo $this->htmlLink($this->url(array('id' => $agent['agent_id'], "controller" => "agent", "action" => "delete"), 'event_extended'), $this->translate("Delete"),array('class' =>"smoothbox"));
                            ?></td>			
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />
        <div>
            <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    </div>
<?php else: ?>
    <br/>
    <div class="tip">
        <span><?php echo $this->translate("You have not created any agent yet.")
    ?></span>
    </div>
<?php endif; ?>