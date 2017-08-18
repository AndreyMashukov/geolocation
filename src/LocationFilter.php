<?php

namespace AM\Geolocation;

class LocationFilter
    {

	/**
	 * Locs
	 *
	 * @var array Locations
	 */
	private $_locs;

	/**
	 * Prepare to work
	 *
	 * @param array $locations Locations for check
	 *
	 * @return void
	 */

	public function __construct(array $locations)
	    {
		$this->_locs = $locations;
		$this->_check();
	    } //end __construct()

	/**
	 * Check locations for mistakes
	 *
	 * @return void
	 */

	private function _check()
	    {
		$result = [
		    "lat"  => [],
		    "lang" => [],
		];

		foreach ($this->_locs as $key => $loc)
		    {
			$result = $this->_calc($result, $loc, "lat", $key);
			$result = $this->_calc($result, $loc, "lang", $key);
		    } //end foreach

		if (count($result["lat"]) === 2 && count($result["lang"]) === 2)
		    {
			$this->_reverse($result["lat"], $result["lang"]);
		    } //end if

	    } //end _check()


	/**
	 * Reverse locations
	 *
	 * @param array $lat  Lats
	 * @param array $lang Langs
	 *
	 * @return void
	 */

	private function _reverse(array $lat, array $lang)
	    {
		$keys = [
		    "lat"  => array_keys($lat),
		    "lang" => array_keys($lang),
		];

		$orderlat = [
		    $lat[$keys["lat"][0]]["value"] => 0,
		    $lat[$keys["lat"][1]]["value"] => 1,
		];
		ksort($orderlat);
		$orderlang = [
		    $lang[$keys["lang"][0]]["value"] => 0,
		    $lang[$keys["lang"][1]]["value"] => 1,
		];
		ksort($orderlang);

		$keys = [
		    "lat"  => $lat[$keys["lat"][array_shift($orderlat)]]["keys"],
		    "lang" => $lang[$keys["lang"][array_shift($orderlang)]]["keys"],
		];

		for ($i = 0; $i < count($keys["lat"]); $i++)
		    {
			if ($keys["lat"][$i] === $keys["lang"][$i])
			    {
				$loc = $this->_locs[$keys["lat"][$i]];
				$this->_locs[$keys["lat"][$i]] = [
				    "lat"  => $loc["lang"],
				    "lang" => $loc["lat"],
				];
			    }
		    }

	    } //end _reverse()


	/**
	 * Calculate data
	 *
	 * @param array  $data Locations data
	 * @param array  $loc  Current location
	 * @param string $name Location name
	 * @param int    $key  Key of array
	 *
	 * @return array Calculated
	 */

	private function _calc(array $data, array $loc, string $name, int $key):array
	    {
		$exploded = explode(".", (string) $loc[$name]);
		if (isset($data[$name][$exploded[0]]) === true)
		    {
			$data[$name][$exploded[0]]["value"]++;
			$data[$name][$exploded[0]]["keys"][] = $key;
		    }
		else
		    {
			$data[$name][$exploded[0]] = [
			    "value" => 1,
			    "keys"  => [$key],
			];
		    } //end if

		return $data;
	    } //end calc()


	/**
	 * Get true locations
	 *
	 * @return array Locations
	 */

	public function get():array
	    {
		return $this->_locs;
	    } //end get()


	/**
	 * Center location
	 *
	 * @return array Center
	 */

	public function center():array
	    {
		$lats  = 0;
		$langs = 0;
		$count = 0;
		foreach ($this->_locs as $loc)
		    {
			$count++;
			$lats  += $loc["lat"];
			$langs += $loc["lang"];
		    } //end foreach

		return [
		    "lat"  => ($lats / $count),
		    "lang" => ($langs / $count),
		];
	    } //end center()


    } //end class


?>