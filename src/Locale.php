<?php
/**
 * A Locale object for returning localizes strings
 * 
 * @author Jon Elofson <jon.elofson@gmail.com>
 *
 */

namespace Vespula;

/**
 * 
 * Basic Locale class that loads locale files via a glob based on a path
 * Locale strings are stored in an array keyed by the locale code
 * 
 * <code>
 * $locale = new \Vespula\Locale('en_CA');
 * // assume you have an en_CA.php and fr_CA.php file in a folder
 *     
 * $locale->load('/path/to/locale_files');
 * // now the locale object will have a store with the locale entries
 * print_r($locale->getStrings());
 * </code>
 * 
 * The above would output
 * <pre>
 * Array(
 *     'en_CA'=>Array(
 *   	   'TEXT_HOME'=>'Home',
 *   	   'TEXT_APPLE=>array('apple', 'apples'),
 * ),
 *     	'fr_CA'=>Array(
 *          'TEXT_HOME'=>'Accueil',
 *         	'TEXT_APPLE=>array('pomme', 'pommes'),
 *     	)
 * )
 * </pre>
 *<code>
 * //Get a translation base on current code:
 * echo $locale->gettext('TEXT_HOME');
 *
 * //Get a pluralized translation
 * echo $locale->gettext('TEXT_HOME', 4); // would return a plural form
 * </code>
 *
 */
class Locale {
	
	/**
	 * Locale code
	 * @var string Locale code
	 */
	protected $code = 'en_CA';
	
	/**
	 * An array to hold locale strings keyed by locale code
	 * @var array The store of locale strings
	 */
	protected $store = array();
	
	/**
	 * Foramt for plural forms. Format is like this:
	 * If there are 0, 1, or 2+, what form should it take?
	 * The array takes 3 strings. The 0 form, the 1 form, and the more than 1 form
	 * 
	 * <pre>
	 * | 0       | 1        | 2+      |
	 * +---------+----------+---------+
	 * | plural  | singular | plural  |
	 * +---------+----------+---------+
	 * </pre>
	 * 
	 * When using gettext(), you may pass a key and a count. Your local should have an array
	 * 
	 *     'TEXT_APPLE'=>array('apple', 'apples')
	 *     echo $this->gettext('TEXT_APPLE', 3);
	 * 
	 * Because 3 is in the 2+ column, it returns `apples` (plural)
	 *     
	 *     echo $this->gettext('TEXT_APPLE', 0);
	 * 
	 * Returns `apples` because 0 is defined as plural in the above
	 * 
	 * In french, you would use this form
	 * 
	 * <pre>
	 * | 0       | 1        | 2+      |
	 * +---------+----------+---------+
	 * | singular| singular | plural  |
	 * +---------+----------+---------+
	 * </pre>
	 * 
	 * You can also use 'other' which will look for a 3rd element in the locale array,
	 * if one exists
	 * 
	 *     'TEXT_APPLE'=>array('apple', 'apples', 'applees')
	 *  
	 * @var array The plural form organized by code. If no code, use default
	 */
	protected $plural_forms = array(
		'default'=>array('plural', 'singular', 'plural'),
	);
	
	/**
	 * Constructor
	 * @param string $code The locale code
	 */
	public function __construct($code = null) 
	{
		if ($code) {
			$this->code = $code;
		}
	}
	
	/**
	 * Accessor for $this->code
	 * @return string 
	 */
	public function getCode()
	{
		return $this->code;
	}
	
	/**
	 * Set the locale code
	 * @param string $code 
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}
	
	/**
	 * Return the 2-letter language part of the code
	 * @return string 
	 */
	public function getLanguageCode()
	{
		return substr($this->code, 0, 2);
	}
	
	/**
	 * Get the strings in the store, optionally by code
	 * @param string $code Optional locale code
	 * @return mixed False if the code is not found, or an array of locale entries
	 */
	public function getStrings($code = null)
	{
		if ($code) {
			if (array_key_exists($code, $this->store)) {
				return $this->store[$code];
			} else {
				return false;
			}
		}
		
		return $this->store;
	}
	
	/**
	 * Loads locale files into the store. Locale files must be
	 * .php files and return an array. Should be named in the 
	 * en_CA.php format.
	 * Locale entries will be overwritten if they exist.
	 * 
	 * @param string $path
	 */
	public function load($path)
	{
		$path = rtrim($path, '/');
		$files = glob($path . "/*.php");
		foreach ($files as $file) {
			$code = basename($file, '.php');
			if (! array_key_exists($code, $this->store)) {
				$this->store[$code] = array();
			}
			$strings = include($file);
			$this->store[$code] = array_merge($this->store[$code], $strings);
		}
	}
	
	/**
	 * Get a plural form. See above
	 * @param string $code Locale code
	 * @return mixed An array if found, false if not
	 */
	public function getPluralForm($code)
	{
		if (isset($this->plural_forms[$code])) {
			return $this->plural_forms[$code];
		}
		return false;
	}
	
	/**
	 * Set a plural form. See above
	 * @param string $code Locale code
	 * @param array $form The plural form array
	 */
	public function setPluralForm($code, $form)
	{
		$form = (array) $form;
        if (count($form) != 3) {
            throw new \Exception('The plural form must be an array of 3 elements.');
        }
        $valid = ['singular', 'plural'];
        
        foreach ($form as $info) {
            if (! in_array($info, $valid)) {
                throw new \Exception('Each form must be one of singular or plural');
                break;
            }
        }
        $this->plural_forms[$code] = $form;
	}
	
	/**
	 * Get a plural/singular/zero form of a locale entry
	 * @param string $key The locale key
	 * @param float|int $count How many items you have
	 */
	public function gettext($key, $count = 1)
	{
		// First check if the code exists
		if (! array_key_exists($this->code, $this->store)) {
			return $key;
		}
		
		// Next check for the key
		$strings = $this->store[$this->code];
		if (! array_key_exists($key, $strings)) {
			return $key;
		}
		
		$string = (array) $strings[$key];
		
		// We only have one element in the array, can't determine plural
		// Ain't going to write an inflection class to pluralize
		if (count($string) == 1) {
			return $string[0];
		}
		
		if (array_key_exists($this->code, $this->plural_forms)) {
			$forms = $this->plural_forms[$this->code];
		} else {
			$forms = $this->plural_forms['default'];
		}
		

		switch ($count) {
			default :
				$form = $forms[2];
			break;
			case 1 :
				$form = $forms[1];
			break;
			case 0 :
				$form = $forms[0];
			break;
		}
		switch ($form) {
			default : 
				return $string[0];
			break;
			case 'singular' : 
				return $string[0];
			break;
			case 'plural' : 
				return $string[1];
			break;
			case 'other' && isset($string[2]) : 
				return $string[2];
			break;	
		}
		
	}
	
}
