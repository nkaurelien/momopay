<?php
/**
 * Created by PhpStorm.
 * User: nkaurelien
 * Date: 20/06/19
 * Time: 11:26
 */

namespace Nkaurelien\Momopay\Helpers;


class JSON
{

    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * @param string $json    JSON data to parse
     * @param bool $assoc     When true, returned objects will be converted
     *                        into associative arrays.
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options.
     *
     * @return mixed
     * @throws \InvalidArgumentException if the JSON cannot be decoded.
     * @link http://www.php.net/manual/en/function.json-decode.php
     */
    public static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        $data = \json_decode($json, $assoc, $depth, $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
              'json_decode error: ' . json_last_error_msg() . PHP_EOL . ', ---- The JSON -->' . PHP_EOL . $json
            );
        }

        return $data;
    }

    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param mixed $value   The value being encoded
     * @param int    $options JSON encode option bitmask
     * @param int    $depth   Set the maximum depth. Must be greater than zero.
     *
     * @return string
     * @throws \InvalidArgumentException if the JSON cannot be encoded.
     * @link http://www.php.net/manual/en/function.json-encode.php
     */
    public static function encode($value, $options = 0, $depth = 512)
    {

//        $options |= JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        $json = \json_encode($value, $options, $depth);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
              'json_encode error: ' . json_last_error_msg() . PHP_EOL . ', ---- The JSON -->' . PHP_EOL . $json
            );
        }

        return $json;
    }


  public static function objToArray($value, $options = 0, $depth = 512)
  {

    $json = self::encode($value);

    return self::decode($json, true);
  }


}