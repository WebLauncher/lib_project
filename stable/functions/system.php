<?php
/**
 * System helper functions
 */

/**
 * Display array formated
 * @param $arr
 * @param $return if true return value instead of writing it
 */
function echopre($arr, $return = false)
{
    $text = '<div align="left" style="font-size:-1;"><pre class="debug">';
    $text .= print_r($arr, true);
    $text .= '</pre></div>';
    if ($return)
        return $text;
    echo $text;
}

/**
 * Return formated display for array
 * @param $arr
 */
function echopre_r($arr)
{
    return echopre($arr, true);
}

/**
 * Echo and return value
 * @param $str
 */
function echo_r($str)
{
    ob_start();
    echo $str;
    $value = ob_get_contents();
    ob_end_clean();
    return $value;
}

/**
 * The system error handler
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 * @param $errcontext
 */
function the_error_handler($errno = '', $errstr = '', $errfile = '', $errline = '', $errcontext = '')
{
    if (defined('PHP_SAPI') && PHP_SAPI == 'cli') {
        echo "($errno) $errstr ([$errline] $errfile)\n";
    } else {
        $errortype = array(
            1 => array(
                'code' => '_ERR_ERROR',
                'name' => 'Error',
                'back' => 'ffcccc',
                'color' => '990000',
                'line' => '660000'
            ),
            2 => array(
                'code' => '_ERR_WARNING',
                'name' => 'Warning',
                'back' => 'FFD5BF',
                'color' => 'CC3300',
                'line' => 'FF9966'
            ),
            4 => array(
                'code' => '_ERR_PARSE',
                'name' => 'Parse Error',
                'back' => 'D7EBFF',
                'color' => '003366',
                'line' => '71B8FF'
            ),
            8 => array(
                'code' => '_ERR_NOTICE',
                'name' => 'Notice',
                'back' => 'EAEAEA',
                'color' => '333333',
                'line' => '999999'
            ),
            16 => array(
                'code' => '_ERR_CORE_ERROR',
                'name' => 'Core Error',
                'back' => 'ffcccc',
                'color' => '990000',
                'line' => '660000'
            ),
            32 => array(
                'code' => '_ERR_CORE_WARNING',
                'name' => 'Core Warning',
                'back' => 'FFD5BF',
                'color' => 'CC3300',
                'line' => 'FF9966'
            ),
            64 => array(
                'code' => '_ERR_COMPILE_ERROR',
                'name' => 'Compile Error',
                'back' => 'ffcccc',
                'color' => '990000',
                'line' => '660000'
            ),
            128 => array(
                'code' => '_ERR_COMPILE_WARNING',
                'name' => 'Compile Warning',
                'back' => 'FFD5BF',
                'color' => 'CC3300',
                'line' => 'FF9966'
            ),
            256 => array(
                'code' => '_ERR_USER_ERROR',
                'name' => 'User Error',
                'back' => 'FFB7B7', //ffcccc',
                'color' => '333333', //990000',
                'line' => '660000'
            ),
            512 => array(
                'code' => '_ERR_USER_WARNING',
                'name' => 'User Warning',
                'back' => 'FFD5BF',
                'color' => 'CC3300',
                'line' => 'FF9966'
            ),
            1024 => array(
                'code' => '_ERR_USER_NOTICE',
                'name' => 'User Notice',
                'back' => 'EAEAEA',
                'color' => '333333',
                'line' => '999999'
            )
        );
        $default = array(
            'code' => '_ERR_UNDEFINED',
            'name' => 'Error Undefined',
            'back' => 'EAEAEA',
            'color' => '333333',
            'line' => '999999'
        );

        $raw = debug_backtrace();
        $hash = base64_encode(microtime());
        $backtrace = '<div id="_php_error_' . $hash . '" style="display:none;"><table cellspacing="0" cellpadding="0" width="100%" style="color:#' . isset_or($errortype[$errno]['color'], $default['color']) . ';background:#' . isset_or($errortype[$errno]['color'], $default['color']) . ';">
        	<tr style="background:#' . isset_or($errortype[$errno]['color'], $default['color']) . '; color:#' . isset_or($errortype[$errno]['back'], $default['back']) . ';">
        		<td>Trace</td><td>File</td><td>Line</td><td>Function</td>
        	</tr>
        ';
        $no = 0;
        $method = '';
        if (isset($raw[1]))
            $method = $raw[1]['function'];
        $raw = array_reverse($raw);
        foreach ($raw as $entry) {
            if (isset_or($entry['function']) != 'the_error_handler') {
                $backtrace .= '<tr style="background:#' . isset_or($errortype[$errno]['back'], $default['back']) . ';"><td>' . $no . '</td>';
                $backtrace .= "<td>" . isset_or($entry['file']) . "</td><td>" . isset_or($entry['line']) . "</td>";
                $backtrace .= "<td><b>" . isset_or($entry['function']) . "</b></td>";
                $no++;
                $backtrace .= '</tr>';
            }
        }
        $backtrace .= '</table></div>';

        $output = '';
        if (error_reporting() & $errno) {
            $output .= '<div>';
            $output .= '<table cellspacing="0" cellpadding="0" width="100%" style="font-family:arial,lucida console,courier new;font-size:12px;color:#' . isset_or($errortype[$errno]['color'], $default['color']) . ';background-color:#' . isset_or($errortype[$errno]['back'], $default['back']) . ';margin:0px;padding:0px;border:1px solid #' . isset_or($errortype[$errno]['line'], $default['line']) . ';margin-bottom:2px;">';
            $output .= '<tr><td style="width:30%;vertical-align:top;padding:2px;">';
            $output .= '<table cellspacing="0" cellpadding="0" width="100%" style="color:#' . isset_or($errortype[$errno]['color'], $default['color']) . ';">';
            $output .= '<tr>';
            $output .= '<td><b>PHP ' . isset_or($errortype[$errno]['name'], $default['name']) . '</b> No:' . $errno . ' <br/><small>[' . isset_or($errortype[$errno]['code'], $default['code']) . ']</small></td>';
            $output .= '';
            $output .= '';
            $output .= '<td colspan=2 style="color:#' . isset_or($errortype[$errno]['color'], $default['color']) . ';padding:2px 4px ;background:#fff;"><b>' . $errstr . '</b></td>';
            $output .= '';
            $output .= '';
            $output .= '<td style="padding:2px;">' . 'File (Line)' . ': ' . $errfile . ' (' . $errline . ')' . ($method ? '<br/>Function: ' . $method : '') . ' (<a href="#" onclick="document.getElementById(\'_php_error_' . $hash . '\').style.display = \'block\';return false;">show trace</a>)</td>';
            $output .= '</tr>';
            $output .= '</table>';
            $output .= '</td></tr><tr><td style="vertical-align:top;padding:2px;">';
            $output .= '' . $backtrace;
            $output .= '</td></tr>';
            $output .= '</table>';
            $output .= '</div>';
            $output = trim($output);
            $output = str_replace("\n", "", $output);
            $output = str_replace("\r", "", $output);
            global $page;
            if (isset_or($page->debug))
                echo $output;

            if (!is_writable(isset_or($page->error_log_path) . 'errs.log.csv')) {
                $handle = @fopen(isset_or($page->error_log_path) . 'errs.log.csv', 'w');
                @fclose($handle);
            }
            if (is_writable(isset_or($page->error_log_path) . 'errs.log.csv')) {
                $handle = fopen(isset_or($page->error_log_path) . 'errs.log.csv', 'a');
                fwrite($handle, '\'' . print_r($errortype[$errno], true) . "','$errfile','$errline','$errstr','" . date('Y-m-d\',\'H:i:s') . "'\n");
                fclose($handle);
            }
        }
    }
}

