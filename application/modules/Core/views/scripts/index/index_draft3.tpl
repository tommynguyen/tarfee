<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Tarfee | August (2015)</title>
	<!-- favicon -->
	<!-- Bootstrap core CSS -->
	<link href="https://www.tarfee.com/landing/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap theme -->
	<link href="https://www.tarfee.com/landing/assets/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	<!-- vegas bg -->
	<link href="https://www.tarfee.com/landing/assets/js/vegas/jquery.vegas.min.css" rel="stylesheet">
	<!-- owl carousel css -->
	<link href="https://www.tarfee.com/landing/assets/js/owl-carousel/owl.carousel.css" rel="stylesheet">
	<link href="https://www.tarfee.com/landing/assets/js/owl-carousel/owl.theme.css" rel="stylesheet">
	<link href="https://www.tarfee.com/landing/assets/js/owl-carousel/owl.transitions.css" rel="stylesheet">
	<!-- intro animations -->
	<link href="https://www.tarfee.com/landing/assets/js/wow/animate.css" rel="stylesheet">
	<!-- font awesome -->
	<link href="https://www.tarfee.com/landing/assets/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<!-- lightbox -->
	<link href="https://www.tarfee.com/landing/assets/js/lightbox/css/lightbox.css" rel="stylesheet">
	
	<!-- styles for this template -->
	<link href="https://www.tarfee.com/landing/assets/css/styles.css" rel="stylesheet">
	
	<!-- place your extra custom styles in this file -->
	<link href="https://www.tarfee.com/landing/assets/css/custom.css" rel="stylesheet">
	
	<!-- load SE css default -->
	<link href="https://www.tarfee.com/application/modules/Core/externals/styles/main.css" rel="stylesheet">
	
	<!-- load SE js default -->
	<script type="text/javascript" src="https://www.tarfee.com/externals/mootools/mootools-core-1.4.5-full-compat-nc.js"></script>
	<script type="text/javascript" src="https://www.tarfee.com/externals/mootools/mootools-more-1.4.0.1-full-compat-nc.js"></script>
	<script type="text/javascript" src="https://www.tarfee.com/externals/chootools/chootools.js"></script>
	<script type="text/javascript" src="https://www.tarfee.com/application/modules/Core/externals/scripts/core.js"></script>
	<script type="text/javascript" src="https://www.tarfee.com/application/modules/User/externals/scripts/core.js"></script>
	<script type="text/javascript" src="https://www.tarfee.com/externals/smoothbox/smoothbox4.js"></script>
	<script type="text/javascript" src="https://www.tarfee.com/application/modules/SocialConnect/externals/scripts/core.js"></script>
  </head>

  <!-- data-default-background-img attr is required for sections (.section-wrapper) below that have no custom background defined, and when background change is disabled for mobile -->
  <!-- data-overlay: "true" - semi-transparent black overlay on top of bg enabled, "false" - disabled -->
  <!-- data-overlay-opacity: set the opacity/transparency of the black overlay -->
  <body data-default-background-img="https://www.tarfee.com/landing/assets/images/other_images/bg5.jpg" data-overlay="true" data-overlay-opacity="0.35">

    <!-- Outer Container -->
    <div id="outer-container">

      <!-- Left Sidebar -->
      <section id="left-sidebar">
        
        <!-- ==================================================================
        LOGO 
        ==================================================================  -->
        <!-- change the img src to your logo -->
        <div class="logo">
          <a href="#intro" class="link-scroll"><img src="https://www.tarfee.com/photos/tarfee-logo.png" alt="Tarfee"></a>
        </div><!-- .logo -->
        <!-- ==================== END: LOGO ==================== -->

        <!-- Menu Icon for smaller viewports -->
        <div id="mobile-menu-icon" class="visible-xs" onClick="toggle_main_menu();"><span class="glyphicon glyphicon-th"></span></div>

        <!-- ==================================================================
        MAIN MENU 
        ==================================================================  -->
        <!-- Each menu item links to a section (<article class="section-wrapper...") in the main content below.
             - set each <li> id to this format: "menu-item-[the id of the .section-wrapper to link to]".
             - set href of each <a> to the id of the .section-wrapper to link to -->
        <ul id="main-menu">
          <li id="menu-item-text" class="menu-item scroll"><a href="#text">Tarfee</a></li>
          <li id="menu-item-carousel" class="menu-item scroll"><a href="#carousel">How it works?</a></li>
          <li id="menu-item-carousel" class="menu-item scroll"><a href="#carousel2">Press</a></li>
          <li id="menu-item-featured" class="menu-item scroll"><a href="#featured">Press</a></li>
          <li id="menu-item-tabs" class="menu-item scroll"><a href="#tabs">About</a></li>

          <!-- to include a link which doesn't scroll to a section inside the page, remove the .scroll class from the <li> - example below -->
          <!-- <li id="menu-item-alt-page" class="menu-item"><a href="http://www.link.com">Outer Link</a></li> -->
        </ul><!-- #main-menu -->
        <!-- ==================== END: MAIN MENU ==================== -->

      </section><!-- #left-sidebar -->
      <!-- end: Left Sidebar -->

      <!-- ==================================================================
      MAIN CONTENT
      ==================================================================  -->
      <!-- all the website sections are contained in the <article> tag with class ".section-wrapper" and a unique id.
           - to edit / remove / create website sections, you have to handle the content contained in these <article> tags -->
      <section id="main-content" class="clearfix">
        
        <!-- ==================== SECTION TYPE: Intro Text ==================== -->
        <!-- This type of section should contain heading and intro paragraph. It can also include links to other sections -->
        <!-- FOR EACH SECTION <article class="section-wrapper".. :
             - remember to use a unique id, 
             - (optional) set a "data-custom-background-img" attribute with a link to custom background image which will be used when the viewer scrolls to this section -->
        <article id="intro" class="section-wrapper clearfix" data-custom-background-img="https://www.tarfee.com/photos/bg1.jpg">
          <div class="content-wrapper clearfix wow fadeInDown" data-wow-delay="0.3s">
            <div class="col-sm-10 col-md-9 pull-right">

              <!-- Start: Section content to edit -->

                <!-- <p> text in .feature-text is larger  -->
                <p><br><br></p>
                <section class="feature-text">
                  <?php echo $this->content()->renderWidget('social-connect.login'); ?>
                  <!-- to add scrolling effects to links linking to same page section, add .link-scroll class -->
                </section>

              <!-- End: Section content to edit -->

            </div><!-- .col-sm-10 -->
          </div><!-- .content-wrapper -->
        </article><!-- .section-wrapper -->
        <!-- ==================== END: SECTION TYPE: Intro Text ==================== -->

        <!-- ==================== SECTION TYPE: Text / Generic ==================== -->
        <!-- This type of section should contain generic content (text, image, etc) -->
        <!-- FOR EACH SECTION <article class="section-wrapper".. :
             - remember to use a unique id, 
             - (optional) set a "data-custom-background-img" attribute with a link to custom background image which will be used when the viewer scrolls to this section -->
        <article id="text" class="section-wrapper clearfix" data-custom-background-img="https://www.tarfee.com/photos/bg2.jpg">
          <div class="content-wrapper clearfix">
            <div class="col-sm-10 col-md-9 pull-right">

              <!-- Start: Section content to edit -->

                <h1 class="section-title">Tarfee</h1>

                <p class="feature-paragraph">Tarfee is a social network that connects football talents with football clubs, universities and scouts worldwide. We believe that every football talent deserves a chance to be noticed and recognized.</p>
                <h4>Therefore, we help football talents, as well as football schools and non-profit organizations to promote their students and their organizations.</h4>
                <p>For many young talents around the world, football is not just a sport, it is an opportunity to improve their lives through scholarships or contracts with football clubs. Our goal is to make this happen, by offering football talents, clubs, universities and scouts a platform that will connect them worldwide.</p>
                <!-- to make a popup/modal link add an onClick function call to the function: 
                     - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                
                
                <!-- content which will be shown in the popup/modal when clicking on the above link 
                     - it is important to set a unique id -->
                <div class="content-to-populate-in-modal" id="modal-content-1">
                  <h1>Lorem Ipsum</h1>
                  <p><img data-img-src="https://www.tarfee.com/landing/assets/images/other_images/transp-image4.png" class="lazy rounded_border hover_effect pull-right" alt="Lorem Ipsum">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in urna vel ante mollis tincidunt. Donec nec magna condimentum tortor laoreet lobortis. Nunc accumsan sapien eu tortor fringilla, et condimentum metus pellentesque. Maecenas rhoncus tortor nec mi congue aliquet. Integer eu turpis scelerisque, iaculis magna non, tempor sem. Quisque consectetur nisi eu felis euismod, sit amet faucibus justo molestie. Ut pretium sapien dui, id facilisis metus interdum pharetra.</p>
                  <p>Tarfee is a social network that connects football talents with football clubs, universities and scouts worldwide. We believe that every football talent deserves a chance to be noticed and recognized.</p>
                  <h3>Duis dictum lorem metus, vitae dapibus</h3>
                  <p><img data-img-src="https://www.tarfee.com/landing/assets/images/other_images/transp-image3.png" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum">Sed auctor urna mi, sed fringilla felis vulputate nec. Cras eu nibh id quam pretium convallis. Donec ante enim, placerat nec sagittis sit amet, tempor in velit. Maecenas ultricies commodo lacus id porta. Suspendisse eros elit, lacinia vitae erat vitae, egestas accumsan nunc. Maecenas dictum odio ipsum, non volutpat erat consequat tempor. Pellentesque sed malesuada turpis. Quisque eget lacus sit amet dui feugiat molestie sit amet eget purus. Morbi eget neque nec lectus tempus sagittis nec at ante. </p>
                  <p>Etiam scelerisque lacus tempor, rhoncus diam vel, gravida felis. Fusce tristique sem et leo aliquam vulputate. Ut eget orci in sapien commodo fringilla. Ut luctus faucibus viverra. Quisque ut ante eget libero rutrum imperdiet. Morbi in diam bibendum, venenatis arcu sed, consequat libero. Nulla imperdiet, ipsum et adipiscing pulvinar, nibh metus porta mauris, et vestibulum dolor sapien sit amet justo. In dignissim leo nec erat faucibus volutpat.</p>
                </div><!-- #modal-content-1 -->

              <!-- End: Section content to edit --> 

            </div><!-- .col-sm-10 -->
          </div><!-- .content-wrapper -->
        </article><!-- .section-wrapper -->
        <!-- ==================== END: SECTION TYPE: Text / Generic ==================== -->

        <!-- ==================== SECTION TYPE: Carousel ==================== -->
        <!-- This type of section should contain slides with an icon, text, and an optional button -->
        <!-- FOR EACH SECTION <article class="section-wrapper".. :
             - remember to use a unique id, 
             - (optional) set a "data-custom-background-img" attribute with a link to custom background image which will be used when the viewer scrolls to this section -->
        <article id="carousel" class="section-wrapper clearfix" data-custom-background-img="https://www.tarfee.com/photos/bg5.jpg">
          <div class="content-wrapper clearfix">

            <!-- Start: Section content to edit -->

              <!-- the carousel is initialised in the initialise-functions.js file, using the id of the container .carousel below -->
              <div id="features-carousel" class="carousel slide with-title-indicators max-height" data-height-percent="70" data-ride="carousel">
                
                <!-- Indicators - slide navigation -->
                <!-- each slide should have data-slide-to set to the index of the slide (starting from 0), and a short title link for the slide -->
                <ol class="carousel-indicators title-indicators">
                  <li data-target="#features-carousel" data-slide-to="0" class="active">Football Talents</li>
                  <li data-target="#features-carousel" data-slide-to="1">Football Schools, NGOs</li>
                  <li data-target="#features-carousel" data-slide-to="2">Football Scouts/Agents</li>
                  <li data-target="#features-carousel" data-slide-to="3">Football Clubs, Universities</li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">

                  <!-- CAROUSEL SLIDE 1 -->
                  <div class="item active">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <p><br><br><br><br></p>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <!-- <span class="icon glyphicon glyphicon-record"></span> -->
                      <h2 class="title">Football Talents</h2>
                      <p>Upload your best videos to be noticed easily by football scouts, clubs, and universities all over the world</p>
                      <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                      <p><a href="" onclick="populate_and_open_modal(event, 'modal-content-2');" class="btn btn-outline-inverse btn-sm">read more</a></p>

                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                      <div class="content-to-populate-in-modal" id="modal-content-2">
                        <h1>Football Talents</h1>
                        <p><img data-img-src="https://www.tarfee.com/photos/t2.v3.png" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum"><li>Upload your best videos to be noticed easily by football scouts, clubs, and universities all over the world</li>
							<li>Get the best contract or scholarship</li>
							<li>Apply for tryouts and events organized by football scouts, clubs, and universities</li>
							<li>Avoid fraud and false offers by checking the ratings and reviews of scouts</li>
							<li>Connect with other players and professionals to share your experience, learn, and to get advice</li></p>
                      </div><!-- #modal-content-2 -->
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: CAROUSEL SLIDE 1 -->

                  <!-- CAROUSEL SLIDE 2 -->
                  <div class="item">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <p><br><br><br><br></p>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <!-- <span class="icon glyphicon glyphicon-stats"></span> -->
                      <h2 class="title">Football Schools, NGOs</h2>
                      <p>Promote your organization and students, and show the world what you stand for!</p>
                      <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                      <p><a href="" onclick="populate_and_open_modal(event, 'modal-content-3');" class="btn btn-outline-inverse btn-sm">read more</a></p>

                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                      <div class="content-to-populate-in-modal" id="modal-content-3">
                        <h1>Football Schools, NGOs</h1>
                        <p><img data-img-src="https://www.tarfee.com/photos/t3.v3.png" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum"><li>Promote your organization and students, and show the world what you stand for!</li>
                        <li>Create player profile for each student and upload his/her videos and information</li>
                        <li>Be engaged with your community, kids, and their parents</li>
                        <li>Create events and tryouts and invite people to join</li>
                        <li>Directly send and receive message from followers</li>
                        <li>Share experiences, give and get advice</li>
                        <li>Ask for recommendations from your followers</li>
                        <li>Avoid fraud by checking the ratings and reviews of scouts</li></p>
                      </div><!-- #modal-content-3 -->
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: CAROUSEL SLIDE 2 -->

                  <!-- CAROUSEL SLIDE 3 -->
                  <div class="item">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <p><br><br><br><br></p>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <!-- <span class="icon glyphicon glyphicon-lock"></span> -->
                      <h2 class="title">Football Scouts/Agents</h2>
                      <p>Access the best football talents all over the world!</p>
                      <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                      <p><a href="" onclick="populate_and_open_modal(event, 'modal-content-4');" class="btn btn-outline-inverse btn-sm">read more</a></p>

                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                      <div class="content-to-populate-in-modal" id="modal-content-4">
                        <h1>Football Scouts/Agents</h1>
                        <p><img data-img-src="https://www.tarfee.com/photos/t4.v3.png" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum"><li>Access the best football talents all over the world!</li>
                        <li>Find exactly what you are looking for quickly and easily: You can customize your search by filtering by age, country, position, rating, etc.</li>
                        <li>Keep eye on the players you like to follow their improvements</li>
                        <li>Direct messaging: Just find the players you are looking for and send them a message</li>
                        <li>Create events & tryouts and invite players to join and submit their profiles</li>
                        <li>Be connected and engage with other professionals around the world</li></p>
                      </div><!-- #modal-content-4 -->
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: CAROUSEL SLIDE 3 -->

                  <!-- CAROUSEL SLIDE 4 -->
                  <div class="item">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <p><br><br><br><br></p>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <!-- <span class="icon glyphicon glyphicon-file"></span> -->
                      <h2 class="title">Football Clubs, Universities</h2>
                      <p>Promote your club or university in a football dedicated environment, where all football lovers gather, and Access the best football talents all over the world!</p>
                      <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                      <p><a href="" onclick="populate_and_open_modal(event, 'modal-content-5');" class="btn btn-outline-inverse btn-sm">read more</a></p>

                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                      <div class="content-to-populate-in-modal" id="modal-content-5">
                        <h1>Football Clubs, Universities</h1>
                        <p><img data-img-src="https://www.tarfee.com/photos/t5.v3.png" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum"><li>Promote your club, university, or agency in a football dedicated environment, where all football lovers gather</li>
                        <li>Directly access and message your fans and followers</li>
                        <li>Access the best football talents all over the world</li>
                        <li>Create events & tryouts and invite players to join and submit their profiles</li>
                        <li>Conduct business with football professionals world wide</li>
                        <li>Engage with the online football community and advice young talents on how to improve their skills, pursue opportunities and become stars</li></p>
                      </div><!-- #modal-content-5 -->
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- CAROUSEL SLIDE 4 -->

                </div><!-- .carousel-inner -->

                <!-- Controls -->
                <!-- the left/right arrows to move from one slide to the other -->
                <a class="left carousel-control" href="#features-carousel" data-slide="prev"></a>
                <a class="right carousel-control" href="#features-carousel" data-slide="next"></a>

              </div><!-- #about-carousel -->

            <!-- End: Section content to edit -->

          </div><!-- .content-wrapper -->
        </article><!-- .section-wrapper -->
        <!-- ==================== END: SECTION TYPE: Carousel ==================== -->
        
                <!-- ==================== SECTION TYPE: Carousel2 ==================== -->
        <!-- This type of section should contain slides with an icon, text, and an optional button -->
        <!-- FOR EACH SECTION <article class="section-wrapper".. :
             - remember to use a unique id, 
             - (optional) set a "data-custom-background-img" attribute with a link to custom background image which will be used when the viewer scrolls to this section -->
        <article id="carousel2" class="section-wrapper clearfix" data-custom-background-img="https://www.tarfee.com/photos/bg3.jpg">
          <div class="content-wrapper clearfix">

            <!-- Start: Section content to edit -->

              <!-- the carousel is initialised in the initialise-functions.js file, using the id of the container .carousel below -->
              <div id="features-carousel2" class="carousel slide with-title-indicators max-height" data-height-percent="70" data-ride="carousel">
                
                <!-- Indicators - slide navigation -->
                <!-- each slide should have data-slide-to set to the index of the slide (starting from 0), and a short title link for the slide -->
                <ol class="carousel-indicators title-indicators">
                  <li data-target="#features-carousel2" data-slide-to="0" class="active">1</li>
                  <li data-target="#features-carousel2" data-slide-to="1">2</li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">

                  <!-- CAROUSEL SLIDE 1 -->
                  <div class="item active">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <p><br><br></p>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <!-- <span class="icon glyphicon glyphicon-record"></span> -->
                     <!-- feature columns -->
                <section class="feature-columns row clearfix">

                  <!-- FEATURED ITEM 1 -->
                  <article class="feature-col col-md-6">
                    <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]', '[optional - selector to scroll to inside popup]', '[optional - add class to the modal container]') -->
                    <!-- if you don't want to link the featured item, replace the below <a> with a <div class="thumbnail"> -->
                    <a target="_blank" href="https://gradstudents.carleton.ca/2015/entrepreneurship-carleton-style-tim-program-leads-the-way/";" class="thumbnail linked">
                      <div class="image-container">
                        <img data-img-src="https://www.tarfee.com/photos/press-1.jpg" class="lazy item-thumbnail" alt="Lorem Ipsum">
                      </div>
                      <div class="caption">
                        <h5>Entrepreneurship Carleton-Style: TIM Program Leads the Way</h5>
                      </div><!-- .caption -->
                    </a><!-- .thumbnail -->

                    <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                                         <!-- end: image slider inside popup --> 
                  

                  </article>
                  <!-- END: FEATURED ITEM 1 -->

                  <!-- FEATURED ITEM 2 -->
                  <article class="feature-col col-md-6">
                    <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]', '[optional - selector to scroll to inside popup]', '[optional - add class to the modal container]') -->
                    <!-- if you don't want to link the featured item, replace the below <a> with a <div class="thumbnail"> -->
                    <a target="_blank" href="http://newsroom.carleton.ca/2015/02/12/go-global-hire-local-matches-technology-venture-teams-talented-professionals-carleton-university/" ;" class="thumbnail linked">
                      <div class="image-container">
                        <img data-img-src="https://www.tarfee.com/photos/press-2.jpg" class="lazy item-thumbnail" alt="Lorem Ipsum">
                      </div>
                      <div class="caption">
                        <h5>“Go Global, Hire Local” at Carleton University</h5>
                      </div><!-- .caption -->
                    </a><!-- .thumbnail -->

                    <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                   

                  </article>
                  <!-- END: FEATURED ITEM 2 -->

			</section><!-- end: .feature-columns -->
                  
                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                                           <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: CAROUSEL SLIDE 1 -->

                  <!-- CAROUSEL SLIDE 2 -->
                  <div class="item">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <p><br><br></p>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <!-- <span class="icon glyphicon glyphicon-stats"></span> -->
                      <!-- feature columns -->
                <section class="feature-columns row clearfix">
                
                <!-- FEATURED ITEM 3 -->
                  <article class="feature-col col-md-6">
                    <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]', '[optional - selector to scroll to inside popup]', '[optional - add class to the modal container]') -->
                    <!-- if you don't want to link the featured item, replace the below <a> with a <div class="thumbnail"> -->
                    <a target="_blank" href="http://newsroom.carleton.ca/2014/12/24/carleton-students-present-startups-philanthropist-entrepreneur-wes-nicol/" class="thumbnail linked">
                      <div class="image-container">
                        <img data-img-src="https://www.tarfee.com/photos/press-3.jpg" class="lazy item-thumbnail" alt="Lorem Ipsum">
                      </div>
                      <div class="caption">
                        <h5>Carleton Students Present their Startups to Philanthropist and Entrepreneur Wes Nicol</h5>
                      </div><!-- .caption -->
                    </a><!-- .thumbnail -->

                    <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                    
                  </article>
                  <!-- END: FEATURED ITEM 3 -->
                  
                  

                </section><!-- end: .feature-columns -->

                
                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                     
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: CAROUSEL SLIDE 2 -->



                </div><!-- .carousel-inner -->

                <!-- Controls -->
                <!-- the left/right arrows to move from one slide to the other -->
                <a class="left carousel-control" href="#features-carousel2" data-slide="prev"></a>
                <a class="right carousel-control" href="#features-carousel2" data-slide="next"></a>

              </div><!-- #about-carousel -->

            <!-- End: Section content to edit -->

          </div><!-- .content-wrapper -->
        </article><!-- .section-wrapper -->
        <!-- ==================== END: SECTION TYPE: Carousel2 ==================== -->



        <!-- ==================== SECTION TYPE: Featured Items ==================== -->
        <!-- This type of section should contain a max. of 3 items each consisting of an image and text. Each item may be linked -->
        <!-- FOR EACH SECTION <article class="section-wrapper".. :
             - remember to use a unique id, 
             - (optional) set a "data-custom-background-img" attribute with a link to custom background image which will be used when the viewer scrolls to this section -->
        <article id="featured" class="section-wrapper clearfix" data-custom-background-img="https://www.tarfee.com/photos/bg3.jpg">
          <div class="content-wrapper clearfix">
              <!-- Start: Section content to edit -->

                <h1 class="section-title">Press</h1>
              
                <!-- feature columns -->
                <div id="featured-carousel" class="carousel slide with-title-indicators max-height" data-height-percent="70" data-ride="carousel">
				<!-- Wrapper for slides -->
                <div class="carousel-inner">
                	
                <!-- FEATURED CAROUSEL SLIDE 1 -->
                  <div class="item active">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <a target="_blank" href="https://gradstudents.carleton.ca/2015/entrepreneurship-carleton-style-tim-program-leads-the-way/" ;"="">
                      	<img src="https://www.tarfee.com/photos/press-1.jpg" class="icon" alt="Lorem Ipsum">
                      <a/>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <a target="_blank" href="https://gradstudents.carleton.ca/2015/entrepreneurship-carleton-style-tim-program-leads-the-way/" ;"="">
                      	<h2 class="title">Entrepreneurship Carleton-Style: TIM Program Leads the Way</h2>
                      <a/>
                      <p>Upload your best videos to be noticed easily by football scouts, clubs, and universities all over the world</p>
                      <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                      <p><a href="" onclick="populate_and_open_modal(event, 'modal-content-6');" class="btn btn-outline-inverse btn-sm">read more</a></p>

                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                      <div class="content-to-populate-in-modal" id="modal-content-6">
                        <h1>Entrepreneurship Carleton-Style: TIM Program Leads the Way</h1>
                        <p><img data-img-src="https://www.tarfee.com/photos/press-1.jpg" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum"><li>Upload your best videos to be noticed easily by football scouts, clubs, and universities all over the world</li>
							<li>Get the best contract or scholarship</li>
							<li>Apply for tryouts and events organized by football scouts, clubs, and universities</li>
							<li>Avoid fraud and false offers by checking the ratings and reviews of scouts</li>
							<li>Connect with other players and professionals to share your experience, learn, and to get advice</li></p>
                      </div><!-- #modal-content-2 -->
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: FEATURED CAROUSEL SLIDE 1 -->
				  <!-- FEATURED CAROUSEL SLIDE 2 -->
                  <div class="item">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <a target="_blank" href="http://newsroom.carleton.ca/2015/02/12/go-global-hire-local-matches-technology-venture-teams-talented-professionals-carleton-university/" ;">
                      	<img src="https://www.tarfee.com/photos/press-2.jpg" class="icon" alt="Lorem Ipsum">
                      </a>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <a target="_blank" href="http://newsroom.carleton.ca/2015/02/12/go-global-hire-local-matches-technology-venture-teams-talented-professionals-carleton-university/" ;">
                      	<h2 class="title">“Go Global, Hire Local” at Carleton University</h2>
                      </a>
                      <p>Promote your organization and students, and show the world what you stand for!</p>
                      <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                      <p><a href="" onclick="populate_and_open_modal(event, 'modal-content-7');" class="btn btn-outline-inverse btn-sm">read more</a></p>

                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                      <div class="content-to-populate-in-modal" id="modal-content-7">
                        <h1>“Go Global, Hire Local” at Carleton University</h1>
                        <p><img data-img-src="https://www.tarfee.com/photos/press-2.jpg" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum"><li>Promote your organization and students, and show the world what you stand for!</li>
                        <li>Create player profile for each student and upload his/her videos and information</li>
                        <li>Be engaged with your community, kids, and their parents</li>
                        <li>Create events and tryouts and invite people to join</li>
                        <li>Directly send and receive message from followers</li>
                        <li>Share experiences, give and get advice</li>
                        <li>Ask for recommendations from your followers</li>
                        <li>Avoid fraud by checking the ratings and reviews of scouts</li></p>
                      </div><!-- #modal-content-3 -->
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: FEATURED CAROUSEL SLIDE 2 -->

                  <!-- FEATURED CAROUSEL SLIDE 3 -->
                  <div class="item">
                    <div class="carousel-text-content">
                      <!-- start: slide content to edit -->
                      <a target="_blank" href="http://newsroom.carleton.ca/2014/12/24/carleton-students-present-startups-philanthropist-entrepreneur-wes-nicol/">
                      	<img src="https://www.tarfee.com/photos/press-3.jpg" class="icon" alt="Lorem Ipsum">
                      </a>
                      <!-- if you want to use bootstrap glyphicons instead, replace the above <img> with the commented <span> below -->
                      <!-- <span class="icon glyphicon glyphicon-lock"></span> -->
                      <a target="_blank" href="http://newsroom.carleton.ca/2014/12/24/carleton-students-present-startups-philanthropist-entrepreneur-wes-nicol/">
                      	<h2 class="title">Carleton Students</h2>
                      </a>
                      <p>Carleton Students Present their Startups to Philanthropist and Entrepreneur Wes Nicol</p>
                      <!-- to make a popup/modal link add an onClick function call to the function: 
                        - populate_and_open_modal(event, '[id of popup content container - see below]') -->
                      <p><a href="" onclick="populate_and_open_modal(event, 'modal-content-8');" class="btn btn-outline-inverse btn-sm">read more</a></p>

                      <!-- content which will be shown in the popup/modal when clicking on the above link 
                        - it is important to set a unique id -->
                      <div class="content-to-populate-in-modal" id="modal-content-8">
                        <h1>Carleton Students</h1>
                        <p><img data-img-src="https://www.tarfee.com/photos/press-3.jpg" class="lazy rounded_border hover_effect pull-left" alt="Lorem Ipsum"><li>Access the best football talents all over the world!</li>
                        <li>Find exactly what you are looking for quickly and easily: You can customize your search by filtering by age, country, position, rating, etc.</li>
                        <li>Keep eye on the players you like to follow their improvements</li>
                        <li>Direct messaging: Just find the players you are looking for and send them a message</li>
                        <li>Create events & tryouts and invite players to join and submit their profiles</li>
                        <li>Be connected and engage with other professionals around the world</li></p>
                      </div><!-- #modal-content-4 -->
                      <!-- end: slide content to edit -->
                    </div>
                  </div><!-- .item -->
                  <!-- END: FEATURED CAROUSEL SLIDE 3 -->
                </div><!-- end: .feature-columns -->
				 <!-- Controls -->
                <!-- the left/right arrows to move from one slide to the other -->
                <a class="left carousel-control" href="#featured-carousel" data-slide="prev"></a>
                <a class="right carousel-control" href="#featured-carousel" data-slide="next"></a>

              <!-- End: Section content to edit -->
            </div><!-- .col-sm-10 -->
          </div><!-- .content-wrapper -->
        </article><!-- .section-wrapper -->
        <!-- ==================== END: SECTION TYPE: Featured Items ==================== -->

        <!-- ==================== SECTION TYPE: Tabs ==================== -->
        <!-- This type of section should contain generic content in tabs -->
        <!-- More information about Bootstrap Tabs can be found on http://getbootstrap.com/javascript/#tabs -->
        <!-- FOR EACH SECTION <article class="section-wrapper".. :
             - remember to use a unique id, 
             - (optional) set a "data-custom-background-img" attribute with a link to custom background image which will be used when the viewer scrolls to this section -->
        <article id="tabs" class="section-wrapper clearfix" data-custom-background-img="https://www.tarfee.com/photos/bg6.jpg">
          <div class="content-wrapper mid-vertical-positioning clearfix">
            <div class="col-sm-10 col-md-9 pull-right">

                <h1 class="section-title">About</h1>

                <div class="tabpanel styled-tabs uniform-height" role="tabpanel">

                  <!-- Nav tabs -->
                  <!-- The "text-hidden-xs" attribute determines whether to show or hide tab text on mobile vieports ("true" - text is hidden) -->
                  <ul class="nav nav-tabs" role="tablist" text-hidden-xs="true">
                    <!-- Each tab should be structure as follows. Link href should be unique and match the ID of the respective tab-pane below. It has to match also with the "aria-controls" attribute. -->
                    <!-- <i> represents the tab icon, <span> represents the tab text. -->
                    <!-- The tab with <li class="active"> is the tab which is enabled by default -->
                    <li role="presentation" class="active"><a href="#tabs-tab1" aria-controls="tabs-tab1" role="tab" data-toggle="tab"><i class="icon fa fa-cloud"></i><span>Overview</span></a></li>
                    <li role="presentation"><a href="#tabs-tab2" aria-controls="tabs-tab2" role="tab" data-toggle="tab"><i class="icon fa fa-diamond"></i><span>Values</span></a></li>
                    <li role="presentation"><a href="#tabs-tab3" aria-controls="tabs-tab3" role="tab" data-toggle="tab"><i class="icon fa fa-users"></i><span>Team</span></a></li>
                    <li role="presentation"><a href="#tabs-tab4" aria-controls="tabs-tab4" role="tab" data-toggle="tab"><i class="icon fa fa-connectdevelop"></i><span>Partners</span></a></li>
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">

                    <!-- Each tab pane is contained in the .tab-pane container with the following structure. Its ID should be unique and match with the tab link above -->
                    <!-- It make container all generic content (text, images, etc). To initialise functions after tab-pane is shown, you need to use Events which are described in the documentation. -->
                    <!-- The tab which is enabled by default should include these classes: "in active" -->
                    <div role="tabpanel" class="tab-pane fade in active" id="tabs-tab1">
                      <img src="https://www.tarfee.com/photos/Canada.png" class="pull-right hidden-xs">
                      <h4>Brief Overview</h4>
                      <p>Tarfee is a born-global venture, based in Ottawa, Canada. We are part of Carleton Led-Accelerator (Campus linked accelerator in Carleton University), associated with Lead to Win ecosystem, and candidate at Technology Innovation Management program at Carleton University. Tarfee is social innovation company that helps football talents unleash their abilities and seek the best opportunities. </p>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="tabs-tab2">
                      <img src="https://www.tarfee.com/landing/assets/images/other_images/tabs-icon3.png" class="pull-right hidden-xs">
                      <h4>Our Values</h4>
                      <p><li>To promote football as a tool to improve players’ lives</li>
                      <li>We believe every football talent deserves a chance</li>
                      <li>We are committed to offer an outstanding service</li></p>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="tabs-tab3">
                      <img src="https://www.tarfee.com/landing/assets/images/other_images/tabs-icon2.png" class="pull-right hidden-xs">
                      <h4>Tarfee Team</h4>
                      <p>Nunc accumsan sapien eu tortor fringilla, et condimentum metus pellentesque. Maecenas rhoncus tortor nec mi congue aliquet. Integer eu turpis scelerisque, iaculis magna non, tempor sem. Quisque consectetur nisi eu felis euismod, sit amet faucibus justo molestie. Ut pretium sapien dui, id facilisis metus interdum pharetra.</p>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="tabs-tab4">
                      <a target="_blank" href="http://timprogram.ca"><img src="https://www.tarfee.com/photos/logo1.v2.png" class="pull-right hidden-xs"></a>
                    	<a target="_blank" href="http://leadtowin.ca"><img src="https://www.tarfee.com/photos/logo3.v1.png" class="pull-right hidden-xs"></a>
                    	<a target="_blank" href="http://carleton.ca"><img src="https://www.tarfee.com/photos/logo2.v2.png" class="pull-right hidden-xs"></a>

                    </div>
                    
                  </div><!-- .tab-content -->

                </div><!-- .tabpanel -->

            </div><!-- .col-sm-10 -->
          </div><!-- .content-wrapper -->
        </article><!-- .section-wrapper -->  
        <!-- ==================== END: SECTION TYPE: Tabs ==================== -->

        <!-- ==================== SECTION TYPE: Contact ==================== -->
        <!-- This type of section should contain text (contact details) and a contact form -->
        <!-- FOR EACH SECTION <article class="section-wrapper".. :
             - remember to use a unique id, 
             - (optional) set a "data-custom-background-img" attribute with a link to custom background image which will be used when the viewer scrolls to this section -->

        <!-- ==================== ENDL SECTION TYPE: Contact ==================== -->

      </section><!-- #main-content -->
      <!-- ==================== END: MAIN CONTENT ==================== -->

      <!-- Footer -->
      <section id="footer">

        <!-- Go to Top -->
        <div id="go-to-top" onclick="scroll_to_top();"><span class="icon glyphicon glyphicon-chevron-up"></span></div>

        <!-- ==================================================================
        SOCIAL ICONS
        ==================================================================  -->
        <!-- Each social icon item consists of the following:
             - Link to the social page in href of <a>
             - social icon in the <img> - a list of social icons options are found in the directory https://www.tarfee.com/landing/assets/images/theme_images/social_icons/ -->
        <ul class="social-icons">
          <li><a href="https://www.facebook.com/TarfeeInc" target="_blank" title="Facebook"><img src="https://www.tarfee.com/landing/assets/images/theme_images/social_icons/facebook.png" alt="Facebook"></a></li>
          <li><a href="https://twitter.com/tarfeeinc" target="_blank" title="Twitter"><img src="https://www.tarfee.com/landing/assets/images/theme_images/social_icons/twitter.png" alt="Twitter"></a></li>
        </ul>
        <!-- ==================== END: Social Icons ==================== -->

        <!-- copyright text -->
        <div class="footer-text-line">&copy; 2015 Tarfee Inc.</div>
      </section>
      <!-- end: Footer -->      

    </div><!-- #outer-container -->
    <!-- end: Outer Container -->

    <!-- Modal -->
    <!-- DO NOT MOVE, EDIT OR REMOVE - this is needed in order for popup content to be populated in it -->
    <div class="modal fade" id="common-modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <div class="modal-body clearfix">
          </div><!-- .modal-body -->
        </div><!-- .modal-content -->
      </div><!-- .modal-dialog -->
    </div><!-- .modal -->    

    <!-- Javascripts
    ================================================== -->

    <!-- Jquery and Bootstrap JS -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="https://www.tarfee.com/landing/assets/js/jquery-1.11.2.min.js"><\/script>')</script>
    <script src="https://www.tarfee.com/landing/assets/bootstrap/js/bootstrap.min.js"></script>

    <!-- Easing - for transitions and effects -->
    <script src="https://www.tarfee.com/landing/assets/js/jquery.easing.1.3.js"></script>

    <!-- background image strech script -->
    <script src="https://www.tarfee.com/landing/assets/js/vegas/jquery.vegas.min.js"></script>

    <!-- detect mobile browsers -->
    <script src="https://www.tarfee.com/landing/assets/js/detectmobilebrowser.js"></script>

    <!-- detect scrolling -->
    <script src="https://www.tarfee.com/landing/assets/js/jquery.scrollstop.min.js"></script>

    <!-- owl carousel js -->
    <script src="https://www.tarfee.com/landing/assets/js/owl-carousel/owl.carousel.min.js"></script>

    <!-- lightbox js -->
    <script src="https://www.tarfee.com/landing/assets/js/lightbox/js/lightbox.min.js"></script>

    <!-- intro animations -->
    <script src="https://www.tarfee.com/landing/assets/js/wow/wow.min.js"></script>

    <!-- responsive videos -->
    <script src="https://www.tarfee.com/landing/assets/js/jquery.fitvids.js"></script>

    <!-- Custom functions for this theme -->
    <script src="https://www.tarfee.com/landing/assets/js/functions.js"></script>
    <script src="https://www.tarfee.com/landing/assets/js/initialise-functions.js"></script>

    <!-- IE9 form fields placeholder fix -->
    <!--[if lt IE 9]>
    <script>contact_form_IE9_placeholder_fix();</script>
    <![endif]-->  
    
  </body>
</html>