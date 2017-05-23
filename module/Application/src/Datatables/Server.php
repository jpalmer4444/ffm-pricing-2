<?php

/*
 * Converts associative data to datatables format JSON
 */

namespace Application\Datatables;

/**
 * Description of Server
 *
 * @author jasonpalmer
 */
class Server {
    
    const JSON_DATA = 'jsonData';
    const DATE_FORMAT = 'm/d/Y';

    public static function buildArrayFromJson($jsonData) {
        $arr = [];
        if (count($jsonData)) {

            foreach ($jsonData as $object) {

                if ($object->name == 'draw') {
                    $arr['draw'] = $object->value;
                }
                
                if ($object->name == 'start') {
                    $arr['start'] = $object->value;
                }
                
                if ($object->name == 'length') {
                    $arr['length'] = $object->value;
                }

                if ($object->name == 'columns') {
                    $arr['columns'] = [];
                    foreach ($object->value as $column) {
                        $columns = [];
                        $columns['data'] = $column->data;
                        $columns['name'] = $column->name;
                        $columns['searchable'] = $column->searchable;
                        $columns['orderable'] = $column->orderable;
                        $columns['search'] = $column->search ? [
                            'value' => $column->search->value,
                            'regex' => $column->search->regex
                                ] :
                                [];
                        $arr['columns'][] = $columns;
                    }
                }
                
                if ($object->name == 'order') {
                    $arr['order'] = [];
                    foreach ($object->value as $order) {
                        $orders = [];
                        $orders['column'] = $order->column;
                        $orders['dir'] = $order->dir;
                        $arr['order'][] = $orders;
                    }
                }
                
                if ($object->name == 'search') {
                    $arr['search'] = [];
                    $arr['search']['value'] = $object->value->value;
                    $arr['search']['regex'] = $object->value->regex;
                }
                
            }
        }
        
        return $arr;
    }
    
    

}
