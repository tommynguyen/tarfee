<?php
    $this->headScript()
        ->appendFile($this->baseUrl() . '/application/modules/Ynfeedback/externals/scripts/jquery-1.7.1.min.js')
        ->appendFile($this->baseUrl() . '/application/modules/Ynfeedback/externals/scripts/jquery.flexslider.js');
    $this->headLink()
        ->appendStylesheet($this->baseUrl() . '/application/modules/Ynfeedback/externals/styles/flexslider.css');
?>

<div id="layout_ynfeedback_highlight_feedback">
<?php if ($this->paginator -> getTotalItemCount() > 0) :?>
	<div id="ynfeedback-highlight-list" class="ynfeedback-highlight-list flexslider">
		<ul class="slides">
        <?php $i_hightlight = 0; ?>
		<?php foreach ($this->paginator as $feedback):?>
            <?php if ($i_hightlight % 4 == 0) echo '<li>'; ?>
				<div class="ynfeedback-highlight-item">
					<div class="ynfeedback-highlight-title">
						<a href="<?php echo $feedback->getHref();?>"><?php echo $feedback->title; ?></a>
					</div>
					<div class="ynfeedback-highlight-content">
                        <span class="ynfeedback-highlight-status" style="background-color: <?php echo $feedback->getStatusColor(); ?>"><?php echo $feedback -> getStatus();?></span>
                        <span class="ynfeedback-listing-category"><i class="fa fa-folder-open"></i> <?php echo $this->htmlLink($feedback->getCategory()->getHref(), $feedback->getCategory()->getTitle());?></span>

					</div>
				</div>
                <?php $i_hightlight++; ?>
            <?php if ($i_hightlight % 4 == 0) echo '</li>'; ?>
		<?php endforeach;?>

        <?php if ($i_hightlight % 4 != 0) echo '</li>'; ?>
		</ul>
	</div>
<?php endif;?>
</div>

<script type="text/javascript">
    jQuery.noConflict();
    (function($) { 

        var ycontainer = $('#ynfeedback-highlight-list');        
        function getGridSize() {
        	if (ycontainer.innerWidth() > 600) {
        		return 2;
        	} else {        		
        		return 1;
        	}
        }

        $(window).load(function() {
            $('#ynfeedback-highlight-list').flexslider({
                animation: "slide",
                controlNav: false,
                prevText: "",
                nextText: "",
                itemWidth: 400,
                itemMargin: 0,
                minItems: 1,
                maxItems: getGridSize(),
            });
        });
    })(jQuery);
</script>