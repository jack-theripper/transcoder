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
	
	/**
	 * @dataProvider dataSettersFailure
	 *
	 * @param string $method
	 * @param mixed  $value
	 */
	public function testSettersFailure($method, $value)
	{
		$point = $this->getInstanceWithoutConstructor();
		
		$methodReflection = new \ReflectionMethod(Point::class, $method);
		$methodReflection->setAccessible(true);
		
		$this->expectException(\InvalidArgumentException::class);
		$methodReflection->invoke($point, $value);
	}
	
	public function dataSettersFailure()
	{
		return [
			['setX', -1],
			['setX', 'string'],
			['setX', ['array']],
			['setX', (object) ['object']],
			['setY', -1],
			['setY', 'string'],
			['setY', ['array']],
			['setY', (object) ['object']],
		];
	}
	
	/** @noinspection MoreThanThreeArgumentsInspection */
	
	/**
	 * @dataProvider dataSettersAndGettersSuccessful
	 *
	 * @param string $methodSetter
	 * @param string $methodGetter
	 * @param mixed  $value
	 * @param mixed  $expectedValue
	 */
	public function testSettersAndGettersSuccessful($methodSetter, $methodGetter, $value, $expectedValue)
	{
		$point = $this->getInstanceWithoutConstructor();
		
		$setterReflection = new \ReflectionMethod(Point::class, $methodSetter);
		$setterReflection->setAccessible(true);
		$setterReflection->invoke($point, $value);
		
		$getterReflection = new \ReflectionMethod(Point::class, $methodGetter);
		$getterReflection->setAccessible(true);
		
		$this->assertEquals($expectedValue, $getterReflection->invoke($point));
	}
	
	public function dataSettersAndGettersSuccessful()
	{
		return [
			['setX', 'getX', 0, 0],
			['setX', 'getX', '100', 100],
			['setX', 'getX', 2.9, 2],
			['setY', 'getY', 0, 0],
			['setY', 'getY', '100', 100],
			['setY', 'getY', 2.9, 2],
		];
	}
	
	/**
	 * @return Point|object
	 */
	protected function getInstanceWithoutConstructor()
	{
		return (new \ReflectionClass(Point::class))
			->newInstanceWithoutConstructor();
	}
	
}
