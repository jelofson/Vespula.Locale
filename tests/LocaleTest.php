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
     * @expectedException     \Exception
     * @expectedExceptionMessage Path to locales does not exist
     */
     
     public function testLoadBadPath()
     {
        $this->locale->load('/var/nosuchplace___');
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
                'TEXT_FOX'=>['fox', 'foxes'],
                'TEXT_CAT'=>['cat', 'cats', 'felines']
            ],
            'fr_CA'=>[
                'TEXT_HOME'=>'Accueil',
                'TEXT_FOX'=>['renard', 'renards'],
                'TEXT_CAT'=>['chat', 'chats', 'felines']
            ]
        ];
        $this->assertEquals($expects, $this->locale->getStrings());
        
        $en_CA = [
            'TEXT_HOME'=>'Home',
            'TEXT_FOX'=>['fox', 'foxes'],
            'TEXT_CAT'=>['cat', 'cats', 'felines']
        ];
        
        $this->assertEquals($en_CA, $this->locale->getStrings('en_CA'));
        $this->assertEquals(false, $this->locale->getStrings('en_GB'));
        
    }
    
    public function testSetGetPluralForm()
    {
        $form = ['singular', 'plural', 'other'];
        $this->locale->setPluralForm('en_CA', $form);
        
        $this->assertEquals($form, $this->locale->getPluralForm('en_CA'));
    }
    
    public function testGetPluralFormDefault()
    {
        $form = ['singular', 'plural', 'plural'];
        $this->locale->setPluralForm('en_CA', $form);
        $default = $this->locale->getPluralForm('default');
        $this->assertEquals($default, $this->locale->getPluralForm('en_GB'));
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
        
        $form = ['singular', 'singular', 'plural'];
        $this->locale->setPluralForm('en_CA', $form);
        
        $this->assertEquals('foxes', $this->locale->gettext('TEXT_FOX', 2));
        
     }
     
     public function testGetTextOther()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $form = ['singular', 'singular', 'other'];
        $this->locale->setPluralForm('en_CA', $form);
        
        $this->assertEquals('felines', $this->locale->gettext('TEXT_CAT', 3));
     }
     
     public function testGetTextOtherMissing()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $form = ['singular', 'singular', 'other'];
        $this->locale->setPluralForm('en_CA', $form);
        
        $this->assertEquals('fox', $this->locale->gettext('TEXT_FOX', 3));
     }
     
     public function testGetTextMissing()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $this->assertEquals('TEXT_FOO', $this->locale->gettext('TEXT_FOO'));
        
        $this->locale->setCode('en_GB');
        
        $this->assertEquals('TEXT_FOO', $this->locale->gettext('TEXT_FOO'));
        
     }
     
     public function testGetTextNoPlural()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $this->assertEquals('Home', $this->locale->gettext('TEXT_HOME', 5));
        
        
     }
     
     public function testGetTextDefaultPlural()
     {
        $this->locale->setCode('en_CA');
        $this->locale->load(__DIR__ . '/locales');
        
        $this->assertEquals('Home', $this->locale->gettext('TEXT_HOME', 5));
        
        
     }
     
}
