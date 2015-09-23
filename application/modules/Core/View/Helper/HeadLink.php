<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc.
 * (http://www.zend.com)
 * @version    $Id: HeadLink.php 16971 2009-07-22 18:05:45Z mikaelkael $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
// require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Zend_Layout_View_Helper_HeadLink
 *
 * @see        http://www.w3.org/TR/xhtml1/dtds.html
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc.
 * (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Core_View_Helper_HeadLink extends Zend_View_Helper_HeadLink
{
    /**
     * @var string registry key
     */
    protected $_regKey = 'Zend_View_Helper_HeadLink';
    /**
     *
     * The folder to be appended to the base url to find minify on your server.
     * The default assumes you installed minify in your documentroot\min
     * directory
     * if you modified the directory name at all, you need to let the helper know
     * here.
     * @var string
     */
    protected $_minifyLocation = 'externals/minify/minify.php';
    /**
     *
     * Known Valid CSS Extension Types
     * @var array
     */
    protected $_cssExtensions = array(
        ".css",
        ".css1",
        ".css2",
        ".css3"
    );
    
    protected $_verb;

    /**
     * headLink() - View Helper Method
     *
     * Returns current object instance. Optionally, allows passing array of
     * values to build link.
     *
     * @return Zend_View_Helper_HeadLink
     */
    public function headLink(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        if (null !== $attributes)
        {
            $item = $this -> createData($attributes);
            switch ($placement)
            {
                case Zend_View_Helper_Placeholder_Container_Abstract::SET :
                    $this -> set($item);
                    break;
                case Zend_View_Helper_Placeholder_Container_Abstract::PREPEND :
                    $this -> prepend($item);
                    break;
                case Zend_View_Helper_Placeholder_Container_Abstract::APPEND :
                default :
                    $this -> append($item);
                    break;
            }
        }
        return $this;
    }
    
     /**
     * Render link elements as string
     *
     * @param  string|int $indent
     * @return string
     */
    public function toString2($indent = null)
    {
        $indent = (null !== $indent)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

        $items = array();
        $this->getContainer()->ksort();
        foreach ($this as $item) {
            $items[] = $this->itemToString($item);
        }

        return $indent . implode($this->_escape($this->getSeparator()) . $indent, $items);
    }

    public function writeMinifySetting($data){
        $filename = APPLICATION_PATH .'/temporary/yn_minify.php';
        $fp =  fopen($filename, 'w');
        fwrite($fp, '<?php return '. var_export($data, true).';?>');
        fclose($fp);
    }
        
    public function readMinifySetting(){
        if(is_readable(APPLICATION_PATH .'/temporary/yn_minify.php')){
            $minifyConfig = (include APPLICATION_PATH .'/temporary/yn_minify.php');
            return $minifyConfig;
        }
        return array();
    }
    
    /**
     *
     * Gets a string representation of the headLinks suitable for inserting
     * in the html head section.
     *
     * It is important to note that the minified files will be minified
     * in reverse order of being added to this object, and ALL files will be
     * rendered
     * prior to inline being rendered.
     *
     * @see Zend_View_Helper_HeadScript->toString()
     * @param  string|int $indent
     * @return string
     */
    public function toString($indent = null)
    {        
        //if($_SERVER['REMOTE_ADDR'] != '192.168.11.146'){
           
        //    return $this->toString2();
        //}
        
         if(APPLICATION_ENV == 'development' ||!Engine_Api::_()->hasModuleBootstrap('minify') || !Engine_Api::_()->getApi('settings','core')->getSetting('minify.mincss.enable',1)){
             return $this->toString2();            
        }        
       
        // The base URL
        $baseUrl = $this -> getBaseUrl();
        
        //$staticBaseUrl = $this -> view -> layout() -> staticBaseUrl;
        
        //remove the slash at the beginning if there is one
        if (substr($baseUrl, 0, 1) == '/')
        {
            $baseUrl = substr($baseUrl, 1);
        }

        $indent = (null !== $indent) ? $this -> getWhitespace($indent) : $this -> getIndent();

        $items = array();
        $stylesheets = array();
        $this -> getContainer() -> ksort();
        
        /*****************************************************************/
        $off = false;
        
        if(isset($_REQUEST['minify']) && $_REQUEST['minify'] === 'off'){
            $off  = true;
        }

        //$maxCombinedFile = $this -> getMaxCombinedFile();
        
        //$counter = '&'. (int) $this->view->layout()->counter;
        $counter = '&'. (int) Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.site.counter', 1);

        
        $minifyConfig = array();
        $cssGroups =  array();
                
        if(is_readable(APPLICATION_PATH .'/temporary/yn_minify.php')){
            $minifyConfig = (include APPLICATION_PATH .'/temporary/yn_minify.php');
            $cssGroups = isset($minifyConfig['groups'])?$minifyConfig['groups']:array();
        }
        else
        {
            $cssGroups = array (
                        'css' => array (),
                        'js'=>array(),	
                        );
        }
        
        $configName =  isset($_REQUEST['config_name']) ? $_REQUEST['config_name'] : 'none';
        
        $allCss = array();
        $tempCss = array();
        $allGroups = array();
        
        if(empty($cssGroups)){
            $off = true;
        }
        
        if($off){
            $cssGroups =  array();
        } 
        
        /*****************************************************************/ 
        
        $xhtml = array();
                        
        foreach ($this as $item)
        {    
            
            if ($item -> type == 'text/css' && $item -> conditionalStylesheet === false && strpos($item -> href, 'http://') === false && $this -> isValidStyleSheetExtension($item -> href))
            {   
                $stylesheets[$item -> media][] = str_replace($this -> getBaseUrl(), '', $item -> href);
                
                /*********************************************************************/
                $matched =  false;
                                                       
                foreach($cssGroups as $groupKey=>$groupValue){                
                   
                  
                   $href =  substr(str_replace($this -> getBaseUrl()."/", '', $item -> href),0,strpos(str_replace($this -> getBaseUrl(), '', $item -> href),"?")-1);
                                                                                             
                    if (in_array($href, $groupValue))
                    {                           
                        $matched =  true;
                        if (!isset($allGroups[$groupKey]))
                        {
                            $allCss[] = 'g='.$groupKey;
                            $allGroups[$groupKey] = 1;
                        }
                    }                  
                           
                }
                         
                if(!$matched)
                {
                    $tempCss[] = $item -> href;
                }
                /*********************************************************************/ 
                
                $xhtml[] = $this->itemToString($item);
            }        
            
            else
            {                
                // first get all the stylsheets up to this point, and get them
                // into
                // the items array                             
                                                         
                                     
                $seen = array();
                
                foreach ($stylesheets as $media => $styles)
                {                     
                    $minStyles = new stdClass();
                    $minStyles -> rel = 'stylesheet';
                    $minStyles -> type = 'text/css';
                    $styles = $this -> cleanURLForMinify($styles);
                                        
                   $allCss = $styles; 
                   
                    if (is_null($baseUrl) || $baseUrl == '')
                    {
                        $minStyles -> href = $this -> getMinUrl() . '?f=' . implode(',', $styles) .'&'.  $this->getVerb();
                    }
                    else
                    {
                        $minStyles -> href = $this -> getMinUrl() . '?b=' . str_replace('/index.php', '', $baseUrl) . '&f=' . implode(',', $styles).'&'.  $this->getVerb();
                    }

                    $minStyles -> media = $media;
                    $minStyles -> conditionalStylesheet = false;
                    if (in_array($this -> itemToString($minStyles), $seen))
                    {
                        continue;
                    }
                    $items[] = $this -> itemToString($minStyles);
                    // add the minified item
                    $seen[] = $this -> itemToString($minStyles);
                    // remember we saw it
                    
                    $xhtml[] = $this -> itemToString($minStyles);
                }
                
                $stylesheets = array();
                // Empty our stylesheets array
                $items[] = $this -> itemToString($item);
                // Add the item
                $xhtml[] = $this->itemToString($item);
               
               
                               
            }    
            
        }                             
            
        // Make sure we pick up the final minified item if it exists.
      
        //$xhtml = array();
        //$b = trim($baseUrl,'/');
         $b = NULL;          
        foreach ($allCss as $css)
        {         
   
            if (is_string($css))
            {
                $minStyles = new stdClass();
                $minStyles -> rel = 'stylesheet';
                $minStyles -> type = 'text/css';                       
              
                                
                if (stripos($css, 'g=') !== FALSE)
                {                    
                    $minStyles -> href = $this -> getMinUrl() . '?' . $css . $counter;
                                      
                }
                else
                if ($b == NULL)
                {
                    $minStyles -> href = $this -> getMinUrl() . '?f=' . $css . $counter;                   
                }
                else
                {
                    $minStyles -> href = $this -> getMinUrl() . '?b=' .  $b. '&f=' . $css .$counter;                    
                }                
                $xhtml[] = $this -> itemToString($minStyles);
            }
            else
            {                
                $xhtml[] = $this -> itemToString($css);
            }
        }        
       
        /*******************************************************************/
               
        if($off){            
            $arr = array();
            foreach ($allCss as $css){
                if (is_string($css)){
                    $arr =  array_merge($arr, explode(',', $css));
                }
            }
            //$this->log('css',var_export($arr,true),Zend_Log::INFO);
                      
            $configName =  isset($_REQUEST['config_name'])?$_REQUEST['config_name']:'none';       
            $config = $this->readMinifySetting();
            $config[$configName]['css'] = $arr;        
            $this->writeMinifySetting($config);
        }    
        
        /*******************************************************************/
    
        return $indent . implode($this -> _escape($this -> getSeparator()) . $indent, $xhtml);

    }

    /**
     *
     * Loops through the defined valid static css extensions we use.
     * @param string $string
     */
    public function isValidStyleSheetExtension($string)
    {
        //return true;
        $string = $this -> cleanURLForMinify(array($string));
        $string = $string[0];
        foreach ($this->_cssExtensions as $ext)
        {
            if (substr_compare($string, $ext, -strlen($ext), strlen($ext)) === 0)
            {
                return true;
            }
        }
        return false;
    }

    /**
     *
     */
    public function getBaseUrl()
    {
        return Zend_Controller_Front::getInstance() -> getBaseUrl();
    }

    /**
     * Retrieve the minify url
     *
     * @return string
     */
    public function getMinUrl()
    {
        return $this -> view -> layout() -> staticBaseUrl . $this -> _minifyLocation;
    }
    
    public function getVerb(){
        if($this->_verb == NULL){
            return time();
        }
        return $this->_verb;
    }

    /**
     * Retrieve array url
     *
     * @return array
     */
    public function cleanURLForMinify($urls)
    {
        
        $clean = array();
        $pattern = array(
            '/(\?.*)/',
            '/\/\//'
        );

        $replacement = array(
            '',
            ''
        );

        $basePath = ltrim(str_replace('/index.php', '', $this -> view -> baseUrl()),'/');
        
        foreach ($urls as $url)
        {
            if($this->_verb == NULL && preg_match("#\?c=(?P<verb>\d+)#", $url, $matches)){
                if(isset($matches['verb'])){
                    $this->_verb =  $matches['verb'];
                }
              }
            $url = ltrim(preg_replace($pattern, $replacement, $url), " /");
            if ($basePath != '/')
            {
                
                $url = str_replace($basePath, '', $url);
                // echo $basePath;
                // var_dump($url);
                // exit;
            }

            $clean[] = $url;
        }
        return $clean;
    }

}
