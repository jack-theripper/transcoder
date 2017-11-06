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

use Arhitector\Transcoder\Dimension;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class DimensionTest
 *
 * @package Arhitector\Transcoder\Tests
 */
class DimensionTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @dataProvider dataConstructorSuccessful
	 *
	 * @param mixed $width
	 * @param mixed $height
	 * @param mixed $expWidth
	 * @param mixed $expHeight
	 */
	public function testConstructorSuccessFul($width, $height, $expWidth, $expHeight)
	{
		$dimension = new Dimension($width, $height);
		
		$this->assertEquals($expWidth, $dimension->getWidth());
		$this->assertEquals($expHeight, $dimension->getHeight());
	}
	
	/**
	 * The data provider for `testConstructorSuccessFul`
	 *
	 * @return array
	 */
	public function dataConstructorSuccessful()
	{
		return [
			[100, 200, 100, 200],
			['100', '200', 100, 200],
			[100.5, 200.7, 100, 200]
		];
	}
	
	/**
	 * @dataProvider dataSettersAndGettersSuccessful
	 *
	 * @param mixed $value
	 * @param mixed $expected
	 */
	public function testSettersAndGettersWidthSuccessful($value, $expected)
	{
		$dimension = $this->getInstanceWithoutConstructor();
		
		$methodReflection = new ReflectionMethod(Dimension::class, 'setWidth');
		$methodReflection->setAccessible(true);
		$methodReflection->invoke($dimension, $value);
		
		$this->assertEquals($expected, $dimension->getWidth());
	}
	
	/**
	 * @dataProvider dataSettersAndGettersSuccessful
	 *
	 * @param mixed $value
	 * @param mixed $expected
	 */
	public function testSettersAndGettersHeightSuccessful($value, $expected)
	{
		$dimension = $this->getInstanceWithoutConstructor();
		
		$methodReflection = new ReflectionMethod(Dimension::class, 'setHeight');
		$methodReflection->setAccessible(true);
		$methodReflection->invoke($dimension, $value);
		
		$this->assertEquals($expected, $dimension->getHeight());
	}
	
	/**
	 * The data provider for setters and getters.
	 *
	 * @return array
	 */
	public function dataSettersAndGettersSuccessful()
	{
		return [
			[100, 100],
			['123', 123],
			[321.789, 321]
		];
	}
	
	/**
	 * @dataProvider dataFromStringSuccessful
	 *
	 * @param string $value
	 * @param int    $width
	 * @param int    $height
	 */
	public function testFromStringSuccessful($value, $width, $height)
	{
		$method = new ReflectionMethod(Dimension::class, 'fromString');
		$method->setAccessible(true);
		
		$dimension = $method->invoke(null, $value);
		
		$this->assertInstanceOf(Dimension::class, $dimension);
		$this->assertEquals($width, $dimension->getWidth());
		$this->assertEquals($height, $dimension->getHeight());
	}
	
	/**
	 * The data provider for `testFromStringSuccessful`
	 *
	 * @return array
	 */
	public function dataFromStringSuccessful()
	{
		return [
			['100x200', 100, 200],
			['500:400', 500, 400],
			['320,180', 320, 180],
			['768;360', 768, 360],
			['200X100', 200, 100]
		];
	}
	
	/**
	 * Test for `getRation` method.
	 */
	public function testRatio()
	{
		$this->assertEquals(1.3333333333333333, (new Dimension(320, 240))->getRatio());
		$this->assertNotEquals(1.33, (new Dimension(320, 240))->getRatio());
		$this->assertNotEquals('123', (new Dimension(100, 100))->getRatio());
	}
	
	/**
	 * @return Dimension|object
	 */
	protected function getInstanceWithoutConstructor()
	{
		return (new ReflectionClass(Dimension::class))->newInstanceWithoutConstructor();
	}
	
}
