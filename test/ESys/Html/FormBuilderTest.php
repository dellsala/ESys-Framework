<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Html/FormBuilder.php';


class ESys_Html_FormBuilderTest extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider nameAndValueData
     */
    public function testCreatesTextInput ($name, $value)
    {
        $form = $this->createFormBuilder(array());
        $expectedDom = $this->createBasicInputDom('text', $name, '');
        $actualDom = $this->createDomFromHtmlString($form->input($name));
        $this->assertEquals($expectedDom, $actualDom);
    }


    /**
     * @dataProvider nameAndValueData
     */
    public function testCreatesPrepopulatedTextInput ($name, $value)
    {
        $form = $this->createFormBuilder(array($name=>$value));
        $expectedDom = $this->createBasicInputDom('text', $name, $value);
        $actualDom = $this->createDomFromHtmlString($form->input($name));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testAddsErrorClass ()
    {
        $name = 'email';
        $value = 'john@smith.com';
        $form = $this->createFormBuilder(array($name=>$value));
        $form->flagErrors(array($name));
        $expectedDom = $this->createBasicInputDom(
            'text', $name, $value, array('class'=>'error'));
        $actualDom = $this->createDomFromHtmlString($form->input($name));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testAddsErrorClassToExistingClassAttribute ()
    {
        $name = 'email';
        $value = 'john@smith.com';
        $form = $this->createFormBuilder(array($name=>$value));
        $form->flagErrors(array($name));
        $expectedDom = $this->createBasicInputDom(
            'text', $name, $value, array('class'=>'emailInput error'));
        $actualDom = $this->createDomFromHtmlString(
            $form->input($name, array('class'=>'emailInput')));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testAddsCustomErrorClass ()
    {
        $name = 'email';
        $value = 'john@smith.com';
        $form = $this->createFormBuilder(array($name=>$value));
        $form->flagErrors(array($name), 'badInput');
        $expectedDom = $this->createBasicInputDom(
            'text', $name, $value, array('class'=>'badInput'));
        $actualDom = $this->createDomFromHtmlString(
            $form->input($name));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testCreatesPrepopulatedTextInputWithHashInputData ()
    {
        $inputData = array(
            'name' => array(
                'first' => 'John',
                'last' => 'Smith',
            ),
        );
        $form = $this->createFormBuilder($inputData);
        $expectedDom[] = $this->createBasicInputDom(
            'text', 'name[first]', $inputData['name']['first']);
        $expectedDom[] = $this->createBasicInputDom(
            'text', 'name[last]', $inputData['name']['last']);
        $actualDom[] = $this->createDomFromHtmlString($form->input('name[first]'));
        $actualDom[] = $this->createDomFromHtmlString($form->input('name[last]'));
        $this->assertDomsListsAreEqual($expectedDom, $actualDom);
    }


    /**
     * @dataProvider nameAndValueData
     */
    public function testCreatesCheckbox ($name, $value)
    {
        $form = $this->createFormBuilder(array());
        $expectedDom = $this->createBasicInputDom('checkbox', $name, $value);
        $actualDom = $this->createDomFromHtmlString($form->checkbox($name, $value));
        $this->assertEquals($expectedDom, $actualDom);
    }


    /**
     * @dataProvider nameAndValueData
     */
    public function testCreatesCheckedCheckbox ($name, $value)
    {
        $form = $this->createFormBuilder(array($name => $value));
        $expectedDom = $this->createBasicInputDom(
            'checkbox', $name, $value, array('checked' => 'checked'));
        $actualDom = $this->createDomFromHtmlString($form->checkbox($name, $value));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testCreatesCheckboxWithHashInputData ()
    {
        $name = "group_ids";
        $value = array(
            '10' => 1,
            '15' => 1,
            '20' => 1,
        );
        $unselectedValueName = $name."[99]";
        $form = $this->createFormBuilder(array($name => $value));
        $expectedDom = $this->createBasicInputDom('checkbox', $unselectedValueName, 1);
        $actualDom = $this->createDomFromHtmlString($form->checkbox($unselectedValueName, 1));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testCreatesCheckedCheckboxWithHashInputData ()
    {
        $name = "group_ids";
        $value = array(
            '10' => 1,
            '15' => 1,
            '20' => 1,
        );
        $selectedValueName = $name."[15]";
        $form = $this->createFormBuilder(array($name => $value));
        $expectedDom = $this->createBasicInputDom(
            'checkbox', $selectedValueName, 1, array('checked'=>'checked'));
        $actualDom = $this->createDomFromHtmlString(
            $form->checkbox($selectedValueName, 1));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testCreatesCheckedCheckboxWithArrayInputData ()
    {
        $fieldName = "options[]";
        $selectedValues = array(
            'option a',
            'option b',
            'option c',
        );
        $inputData = array(
            'options' => $selectedValues
        );
        $form = $this->createFormBuilder($inputData);
        $expectedDom = $this->createBasicInputDom(
            'checkbox', $fieldName, $selectedValues[0], array('checked'=>'checked'));
        $actualDom = $this->createDomFromHtmlString(
            $form->checkbox($fieldName, $selectedValues[0]));
        $this->assertEquals($expectedDom, $actualDom,
            'checkbox with a value in the input values should be checked');
        $expectedDom = $this->createBasicInputDom(
            'checkbox', $fieldName, 'unselected option');
        $actualDom = $this->createDomFromHtmlString(
            $form->checkbox($fieldName, 'unselected option'));
        $this->assertEquals($expectedDom, $actualDom,
            'checkbox with a value not in the input values should not be checked');
    }


    /**
     * @dataProvider nameAndValueData
     */
    public function testCreatesCheckedRadio ($name, $value)
    {
        $form = $this->createFormBuilder(array($name => $value));
        $expectedDom = $this->createBasicInputDom(
            'radio', $name, $value, array('checked'=>'checked'));
        $actualDom = $this->createDomFromHtmlString($form->radio($name, $value));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testCreatesSelect ()
    {
        $name = 'country';
        $value = 'CA';
        $optionList = array(
            'United States' => 'US',
            'Canada' => 'CA',
            'Australia' => 'AU',
        );
        $form = $this->createFormBuilder(array($name=>$value));
        $expectedDom = $this->createBasicSelectDom($name, $optionList, $value);
        $actualDom = $this->createDomFromHtmlString($form->select($name, $optionList));
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function testCreatesMultipleSelect ()
    {
        $name = 'country';
        $value = array('CA', 'AU');
        $optionList = array(
            'United States' => 'US',
            'Canada' => 'CA',
            'Australia' => 'AU',
        );
        $form = $this->createFormBuilder(array($name=>$value));
        $expectedDom = $this->createBasicSelectDom(
            $name.'[]', $optionList, $value, array('multiple'=>'multiple')
        );
        $actualDom = $this->createDomFromHtmlString(
            $form->select($name, $optionList, array('multiple'=>true))
        );
        $this->assertEquals($expectedDom, $actualDom);
    }


    public function createBasicSelectDom (
        $name, $optionList, $selectedValue = null, $attributes = array())
    {
        $dom = new DOMDocument();
        $dom->loadHTML('<html>');
        $bodyElement = $dom->documentElement->appendChild(new DOMElement('body'));
        $selectElement = $bodyElement->appendChild(new DOMElement('select'));
        $selectElement->appendChild(new DOMAttr('name', $name));
        foreach ($attributes as $attributeName => $attributeValue) {
            $selectElement->appendChild(new DOMAttr($attributeName, $attributeValue));
        }
        foreach ($optionList as $label => $value) {
            $optionElement = $selectElement->appendChild(new DOMElement('option', $label));
            $optionElement->appendChild(new DOMAttr('value', $value));
            if (is_array($selectedValue) 
                ? in_array($value, $selectedValue)
                : $selectedValue == $value) 
            {
                $optionElement->appendChild(new DOMAttr('selected', 'selected'));
            }
        }
        return $dom;
    }


    protected function createBasicInputDom ($type, $name, $value, $attributes = array())
    {
        $dom = new DOMDocument();
        $dom->loadHTML('<html>');
        $bodyElement = $dom->documentElement->appendChild(new DOMElement('body'));
        $inputElement = $bodyElement->appendChild(new DOMElement('input'));
        $inputElement->appendChild(new DOMAttr('type', $type));
        $inputElement->appendChild(new DOMAttr('name', $name));
        $inputElement->appendChild(new DOMAttr('value', $value));
        foreach ($attributes as $attributeName => $attributeValue) {
            $inputElement->appendChild(new DOMAttr($attributeName, $attributeValue));
        }
        return $dom;
    }


    public function createDomFromHtmlString ($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        return $dom;
    }


    public function nameAndValueData ()
    {
        return array(
            array(
                'first_name',
                "Joe",
            ),
            array(
                'some_utf8_data',
                "D’Angel",
            ),
            array(
                'data_with_html_chars',
                "John & Jill went > up the hill <",
            ),
        );
    }


    protected function createFormBuilder ($data)
    {
        $form = new ESys_Html_FormBuilder($data);
        $form->setAutoEcho(false);
        return $form;
    }


    protected function assertDomsListsAreEqual ($expectedList, $actualList)
    {
        $this->assertEquals(
            count($expectedList), count($actualList), "Dom lists are not the same size.");
        for ($i=0; $i < count($expectedList); $i++) {
            $this->assertEquals(
                $expectedList[$i], $actualList[$i], "DOM elements at position {$i} do not match.");
        }
    }



}