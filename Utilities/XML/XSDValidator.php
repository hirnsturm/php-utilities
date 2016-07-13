<?php

namespace Sle\Utilities\XML;

use \DOMDocument;
use \Exception;

/**
 * XMLValidator
 *
 * This class offers validation for XML by a given XSD schema
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class XSDValidator
{

    private $xml = null;
    private $xsd = null;
    private $errors = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        libxml_use_internal_errors(true);
    }


    /**
     *
     * @param string $xml XML as string
     * @return $this
     */
    public function setXML($xml)
    {
        $this->xml = new DOMDocument();
        $this->xml->loadXML($xml);

        return $this;
    }

    /**
     *
     * @param string $filename
     * @return $this
     */
    public function setXMLFile($filename)
    {
        $this->xml = new DOMDocument();
        $this->xml->load($filename);

        return $this;
    }

    /**
     *
     * @param string $filename
     * @return $this
     */
    public function setXSDFile($filename)
    {
        $this->xsd = $filename;

        return $this;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Validate XML
     *
     * @return boolean
     * @throws Exception
     */
    public function isValid()
    {
        if (isset($this->xml) && isset($this->xsd)) {
            return $this->validate();
        } else {
            throw new Exception('No XML or XSD given!');
        }
    }

    /**
     * Do validate by XSD schema
     *
     * @return boolean
     */
    private function validate()
    {
        if ($this->xml->schemaValidate($this->xsd)) {
            return true;
        } else {
            $this->addErrors(libxml_get_errors());
            return false;
        }
    }

    /**
     * @param $errors
     */
    private function addErrors($errors)
    {
        foreach ($errors as $error) {
            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $this->errors[] = array(
                        'type' => 'WARNING',
                        'code' => $error->code,
                        'message' => $error->message,
                        'file' => $error->file,
                        'line' => $error->line
                    );
                    break;
                case LIBXML_ERR_ERROR:
                    $this->errors[] = array(
                        'type' => 'ERROR',
                        'code' => $error->code,
                        'message' => $error->message,
                        'file' => $error->file,
                        'line' => $error->line
                    );
                    break;
                case LIBXML_ERR_FATAL:
                    $this->errors[] = array(
                        'type' => 'FATAL ERROR',
                        'code' => $error->code,
                        'message' => $error->message,
                        'file' => $error->file,
                        'line' => $error->line
                    );
                    break;
                default:
                    $this->errors[] = array(
                        'type' => 'UNKNOWN ERROR',
                        'message' => $error->message,
                        'file' => $error->file,
                        'line' => $error->line
                    );
            }
        }
    }

}
