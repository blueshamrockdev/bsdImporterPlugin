<?PHP

class bsdImportRowValidator  extends sfValidatorBase 
{

  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('reqdFields', 'Required Field (%reqdField%) is not present.');
    $this->addMessage('multiDemArray', __CLASS __ ." expected a single row but found many. Possibly " . __CLASS __ . " was given all rows?");

    $this->addOption('reqdFields');
  }


  public function is_array_multi($array)
  {
    return (bool) (count($array) != count($array, COUNT_RECURSIVE));
  }

  public function clean($value)
  {
      $this->doClean($value);
  }

  protected function doClean($value)
  {
    $RowToBeValidated = $value;

    if($this->is_array_multi($RowToBeValidated))
    {
       throw new sfValidatorError($this, 'multiDemArray', array());
    }

    foreach($this->getOption("reqdFields") as $reqdField)
    {
      if(is_null($RowToBeValidated[$reqdField]) || $RowToBeValidated[$reqdField] == "")
      {
        throw new sfValidatorError($this, 'reqdFields', array('reqdField' => $reqdField));
      }
    }

    return $RowToBeValidated;
  }


}
