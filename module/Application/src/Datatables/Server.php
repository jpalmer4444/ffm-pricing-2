<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Datatables;

/**
 * Description of Server
 *
 * @author jasonpalmer
 */
class Server {

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
