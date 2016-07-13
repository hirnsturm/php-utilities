<?php

namespace Sle\Utilities\XML;

/**
 * XMLUtility
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class XMLUtility
{

    /**
     * Returns an XML that was builded by an given array structure
     *
     * @deprecated since version 1.5.0
     *
     * @param array $xmlStructure
     * @param boolean $formated
     * @return xml
     */
    public static function buildXMLByStructureArray(array $xmlStructure, $formated = true)
    {
        return self::array2xml($xmlStructure, $formated);
    }

    /**
     * Returns an XML that was builded by an given array structure
     *
     * @param array $xmlStructure
     * @param boolean $formated
     * @return xml
     */
    public static function array2xml(array $xmlStructure, $formated = true)
    {
        $result = null;
        $eol = null;
        $domDoc = new \DOMDocument();

        if ($formated) {
            $domDoc->formatOutput = true;
            $eol = PHP_EOL;
        }

        if (count($xmlStructure) == 1) {
            $result = $domDoc->saveXML(self::createXmlElementWithAttributesAndChildren($domDoc, $xmlStructure));
        } else {
            foreach ($xmlStructure as $key => $sub) {
                $result .= $domDoc->saveXML(self::createXmlElementWithAttributesAndChildren($domDoc,
                        array($key => $sub))) . $eol;
            }
        }

        return $result;
    }

    /**
     * @param \DOMDocument $domDoc
     * @param array $array
     * @return \DOMElement
     */
    public static function createXmlElementWithAttributesAndChildren(\DOMDocument $domDoc, array $array)
    {
        $elementBefore = null;

        foreach ($array as $key => $val) {
            $element = $domDoc->createElement($key);

            // set attributes
            if (!is_array($val)) {
                $nodeValue = $domDoc->createTextNode($val);
                $element->appendChild($nodeValue);
            } elseif (isset($val['value']) && !empty($val['value'])) {
                // set attributes
                if (isset($val['attr']) && !empty($val['attr'])) {
                    foreach ($val['attr'] as $attrKey => $attrVal) {
                        $element->setAttribute($attrKey, $attrVal);
                    }
                }
                // For backward compatibility
                $nodeValue = $domDoc->createTextNode($val['value']);
                $element->appendChild($nodeValue);
            } else {
                // set attributes
                if (isset($val['attr']) && !empty($val['attr'])) {
                    foreach ($val['attr'] as $attrKey => $attrVal) {
                        $element->setAttribute($attrKey, $attrVal);
                    }
                }
                // set sub children elements
                if (isset($val['children']) && !empty($val['children'])) {
                    foreach ($val['children'] as $childKey => $childVal) {
                        $element->appendChild(self::createXmlElementWithAttributesAndChildren($domDoc,
                            array($childKey => $childVal)));
                    }
                }
            }

            $elementBefore = $element;
        }

        return $element;
    }

    /**
     * Removes element from DOMDocument, if it is not in keep list
     *
     * @param \DOMDocument $dom
     * @param string $tagName
     * @param string $attr
     * @param array $keep
     * @return \DOMDocument
     */
    public static function removeElementsFromDomByAttributes(\DOMDocument $dom, $tagName, $attr, array $keep)
    {
        $elements = $dom->getElementsByTagName($tagName);

        for ($i = 0; $i < $elements->length; $i++) {
            $node = $elements->item($i);
            if (!in_array($node->getAttribute($attr), $keep)) {
                $node->parentNode->removeChild($node);
                $i--;
            }
        }

        return $dom;
    }

    /**
     * Converts an XML-String into an Array
     *
     * @param string $xmlstr
     * @return array
     */
    public function xmlStr2Array($xmlstr)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xmlstr);
        $root = $doc->documentElement;
        $output = $this->domNode2Array($root);
        $output['root'] = $root->tagName;

        return $output;
    }

    /**
     * @param \DOMDocument $node
     * @return array|string
     */
    private function domNode2Array(\DOMDocument  $node)
    {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domNode2Array($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } elseif ($v || $v === '0') {
                        $output = (string)$v;
                    }
                }
                if ($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
                    $output = array('content' => $output); //Change output into an array.
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string)$attrNode->value;
                        }
                        $output['attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != 'attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }

        return $output;
    }

    /**
     * Validates a XML-String by libxml_use_internal_errors
     *
     * @param string $xmlString
     * @return mixed Returns an array with LibXMLError objects if there are any errors in the buffer, or an empty array otherwise. (libxml_get_errors)
     */
    public static function validateXmlString($xmlString)
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadXML($xmlString);

        return libxml_get_errors();
    }

}
