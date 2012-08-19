<?php


/**
 * @package ESys
 */
class ESys_Pager {


    protected $itemCount = 0;


    protected $itemsPerPage = 10;


    protected $selectedPage = 1;


    public function __construct ()
    {
    }


    /**
     * @param int $itemCount
     * @return void
     */
    public function setItemCount ($itemCount)
    {
        $this->itemCount = $itemCount;
    }


    /**
     * @param int $itemsPerPage
     * @return void
     */
    public function setItemsPerPage ($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }


    /**
     * @param int $selectedPage
     * @return void
     */
    public function setSelectedPage ($selectedPage)
    {
        if ($selectedPage > $this->getPageCount()) {
            $selectedPage = $this->getPageCount();
        }
        $this->selectedPage = $selectedPage;
    }


    /**
     * @return ESys_PagerItemRange
     */
    public function getItemRange ()
    {
        $startItem = ($this->itemsPerPage * ($this->selectedPage - 1));
        $length = $this->itemsPerPage;
        $range = new ESys_PagerItemRange($startItem, $length);
        return $range;
    }


    /**
     * @return int
     */
    public function getPageCount ()
    {
        if (! $this->itemsPerPage) {
            return 0;
        }
        return ceil($this->itemCount / $this->itemsPerPage);
    }


    /**
     * @return int
     */
    public function getItemCount ()
    {
        return $this->itemCount;
    }


    /**
     * @return int
     */
    public function getItemsPerPage ()
    {
        return $this->itemsPerPage;
    }


    /**
     * @return int
     */
    public function getSelectedPage ()
    {
        return $this->selectedPage;
    }


    /**
     * @return int
     */
    public function getNextPage ()
    {
        $nextPage = $this->selectedPage + 1;
        if ($nextPage > $this->getPageCount()) {
            $nextPage = false;
        }
        return $nextPage;
    }


    /**
     * @return int
     */
    public function getPreviousPage ()
    {
        $prevPage = $this->selectedPage - 1;
        if ($prevPage < 1) {
            $prevPage = false;
        }
        return $prevPage;
    }


}


/**
 * @package ESys
 */
class ESys_PagerItemRange {


    protected $start;
    
    
    protected $length;


    /**
     * @param int $start
     * @param int $length
     */
    public function __construct ($start, $length)
    {
        $this->start = $start;
        $this->length = $length;
    }


    /**
     * @return int
     */
    public function getStart ()
    {
        return $this->start;
    }


    /**
     * @return int
     */
    public function getEnd ()
    {
        return $this->start + $this->length;
    }


    /**
     * @return int
     */
    public function getLength ()
    {
        return $this->length;
    }


}


