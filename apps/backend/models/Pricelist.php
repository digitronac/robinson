<?php
namespace Robinson\Backend\Models;

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
        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                array(
                    'beforeValidationOnCreate' => array(
                        'field' => 'createdAt',
                        'format' => 'Y-m-d H:i:s',
                    ),
                )
            )
        );
    }

    /**
     * Creates new pricelist record from uploaded file.
     *
     * @param \Phalcon\Http\Request\File $file uploaded file
     *
     * @throws \Exception if file aready exists
     *
     * @return bool
     */
    public function createFromUploadedFile(\Phalcon\Http\Request\File $file)
    {
        $this->filename = $file->getName();
        /** @var \Symfony\Component\Filesystem\Filesystem $filesystem */
        $filesystem = $this->getDI()->get('Symfony\Component\Filesystem\Filesystem');
        if ($filesystem->exists($this->getFilepath())) {
            throw new \Exception('Pricelist ' . $file->getName() . ' already exists.');
        }
        $file->moveTo($this->getFilepath());
        return $this->create();
    }

    /**
     * OnAfterDelete.
     *
     * @return void
     */
    public function afterDelete()
    {
        /** @var \Symfony\Component\Filesystem\Filesystem $filesystem */
        $filesystem = $this->getDI()->get('Symfony\Component\Filesystem\Filesystem');
        $filesystem->remove($this->getFilepath());
    }

    /**
     * Gets link to file.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->getDI()->get('config')->application->pricelistPdfWebPath . '/' . rawurlencode($this->filename);
    }

    /**
     * Filepath getter method.
     *
     * @return string
     */
    public function getFilepath()
    {
        return $this->getDI()->get('config')->application->pricelistPdfPath . '/' . $this->filename;
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
     * PricelistId getter method.
     *
     * @return int
     */
    public function getPricelistId()
    {
        return (int) $this->pricelistId;
    }
}
