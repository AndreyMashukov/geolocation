<?php

namespace Tests;

use \SimpleXMLElement;
use \AM\Geolocation\LocationFilter;
use \PHPUnit\Framework\TestCase;

class LocationFilterTest extends TestCase
    {

	/**
	 * Should remove or reverse not true locations
	 *
	 * @return void
	 */

	public function testShouldRemoveOrReverseNotTrueLocations()
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
	    } //end testShouldRemoveOrReverseNotTrueLocations()


	/**
	 * Should reverse locations with many mistakes
	 *
	 * @return void
	 */

	public function testShouldReverseLocationsWithManyMistakes()
	    {
		$locs = json_decode(file_get_contents(__DIR__ . "/coordinates/new.json"), true);

		$locs[] = [
		    "lat" => 52.22013,
		    "lang" => 102.291332,
		];

		$filter   = new LocationFilter($locs);
		$expected = json_decode(file_get_contents(__DIR__ . "/coordinates/expected_2.json"), true);
		$this->assertEquals($expected, $filter->get());
	    } //end testShouldReverseLocationsWithManyMistakes()

    } //end class

?>
