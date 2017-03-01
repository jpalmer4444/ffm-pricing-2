<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * This view helper class displays a menu bar.
 */
class Menu extends AbstractHelper 
{
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
    public function __construct($items=[]) 
    {
        $this->items = $items;
    }
    
    /**
     * Sets menu items.
     * @param array $items Menu items.
     */
    public function setItems($items) 
    {
        $this->items = $items;
    }
    
    /**
     * Sets ID of the active items.
     * @param string $activeItemId
     */
    public function setActiveItemId($activeItemId) 
    {
        $this->activeItemId = $activeItemId;
    }
    
    /**
     * Renders the menu.
     * @return string HTML code of the menu.
     */
    public function render() 
    {
        if (count($this->items)==0)
            return ''; // Do nothing if there are no items.
        
        /*
         * <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?= $this->url('home') ?>">
         * 
         * BASE_PATH.'/'
         * 
                        <img src="<?php echo $this->basePath('img/pricing-logo.svg') ?>" alt="Pricing Logo" class="logo ffm-svg-header"/>
                        <span class='text-primary ffm-text-header'>
                            FFM
                        </span>
                    </a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                            $items:float:left
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                            $items:float:right
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div>
        </nav>
         */
        
        $urlHelper = $this->getView()->plugin('url');
        $result = '<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">';
        $result .= '<div class="container">';
        $result .= '<div class="navbar-header">';
        //display:none because the navbar collapse and toggle is not enabled.
        $result .= '<button type="button" style="display:none;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">';
        $result .= '<span class="icon-bar"></span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '</button>';
        $result .= '<a class="navbar-brand" href="' . $urlHelper('home') . '">';
        $result .= '<img src="/img/pricing-logo.svg' . '" alt="Pricing Logo" class="logo ffm-svg-header"/>';
        $result .= '<span class="text-primary ffm-text-header">FFM</span>';
        $result .= '</a>';
        $result .= '</div>';
        $result .= '<div class="collapse navbar-collapse">';
        $result .= '<ul class="nav navbar-nav">';
        
        // Render items
        foreach ($this->items as $item) {
            if(!isset($item['float']) || $item['float']=='left')
                $result .= $this->renderItem($item);
        }
        
        $result .= '</ul>';
        $result .= '<ul class="nav navbar-nav navbar-right">';
        
        // Render items
        foreach ($this->items as $item) {
            if(isset($item['float']) && $item['float']=='right')
                $result .= $this->renderItem($item);
        }
        
        $result .= '</ul>';
        $result .= '</div>';
        $result .= '</div>';
        $result .= '</nav>';
        
        return $result;
        
    }
    
    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @return string HTML code of the item.
     */
    protected function renderItem($item) 
    {
        $id = isset($item['id']) ? $item['id'] : '';
        $isActive = ($id==$this->activeItemId);
        $label = isset($item['label']) ? $item['label'] : '';
             
        $result = ''; 
     
        $escapeHtml = $this->getView()->plugin('escapeHtml');
        
        if (isset($item['dropdown'])) {
            
            $dropdownItems = $item['dropdown'];
            
            $result .= '<li class="dropdown ' . ($isActive?'active':'') . '">';
            $result .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
            $result .= $escapeHtml($label) . ' <b class="caret"></b>';
            $result .= '</a>';
           
            $result .= '<ul class="dropdown-menu">';
            foreach ($dropdownItems as $item) {
                $link = isset($item['link']) ? $item['link'] : '#';
                $label = isset($item['label']) ? $item['label'] : '';
                
                $result .= '<li>';
                $result .= '<a href="'.$escapeHtml($link).'">'.$escapeHtml($label).'</a>';
                $result .= '</li>';
            }
            $result .= '</ul>';
            $result .= '</li>';
            
        } else {        
            $link = isset($item['link']) ? $item['link'] : '#';
            
            $result .= $isActive?'<li class="active">':'<li>';
            $result .= '<a href="'.$escapeHtml($link).'">'.$escapeHtml($label).'</a>';
            $result .= '</li>';
        }
    
        return $result;
    }
}
