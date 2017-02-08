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
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructor()
	{
		$this->assertEquals('codec string', (new Codec('codec string'))->getCode());
		$this->assertEquals('codec name', (new Codec('codec string', 'codec name'))->getName());
		
		new Codec([]);
	}
	
	/**
	 * Getters testing.
	 */
	public function testGetters()
	{
		$codec = new Codec('codec', 'codec name');
		
		$this->assertEquals('codec', $codec->getCode());
		$this->assertEquals('codec name', $codec->getName());
		$this->assertEquals('codec', (string) $codec);
	}
	
	public function testSetters()
	{
		$codec = new Codec('codec');
		$methodSetCode = new \ReflectionMethod(Codec::class, 'setCode');
		$methodSetCode->setAccessible(true);
		
		$methodSetCode->invoke($codec, 'codec string');
		$this->assertEquals($codec->getCode(), 'codec string');
		
		try
		{
			$methodSetCode->invoke($codec, new \stdClass);
		}
		catch (\Exception $exception)
		{
			$this->assertInstanceOf(\InvalidArgumentException::class, $exception);
		}
		
		$methodSetName = new \ReflectionMethod(Codec::class, 'setName');
		$methodSetName->setAccessible(true);
		
		$methodSetName->invoke($codec, 'codec name');
		$this->assertEquals($codec->getName(), 'codec name');
		
		try
		{
			$methodSetName->invoke($codec, new \stdClass);
		}
		catch (\Exception $exception)
		{
			$this->assertInstanceOf(\InvalidArgumentException::class, $exception);
		}
	}
	
}
