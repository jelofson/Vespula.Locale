<?php
namespace Vespula\Locale;
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    protected $locale;
    
    protected function setUp()
    {
        $this->locale = new Locale('en_CA');
    }
    
    public function testGetCode()
    {
        return $this->assertEquals('en_CA', $this->locale->getCode());
    }
    
    public function testSetCode()
    {
        $code = 'fr_CA';
        $this->locale->setCode($code);
        return $this->assertEquals('fr_CA', $this->locale->getCode());
    }
    
    public function testGetLanguageCode()
    {
        $code = 'fr_CA';
        $this->locale->setCode($code);
        return $this->assertEquals('fr', $this->locale->getLanguageCode());
    }
    
    /**
     * @covers \Vespula\Locale\Locale::load
     * @covers \Vespula\Locale\Locale::getStrings
     */
    public function testGetStrings()
    {
        $this->locale->setCode('en_CA');
        
        $this->locale->load(__DIR__ . '/locales');
        
        $expects = [
            'en_CA'=>[
                'TEXT_HOME'=>'Home',
                'TEXT_FOX'=>['fox', 'foxes']
            ],
            'fr_CA'=>[
                'TEXT_HOME'=>'Accueil',
                'TEXT_FOX'=>['renard', 'renards']
            ]
        ];
        return $this->assertEquals($expects, $this->locale->getStrings());
    }
    
    public function testSetGetPluralForm()
    {
        $form = ['singular', 'plural', 'plural'];
        $this->locale->setPluralForm('en_CA', $form);
        
        $this->assertEquals($form, $this->locale->getPluralForm('en_CA'));
    }
    
    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage The plural form must be an array of 3 elements.
     */
     
     public function testSetPluralFormExceptionSize()
     {
        $this->locale->setPluralForm('en_CA', 'whatever');
     }
     
     /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Each form must be one of singular or plural
     */
     
     public function testSetPluralFormExceptionValues()
     {
        $form = ['singular', 'plural', 'foo'];
        $this->locale->setPluralForm('en_CA', $form);
     }
     
     public function testGetTextSingular()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $this->assertEquals('fox', $this->locale->gettext('TEXT_FOX'));
        $this->assertEquals('fox', $this->locale->gettext('TEXT_FOX', 1));
     }
     
     public function testGetTextZero()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $this->assertEquals('foxes', $this->locale->gettext('TEXT_FOX', 0));
        
        $form = ['singular', 'singular', 'plural'];
        $this->locale->setPluralForm('en_CA', $form);
        
        $this->assertEquals('fox', $this->locale->gettext('TEXT_FOX', 0));
        
     }
     
     public function testGetTextMany()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $this->assertEquals('foxes', $this->locale->gettext('TEXT_FOX', 10));
        
        $form = ['singular', 'singular', 'plural'];
        $this->locale->setPluralForm('en_CA', $form);
        
        $this->assertEquals('foxes', $this->locale->gettext('TEXT_FOX', 2));
        
     }
     
}
