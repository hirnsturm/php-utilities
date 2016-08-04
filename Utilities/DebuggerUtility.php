<?php

namespace Sle\Utilities;

/**
 * Class DebuggerUtility
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class DebuggerUtility
{

    /**
     * @var string
     */
    protected static $type = '';

    /**
     * Enables php error_reporting (E_ALL) and display_errors
     */
    public static function enableErrorReporting()
    {
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
    }

    /**
     * Returns or prints out debugging output
     *
     * @param mixed $data
     * @param string $title
     * @param bool $visible
     * @param bool $return
     * @return string
     */
    public static function debug($data, $title = null, $visible = true, $return = false)
    {
        $outputData = self::renderOutputData($data);

        $output = vsprintf(self::getOutputTemplate(), array(
            (null == $title) ? 'Debug Output' : $title,
            self::$type,
            $outputData,
        ));

        if (true == $return) {
            return self::getDebugCss($visible) . $output;
        } else {
            echo self::getDebugCss($visible) . $output;
        }
    }

    /**
     * @param mixed $data
     * @return mixed|string
     */
    protected static function renderOutputData($data)
    {
        if (is_string($data)) {
            self::$type = 'String';
            return $data;
        } elseif (is_int($data)) {
            self::$type = 'Integer';
            return $data;
        } elseif (is_float($data)) {
            self::$type = 'Float';
            return $data;
        } elseif (is_bool($data)) {
            self::$type = 'Boolean';
            return ($data) ? 'true' : 'false';
        } elseif (is_array($data)) {
            self::$type = 'Array';
            return print_r($data, true);
        } elseif (is_array(json_encode($data))) {
            self::$type = 'JSON';
            return print_r(json_encode($data), true);
        } elseif (is_object($data)) {
            self::$type = 'Object';
            return self::getVarDumpOutput($data);
        } elseif (is_object(json_encode($data, JSON_FORCE_OBJECT))) {
            self::$type = 'JSON Object';
            return self::getVarDumpOutput(json_encode($data, JSON_FORCE_OBJECT));
        } else {
            self::$type = 'Unknown';
            return 'Unsupported data type!';
        }
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected static function getVarDumpOutput($data)
    {
        ob_start();
        var_dump($data);
        $dump = ob_get_contents();
        ob_end_clean();

        return $dump;
    }

    /**
     * @return string
     */
    protected static function getOutputTemplate()
    {
        return '<div class="sle-debuggerutility-debug-wrapper"><div class="sle-debuggerutility-debug-title">%s (TYPE: %s)</div><pre class="sle-debuggerutility-debug-output">%s</pre></div>';
    }

    /**
     * @param bool $visible
     * @return string
     */
    protected static function getDebugCss($visible = true)
    {
        if (false == $visible) {
            return '<style type=\'text/css\'>
				    div.sle-debuggerutility-debug-wrapper {
				        display: none;
				    }
				</style>
		    ';
        } else {
            return '<style type=\'text/css\'>
				    div.sle-debuggerutility-debug-wrapper {
				        display: block;
				        border-left:2px solid red;
				        margin-bottom:10px;
				        background-color:#ddd;
				        padding:0;
				        font-family: Consolas;
				        font-size:12px;
				    }
				    div.sle-debuggerutility-debug-title {
				        padding:10px;
				    }
				    pre.sle-debuggerutility-debug-output {
                        max-height:300px;
                        overflow:auto;
                        color:#eee;
                        background-color:#333;
                        padding:10px 10px;
                        margin:0;
				    }
				</style>
		    ';
        }
    }

}
