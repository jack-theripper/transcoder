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
namespace Arhitector\Transcoder\Tests\Traits;

use Arhitector\Transcoder\Traits\MetadataTrait;

/**
 * Class MetadataTraitTest
 *
 * @package Arhitector\Transcoder\Tests\Traits
 */
class MetadataTraitTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Test of the container.
	 */
	public function testContainer()
	{
		/** @var MetadataTrait $mock */
		$mock = $this->getObjectForTrait(MetadataTrait::class);
		
		$method = new \ReflectionMethod(get_class($mock), 'setMetadata');
		$method->setAccessible(true);
		
		// empty container
		$this->assertEquals([], $mock->getMetadata());
		
		// simple container
		$method->invoke($mock, ['key' => 'value']);
		$this->assertEquals(['key' => 'value'], $mock->getMetadata());
		
		// only scalar values
		$value = new \StdClass;
		$method->invoke($mock, ['key' => $value, 'scalar' => true]);
		$this->assertEquals(['scalar' => true], $mock->getMetadata());
		
		// key with value
		$value = mt_rand();
		$method->invoke($mock, 'key', $value);
		$this->assertEquals(['key' => $value], $mock->getMetadata());
	}
	
	/**
	 * Methods: offsetExists, offsetGet, offsetSet, offsetUnset
	 */
	public function testArrayAccess()
	{
		$value = mt_rand();
		
		/** @var MetadataTrait $mock */
		$mock = $this->getObjectForTrait(MetadataTrait::class);
		
		// setter
		$this->assertEmpty($mock->getMetadata());
		$mock->offsetSet($value, 'value');
		
		// exists
		$this->assertTrue($mock->offsetExists($value));
		$this->assertFalse($mock->offsetExists($value + 1));
		
		// getter
		$this->assertEquals('value', $mock->offsetGet($value));
		$this->assertNull($mock->offsetGet($value - 1));
		
		// unset
		$mock->offsetUnset($value);
		$this->assertEmpty($mock->getMetadata());
	}
	
}
