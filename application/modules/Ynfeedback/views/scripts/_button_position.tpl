<div class="form-wrapper">
    <div class="form-label" style="height:1px;"></div>
    <div id="wrapper" class="form-element">
        <input id="range" type="range" min="0" max="100" value="<?php echo $this->position?>" onchange="changePos(this)"/>
        <div id="screen">
            <button id="button" onclick="event.preventDefault();" style="top: <?php echo $this->position?>%">button</button>
        </div>
        <input id="position" name="position" value="<?php echo $this->position?>" type="hidden" />
    </div>
</div>

