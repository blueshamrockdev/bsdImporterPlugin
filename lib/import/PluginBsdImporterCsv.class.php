<?PHP

class PluginBsdImporterCsv extends PluginBsdImporter
{

    public function readData($filename)
    {
        if (($handle = fopen($filename, "r")) !== FALSE) 
        {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $dataRow = $data;
            }
            fclose($handle);
        }
        $this->DataRows = $this->genHeaderBasedArray($dataRow);
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

                $this->execute($rowData, $dryRun);
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
