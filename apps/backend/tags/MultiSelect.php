<?php
namespace Robinson\Backend\Tag;
class MultiSelect extends \Phalcon\Tag
{
    /**
     * Generated html.
     * 
     * @var string 
     */
    protected $html;
    
    /**
     * Constructor of MultiSelect tag sets generated html.
     * 
     * @param string $name        name of element
     * @param array  $data        data to be converted into multi select
     * @param string $emptyOption when set, MultiSelect will generate empty option tag
     */
    public function __construct($name, $data, $emptyOption = '')
    {
        $this->html = $this->multiSelect($name, $data, $emptyOption);
    }
    
    /**
     * Main method of MultiSelect, does all the hard work.
     * 
     * @param string $name        name of element
     * @param array  $data        data to be converted into multi select
     * @param string $emptyOption when set, MultiSelect will generate empty option tag
     * 
     * @return string generated html
     */
    public function multiSelect($name, $data, $emptyOption = '')
    {
        $html = '';
        $html .= '    <select class="form-control" name="' . $name . '" required="required">' . PHP_EOL;
        
        if ($emptyOption)
        {
            $html .= '        <option value="">' . $emptyOption . '</option>' . PHP_EOL;
        }
        
        foreach ($data as $group => $options)
        {
            $html .= '        ' . $this->compileOptGroup($group) . PHP_EOL;
            
            foreach ($options as $value => $option)
            {
                $html .= '            ' . $this->compileOption($name, $value, $option) . PHP_EOL;
            }
        }
        
        $html .= '    </select>' . PHP_EOL;
        
        return $html;
    }
    
    /**
     * Compiles optgroup tag.
     * 
     * @param string $label label to be used
     * 
     * @return string compiled optgroup tag
     */
    protected function compileOptGroup($label)
    {
        return '<optgroup label="' . $this->getEscaperService()->escapeHtmlAttr($label) . '">' . 
                $this->getEscaperService()->escapeHtml($label) . '</optgroup>';
    }
    
    /**
     * Compiles option tag.
     * 
     * @param string     $name   name of element
     * @param array      $value  value of option tag
     * @param string|int $option text of option tag
     * 
     * @return string compiled option tag
     */
    protected function compileOption($name, $value, $option)
    {
        $selected = '';
        
        if ($this->hasValue($name))
        {
            $selected = ($value === $this->getValue($name)) ? 'selected="selected" ' : '';
        }
       
        return '<option ' . $selected . 'value="' . 
                    $this->getEscaperService()->escapeHtmlAttr($value) . '">' . 
                    $this->getEscaperService()->escapeHtml($option) . '</option>';
    }
    
    /**
     * Magic method. Will return html as string.
     * 
     * @return string html
     */
    public function __toString()
    {
        return $this->html;
    }
}