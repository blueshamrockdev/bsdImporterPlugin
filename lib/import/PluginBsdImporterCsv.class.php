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

    /**
     * validateHeaders() - Examines the header row in given csv
     * file and verifies that these headers exist.
     * 
     * @return boolean 
     */
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
  
    /**
     * validateRequiredFieldsInRow() - examines row provided
     * and determines if the predefined required fields exist
     * and returns based of that examination.
     * 
     * @param array $row
     * @return boolean 
     */
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
  
    /**
     * Validates all rows in csv for the fields set as required
     * by passing them through $this->validateRequiredFieldsInRow()
     * 
     * @see validateRequiredFieldsInRow
     * @return boolean 
     */
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

    /**
     * processImport() - Runs validation and beings import functonality 
     * dryRun gives you the optional parameter that can be passed
     * on to your execute function so you can treat dryRun import 
     * differently than a real import.
     *
     * @param boolean $dryRun
     * @return array 
     */
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
        
        return array("success" => true);
    }

    /**
     * execute() - Programmer defined logic for what should be done 
     * with each row as it's processed. 
     * 
     * @see processImport()
     * @param array $row
     * @param boolean $dryRun 
     */ 
    public function execute($row, $dryRun = false) 
    {
    }


}
