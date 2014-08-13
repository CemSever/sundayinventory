<?php
/**
 * @category   Apptrian
 * @package    Apptrian_Subcategories
 * @author     Apptrian
 * @copyright  Copyright (c) 2014 Apptrian (http://www.apptrian.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Apptrian_Subcategories_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/**
	 * Finds and returns array of subcategories.
	 * 
	 * @param string $pageType
	 * @return array
	 */
    public function getSubcategories($pageType = 'category_page')
    {
    	
    	$categories = array();
    	
    	$attributesToSelect = array('name', 'url_path', 'image', 'thumbnail', 'description', 'meta_description');
    	
        // Get sort attribute and sort direction from config
        // Attribute options: "name", "position", and "created_at"
        // Direction options: "asc" and "desc"
        $sortAttribute = Mage::getStoreConfig('apptrian_subcategories/' . $pageType . '/sort_attribute');
        $sortDirection = Mage::getStoreConfig('apptrian_subcategories/' . $pageType . '/sort_direction');
        
        // Get category IDs from config
        $categoryIds = trim(Mage::getStoreConfig('apptrian_subcategories/home_page/category_ids'), ',');
        
        // For home page when category_ids is provided
        if ($pageType == 'home_page' && $categoryIds != '') {
        	
        	// "Random" mode
        	if (Mage::getStoreConfig('apptrian_subcategories/home_page/mode') == 'random') {
        		
        		// Get random parent ID
        		$id = $this->getRandomId($categoryIds);
        		
        		$category = Mage::getModel('catalog/category')->load($id);
        		$collection = $category->getCollection()
        		    ->addAttributeToSelect($attributesToSelect)
        		    ->addAttributeToFilter('is_active', 1)
        		    ->addAttributeToSort($sortAttribute, $sortDirection)
        		    ->addIdFilter($category->getChildren());
        		 
        		// Get categories array from collection
        		$categories = $this->getCategoriesFromCollection($collection);
        		
        	// "Specific" mode
        	} else {
        		
        		$collection = Mage::getModel('catalog/category')->getCollection()
        		->addAttributeToSelect($attributesToSelect)
        		->addAttributeToFilter('is_active', array('eq'=>'1'))
        		->addIdFilter($categoryIds);
        		 
        		// In this context "position" is different and must be done programmatically
        		// so there is no need to sort it
        		if ($sortAttribute != 'position') {
        		
        			$collection->addAttributeToSort($sortAttribute, $sortDirection);
        		
        			// Get categories array from collection
        			$categories = $this->getCategoriesFromCollection($collection);
        		
        		} else {
        		
        			// Get categories array from collection sorted by $categoryIDs
        			$categories = $this->getCategoriesFromCollection($collection, $categoryIds);
        		
        		}
        		
        	}
        	
        // For category pages and home page when category_ids field is empty
        } else {
            
        	$category = Mage::getSingleton('catalog/layer')->getCurrentCategory();
        	$collection = $category->getCollection()
        	    ->addAttributeToSelect($attributesToSelect)
        	    ->addAttributeToFilter('is_active', 1)
        	    ->addAttributeToSort($sortAttribute, $sortDirection)
        	    ->addIdFilter($category->getChildren());
        	
        	// Get categories array from collection
        	$categories = $this->getCategoriesFromCollection($collection);
        	
        }
        
        return $categories;
        
    }
    
    /**
     * Based on provided comma separated list of Ids, returns one random id.
     * 
     * @param string $categoryIds
     * @return string
     */
    public function getRandomId($categoryIds)
    {
    	
    	$pool = explode(',', $categoryIds);
    	
    	$index = array_rand($pool, 1);
    	
    	return $pool[$index];
    	
    }
    
    /**
     * Based on provided Collection and optionally sort order, returns sorted array of categories.
     * 
     * @param Mage_Catalog_Model_Resource_Category_Collection $collection
     * @param string $sortOrder
     * @return array
     */
    public function getCategoriesFromCollection($collection, $sortOrder = '')
    {
        
        $categories = array();
        
        if ($sortOrder != '') {
        	
        	$sort = explode(',', $sortOrder);
        	
        	foreach ($sort as $id) {
        		
        		$c = $collection->getItemById($id);
        		
        		if ($c != null) {
        			
        			$categories[$id] = $this->categoryToArray($c);
        			
        		}
        		
        	}
        	
        } else {
        	
        	foreach ($collection as $c) {
        		
        		$id = $c->getId();
        		
        		$categories[$id] = $this->categoryToArray($c);
        		
        	}
        	
        }
        
        return $categories;
        
    }
    
    /**
     * Based on provided category object returns small category array with necessary data.
     * 
     * @param Mage_Catalog_Model_Category $c
     * @return array
     */
    public function categoryToArray($c)
    {
    	
    	$category = array();
    	
    	$category['url']              = $c->getUrl();
    	$category['name']             = Mage::helper('core')->htmlEscape($c->getName());
    	$category['image']            = $c->getImage();
    	$category['thumbnail']        = $c->getThumbnail();
    	$category['description']      = Mage::helper('core')->htmlEscape($c->getDescription());
    	$category['meta_description'] = Mage::helper('core')->htmlEscape($c->getMetaDescription());
    	
    	return $category;
    	
    }
    
    /**
     * Generates image url based on provided data.
     * 
     * @param array $category
     * @param string $showImage
     * @param string $placeholderImageUrl
     * @return string
     */
    public function getImageUrl($category, $showImage, $placeholderImageUrl)
    {
        
        if ($showImage == 'image') {
            
            if ($category['image'] != null) {
                $url = Mage::getBaseUrl('media') . 'catalog/category/' . $category['image'];
            } else {
                $url = $placeholderImageUrl;
            }
            
        } elseif ($showImage == 'thumbnail') {
            
            if ($category['thumbnail'] != null) {
                $url = Mage::getBaseUrl('media') . 'catalog/category/' . $category['thumbnail'];
            } else {
                $url = $placeholderImageUrl;
            }
            
        } else {
            
            $url = '';
            
        }
        
        return $url;
        
    }
    
    /**
     * Returns proper description text based on provided data.
     * 
     * @param array $category
     * @param string $showDescription
     * @return string
     */
    public function getDescription($category, $showDescription)
    {
        
        // description field shold be used
        if ($showDescription == 'description') {
            
            $text = $category['description'];
            
            if ($text != '') {
                $categoryDescription = '<div class="category-description">' . $text . '</div>';
            } else {
                $categoryDescription = '';
            }
            
        // meta_description field shold be used
        } elseif ($showDescription == 'meta_description') {
            
            $text = $category['meta_description'];
            
            if ($text != '') {
                $categoryDescription = '<div class="category-description"><p>' . $text . '</p></div>';
            } else {
                $categoryDescription = '';
            }
            
        // none (do not show category description)
        } else {
        
            $categoryDescription = '';
            
        }
        
        return $categoryDescription;
        
    }
    
    /**
     * Returns array of exclude Ids from config.
     * 
     * @return array
     */
    public function getExcludedIds()
    {
    	$excludeIds = trim(Mage::getStoreConfig('apptrian_subcategories/category_page/exclude_ids'), ',');
    	
    	return explode(',', $excludeIds);
    	
    }
    
    /**
     * Checks if category is in excluded list.
     * 
     * @return boolean
     */
    public function isExcluded()
    {
    	$c = Mage::registry('current_category');
    	
    	if ($c !== null) {
    		
    		$excluded = $this->getExcludedIds();
    		
    		if (count($excluded) > 0 && in_array($c->getId(), $excluded)) {
    			
    			return true;
    			
    		// Exclude list is empty
    		} else {
    			
    			return false;
    			
    		}
    		
    	// Not a category page
    	} else {
    		
    		return false;
    		
    	}
    	
    }
    
}