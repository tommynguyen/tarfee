<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/favicon.ico">

    <title>Tarfee - World's Sport Network</title>

    <!-- Bootstrap core CSS -->
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for bootstrap -->
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/overwrite.css" rel="stylesheet">

	<!-- Custom styles for fontawesome icon -->
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/font-awesome.css" rel="stylesheet">

    <!-- Flexslider -->
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/flexslider.css" rel="stylesheet">

    <!-- prettyPhoto -->	
	<link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/prettyPhoto.css" rel="stylesheet">	

    <!-- animate -->
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/animate.css" rel="stylesheet">
	
    <!-- Custom styles for this template -->
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/style.css" rel="stylesheet">
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/css/custom.css" rel="stylesheet">
	
	<!-- Font for this template -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
	
	<!-- Custom styles for template skin -->
    <link href="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/skins/default/skin.css" rel="stylesheet">
	
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/html5shiv.js"></script>
      <script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/respond.min.js"></script>
    <![endif]-->
    
    <!-- load SE css default -->
	<link href="//<?php echo $_SERVER['HTTP_HOST']?>/application/modules/Core/externals/styles/main.css" rel="stylesheet">
	
	<!-- load SE js default -->
	<script type="text/javascript" src="//<?php echo $_SERVER['HTTP_HOST']?>/externals/mootools/mootools-core-1.4.5-full-compat-nc.js"></script>
	<script type="text/javascript" src="//<?php echo $_SERVER['HTTP_HOST']?>/externals/mootools/mootools-more-1.4.0.1-full-compat-nc.js"></script>
	<script type="text/javascript" src="//<?php echo $_SERVER['HTTP_HOST']?>/externals/chootools/chootools.js"></script>
	<script type="text/javascript" src="//<?php echo $_SERVER['HTTP_HOST']?>/application/modules/Core/externals/scripts/core.js"></script>
	<script type="text/javascript" src="//<?php echo $_SERVER['HTTP_HOST']?>/application/modules/User/externals/scripts/core.js"></script>
	<script type="text/javascript" src="//<?php echo $_SERVER['HTTP_HOST']?>/externals/smoothbox/smoothbox4.js"></script>
	<script type="text/javascript" src="//<?php echo $_SERVER['HTTP_HOST']?>/application/modules/SocialConnect/externals/scripts/core.js"></script>

  </head>

  <body>
	<!-- Start home -->
	<section id="home" class="bgslider-wrapper">
		<div id="animated-bg">
			<div id="animated-bg1" class="bg-slider"></div>
			<div id="animated-bg2" class="bg-slider"></div>
			<div id="animated-bg3" class="bg-slider"></div>
		</div>
		<div class="home-contain">
			<div class="container">
				<div class="row wow fadeInDown" data-wow-delay="0.2s">
					<div class="col-md-12">
						<a href="#home" class="logo"><img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/tarfee-logo.png" class="img-responsive" alt="" /></a>
					</div>
					
					<div class="col-md-6 col-md-offset-3 home-headline">
						<?php echo $this->content()->renderWidget('social-connect.login'); ?>
					</div>
				</div>
				
				<div class="row wow fadeInUp" data-wow-delay="0.2s">
					<div class="col-md-12">
						<div class="start-page">
							<a href="#intro" class="btn-scroll"><?php echo $this -> translate('Learn more')?><br /><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="sparator-line"></div>
					</div>
				</div>				
			</div>
		</div>
	</section>
	<!-- End home -->

	<!-- Start navigation -->
	<header>
		<div class="navbar navbar-default" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#"><img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/tarfee-small2.png" alt="" /></a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li><a href="#home"><?php echo $this -> translate('Home')?></a></li>
						<li><a href="#intro"><?php echo $this -> translate('Tarfee')?></a></li>
						<li><a href="#testimoni"><?php echo $this -> translate('How it Works?')?></a></li>
						<li><a href="#team"><?php echo $this -> translate('Team')?></a></li>
						<li><a href="#blog"><?php echo $this -> translate('Press')?></a></li>		
						<li><a href="#contact"><?php echo $this -> translate('Contact')?></a></li>				
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</header>
	<!-- End navigation -->
	
	<!-- Start introduce -->
	<section id="intro" class="contain colorbg">
		<div class="container">
			<div class="row">
				<div class="col-md-6 wow bounceInDown" data-wow-delay="0.2s">
					<h3 class="headline"><span></span>tarfee</h3>
					<?php echo $this -> translate('
					<p>
					Tarfee is a social network that connects football talents with football clubs, universities and scouts worldwide. We believe that every football talent deserves a chance to be noticed and recognized.
					</p>
					<p>
					Therefore, we help football talents, as well as football schools and non-profit organizations to promote their students and their organizations.
					</p>
					<p>
					For many young talents around the world, football is not just a sport, it is an opportunity to improve their lives through scholarships or contracts with football clubs. Our goal is to make this happen, by offering football talents, clubs, universities and scouts a platform that will connect them worldwide.
					</p>')?>
					<a href="#testimoni" class="btn btn-default btn-lg btn-scroll"><?php echo $this -> translate('How it Works?')?></a>					
				</div>
				<div class="col-md-6 wow bounceInDown" data-wow-delay="0.6s">
					<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/player3.png" class="img-responsive pull-right" alt="" />
				</div>
			</div>
		</div>
	</section>
	<!-- End introduce -->
	


	<!-- Start testimoni -->
	<section id="testimoni" class="contain darkbg">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="divider clearfix"></div>
					<h4 class="heading wow flipInX" data-wow-delay="0.2s"><span>How it Works?</span></h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="testimoni-wrapper">
						<i class="fa fa-quote-left icon-title wow rotateIn" data-wow-delay="0.4s"></i>
						<div class="flexslider wow rotateInDownLeft" data-wow-delay="0.4s">
							<ul class="slides">
								<li>
									<div class="testimoni-box">
										<div class="testimoni-avatar">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/tarfee4.jpg" class="img-responsive" alt="" />
										</div>
										<div class="testimoni-text">
											<h3><span><?php echo $this -> translate('Football')?></span> <?php echo $this -> translate('Talents')?></h3>
											<blockquote>
												<p><?php echo $this -> translate('
												<li>- Upload your best videos to be noticed easily by football scouts, clubs, and universities all over the world</li>
												<li>- Get the best contract or scholarship</li>
												<li>- Apply for tryouts and events organized by football scouts, clubs, and universities</li>
												<li>- Avoid fraud and false offers by checking the ratings and reviews of scouts</li>
												<li>- Connect with other players and professionals to share your experience, learn, and to get advice</li>')?>
												</p>
											</blockquote>
										
										</div>	
									</div>
								</li>
								<li>
									<div class="testimoni-box">
										<div class="testimoni-avatar">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/tarfee1.jpg" class="img-responsive" alt="" />
										</div>
										<div class="testimoni-text">
											<h3><span><?php echo $this -> translate('Football')?></span> <?php echo $this -> translate('Schools, NGOs')?></h3>
											<blockquote>
												<p><?php echo $this -> translate('
												<li>- Promote your organization and students, and show the world what you stand for!</li>
												<li>- Create player profile for each student and upload his/her videos and information</li>
												<li>- Be engaged with your community, kids, and their parents</li>
												<li>- Create events and tryouts and invite people to join</li>
												<li>- Directly send message your followers</li>
												<li>- Avoid fraud by checking the ratings and reviews of scouts</li>')?>
												</p>
											</blockquote>
											
										</div>	
									</div>
								</li>
								<li>
									<div class="testimoni-box">
										<div class="testimoni-avatar">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/tarfee3.jpg" class="img-responsive" alt="" />
										</div>
										<div class="testimoni-text">
											<h3><span><?php echo $this -> translate('Football')?></span> <?php echo $this -> translate('Scouts, Agents')?></h3>
											<blockquote>
												<p><?php echo $this -> translate('
												<li>- Access the best football talents all over the world!</li>
												<li>- Find exactly what you are looking for quickly and easily: You can customize your search by filtering by age, country, position, rating, etc.</li>
												<li>- Keep eye on the players you like to follow their improvements</li>
												<li>- Create events & tryouts and invite players to join and submit their profiles</li>
												<li>- Be connected and engage with other professionals around the world</li>')?>
												</p>
											</blockquote>
					
										</div>	
									</div>
								</li>
								<li>
									<div class="testimoni-box">
										<div class="testimoni-avatar">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/tarfee2.jpg" class="img-responsive" alt="" />
										</div>
										<div class="testimoni-text">
											<h3><span><?php echo $this -> translate('Football')?></span> <?php echo $this -> translate('Clubs, Universities')?></h3>
											<blockquote>
												<p>
												<?php echo $this -> translate('<li>- Promote your club, university, or agency in a football dedicated environment, where all football lovers gather</li>
												<li>- Directly access and message your fans and followers</li>
												<li>- Access the best football talents all over the world</li>
												<li>- Create events & tryouts and invite players to join and submit their profiles</li>
												<li>- Conduct business with football professionals world wide</li>')?>
												
												</p>
											</blockquote>
											
										</div>	
									</div>
								</li>									
							</ul>
						</div>
					</div>
				</div>
			</div>			
		</div>
	</section>
	<!-- End testimoni -->
	

	<!-- Start team -->
	<section id="team" class="contain colorbg">
		<div class="container">
			<div class="row">
				<div class="col-md-10 col-md-offset-1">
					<div class="divider clearfix"></div>
					<h4 class="heading wow flipInX" data-wow-delay="0.2s"><span><?php echo $this -> translate('Our team')?></span></h4>			
					<div class="team-wrapper">
						<div class="team">
							<i class="fa fa-group icon-title centered wow rotateIn" data-wow-delay="0.4s"></i>
							<div class="team-left">
								<div class="team-box wow bounceInDown" data-wow-delay="0.4s">
									<div class="team-profile">
										<h6>Simon</h6>
										<p>CEO</p>
										<!--<a href="#"><i class="fa fa-facebook icon-social"></i></a>-->
										<a href="https://twitter.com/oabushaban" target="_blank"><i class="fa fa-twitter icon-social"></i></a>
										<a href="https://ca.linkedin.com/in/abushaban" target="_blank"><i class="fa fa-linkedin icon-social"></i></a>						
									</div>
									<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/team5.jpg" class="img-responsive" alt="" />
								</div>
								<div class="team-box  wow bounceInDown" data-wow-delay="0.6s">
									<div class="team-profile">
										<h6>Abdallah</h6>
										<p>COO</p>
										<!--<a href="#"><i class="fa fa-facebook icon-social"></i></a>-->
										<a href="https://twitter.com/Abdulla_Sanna" target="_blank"><i class="fa fa-twitter icon-social"></i></a>
										<a href="https://ca.linkedin.com/pub/abdallah-sunna/33/560/a09" target="_blank"><i class="fa fa-linkedin icon-social"></i></a>						
									</div>							
									<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/team3.jpg" class="img-responsive" alt="" />
								</div>
							</div>
							<div class="team-right">
								<div class="team-box wow bounceInDown" data-wow-delay="0.8s">
									<div class="team-profile">
										<h6>Vanessa</h6>
										<p>Relationship Manager</p>
										<!--<a href="#"><i class="fa fa-facebook icon-social"></i></a>-->
										<a href="https://twitter.com/vanecute" target="_blank"><i class="fa fa-twitter icon-social"></i></a>
										<a href="https://ca.linkedin.com/pub/vanessa-jiménez-escudero/12/91b/212" target="_blank"><i class="fa fa-linkedin icon-social"></i></a>						
									</div>						
									<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/team2.jpg" target="_blank" class="img-responsive" alt="" />
								</div>
								<div class="team-box wow bounceInDown" data-wow-delay="1s">
									<div class="team-profile">
										<h6>Tommy</h6>
										<p>CTO</p>
										<!--<a href="#"><i class="fa fa-facebook icon-social"></i></a>-->
										<a href="https://twitter.com/ttommynguyen" target="_blank"><i class="fa fa-twitter icon-social"></i></a>
										<a href="https://vn.linkedin.com/in/ttommynguyen" target="_blank"><i class="fa fa-linkedin icon-social"></i></a>						
									</div>							
									<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/team4.jpg" class="img-responsive" alt="" />
								</div>
							</div>
						</div>
					</div>				
				</div>
			</div>			
		</div>
	</section>
	<!-- End team -->
	
	<!-- Start blog -->
	<section id="blog" class="contain colorbg">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="divider clearfix"></div>
					<h4 class="heading wow flipInX" data-wow-delay="0.2s"><span>Press</span></h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="blog-wrapper">
						<i class="fa fa-pencil icon-title wow rotateIn" data-wow-delay="0.4s"></i>
						<div class="flexslider wow rotateInDownLeft" data-wow-delay="0.4s">
							<ul class="slides">
								<li>
									<div class="blog-box">
										<div class="blog-thumbls">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/press-2.jpg" class="img-responsive" alt="" />
										</div>
										<div class="artcle">
											<div class="article-head">
												<div class="date-post">
													<span class="date">18</span>
													<span class="mo-year">03-2015</span>
												</div>
												<h5><?php echo $this -> translate('Entrepreneurship Carleton-Style: TIM Program Leads the Way')?></h5>

											</div>
											<div class="article-post">
												<p><?php echo $this -> translate('Carleton grad student Osama Abushaban stood before a panel of four judges, all of them experienced entrepreneurs. Dressed in smart casual, he didn’t realize how nervous he was and that he had been holding his breath for quite a while.')?>
												
												</p>
												<a href="https://carleton.ca/our-stories/stories/entrepreneurship-carleton-style/" target="_blank"><?php echo $this -> translate('Read more...')?></a>
											</div>
										</div>	
									</div>
								</li>
								<li>
									<div class="blog-box">
										<div class="blog-thumbls">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/press-3.jpg" class="img-responsive" alt="" />
										</div>
										<div class="artcle">
											<div class="article-head">
												<div class="date-post">
													<span class="date">12</span>
													<span class="mo-year">02-2015</span>
												</div>
												<h5><?php echo $this -> translate('“Go Global, Hire Local” Matches Technology Venture Teams with Talented Professionals')?></h5>
								
											</div>
											<div class="article-post">
												<p>
													<?php echo $this -> translate('Go Global, Hire Local matches young technology entrepreneurs who wish to define and exploit global opportunities with international professionals who possess appropriate skills.')?>
												
												</p>
												<a href="http://newsroom.carleton.ca/2015/02/12/go-global-hire-local-matches-technology-venture-teams-talented-professionals-carleton-university/" target="_blank"><?php echo $this -> translate('Read more...')?></a>
											</div>
										</div>	
									</div>
								</li>
								<li>
									<div class="blog-box">
										<div class="blog-thumbls">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/press-4.jpg" class="img-responsive" alt="" />
										</div>
										<div class="artcle">
											<div class="article-head">
												<div class="date-post">
													<span class="date">24</span>
													<span class="mo-year">12-2014</span>
												</div>
												<h5><?php echo $this -> translate('Carleton Students Present their Startups to Mr. Wes Nicol')?></h5>
												
											</div>
											<div class="article-post">
												<p>
												<?php echo $this -> translate('For student entrepreneurs at Carleton University, there are few people who serve as a greater inspiration than successful businessman and generous philanthropist Wes Nicol.')?>
												</p>
												<a href="http://newsroom.carleton.ca/2014/12/24/carleton-students-present-startups-philanthropist-entrepreneur-wes-nicol/" target="_blank">Read more...</a>
											</div>
										</div>	
									</div>
								</li>
								<li>
									<div class="blog-box">
										<div class="blog-thumbls">
											<img src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/img/press-5.jpg" class="img-responsive" alt="" />
										</div>
										<div class="artcle">
											<div class="article-head">
												<div class="date-post">
													<span class="date">10</span>
													<span class="mo-year">12-2014</span>
												</div>
												<h5><?php echo $this -> translate('Carleton Technology Entrepreneurs Show Off Their Startups for Senior Policy Advisers')?>
													</h5>

											</div>
											<div class="article-post">
												<p><?php echo $this -> translate('									Student entrepreneurs in Carleton’s Technology Innovation Management program had a chance to show off their startups during an event attended by senior policy advisers with the Ministry of Research and Innovation.')?>
												</p>
												<a href="http://newsroom.carleton.ca/2014/12/10/carleton-technology-entrepreneurs-show-off-startups-senior-policy-advisers/" target="_blank"><?php echo $this -> translate('Read more...')?></a>
											</div>
										</div>	
									</div>
								</li>								
							</ul>
						</div>
					</div>
				</div>
			</div>			
		</div>
	</section>
	<!-- End blog -->



	<!-- Start contact -->
	<section id="contact" class="contain colorbg">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="divider clearfix"></div>
					<h4 class="heading wow flipInX" data-wow-delay="0.2s"><span>Contact us</span></h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="contact-wrapper">
						<i class="fa fa-envelope icon-title wow rotateIn" data-wow-delay="0.4s"></i>
						<div class="contact-body wow rotateInDownLeft" data-wow-delay="0.4s">
							<p>
							<strong><?php echo $this -> translate('Address :')?></strong><?php echo $this -> translate('102 St. Patricks Building, 1125 Colonel By Dr, Ottawa, ON K1S 5B6')?> <br />
							<strong><?php echo $this -> translate('Phone :')?></strong> <?php echo $this -> translate('+1 (647) 500-0800 - ')?><strong><?php echo $this -> translate('Email :')?></strong> hello@tarfee.com
							</p>
						</div>
					</div>
					<div class="divider pull-left"></div>
				</div>
			</div>
		</div>	
	</section>
	<!-- End contact -->
	
	<!-- Start footer -->
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<a href="#home" class="totop wow rotateIn btn-scroll" data-wow-delay="0.4s"><i class="fa fa-chevron-up"></i></a>
					<a href="https://www.facebook.com/TarfeeInc" target="_blank" class="social-network wow bounceInDown" data-wow-delay="0.2s"><i style="line-height: 2" class="fa fa-facebook"></i></a>
					<a href="https://twitter.com/tarfeeinc" target="_blank" class="social-network wow bounceInDown" data-wow-delay="0.4s"><i style="line-height: 2" class="fa fa-twitter"></i></a>
					<!-- <a href="#" class="social-network wow bounceInDown" data-wow-delay="0.6s"><i class="fa fa-google-plus"></i></a> -->
					<!-- <a href="#" class="social-network wow bounceInDown" data-wow-delay="0.8s"><i class="fa fa-linkedin"></i></a> -->
					<!-- <a href="#" class="social-network wow bounceInDown" data-wow-delay="1s"><i class="fa fa-pinterest"></i></a> -->
					<!-- <a href="#" class="social-network wow bounceInDown" data-wow-delay="1.2s"><i class="fa fa-dribbble"></i></a> -->
				</div>
			</div>
		</div>
		<div class="subfooter">
			<p class="copyrigh">2015 &copy; Copyright <a href="www.tarfee.com">Tarfee Inc.</a>. All rights Reserved.</p>
			  <span class="ynresponsive_languges">
			     <?php if( 1 !== count($this->languageNameList) ):?>
			        <form id="form_language" method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
			            <?php $selectedLanguage = $this->translate()->getLocale() ?>
			            <div class="language-dropdown render-once" data-view="LanguageDropdown" data-hash="LanguageDropdown">
			            	<i class="fa fa-globe"></i>
			          <span><?php echo strtoupper(substr($selectedLanguage, 0, 2))?></span>
			            	<ul>
			            		<?php foreach($this->languageNameList as $key => $language):?>
			            		<li>
			            			<a onclick="changeLanguages('<?php echo $key?>')" data-locale="<?php echo $key?>" class="locale old-app"><?php echo strtoupper(substr($key,0, 2))?></a>
			            		</li>
			            		<?php endforeach;?>
			            	</ul>
			
			            </div>
			            <?php echo $this->formHidden('language', $selectedLanguage);?>
			            <?php echo $this->formHidden('return', $this->url()) ?>
			        </form>
			        <script type="text/javascript">
			        var changeLanguages = function(lang)
			        {
			        	$('#language').val(lang);
			        	$('#form_language').submit();
			        }
			        </script>
			     <?php endif; ?>
			  </span>
		</div>
	</footer>
	<!-- End footer -->
	
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/jquery.min.js"></script>
    <script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/bootstrap.min.js"></script>

	<!-- Fixed navigation -->
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/navigation/jquery.smooth-scroll.js"></script>	
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/navigation/navbar.js"></script>	
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/navigation/waypoints.min.js"></script>
	
	<!-- WOW JavaScript -->
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/wow.min.js"></script>
	
	<!-- JavaScript bgSlider slider -->
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/bgslider/bgSlider.js"></script>		

	<!-- Flexslider -->
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/flexslider/jquery.flexslider.js"></script>
    <script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/flexslider/setting.js"></script>

	<!-- prettyPhoto -->
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/prettyPhoto/jquery.prettyPhoto.js"></script>
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/prettyPhoto/setting.js"></script>

	<!-- Contact validation js -->
    <script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/validation.js"></script>
	
	<!-- Custom JavaScript -->
	<script src="//<?php echo $_SERVER['HTTP_HOST']?>/landing/new_landing/js/custom.js"></script>
	
  </body>
</html>