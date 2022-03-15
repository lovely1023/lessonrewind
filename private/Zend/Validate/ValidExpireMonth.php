<?php
/**
 * @author Edward Hew http://hewmc.blogspot.com/
 * This class designed to be used on the Year field element, but tell the contructor 
 * which is your month field, otherwise it'll explode.
 * $expYear->addValidator('OnlyFutureMonthYear', false, array('exp_month'));
 *
 * (Name the class according to your needs.)
 */
 
 require_once 'Zend/Validate/Abstract.php';
 
class Zend_Validate_ValidExpireMonth extends Zend_Validate_Abstract {
    const PAST_DATE = 'past';

    protected $_monthField;

    public function __construct($monthField) {
        $this->setMonthField($monthField);
    }

    public function setMonthField($y) {
        $this->_monthField = $y;
    }

    public function getMonthField() {
        return $this->_monthField;
    }

    /**
     * This is the error message displayed on failing the validation, change it to your needs.
     * Note this can also be done in the form class with 
     * $validator->setMessage(string, 
     *     App_Form_Validate_OnlyFutureMonthYear::PAST_DATE);
     * Documentation: http://framework.zend.com/manual/en/zend.validate.set.html
     *
     * @var string
     */
    protected $_messageTemplates = array(
        self::PAST_DATE => "'%value%' is set in the past"
    );

    /**
     * Returns true if month/year fields equals current date() month/year, or if its a future date.
     * @param string $value
     * @param ?? $context
     * @return boolean
     */
    public function isValid($value, $context = '') {

        $currentMonth = date('n');
        $currentYear = date('Y');

        $monthField = $this->getMonthField();
        $this->_setValue($context[$monthField] . '/' . $value);

        if ((int) $context[$monthField] < $currentMonth && $value <= $currentYear) {
            $this->_error(self::PAST_DATE);
            return false;
        }
        return true;
    }
}