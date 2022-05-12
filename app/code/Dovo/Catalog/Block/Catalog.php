<?php
/**
 * Page header block
 */

namespace Dovo\Catalog\Block;


use Magento\Framework\View\Element\Template\Context;

class Catalog extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    public function setGridCount($count) {
        $this->addColumnCountLayoutDepend('2columns-left', $count);
    }
    public function my_test( ) {
        echo 'rgorrrr';
    }
}