set_error_handler('the_error_handler');

/**
 * Register shutdown method
 */
function the_register_shutdown()
{
    global $page;
    @session_write_close();
    # Getting last error
    $error = error_get_last();
    if (isset($page) && $page->debug)
        //print_r($error);
        # Checking if last error is a fatal error
        if (($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR)) {

            if ($page->error_log_email) {
                $page->import('library', 'mail');
                $mail = new Mail();
                $message = 'Found error: <br/>' . echopre_r($error);
                $mail->send_mail($page->error_log_email, 'Fatal error on server ' . $page::$hostname, $message, $page->error_log_email, 'Fatal Errors Sender');
            }
            echo 'Sorry, a serious error has occured in the system.';
            // . $error['file'];
        }
}

register_shutdown_function('the_register_shutdown');

/**
 * The system exception handler
 * @param $exception
 */
function the_exception_handler($exception)
{
    echo "Uncaught exception: ", $exception->getMessage(), "\n";
}

set_exception_handler('the_register_shutdown');

/**
 * Check if isset else return alternate
 * @param $check
 * @param $alternate
 */
function isset_or(&$check, $alternate = NULL)
{
    return (isset($check)) ? $check : $alternate;
}

/**
 * Clean database insertion fields
 * @param $string
 */
function sat($string)
{
    global $dal;
    return $dal->db->stringEscape(stripslashes(addslashes(trim($string))));
}

