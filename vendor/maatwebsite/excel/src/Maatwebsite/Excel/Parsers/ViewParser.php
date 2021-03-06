<?php namespace Maatwebsite\Excel\Parsers;

use View;
use Maatwebsite\Excel\Readers\Html;

/**
 *
 * LaravelExcel ViewParser
 *
 * @category   Laravel Excel
 * @version    1.0.0
 * @package    maatwebsite/excel
 * @copyright  Copyright (c) 2013 - 2014 Maatwebsite (http://www.maatwebsite.nl)
 * @author     Maatwebsite <info@maatwebsite.nl>
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class ViewParser {

    /**
     * View file
     * @var string
     */
    public $view;

    /**
     * Data array
     * @var array
     */
    public $data = array();

    /**
     * View merge data
     * @var array
     */
    public $mergeData = array();

    /**
     * Construct the view parser
     * @param Html $reader
     * @return  void
     */
    public function __construct(Html $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Parse the view
     * @param  LaravelExcelWorksheet $sheet
     * @return LaravelExcelWorksheet
     */
    public function parse($sheet)
    {
        $html = View::make($this->getView(), $this->getData(), $this->getMergeData())->render();
        return $this->_loadHTML($sheet, $html);
    }

    /**
     * Load the HTML
     * @param  LaravelExcelWorksheet $sheet
     * @param  string $html
     * @return LaravelExcelWorksheet
     */
    protected function _loadHTML($sheet, $html)
    {
        return $this->reader->load($html, true, $sheet);
    }

    /**
     * Get the view
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get merge data
     * @return array
     */
    public function getMergeData()
    {
        return $this->mergeData;
    }

    /**
     * Set the view
     * @param string $view
     */
    public function setView($view = false)
    {
        if($view)
            $this->view = $view;
    }

    /**
     * Set the data
     * @param array $data
     */
    public function setData($data = array())
    {
        if(!empty($data))
            $this->data = array_merge($this->data, $data);
    }

    /**
     * Set the merge data
     * @param array $mergeData
     */
    public function setMergeData($mergeData = array())
    {
        if(!empty($mergeData))
            $this->mergeData = array_merge($this->mergeData, $mergeData);
    }

}