<?php

namespace CommerceGuys\Intl\Tests\Currency;

use CommerceGuys\Intl\Currency\CurrencyRepository;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \CommerceGuys\Intl\Currency\CurrencyRepository
 */
class CurrencyRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base currency definitions.
     *
     * @var array
     */
    protected $baseDefinitions = array(
        'USD' => array(
            'code' => 'USD',
            'numeric_code' => '840',
        ),
        'EUR' => array(
            'code' => 'EUR',
            'numeric_code' => '840',
            'fraction_digits' => '2',
        ),
    );

    /**
     * English currency definitions.
     *
     * @var array
     */
    protected $englishDefinitions = array(
        'USD' => array(
            'name' => 'US Dollar',
            'symbol' => '$',
        ),
        'EUR' => array(
            'name' => 'Euro',
            'symbol' => '€',
        ),
    );

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        // Mock the existence of JSON definitions on the filesystem.
        $root = vfsStream::setup('resources');
        vfsStream::newFile('currency/base.json')->at($root)->setContent(json_encode($this->baseDefinitions));
        vfsStream::newFile('currency/en.json')->at($root)->setContent(json_encode($this->englishDefinitions));

        // Instantiate the currency repository and confirm that the definition path
        // was properly set.
        $currencyRepository = new CurrencyRepository('vfs://resources/currency/');
        $definitionPath = $this->getObjectAttribute($currencyRepository, 'definitionPath');
        $this->assertEquals($definitionPath, 'vfs://resources/currency/');

        return $currencyRepository;
    }

    /**
     * @covers ::get
     * @covers ::loadDefinitions
     * @covers ::createCurrencyFromDefinition
     * @uses \CommerceGuys\Intl\Currency\Currency
     * @uses \CommerceGuys\Intl\LocaleResolverTrait
     * @depends testConstructor
     */
    public function testGet($currencyRepository)
    {
        $currency = $currencyRepository->get('USD');
        $this->assertInstanceOf('CommerceGuys\\Intl\\Currency\\Currency', $currency);
        $this->assertEquals($currency->getCurrencyCode(), 'USD');
        $this->assertEquals($currency->getName(), 'US Dollar');
        $this->assertEquals($currency->getNumericCode(), '840');
        $this->assertEquals($currency->getFractionDigits(), '2');
        $this->assertEquals($currency->getSymbol(), '$');
        $this->assertEquals($currency->getLocale(), 'en');
    }

    /**
     * @covers ::get
     * @covers ::loadDefinitions
     * @uses \CommerceGuys\Intl\LocaleResolverTrait
     * @expectedException \CommerceGuys\Intl\Exception\UnknownCurrencyException
     * @depends testConstructor
     */
    public function testGetInvalidCurrency($currencyRepository)
    {
        $currencyRepository->get('RSD');
    }

    /**
     * @covers ::getAll
     * @covers ::loadDefinitions
     * @covers ::createCurrencyFromDefinition
     * @uses \CommerceGuys\Intl\Currency\Currency
     * @uses \CommerceGuys\Intl\LocaleResolverTrait
     * @depends testConstructor
     */
    public function testGetAll($currencyRepository)
    {
        $currencies = $currencyRepository->getAll();
        $this->assertArrayHasKey('USD', $currencies);
        $this->assertArrayHasKey('EUR', $currencies);
        $this->assertEquals($currencies['USD']->getCurrencyCode(), 'USD');
        $this->assertEquals($currencies['EUR']->getCurrencyCode(), 'EUR');
    }
}