/**
 * Serialize array
 * @param $array
 */
function ser($array)
{
    if (is_array($array))
        return @serialize($array);
    return $array;
}

/**
 * Unserialize array
 * @param $string
 */
function unser($string)
{
    if (is_string($string)) {
        return @unserialize($string);
    }
    return $string;
}

/**
 * validate numeric string structure
 *
 * @return true if ok false if not
 *
 * @param $value
 *
 * @author silviu
 */
function checkNumericString($value)
{
    $exp = '^[0-9]*$';
    if (!eregi($exp, $value)) {
        return false;
    } else {
        return true;
    }//end if (!eregi($exp, $value))
}//end function checkNumericString($value)

/**
 * transform the date to the age of the user
 *
 * @return the number of years or unknown
 *
 * @param $birthday the data used to calculate the age
 *
 * @author alex
 */
function date2birthday($birthday)
{
    if (($birthday != '0000-00-00') AND ($birthday != '0000-00-00 00:00:00') AND ($birthday != '')) {
        list($year, $month, $day) = explode("-", $birthday);
        $year_diff = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff = date("d") - $day;
        if ($day_diff < 0 || $month_diff < 0) {
            $year_diff--;
        }//end if ($day_diff < 0 || $month_diff < 0)
        return $year_diff;
    } else {
        return 'unknown';
    }//end if (($birthday != '0000-00-00') AND ($birthday != '0000-00-00 00:00:00')
    // AND ($birthday != ''))
}//end function date2birthday ($birthday)

/**
 * Encrypt data
 * @param string $plain_text
 */
function encrypt($plain_text)
{
    global $page;
    $key = $page->crypt_key;

    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, utf8_encode($plain_text), MCRYPT_MODE_ECB, $iv);
    return urlencode(base64_encode($encrypted_string));
}

/**
 * Decrypt data
 * @param string $crypted_text
 */
function decrypt($crypted_text)
{
    global $page;
    $key = $page->crypt_key;
    $crypted_text = base64_decode(urldecode($crypted_text));

    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $crypted_text, MCRYPT_MODE_ECB, $iv);
    return trim($decrypted_string);
}

/**
 * Translate function
 * @param string $content
 * @param int $language_id
 * @param string $tags
 * @param array $params
 */
function tr($content, $language_id = 0, $tags = 'site', $params = array())
{
    if ($content != "") {
        global $page;
        $language = $language_id ? $language_id : isset($page->session['language_id']) ? $page->session['language_id'] : 0;
        $quer = $content;

        if ($page && $page->db_conn_enabled && $page->libraries_settings['wbl_locale']['type'] == 'db') {
            $hostname = '';
            if ($page->admin)
                $key = sha1($content);
            else {
                $hostname = System::get_hostname();
                $key = sha1($content . '_' . $hostname);
            }
            if (!isset($page->translations))
                $page->translations = array();
            if (isset($page->translations[$key])) {
                $content = $page->translations[$key];
            } else {
                $query = "select " . $page->db_conn->tables['tbl_x_conf_texts'] . ".id," . $page->db_conn->tables['tbl_x_conf_texts'] . ".tags," . $page->db_conn->tables['tbl_x_conf_texts'] . ".query," . $page->db_conn->tables['tbl_x_conf_texts'] . ".value," . $page->db_conn->tables['tbl_x_conf_translations'] . ".value as translation from " . $page->db_conn->tables['tbl_x_conf_texts'] . " left join " . $page->db_conn->tables['tbl_x_conf_translations'] . " on " . $page->db_conn->tables['tbl_x_conf_translations'] . ".text_id=" . $page->db_conn->tables['tbl_x_conf_texts'] . ".id where " . $page->db_conn->tables['tbl_x_conf_texts'] . ".key=" . sat($key) . "";

                $text = $page->db_conn->getRow($query);
                if ($text) {
                    $text['tags'] = explode(" ", $text['tags']);
                    if (sat($text['query']) != sat($quer))
                        $page->db_conn->query("update " . $page->db_conn->tables['tbl_x_conf_texts'] . " set query=" . sat($quer) . " where id=" . $text['id']);
                    if (count(array_diff($text['tags'], explode(" ", $tags)))) {
                        $tags = implode(" ", array_merge(array_diff(explode(" ", $tags), $text['tags']), $text['tags']));

                        $page->db_conn->query("update " . $page->db_conn->tables['tbl_x_conf_texts'] . " set tags=" . sat($tags) . " where id=" . $text['id']);
                    }
                    if ($language > 0 && isset_or($text['translation']))
                        $text['value'] = $text['translation'];
                    $content = $text['value'];
                } else {
                    $query = "INSERT INTO `" . $page->db_conn->tables['tbl_x_conf_texts'] . "` (
				`id` ,
				`key` ,
				`value`,
				`query`,
				`tags`,
				`hostname`,
				`admin`
				)
				VALUES (
				NULL , " . sat($key) . ", " . sat($content) . "," . sat($quer) . "," . sat($tags) . "," . sat($hostname) . "," . $page->admin . "
				);";
                    $text = $page->db_conn->query($query);
                }
                $page->translations[$key] = $content;
            }
        } else {
            // set plural parameters 'plural' and 'count'.
            if (isset($params['plural'])) {
                $plural = $params['plural'];
                unset($params['plural']);
                // set count
                if (isset($params['count'])) {
                    $count = $params['count'];
                    unset($params['count']);
                }
            }
            // get domain param
            if (isset($params['domain'])) {
                $domain = $params['domain'];
                unset($params['domain']);
            } else {
                $domain = null;
            }
            // use plural if required parameters are set
            if (isset($count) && isset($plural)) {
                // use specified textdomain if available
                if (isset($domain)) {
                    $content = dngettext($domain, $content, $plural, $count);
                } else {
                    $content = ngettext($content, $plural, $count);
                }
            } else {
                // use specified textdomain if available
                if (isset($domain)) {
                    $content = dgettext($domain, $content);
                } else {
                    $content = gettext($content);
                }
            }

        }
        // run strarg if there are parameters
        if (count($params)) {
            $tr = array();
            $p = 0;
            foreach ($params as $aarg) {
                $tr['%' . ++$p] = $aarg;
            }
            $content = strtr($content, $tr);
        }
    }
    return $content;
}

