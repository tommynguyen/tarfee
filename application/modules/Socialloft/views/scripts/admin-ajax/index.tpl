<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Bookmarklet
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */
?>

<style>
    {literal}   
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }

    #tabs-socialloft-support .context h3 {
        background: url("http://www.socialloft.com/wp-content/themes/social/images/bgd-title1.jpg") no-repeat scroll 0 bottom transparent;
        color: #252525;
        font-family: 'Buenard';
        font-size: 30px;
        font-weight: normal;
        line-height: 32px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom : none;
    }
    #tabs-socialloft-support p {
        color: #464646;
        font-size: 14px;
    }
    #tabs-socialloft-support .btn-quote-free {
        margin: 10px 0 60px;
    }
    #tabs-socialloft-support .btn-quote-free a {
        background: none repeat scroll 0 0 #365FB7;
        color: #FFFFFF;
        display: block;
        font-size: 24px;
        height: 44px;
        line-height: 44px;
        text-align: center;
        width: 280px;
        text-decoration:none;
    }
    #tabs-socialloft-support .process h3 {
        color: #252525;
        font-family: 'Buenard';
        font-size: 30px;
        font-weight: normal;
        line-height: 32px;
        margin-bottom: 10px;
        border-bottom : none;
    }
    #tabs-socialloft-support .process-detail {
        border: 1px solid #E6E6E6;
        overflow: hidden;
    }

    #tabs-socialloft-support .process-detail li {
        border-right: 1px solid #E6E6E6;
        float: left;
        height: 320px;
        overflow: hidden;
        padding: 25px 13px;
        width: 142px;
    }
    #tabs-socialloft-support .process-detail li p {
        font-size: 14px;
        padding-bottom: 20px;
    }
    #tabs-socialloft-support .title-process {
        color: #000000;
        font-size: 14px;
        line-height: 16px;
        text-transform: uppercase;
    }
    {/literal}   
</style>
<h2>SocialLOFT Dashboard</h2>
<div id="loft-main" style="display:none;">
<div id="tabs" >
    <ul>    
        <li><a href="#tabs-socialloft-plugin" rel="tabs-socialloft-plugin">SocialLOFT Plugin</a></li>
        <li><a href="#tabs-my-plugin" rel="tabs-my-plugin">My Plugin</a></li>
        <li style="display:none;"><a href="#tabs-socialloft-support">Social Support</a></li>

    </ul>
    <div id="tabs-socialloft-plugin">
        <div id="loft-socialplugin"></div>
      
    </div>
    <div id="tabs-my-plugin">
        <div id="loft-myplugin"></div>
    </div>
    <div id="tabs-socialloft-support">
        <div class="context fck">
            <h3>Social Networking Maintenance and Support</h3>
            <p>For small and urgent requirements such as quick support on server stuffs, minor fixes, minor improvement or small customization that you want to make on your existing social network. Please work with us using this prepaid plan service. Please refer to our prepaid cost model for more details</p>
            <div class="btn-quote-free"><a href="http://www.socialloft.com/contact/get-a-free-quote/" target="_blank" title="Get a Free Quote">Get a Free Quote</a></div>
        </div>
        <div class="process">
            <h3>How does the process go ?</h3>
            <ul class="process-detail">
                <li>
                    <p class="title-process">Communication</p>
                    <p><img title="Communication" alt="Communication" src="http://www.socialloft.com/wp-content/themes/social/images/communication.jpg"></p>
                    <p>Communicating with a client, understanding the client's goals, audience and preferences. Each client receives our questionnaire to fill out, which will keep all the details of the project documented.</p>
                </li>
                <li>
                    <p class="title-process">Planning</p>
                    <p><img title="Planning" alt="Planning" src="http://www.socialloft.com/wp-content/themes/social/images/planning.jpg"></p>
                    <p>Planning the content and structure of the web site, establishing timelines and development solutions.</p>
                </li>
                <li>
                    <p class="title-process">Implementation</p>
                    <p><img title="Implementation" alt="Implementation" src="http://www.socialloft.com/wp-content/themes/social/images/implementation.jpg"></p>
                    <p>Programming, implementing, configuring the necessary functionality for the site.</p>
                </li>
                <li class="last">
                    <p class="title-process">Testing</p>
                    <p><img title="Testing" alt="Testing" src="http://www.socialloft.com/wp-content/themes/social/images/testing.jpg"></p>
                    <p>Testing if everything is working perfectly, showing the results to the client, revising if necessary.</p>
                </li>
            </ul>
        </div>
    </div>
</div>
<div id="loft-dialog-form-verify" title="License Verification">
    <p class="validateTips">Please enter license key to verify your purchase.</p>
    <form id="loft-form">
        <fieldset>      
            <input type="hidden" name="product" id="loft-product" value=""/>
            <input type="text" name="license" id="loft-license" class="text ui-widget-content ui-corner-all" />
        </fieldset>
    </form>
    <div id="loft-progressbar" style="display:none; width:95%;"></div>
</div>
<div id="loft-dialog-message" title="License Confirmation" style="display:none"></div>

</div>
