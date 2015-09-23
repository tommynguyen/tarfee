<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Form_Search extends Engine_Form 
{
    public function init() {
        $this->setAttribs(array('class' => 'global_form_box ynvideo_form_widget', 'id' => 'filter_form'))
                ->setMethod('GET');
        
        // prepare categories
        $categories = Engine_Api::_()->getDbTable('categories', 'ynvideo')->getAllCategoriesAndSortByLevel(null, 'category_name');
        $categories_prepared[''] = "All Categories";
        $categories_prepared['0'] = "Non-category";
        foreach ($categories as $category) {
            $name = $category->category_name;
            if (!$category->parent_id) {
                $categories_prepared[$category->category_id] = $category->category_name;
                foreach($category->getSubCategories() as $subCat) {
                    $categories_prepared[$subCat->category_id] = "--" . $subCat->category_name;
                }
            }
        }

        $this->addElement('Text', 'text', array(
            'label' => 'Search',
            'alt' => 'Search videos'
        ));

        $this->addElement('Hidden', 'tag');

        $this->addElement('Select', 'orderby', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'creation_date' => 'Most Recent',
                'view_count' => 'Most Viewed',
                'rating' => 'Highest Rated',
                'most_liked' => 'Most Liked',
                'most_commented' => 'Most Commented',
                'featured' => 'Featured'
            ),
        ));

        // category field
        $this->addElement('Select', 'category', array(
            'label' => 'Category',
            'multiOptions' => $categories_prepared,
        ));
        
        // Buttons
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}