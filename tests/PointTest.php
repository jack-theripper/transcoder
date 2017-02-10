<?php
/**
 * This file is part of the arhitector/transcoder library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Tests;

use Arhitector\Transcoder\Point;

/**
 * Class PointTest.
 *
 * @package Arhitector\Transcoder\Tests
 */
class PointTest extends \PHPUnit_Framework_TestCase
{
	/** @noinspection MoreThanThreeArgumentsInspection */
	
	/**
	 * @dataProvider dataConstructorSuccessful
	 *
	 * @param int  $x
	 * @param int  $y
	 * @param null $expectedX
	 * @param null $expectedY
	 */
	public function testConstructorSuccessFul($x, $y, $expectedX = null, $expectedY = null)
	{
		$point = new Point($x, $y);
		$this->assertEquals($expectedX ?: $x, $point->getX());
		$this->assertEquals($expectedY ?: $y, $point->getY());
	}
	
	public function dataConstructorSuccessful()
	{
		return [
			[0, 0],
			[0, 100],
			[150, 0],
		    ['150', '150'],
		    ['1.6', 1.1, 1, 1]
		];
	}
	
	/**
	 * @dataProvider dataConstructorFailure
	 *
	 * @param int $x
	 * @param int $y
	 */
	public function testConstructorFailure($x, $y)
	{
		$this->expectException(\InvalidArgumentException::class);
		new Point($x, $y);
	}
	
	public function dataConstructorFailure()
	{
		return [
			[-1, 0],
		    ['string', 0],
		    [['array'], 0],
		    [(object) ['object'], 0],
		    [0, -1],
		    [0, 'string'],
		    [0, ['array']],
		    [0, (object) ['object']]
		];
	}
	
}
