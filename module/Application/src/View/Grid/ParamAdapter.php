<?php
namespace Application\View\Grid;
/**
 * @copyright Copyright (c) 2017 Jason Palmer jpalmer@meadedigital.com
 */

use ZfTable\Params\AdapterArrayObject;

class ParamAdapter extends AdapterArrayObject
{
    public function getValueOfFilter($key, $prefix = 'zff_')
    {
        if (isset($this->filters[$prefix . $key])) {
            return $this->filters[$prefix . $key];
        }

        return null;
    }

    public function setFilter($key, $value)
    {
        $this->filters['zff_' . $key] = $value;
    }

    public function getFilters()
    {
        return $this->filters;
    }
}
