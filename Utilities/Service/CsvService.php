<?php

namespace Sle\Utilities\Service;

/**
 * Class CsvService
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class CsvService
{

    /**
     * @param string $file Path and filename
     * @param string $delimiter
     * @return array|null
     * @throws \Exception
     */
    public static function loadCvsAsAssocArray($file, $delimiter = ',')
    {
        if (!file_exists($file)) {
            return null;
        }

        $array = $fields = array();
        $i = 0;
        $handle = @fopen($file, 'r');
        if ($handle) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                throw new \Exception('Error: unexpected fgets() fail');
            }
            fclose($handle);
        }

        return $array;
    }

    /**
     * @param string $file Filename with path
     * @param array $array Data
     * @param string $mode
     */
    public static function writeAssocArrayIntoCsvFile($file, array $array, $mode = 'w+')
    {
        $header = false;
        $file = new \SplFileObject($file, $mode);

        foreach ($array as $row) {
            if (false == $header) {
                $file->fputcsv(array_keys($row));
                $file->fputcsv($row); // do the first row of data too
                $header = true;
            } else {
                $file->fputcsv($row);
            }
        }
    }

}
