<?php


/**
 * @package ESys
 */
class ESys_Text
{

    /**
     * An enhanced version of utf8_decode() that detects a few common
     * Unicode characters that aren't part of Latin-1, and translates
     * them into Latin-1 approximations of those characters.
     *
     * The translated characters include smart quotes, elipses,
     * em/en dashes, and bullet points.
     *
     * @param string $string
     * @return string
     */
    public static function utf8ToLatin1 ($string)
    {
        $specialChars = array(
            "\xe2\x80\x98", // left single quote
            "\xe2\x80\x99", // right single quote
            "\xe2\x80\x9c", // left double quote
            "\xe2\x80\x9d", // right double quote
            "\xe2\x80\x93", // long dash
            "\xe2\x80\x94", // em dash
            "\xe2\x80\xa6", // elipses
            "\xe2\x80\xa2",  // bullet point
            "\xe2\x84\xA2",  // trade mark sign
        );
        $specialCharsAscii = array(
            "'",
            "'",
            '"',
            '"',
            '-',
            '--',
            '...',
            '*',
            'TM',
        );
        $string = str_replace($specialChars , $specialCharsAscii , $string);
        $string = utf8_decode($string);
        return $string;
    }


    /**
     * @param array $array
     * @return array
     */
    public static function utf8ToLatin1Recursive ($array)
    {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $array[$key] = ESys_Text::utf8ToLatin1Recursive($val);
            } else {
                $array[$key] = ESys_Text::utf8ToLatin1($val);
            }
        }
        return $array;
    }



    /**
     * @param array $array
     * @return array
     */
    public static function latin1ToUtf8Recursive ($array)
    {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $array[$key] = ESys_Text::latin1ToUtf8Recursive($val);
            } else {
                $array[$key] = utf8_encode($val);
            }
        }
        return $array;
    }


    /**
     * Converting a string to UTF-7 (RFC 2152)
     *
     * Code taken from PEAR::Validate
     *
     * @param   string  $string string to be converted
     * @return  string  converted string
     */
    public static function toUtf7($string) 
    {
        $return = '';
        $utf7 = array(
                        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                        'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
                        'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g',
                        'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
                        's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2',
                        '3', '4', '5', '6', '7', '8', '9', '+', ','
                    );

        $state = 0;
        if (!empty($string)) {
            $i = 0;
            while ($i <= strlen($string)) {
                $char = substr($string, $i, 1);
                if ($state == 0) {
                    if ((ord($char) >= 0x7F) || (ord($char) <= 0x1F)) {
                        if ($char) {
                            $return .= '&';
                        }
                        $state = 1;
                    } elseif ($char == '&') {
                        $return .= '&-';
                    } else {
                        $return .= $char;
                    }
                } elseif (($i == strlen($string) || 
                            !((ord($char) >= 0x7F)) || (ord($char) <= 0x1F))) {
                    if ($state != 1) {
                        if (ord($char) > 64) {
                            $return .= '';
                        } else {
                            $return .= $utf7[ord($char)];
                        }
                    }
                    $return .= '-';
                    $state = 0;
                } else {
                    switch($state) {
                        case 1:
                            $return .= $utf7[ord($char) >> 2];
                            $residue = (ord($char) & 0x03) << 4;
                            $state = 2;
                            break;
                        case 2:
                            $return .= $utf7[$residue | (ord($char) >> 4)];
                            $residue = (ord($char) & 0x0F) << 2;
                            $state = 3;
                            break;
                        case 3:
                            $return .= $utf7[$residue | (ord($char) >> 6)];
                            $return .= $utf7[ord($char) & 0x3F];
                            $state = 1;
                            break;
                    }
                }
                $i++;
            }
            return $return;
        }
        return '';
    }


}

