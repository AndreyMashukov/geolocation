<?php

namespace Tests;

use \AM\Geolocation\LocationFilter;
use \PHPUnit\Framework\TestCase;

class LocationFilterTest extends TestCase
    {

	/**
	 * Should remove not true locations
	 *
	 * @return void
	 */

	public function testShouldRemoveNotTrueLocations()
	    {
		$locs  = file_get_contents(__DIR__ . "/coordinates/1.json");
		$array = json_decode($locs, true);

		$locationfilter = new LocationFilter($array);

		$locs     = file_get_contents(__DIR__ . "/coordinates/expected.json");
		$expected = json_decode($locs, true);
		$this->assertEquals($expected, $locationfilter->get());
		$exp = [
		    "lat"  => "52.263799164953",
		    "lang" => "104.26847049742",
		];
		$this->assertEquals($exp, $locationfilter->center());
	    } //end testShouldRemoveNotTrueLocations()


    } //end class

?>
