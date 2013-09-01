<?php


require_once 'ESys/Pager.php';


class ESys_PagerTest extends PHPUnit_Framework_TestCase {

    public function  setUp ()
    {
    }


    public function testCalculatesTotalPageCount ()
    {
        $pager = $this->createPager(5, 15);
        $this->assertEquals(3, $pager->getPageCount());
    }


    protected function createPager ($itemsPerPage, $itemCount)
    {
        $pager = new ESys_Pager();
        $pager->setItemsPerPage($itemsPerPage);
        $pager->setItemCount($itemCount);
        return $pager;
    }


}