<?php
namespace Robinson\Backend\Models;
class Pdf implements \Phalcon\DI\InjectionAwareInterface
{
    /**
     *
     * @var \Phalcon\DI 
     */
    protected $di;
    
    protected $filesystem;
    
    /**
     * Gets DI.
     * 
     * @return \Phalcon\DI
     */
    public function getDI()
    {
        return $this->di;
    }

    /**
     * Sets di.
     * 
     * @param \Phalcon\DI $dependencyInjector di
     * 
     * @return \Robinson\Backend\Models\Pdf
     */
    public function setDI($dependencyInjector)
    {
        $this->di = $dependencyInjector;
        return $this;
    }

    /**
     * Pdf's package
     * 
     * @var \Robinson\Backend\Models\Package
     */
    protected $package;
    
    protected $baseDir;
    
    /**
     * Constructs pdf model.
     * 
     * @param \Symfony\Component\Filesystem\Filesystem $filsystem filesystem
     * @param \Robinson\Backend\Models\Package         $package   pdf's package
     * @param string                                   $baseDir   path to package pdf folder
     */
    public function __construct(\Symfony\Component\Filesystem\Filesystem $filsystem, 
        \Robinson\Backend\Models\Package $package, $baseDir)
    {
        $this->filesystem = $filsystem;
        $this->package = $package;
        $this->baseDir = $baseDir;
    }
    
    /**
     * Returns absolute path to html file generated from pdf.
     * 
     * @return string absolute path to html file
     * 
     * @throws \Robinson\Backend\Models\Exception if file could not be found
     */
    public function getHtmlFile()
    {
        $html = $this->getPdfPath() . '.html';
        if (!$this->filesystem->exists($html))
        {
            // generate .html
            $command = $this->getCompiledCommand($html);
            chmod($this->getPdfPath(), 0777);
            $this->execute($command);
            chmod($html, 0777);
        }
        
        if (!$this->filesystem->exists($html))
        {
            throw new \Robinson\Backend\Models\Exception(sprintf('HTML file does not exist at location: "%s"', $html));
        }
        
        return $html;
    }
    
    /**
     * Returns domdocument of pdf file which is parsed for web display.
     * 
     * @param string $baseUri  uri which will be used as href in <base> html element
     * @param string $version  default 1.0
     * @param string $encoding default UTF-8
     * 
     * @return \DOMDocument
     */
    public function getHtmlDocument($baseUri, $version = '1.0', $encoding = 'UTF-8')
    {
        /* @var $document \DOMDocument */
        $document = $this->getDI()->get('DomDocument', array($version, $encoding));
        $document->strictErrorChecking = false;
        $document->load($this->getHtmlFile());
        echo $document->saveHTML();
        die();
        $base = $document->createElement('base');
        $base->setAttribute('href', $baseUri . '/' . $this->package->getPackageId() . '/');
        $document->getElementsByTagName('head')->item(0)->appendChild($base);
        $document->getElementsByTagName('head')->item(0)->removeChild($document->getElementsByTagName('title')
            ->item(0));
        return $document;
    }
    
    /**
     * Returns absolute file path to pdf file.
     * 
     * @return string
     * 
     * @throws \Robinson\Backend\Models\Exception if pdf could not be located
     */
    public function getPdfFile()
    {
        $pdf = $this->getPdfPath();
        
        if (!$this->filesystem->exists($pdf))
        {
            throw new \Robinson\Backend\Models\Exception(sprintf('Pdf does not exist at location: "%s"', $pdf));
        }
        
        return $pdf;
    }
    
    /**
     * Compiled command to be sent to shell_exec
     * 
     * @param string $htmlFileName converted filename
     * 
     * @return string command to be executed
     */
    public function getCompiledCommand($htmlFileName)
    {
        return sprintf('pdftohtml -noframes -s -zoom 2 "%s" "%s" 2>&1', $this->getPdfPath(), $htmlFileName);
    }
    
    /**
     * Executes given command.
     * 
     * @param string $command command
     * 
     * @return bool
     */
    protected function execute($command)
    {
        $result = shell_exec($command);
        $this->getDI()->getShared('log')->log($result, \Phalcon\Logger::DEBUG);
        return $result;
    }
    
    /**
     * Returns absolute file path to pdf.
     * 
     * @return string
     */
    protected function getPdfPath()
    {
        return $this->baseDir . '/' . $this->package->getPackageId() . '/' . 
            $this->package->getPdf();
    }
}