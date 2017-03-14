<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * This view helper class displays a menu bar.
 */
class Menu extends AbstractHelper {
    
    protected $siteMapOverride;

    /**
     * Menu items array.
     * @var array 
     */
    protected $items = [];

    /**
     * Active item's ID.
     * @var string  
     */
    protected $activeItemId = '';

    /**
     * Constructor.
     * @param array $items Menu items.
     */
    public function __construct($items = []) {
        $this->items = $items;
    }

    /**
     * Sets menu items.
     * @param array $items Menu items.
     */
    public function setItems($items) {
        $this->items = $items;
    }
    
    /**
     * Sets siteMapOverride.
     * @param string siteMapOverride.
     */
    public function setSiteMapOverride($siteMapOverride) {
        $this->siteMapOverride = $siteMapOverride;
    }

    /**
     * Sets ID of the active items.
     * @param string $activeItemId
     */
    public function setActiveItemId($activeItemId) {
        $this->activeItemId = $activeItemId;
    }

    /**
     * Renders the menu.
     * @return string HTML code of the menu.
     */
    public function render() {
        if (count($this->items) == 0)
        {
            //render none if there are no items.
            //return '';
        }

        $urlHelper = $this->getView()->plugin('url');
        $result =  ' <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">';
        $result .= '    <div class="container">';
        $result .= '        <div class="navbar-header">';
        $result .= '            <button type="button" style="display:none;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">';
        $result .= '                <span class="icon-bar"></span>';
        $result .= '                <span class="icon-bar"></span>';
        $result .= '                <span class="icon-bar"></span>';
        $result .= '            </button>';
        $result .= '            <a class="navbar-brand" href="' . $urlHelper('home') . '">';
        $result .= '                <img src="/img/pricing-logo.svg' . '" alt="Pricing Logo" class="logo ffm-svg-header"/>';
        //$result .= '                <i class="ion-android-restaurant ffm-svg-header"></i>';
        $result .= '                <span class="text-primary ffm-text-header">FFM</span>';
        $result .= '            </a>';
        $result .= '            <h1 class="page-title">' . $this->siteMapOverride . '</h1>';
        $result .= '        </div>';
        $result .= '        <div class="collapse navbar-collapse">';
        $result .= '            <ul class="nav navbar-nav">';

        // Render items
        foreach ($this->items as $item) {
            if (!isset($item['float']) || $item['float'] == 'left')
                $result .= $this->renderItem($item);
        }

        $result .= '            </ul>';
        
        //now build the right-hand Navigation with Settings link in top-right corner.
        $result .= '            <ul class="nav navbar-nav navbar-right">';

        //print out any static Links that should render left of Settings Dropdown in right corner of navbar.
        //pass these items in with property float="static"
        foreach ($this->items as $item) {
            if (isset($item['float']) && $item['float'] == 'static'){
                $result .= '        '.$this->renderItem($item);
            }
        }

        // Render Settings link (top-right corner).
        foreach ($this->items as $item) {
            if (isset($item['float']) && $item['float'] == 'right'){
                if ($item['id'] == 'login' && $this->activeItemId == 'login'){
                    //do nothing so we do not render the Sign-In Link in upper - right - corner.
                }else{
                    $result .= '        '.$this->renderItem($item);
                }
            }
        }

        $result .= '            </ul>';
        $result .= '        </div>';
        $result .= '    </div>';
        $result .= ' </nav>';

        return $result;
    }

    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @return string HTML code of the item.
     */
    protected function renderItem($item) {
        $id = isset($item['id']) ? $item['id'] : '';
        $isActive = ($id == $this->activeItemId);
        $label = isset($item['label']) ? $item['label'] : '';
        $sanitizeLabel = TRUE;
        $dropdownToggleToolTip = FALSE;

        //hack for settings SVG icon in top right corner.
        if (strpos($label, 'ion-person')) {
            $sanitizeLabel = FALSE;
            $dropdownToggleToolTip = "";
        }
        
        if (strpos($label, 'ion-gear-a')) {
            $sanitizeLabel = FALSE;
            $dropdownToggleToolTip = "";
        }

        $result = '';

        $escapeHtml = $this->getView()->plugin('escapeHtml');

        if (isset($item['dropdown'])) {

            $dropdownItems = $item['dropdown'];

            $result .= '<li uib-dropdown class="dropdown ' . ($isActive ? 'active' : '') . '">';
            $result .= '<a uib-dropdown-toggle href="#" class="dropdown-toggle" ' . (!empty($dropdownToggleToolTip) ? 'title="' . $dropdownToggleToolTip . '"' : '') . ' data-toggle="dropdown">';
            $result .= ($sanitizeLabel ? $escapeHtml($label) : $label) . ' <b class="caret"></b>';
            $result .= '</a>';

            $result .= '<ul uib-dropdown-menu class="dropdown-menu">';
            foreach ($dropdownItems as $item) {
                $link = isset($item['link']) ? $item['link'] : '#';
                $label = isset($item['label']) ? $item['label'] : '';

                $result .= '<li>';
                $result .= '<a href="' . $escapeHtml($link) . '">' . ($sanitizeLabel ? $escapeHtml($label) : $label) . '</a>';
                $result .= '</li>';
            }
            $result .= '</ul>';
            $result .= '</li>';
        } else {
            $link = isset($item['link']) ? $item['link'] : '#';

            $result .= $isActive ? '<li class="active">' : '<li>';
            $result .= '<a href="' . $escapeHtml($link) . '">' . ($sanitizeLabel ? $escapeHtml($label) : $label) . '</a>';
            $result .= '</li>';
        }

        return $result;
    }

}