/**
 * Execute and wait a system command
 * @param string $path
 * @param string $exe
 * @param string $args
 */
function execute_wait($path, $exe, $args = "")
{
    if (is_file($path . $exe)) {
        $oldpath = getcwd();
        chdir($path);

        if (substr(php_uname(), 0, 7) == "Windows") {
            $cmd = $path . $exe;
            $cmdline = "cmd /c $cmd " . $args;
            $WshShell = new COM("WScript.Shell");
            $oExec = $WshShell->Run($cmdline, 0, true);
        } else {
            exec("./" . $exe . " " . $args);
        }
        chdir($oldpath);
    }
}

/**
 * Current date and time formated a database
 */
function nowfull()
{
    return date("Y-m-d H:i:s");
}

/**
 * Current date formated as database
 */
function now()
{
    return date("Y-m-d");
}

/**
 * Get number of days
 * @param string $start
 * @param string $end
 */
function getNoDays($start, $end)
{
    // Vars
    $day = 86400;
    // Day in seconds
    $format = 'Y-m-d';
    // Output format (see PHP date funciton)
    $sTime = strtotime($start);
    // Start as time
    $eTime = strtotime($end);
    // End as time
    $numDays = round(($sTime - $eTime) / $day) + 1;
    return intval($numDays);
}

/**
 * Parse date from string
 * @param string $str
 * @param string $format
 * @param string $target_format
 */
function parse_date($str, $format = 'Y-m-d', $target_format = 'Y-m-d')
{
    $date = date_parse_from_format($format, $str);
    return date($target_format, mktime(isset_or($date['hour'], 0), isset_or($date['minute'], 0), isset_or($date['second'], 0), isset_or($date['month'], 0), isset_or($date['day'], 0), isset_or($date['year'], 0)));
}

/**
 * Get current page url
 * @return String URL
 */
function page_url()
{
    $pageURL = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {$pageURL .= 's';
    }
    $pageURL .= '://';
    $pageURL .= isset_or($_SERVER['SERVER_NAME']) . isset_or($_SERVER['REQUEST_URI']);
    if ($url_parts = parse_url($pageURL)) {
        if (isset($url_parts['path'])) {
            if (substr($url_parts['path'], strlen($url_parts['path']) - 1, 1) != '/')
                $url_parts['path'] .= '/';
            $pageURL = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . (isset_or($url_parts['query']) ? '?' . $url_parts['query'] : '');
        }
    }
    return $pageURL;
}

/**
 * Get hostname
 */
function get_hostname()
{
    $server_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (defined("RHOST") ? RHOST : '');
    if (strpos($server_host, 'www.') == 0)
        $server_host = str_replace('www.', '', $server_host);
    return $server_host;
}

/**
 * Generate seo link
 * @param string $input
 * @param string $replace
 * @param bool $remove_words
 * @param array $words_array
 */
