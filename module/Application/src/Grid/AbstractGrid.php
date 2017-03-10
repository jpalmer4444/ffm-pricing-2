<?php
namespace Application\Grid;

use Application\View\Grid\ModuleOptions;
use ZfTable\AbstractTable;
use ZfTable\Render;
/**
 * @copyright  Copyright (c) 2015 Busteco Global Brain
 * @author     Valentina <valentina@busteco.ro>
 */

class AbstractGrid extends AbstractTable
{
    public function getRender()
    {
        if (!$this->render) {
            $this->render = new Render($this);
        }
        return $this->render;
    }

    /**
     *
     * @return ModuleOptions
     * @throws Exception
     */
    public function getOptions()
    {
        if (is_array($this->config)) {
            $this->config = new ModuleOptions($this->config);
        } elseif (!$this->config instanceof ModuleOptions) {
            throw new Exception('Config class problem');
        }
        return $this->config;
    }
}
