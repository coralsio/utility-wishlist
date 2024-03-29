<?php

namespace Corals\Utility\Wishlist\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Utility\Wishlist\Models\Wishlist;
use Corals\Utility\Wishlist\Transformers\WishlistTransformer;
use Yajra\DataTables\EloquentDataTable;

class WishlistDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new WishlistTransformer());
    }

    /**
     * @param Wishlist $model
     * @return mixed
     */
    public function query(Wishlist $model)
    {
        return $model->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['visible' => false],
            'object' => ['title' => trans('utility-wishlist::attributes.wishlist.object'), "searchable" => false, 'orderable' => false],
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
        ];
    }

    protected function getOptions()
    {
        return ['has_action' => true];
    }
}
