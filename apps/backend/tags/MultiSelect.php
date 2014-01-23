<?php
namespace Robinson\Backend\Tag;
class MultiSelect extends \Phalcon\Tag
{
    protected $html;
    
    public function __construct($name, $data, $emptyOption = false, $selectedValue = false)
    {
        $this->html = $this->multiSelect($name, $data, $emptyOption, $selectedValue);
    }
    /**
     * Main method of this tag.
     * 
     * @return html
     */
    public function multiSelect($name, $data, $emptyOption = false, $selectedValue = false)
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
                $html .= '            ' . $this->compileOption($value, $option, $selectedValue) . PHP_EOL;
            }
        }
        
        $html .= '    </select>' . PHP_EOL;
        
        return $html;
    }
    
    protected function compileOptGroup($label)
    {
        return '<optgroup label="' . $this->getEscaperService()->escapeHtmlAttr($label) . '">' . 
                $this->getEscaperService()->escapeHtml($label) . '</optgroup>';
    }
    
    protected function compileOption($value, $option, $selectedValue = false)
    {
        $selected = ($value === $selectedValue) ? 'selected="selected" ' : '';
        return '<option ' . $selected . 'value="' . 
                    $this->getEscaperService()->escapeHtmlAttr($value) . '">' . 
                    $this->getEscaperService()->escapeHtml($option) . '</option>';
    }
    
    public function __toString()
    {
        return $this->html;
    }
}