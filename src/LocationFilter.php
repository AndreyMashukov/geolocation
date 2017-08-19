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
		    }
		else if (count($result["lat"]) > 2 || count($result["lang"]) > 2)
		    {
			$this->_reverse($result["lat"], $result["lang"], false);
		    } //end if

	    } //end _check()


	/**
	 * Reverse locations
	 *
	 * @param array $lat    Lats
	 * @param array $lang   Langs
	 * @param bool  $double Double locations
	 *
	 * @return void
	 */

	private function _reverse(array $lat, array $lang, bool $double = true)
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

		if ($double === true)
		    {
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
				    } //end if

			    } //end for

		    }
		else
		    {
			$truelat  = $keys["lat"][0];
			$truelang = $keys["lang"][0];
			if (count($keys["lat"]) > count($keys["lat"]))
			    {
				$key  = "lat";
				$true = $keys["lat"];
				unset($true[0]);
			    }
			else
			    {
				$key  = "lang";
				$true = $keys["lang"];
				unset($true[0]);
			    } //end if

			foreach ($true as $loc)
			    {
				$maybe = [
				    "lat_1"  => $truelat  - 1,
				    "lang_1" => $truelang - 1,
				    "lat_2"  => $truelat  + 1,
				    "lang_2" => $truelang + 1,
				    "lat"    => $truelat,
				    "lang"   => $truelang,
				];
				$truelocations = [
				    "lat"  => [
					"lat_1"  => $truelat  - 1,
					"lat_2"  => $truelat  + 1,
					"lat"    => $truelat,
				    ],
					"lang" => [
					"lang_1" => $truelang - 1,
					"lang_2" => $truelang + 1,
					"lang"   => $truelang,
				    ],
				];

				if (in_array($loc, $maybe) === true)
				    {
					if ($key === "lat")
					    {
						foreach ($lat[$loc]["keys"] as $location)
						    {
							if (in_array($loc, $truelocations[$key]) === false)
							    {
								$reverse = $this->_locs[$location];
								$this->_locs[$location] = [
								    "lat"  => $reverse["lang"],
								    "lang" => $reverse["lat"],
								];
							    } //end if

						    } //end foreach

					    }
					else
					    {
						foreach ($lang[$loc]["keys"] as $location)
						    {
							if (in_array($loc, $truelocations[$key]) === false)
							    {
								$reverse = $this->_locs[$location];
								$this->_locs[$location] = [
								    "lat"  => $reverse["lang"],
								    "lang" => $reverse["lat"],
								];
							    } //end if

						    } //end foreach

					    } //end if

				    }
				else
				    {
					if ($key === "lat")
					    {
						foreach ($lat[$loc]["keys"] as $location)
						    {
							unset($this->_locs[$location]);
						    } //end foreach

					    }
					else
					    {
						foreach ($lang[$loc]["keys"] as $location)
						    {
							unset($this->_locs[$location]);
						    } //end foreach

					    } //end if
				    } //end if

			    } //end foreach

		    } //end if

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