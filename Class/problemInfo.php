<?php
/**
 * User: imyxz
 * Date: 2017-07-30
 * Time: 10:52
 * Github: https://github.com/imyxz/
 */
require (_Root."vendor/autoload.php");
use PHPHtmlParser\Dom;
class problemExample
{
    public $example_input,$example_output;
}
class problemInfo
{
    public $description,$input,$output,$examples,$hint;
    function generate()
    {
        $examples='';
        foreach($this->examples as $one)
        {
            /** @var problemExample $one */
            $tmp=array(
                '<div class="problem-example">',
                    '<div class="problem-input">',
                        '<div>Input</div>',
                        '<pre>',
                            $one->example_input,
                        '</pre>',
                    '</div>',
                    '<div class="problem-output">',
                        '<div>Output</div>',
                        '<pre>',
                            $one->example_output,
                        '</pre>',
                    '</div>',
                '</div>'
                );
            $examples=$examples. implode('',$tmp);

        }
        $ret=array();
        if(!empty($this->description))
            array_push($ret,
                '<div class="problem-desc">',
                $this->description,
                '</div>');
        if(!empty($this->input))
            array_push($ret,
                '<div class="problem-input-div">',
                '<div>Input</div>',
                $this->input,
                '</div>');
        if(!empty($this->output))
            array_push($ret,
                '<div class="problem-output-div">',
                '<div>Output</div>',
                $this->output,
                '</div>');
        if(!empty($examples))
            array_push($ret,
                '<div class="problem-examples">',
                '<div>Examples</div>',
                $examples,
                '</div>');
        if(!empty($this->hint))
            array_push($ret,
                '<div class="problem-hint">',
                '<div>Hint</div>',
                $this->hint,
                '</div>');

        return implode("",$ret);
    }
}