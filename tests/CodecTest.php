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

use Arhitector\Transcoder\Codec;

/**
 * Class CodecTest.
 *
 * @package Arhitector\Transcoder\Tests
 */
class CodecTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Testing constructor on successful.
	 *
	 * @dataProvider dataValidConstructor
	 *
	 * @param mixed $code
	 * @param mixed $name
	 */
	public function testConstructorSuccessful($code, $name)
	{
		$codec = new Codec($code);
		$this->assertEquals($code, $codec->getCode());
		
		$codec = new Codec($code, $name);
		$this->assertEquals($code, $codec->getCode());
		$this->assertEquals($name, $codec->getName());
	}
	
	public function dataValidConstructor()
	{
		return [
			['codec value', null],
			['codec value', 'name'],
			['codec value', '']
		];
	}
	
	/**
	 * Testing constructor on failure.
	 *
	 * @dataProvider dataInvalidConstructor
	 *
	 * @param mixed $code
	 * @param mixed $name
	 */
	public function testConstructorFailure($code, $name)
	{
		$this->expectException(\InvalidArgumentException::class);
		new Codec($code, $name);
	}
	
	public function dataInvalidConstructor()
	{
		return [
			// for 'code'
			['', 'valid value'],
			[null, 'valid value'],
			[true, 'valid value'],
			[false, 'valid value'],
			[1, 'valid value'],
			[1.1, 'valid value'],
			[['array'], 'valid value'],
			[(object) ['object' => true], 'valid value'],
			
			// for 'name'
			['valid value', true],
			['valid value', false],
			['valid value', 1],
			['valid value', 1.1],
			['valid value', ['array']],
			['valid value', (object) ['object' => true]]
		];
	}
	
	public function testCanSerializeToString()
	{
		$expected = 'abcdefghijklmnopqrstuvwxyz';
		$codec = new Codec($expected);
		$this->assertEquals($expected, $codec);
	}
	
	/**
	 * @dataProvider dataSettersFailure
	 *
	 * @param string $setter
	 * @param mixed  $value
	 */
	public function testSettersFailure($setter, $value)
	{
		$codec = $this->getInstanceWithoutConstructor();
		
		$setterReflection = new \ReflectionMethod(Codec::class, $setter);
		$setterReflection->setAccessible(true);
		
		$this->expectException(\InvalidArgumentException::class);
		$setterReflection->invoke($codec, $value);
	}
	
	public function dataSettersFailure()
	{
		return [
			['setCode', ''],
			['setCode', null],
			['setCode', true],
			['setCode', false],
			['setCode', 1],
			['setCode', 1.1],
			['setCode', ['array']],
			['setCode', (object) ['object' => true]]
		];
	}
	
	/**
	 * @dataProvider dataSettersAndGettersSuccessful
	 *
	 * @param string $setter
	 * @param string $getter
	 * @param mixed  $value
	 */
	public function testSettersAndGettersSuccessful($setter, $getter, $value)
	{
		$codec = $this->getInstanceWithoutConstructor();
		
		$setterReflection = new \ReflectionMethod(Codec::class, $setter);
		$setterReflection->setAccessible(true);
		$setterReflection->invoke($codec, $value);
		
		$getterReflection = new \ReflectionMethod(Codec::class, $getter);
		$getterReflection->setAccessible(true);
		
		$this->assertEquals($value, $getterReflection->invoke($codec));
	}
	
	public function dataSettersAndGettersSuccessful()
	{
		return [
			['setCode', 'getCode', 'valid value'],
			['setName', 'getName', 'valid value'],
			['setName', 'getName', '']
		];
	}
	
	/**
	 * @return Codec|object
	 */
	protected function getInstanceWithoutConstructor()
	{
		return (new \ReflectionClass(Codec::class))
			->newInstanceWithoutConstructor();
	}
	
}
