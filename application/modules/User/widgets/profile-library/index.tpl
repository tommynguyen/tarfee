<?php if($this -> viewer() -> isSelf($this -> subject())) :?>
<div class="user-library-item-action">
    <span class="ul-item-action-title"><?php echo '<i class="fa fa-plus-square fa-lg"></i>&nbsp;&nbsp;'.$this -> translate('add more');?></span>

    <ul>
		<li>
			<?php echo $this->htmlLink(array(
					'route' => 'video_general',
					'action' => 'create',
					'parent_type' =>'user_library',
					'subject_id' =>  $this->library->getIdentity(),
					'tab' => $this->identity,
				), '<i class="fa fa-video-camera fa-lg"></i>&nbsp;&nbsp;'.$this->translate('Add Video'), array(
				'class' => 'buttonlink'
				)) ;
			?>
		</li>
		<li>
			<?php echo $this->htmlLink(array(
					'route' => 'user_library',
					'action' => 'create-sub-library',
				), '<i class="fa fa-folder-open fa-lg"></i>&nbsp;&nbsp;'.$this->translate('Create Sub Library'), array(
				'class' => 'smoothbox buttonlink'
				)) ;
			?>
		</li>
	</ul>

</div>
<?php endif;?>
	<?php if(count($this -> mainVideos)) :?>
		<ul class="videos_browse tf_library_videos">
	 	<?php foreach ($this->mainVideos as $item): ?>
	        <?php
	        echo $this->partial('_video_listing.tpl', 'user', array(
	            'video' => $item,
	            'library' => $this->library,
	            'main' => true,
	            'tab_id' => $this->identity,
	        ));
	        ?>
		<?php endforeach; ?>
		</ul>
	<?php endif;?>
	<!-- get sub libraries -->
	<?php $subLibraries = $this -> library -> getSubLibrary(); ?>
	<ul class="tf_list_sublibrary">
	<?php foreach($subLibraries as $subLibrary) :
	$totalVideo = $subLibrary -> getTotalVideo();
	$totalVideoView = $subLibrary -> getTotalVideoView();
	$totalVideoComment = $subLibrary -> getTotalVideoComment();
	if($subLibrary -> isViewable()) :?>
	<li class="tf_item_sublibrary">
		<div class="item_sublibrary">
			<div class="item_background" style="background-image: url(<?php echo $subLibrary -> getPhotoUrl();?>)">
				<?php if($this -> viewer() -> isSelf($this -> subject())) :?>
				<div class="avatar-box-hover">
				    <ul class="actions">
						<li>
							<!-- delete link for sub library -->
							<?php echo $this->htmlLink(array(
								'route' => 'user_library',
								'action' => 'delete',
								'id' => $subLibrary -> getIdentity(),
								), '<i class="fa fa-times"></i>', array(
								'class' => 'smoothbox buttonlink'
								)) ;
							?>
						</li>	
						<li>	
							<!-- edit link for sub library -->
							<?php echo $this->htmlLink(array(
								'route' => 'user_library',
								'action' => 'edit',
								'id' => $subLibrary -> getIdentity(),
								), '<i class="fa fa-pencil"></i>', array(
								'class' => 'smoothbox buttonlink'
								)) ;
							?>
						</li>
						<li>
							<!-- create video link for sub library -->
							<?php echo $this->htmlLink(array(
									'route' => 'video_general',
									'action' => 'create',
									'parent_type' =>'user_library',
									'subject_id' =>  $subLibrary->getIdentity(),
									'tab' => $this->identity,
								), '<i class="fa fa-video-camera"></i>', array(
								'class' => 'buttonlink'
							)) ;
							?>
						</li>	
					</ul>
				</div>	
				<?php endif;?>
			</div>
			
			<div class="tf_sublibrary_title">
				<?php echo $subLibrary -> getTitle();?>
			</div>

			<div class="tf_sublibrary_count">
				<div class="count_videos">
					<span>
						<?php echo $totalVideo; ?>
					</span>
					<span>
						<?php echo $this->translate('videos') ?>
					</span>
				</div>

				<div class="count_views_comments">
					<?php echo $this->translate(array('%s view', '%s views', $totalVideoView), $totalVideoView); ?> <br>
					<?php echo $this->translate(array('%s comment', '%s comments', $totalVideoComment), $totalVideoComment); ?>
				</div>
			</div>

			<div class="tf_sublibrary_author">
				<?php echo $this -> translate("by");?> <span><?php echo $subLibrary -> getOwner()?></span>
			</div>
		</div>
		<!-- show video of sub library -->
		<div>
			<!-- get videos of sub libraries -->
			<?php $subVideos = $subLibrary -> getVideos();?>
			<?php if(count($subVideos)):?>
				<ul class="videos_browse">
				 <?php foreach ($subVideos as $item): ?>
			            <?php
			            echo $this->partial('_video_listing.tpl', 'user', array(
			                'video' => $item,
			                'library' => $subLibrary,
			                'tab_id' => $this->identity,
			            ));
			            ?>
				<?php endforeach; ?>
				</ul>
			<?php endif;?>
		</div><!-- show video of sub library -->
	</li><!-- end item sublibrary -->
	<?php endif;?>
	<?php endforeach; ?>
	</ul>

<script type="text/javascript">
	window.addEvent('domready', function(){

		//Chose sub library show video
		$$('.tf_sublibrary_title').addEvent('click',function(){
			var padding = parseInt(this.getParent('.tf_item_sublibrary').getStyle('height')) + 15;
			$$('.tf_list_sublibrary').setStyle('padding-top',padding);

			$$('.tf_item_sublibrary').removeClass('chose_player');
			this.getParent('.tf_item_sublibrary').addClass('chose_player');

		});

		 
		$$('.user-library-item-action').addEvent('outerClick', function(){
	    	if ( this.hasClass('open-submenu') ) {
	    		this.removeClass('open-submenu');	
	    	}
	    });
	
		$$('.user-library-item-action').addEvent('click', function(){
			if ( this.hasClass('open-submenu') ) {
	    		this.removeClass('open-submenu');	
	    	} else {
	    		$$('.open-submenu').removeClass('open-submenu');
	    		this.addClass('open-submenu');
	    	}  
		});
		 
		 $$('.user-library-close-box').addEvent('click', function(){
		 	var parent = this.getParent().getParent().getParent();
			parent.removeClass('open-submenu');				
		});
		
				
  	});	
  var unfavorite_video_lib = function(videoId)
   {
   	   var url = '<?php echo $this -> url(array('action' => 'remove-favorite'), 'video_favorite', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'video_id' : videoId
            },
            'onComplete':function(responseObject)
            {  
            	obj = document.getElementById('favorite_'+ videoId);
                obj.innerHTML = '<a href="javascript:;" onclick="favorite_video('+videoId+')">' + '<i class="fa fa-heart-o"></i>' + '</a>';
            }
        });
        request.send();  
   } 
   var favorite_video = function(videoId)
   {
   	   var url = '<?php echo $this -> url(array('action' => 'add-favorite'), 'video_favorite', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'video_id' : videoId
            },
            'onComplete':function(responseObject)
            {  
            	obj = document.getElementById('favorite_' + videoId);
                obj.innerHTML = '<a href="javascript:;" onclick="unfavorite_video_lib('+videoId+')">' + '<i class="fa fa-heart"></i>' + '</a>';
            }
        });
        request.send();  
   } 
</script>
