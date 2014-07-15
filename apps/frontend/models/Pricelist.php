<?php
namespace Robinson\Frontend\Model;

/**
 * Class Pricelist.
 *
 * @package Robinson\Frontend\Model
 */
class Pricelist extends \Phalcon\Mvc\Model
{
    /**
     * Initialization.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('pricelists');
    }

    /**
     * Filename getter method.
     *
     * @return \Phalcon\Mvc\Model\Resultset
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Uri to pdf.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->getDI()->get('config')->application->pricelistPdfWebPath . '/' . rawurldecode($this->filename);
    }
}
