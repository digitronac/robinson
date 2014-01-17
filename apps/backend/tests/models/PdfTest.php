<?php
namespace Robinson\Tests\Models;
// @codingStandardsIgnoreStart
class PdfTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected $pdfFolder;
    
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
        $this->pdfFolder = \org\bovigo\vfs\vfsStream::setup('package/pdf');
        $this->getDI()->getShared('config')->application->packagePdfPath = \org\bovigo\vfs\vfsStream::url('package/pdf');
    }
    
    public function testCanCreateModel()
    {
        $package = \Robinson\Backend\Models\Package::findFirst();
        $model = new \Robinson\Backend\Models\Pdf($this->getDI()->getShared('fs'), $package, 
            $this->getDI()->getShared('config')->application->packagePdfPath);
        $this->assertInstanceOf('Robinson\Backend\Models\Pdf', $model);
    }
    
    /**
     * @expectedException \Robinson\Backend\Models\Exception
     * @expectedExceptionMessage Pdf does not exist at location: "vfs://package/pdf/1/pdffile-1.pdf"
     */
    public function testCallingGetPdfPathOnNotExistingFileShouldThrowException()
    {
        $package = \Robinson\Backend\Models\Package::findFirst();
        $model = new \Robinson\Backend\Models\Pdf($this->getDI()->getShared('fs'), $package, 
            $this->getDI()->getShared('config')->application->packagePdfPath);
        $model->getPdfFile();   
    }
    
    public function testCallingGetPdfPathOnExistingFileShouldWorkAsExpected()
    {
        $testFs = \org\bovigo\vfs\vfsStream::create(array
        (
            'pdf' => array
            (
                '1' => array
                (
                    'pdffile-1.pdf' => 'content',
                ),
            )
        ), $this->pdfFolder);
        
        // debug!!
        //print_r(\org\bovigo\vfs\vfsStream::inspect(new \org\bovigo\vfs\visitor\vfsStreamStructureVisitor())->getStructure());
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $model = new \Robinson\Backend\Models\Pdf($this->getDI()->getShared('fs'), $package, 
            $this->getDI()->getShared('config')->application->packagePdfPath);
        $this->assertEquals('vfs://package/pdf/1/pdffile-1.pdf', $model->getPdfFile());
    }
    
    /**
     * @expectedException \Robinson\Backend\Models\Exception
     * @expectedExceptionMessage HTML file does not exist at location: "vfs://package/pdf/1/pdffile-1.pdf.html"
     */
    public function testCallingGetHtmlOnNonExistingFileShouldThrowException()
    {
        $package = \Robinson\Backend\Models\Package::findFirst();
        $model = $this->getDI()->get('Robinson\Backend\Models\Pdf', array($this->getDI()->getShared('fs'), $package, 
            $this->getDI()->getShared('config')->application->packagePdfPath));
        $model->getHtmlFile();
    }
   
    public function testCallingGetHtmlOnExistingFileShouldThrowException()
    {
        $testFs = \org\bovigo\vfs\vfsStream::create(array
        (
            'pdf' => array
            (
                '1' => array
                (
                    'pdffile-1.pdf' => 'content',
                    'pdffile-1.pdf.html' => 'html content',
                ),
            )
        ), $this->pdfFolder);
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $model = $this->getDI()->get('Robinson\Backend\Models\Pdf', array($this->getDI()->getShared('fs'), $package, 
            $this->getDI()->getShared('config')->application->packagePdfPath));
        $this->assertEquals('vfs://package/pdf/1/pdffile-1.pdf.html', $model->getHtmlFile());
    }
    
    public function testCallingGetCompiledCommandShouldWorkAsExpected()
    {
        $testFs = \org\bovigo\vfs\vfsStream::create(array
        (
            'pdf' => array
            (
                '1' => array
                (
                    'pdffile-1.pdf' => 'content',
                ),
            )
        ), $this->pdfFolder);
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $model = $this->getDI()->get('Robinson\Backend\Models\Pdf', array($this->getDI()->getShared('fs'), $package, 
            $this->getDI()->getShared('config')->application->packagePdfPath));
        $this->assertEquals('pdftohtml -noframes -s -zoom 3 vfs://package/pdf/1/pdffile-1.pdf vfs://package/pdf/1/pdffile-1.pdf.html 2>&1', 
            $model->getCompiledCommand(\org\bovigo\vfs\vfsStream::url('package/pdf/1/pdffile-1.pdf.html')));
    }
    
}