function generate_seo_link($input, $replace = '-', $remove_words = true, $words_array = array())
{
    //make it lowercase, remove punctuation, remove multiple/leading/ending spaces
    $return = trim(str_replace(' +', ' ', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($input))));

    //remove words, if not helpful to seo
    //i like my defaults list in remove_words(), so I wont pass that array
    if ($remove_words) { $return = remove_words($return, $replace, $words_array);
    }

    //convert the spaces to whatever the user wants
    //usually a dash or underscore..
    //...then return the value.
    return str_replace(' ', $replace, $return);
}

/**
 * Remove words from string
 * @param string $input
 * @param string $replace
 * @param array $words_array
 * @param bool $unique_words
 */
function remove_words($input, $replace, $words_array = array(), $unique_words = true)
{
    //separate all words based on spaces
    $input_array = explode(' ', $input);

    //create the return array
    $return = array();

    //loops through words, remove bad words, keep good ones
    foreach ($input_array as $word) {
        //if it's a word we should add...
        if (!in_array($word, $words_array) && ($unique_words ? !in_array($word, $return) : true)) {
            $return[] = $word;
        }
    }

    //return good words separated by dashes
    return implode($replace, $return);
}

// PHP < 5.5.0
if (!function_exists('array_column')) {
    /**
     * Array collumn function for php version < 5.5
     * @param array $input
     * @param string $column_key
     * @param string $index_key
     */
    function array_column($input, $column_key, $index_key = null)
    {
        if ($index_key !== null) {
            // Collect the keys
            $keys = array();
            $i = 0;
            // Counter for numerical keys when key does not exist

            foreach ($input as $row) {
                if (array_key_exists($index_key, $row)) {
                    // Update counter for numerical keys
                    if (is_numeric($row[$index_key]) || is_bool($row[$index_key])) {
                        $i = max($i, (int)$row[$index_key] + 1);
                    }

                    // Get the key from a single column of the array
                    $keys[] = $row[$index_key];
                } else {
                    // The key does not exist, use numerical indexing
                    $keys[] = $i++;
                }
            }
        }

        if ($column_key !== null) {
            // Collect the values
            $values = array();
            $i = 0;
            // Counter for removing keys

            foreach ($input as $row) {
                if (array_key_exists($column_key, $row)) {
                    // Get the values from a single column of the input array
                    $values[] = $row[$column_key];
                    $i++;
                } elseif (isset($keys)) {
                    // Values does not exist, also drop the key for it
                    array_splice($keys, $i, 1);
                }
            }
        } else {
            // Get the full arrays
            $values = array_values($input);
        }

        if ($index_key !== null) {
            return array_combine($keys, $values);
        }

        return $values;
    }

    /**
     *
     * public function float
     * @param $str = the string that contains the float number
     * @param $set = setting up parameters see details below
     * echo float('foo 123,01 bar'); // returns 123.00
     echo '<br/>';
     echo float('foo 123.01 bar', array('single_dot_as_decimal'=> TRUE)); //returns
     123.000
     echo '<br/>';
     echo float('foo 123.01 bar', array('single_dot_as_decimal'=> FALSE)); //returns
     123000
     echo '<br/>';
     echo float('foo 222.123.01 bar', array('single_dot_as_decimal'=> TRUE));
     //returns 222123000
     echo '<br/>';
     echo float('foo 222.123.01 bar', array('single_dot_as_decimal'=> FALSE));
     //returns 222123000
     echo '<br/>';

     // The decimal part can also consist of '-'
     echo float('foo 123,-- bar'); // returns 123.00
     echo '<br/>';
     *
     */
    function float($str, $set = FALSE)
    {
        if (preg_match("/([0-9\.,-]+)/", $str, $match)) {
            // Found number in $str, so set $str that number
            $str = $match[0];

            if (strstr($str, ',')) {
                // A comma exists, that makes it easy, cos we assume it separates the
                // decimal part.
                $str = str_replace('.', '', $str);
                // Erase thousand seps
                $str = str_replace(',', '.', $str);
                // Convert , to . for floatval command

                return floatval($str);
            } else {
                // No comma exists, so we have to decide, how a single dot shall be
                // treated
                if (preg_match("/^[0-9]*[\.]{1}[0-9-]+$/", $str) == TRUE && $set['single_dot_as_decimal'] == TRUE) {
                    // Treat single dot as decimal separator
                    return floatval($str);

                } else {
                    // Else, treat all dots as thousand seps
                    $str = str_replace('.', '', $str);
                    // Erase thousand seps
                    return floatval($str);
                }
            }
        } else {
            // No number found, return zero
            return 0;
        }
    }

}
?>
