<?PHP

class bsdImportValidator  extends sfValidatorBase 
{

  protected function configure($options = array(), $messages = array())
  {

    $this->addMessage('reqdFields', 'Required Field (%reqdField%) is not present on line: %line%.');
    $this->addMessage('NotMultiDemArray', __CLASS__  ." expected a multidemensional array but found only a single array was found. Possibly ". __CLASS__ . " was given a single row?");

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
    $clean = $value;

    if(!$this->is_array_multi($clean))
    {
       throw new sfValidatorError($this, 'NotMultiDemArray', array());
    }


    foreach($clean as $line => $row)
    {
      foreach($this->getOption("reqdFields") as $reqdField)
      {
        if(is_null($row[$reqdField]) || $row[$reqdField] == "")
        {
           throw new sfValidatorError($this, 'reqdFields', array('reqdField' => $reqdField, 'lineNo' => $line));
        }
      }
    }

  }


}
