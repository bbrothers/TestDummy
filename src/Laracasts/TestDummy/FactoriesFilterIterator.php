<?php namespace Laracasts\TestDummy;


class FactoriesFilterIterator extends \RecursiveFilterIterator
{

    protected $exclude = [];

    /**
     * Static Factory Method for adding additional filters
     *
     * @param \RecursiveIterator $iterator
     * @param                    $filters
     * @return static
     */
    public static function createWithFilters(\RecursiveIterator $iterator, $filters)
    {

        $instance = new static($iterator);
        $instance->setExclusionFilters($filters);
        return $instance;
    }

    /**
     * Add additional filters to exclude files from the DirectoryIterator
     *
     * @param $exclude
     */
    protected function setExclusionFilters(array $exclude)
    {
        $this->exclude = $exclude;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the current element of the iterator is acceptable
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {

        if (count($this->exclude) < 1) {
            return true;
        }

        return (bool) ! preg_match(
            '/(' . implode('|', $this->exclude) . ')/',
            $this->current()->getFilename()
        );
    }
}
