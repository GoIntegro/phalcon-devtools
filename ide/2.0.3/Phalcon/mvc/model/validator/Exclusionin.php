<?php

namespace Phalcon\Mvc\Model\Validator;

/**
 * Phalcon\Mvc\Model\Validator\ExclusionIn
 * Check if a value is not included into a list of values
 * <code>
 * use Phalcon\Mvc\Model\Validator\ExclusionIn as ExclusionInValidator;
 * class Subscriptors extends \Phalcon\Mvc\Model
 * {
 * public function validation()
 * {
 * $this->validate(new ExclusionInValidator(array(
 * 'field' => 'status',
 * 'domain' => array('A', 'I')
 * )));
 * if ($this->validationHasFailed() == true) {
 * return false;
 * }
 * }
 * }
 * </code>
 */
class Exclusionin extends \Phalcon\Mvc\Model\Validator implements \Phalcon\Mvc\Model\ValidatorInterface
{

    /**
     * Executes the validator
     *
     * @param mixed $record 
     * @return bool 
     */
    public function validate(\Phalcon\Mvc\ModelInterface $record) {}


     function zephir_init_properties_Phalcon_Mvc_Model_Validator_Exclusionin() {}

}
