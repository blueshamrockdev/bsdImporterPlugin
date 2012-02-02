<?PHP

class PluginBsdImporterCsv extends PluginBsdImporter
{

  public function validateHeaders()
  {
    foreach($this->getRequiredHeaders() as $key => $header)
    {
      $headers = $this->getHeaders();
      if($headers[$key] != $header)
      {
         return false;
      }
    }
    return true;
  }

  public function validateRequiredFieldsInRow($row)
  {
    $requiredFields = $this->getRequiredFields();
    foreach($requiredFields as $key => $reqdField)
    {
      $field = $row[$reqdField];
      if($field == "" || is_null($field) )
      {
        return false;
      }
    }
  }

  public function validateRequiredFields()
  {
    $requiredFields = $this->getRequiredFields();
    foreach($this->getDataRows() as $row => $rowData)
    {
      $rowTest = $this->validateRequiredFieldsInRow($rowData);
      if(!$rowTest)
      {
        return false;
      }
    }
    return true;
  }

}
