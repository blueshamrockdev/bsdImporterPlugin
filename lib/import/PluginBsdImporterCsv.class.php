<?PHP

class PluginBsdImporterCsv extends PluginBsdImporter
{

    public function readData($filename)
    {
        if (($handle = fopen($filename, "r")) !== FALSE) 
        {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $this->DataRows[] = $data;
            }
            fclose($handle);
        }
    }

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

    public function processImport($dryRun = null)
    {
        if ( $this->validation ==  self::PRE_PROCCESS_VALIDATE )
        {
            $this->validateRequiredFields();
        }
        elseif ( $this->validation ==  self::BY_ROW_VALIDATE )
        {
                $rowValidation = true;
        }


        foreach ($this->DataRows as $row => $rowData)
        {
                if ($rowValidation)
                {
                        if (!($this->validateRequiredFieldsInRow($rowData)))
                        {
                                return array("success"=> "failure", "message" => "Validation Error in row  $row.  Please check the file and try again.");
                        }
                }

                if($dryRun)
                {
                        $this->dryRunExecute($rowData);
                }
                else {
                        $this->execute($rowData);
                }
        }
 
  
    }



}
