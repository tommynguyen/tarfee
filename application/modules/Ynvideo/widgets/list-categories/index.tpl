<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
$session = new Zend_Session_Namespace('mobile');
?>
<ul class="ymb_menuRight_wapper generic_list_widget">
    <table class="ynvideo-category">
        <?php foreach ($this->categories as $category) : ?>
            <?php if ($category->parent_id == 0) : ?>
                <tr>
                    <td <?php  $request = Zend_Controller_Front::getInstance()->getRequest(); if($request-> getParam('category') == $category -> category_id) echo 'class = "active"';?>>
                        <img src="<?php echo $this->baseUrl() . '/' . $category->getIconUrl()?>" />
                        <?php 
                            echo $this->htmlLink(
                                    $category->getHref(), 
                                    $this->string()->truncate($category->category_name, 20),
                                    array('title' => $category->category_name, 'class' => 'ynvideo-link-category'));
                        ?>
                        <?php if(count($category->getSubCategories()) > 0 && !$session-> mobile) : ?>
                            <div class="ynvideo-category-collapse-control ynvideo-category-collapsed"></div>
                        <?php else : ?>
                            <div class="ynvideo-category-collapse-nocontrol"></div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php foreach($category->getSubCategories() as $subCat) : ?>
                    <tr class="ynvideo-category-sub-category">
                        <td>
                            <img src="<?php echo $this->baseUrl() . '/' . $subCat->getIconUrl()?>" />
                            <?php echo $this->htmlLink($subCat->getHref(), $subCat->category_name)?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr>
            <td>
                <img src="<?php echo $this->baseUrl() . '/' . Ynvideo_Model_Category::defaultIconUrl()?>" />
                <?php 
                    echo $this->htmlLink($this->url(
                        array('action' => 'list', 'category' => 0), 'video_general'), 
                        $this->translate('Non-category'));
                ?>
            </td>
        </tr>
    </table>
</ul>