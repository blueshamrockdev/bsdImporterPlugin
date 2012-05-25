<?PHP

/**
 * @package bsdImporterPlugin (com.blueshamrock.importer)
 * @author Micah Breedlove <micah@BlueShamrock.com>
 * 
 * Abstract class which contains functions common to both CSV and Excel 
 * imports. Your extended classes will require at minimum an execute function
 * accepting the (array) row to be processed and (boolean) if the execute should
 * be treated as dry run.
 * 
 * @see PluginBsdImporterCsv
 * @see PluginBsdImporterExcel
 * 
 */
abstract class PluginBsdImporter 
{

    const PRE_PROCESS_VALIDATE  = 1;
    const BY_ROW_VALIDATE       = 2;
    const NO_VALIDATION         = 13;

    const INVALID_COLUMN_HEADERS  = 'Z';
    const INVALID_COLUMN_COUNT    = 'Y';
    const INVALID_REQUIRED_FIELDS = 'X';
    
    protected $allowOptionalHeaders = false;
    protected $requiredHeaders = array();
    protected $fileHeaders     = array();
    protected $requiredFields  = array();
    protected $DataRows        = array();
    protected $validation      = self::PRE_PROCESS_VALIDATE;
    protected $errorMessages   = array(
        self::INVALID_COLUMN_HEADERS  => "ERROR (0014): Headers do not match required column headers.",
        self::INVALID_COLUMN_COUNT    => "ERROR (0015): Array Combination Failed: column count does not match!",
        self::INVALID_REQUIRED_FIELDS => "ERROR (0016): Required field was found empty. Please check file and try again.",
    );

    /**
     * constructor should I really explain what a constructor is?
     * @param string $fileToProcess the full path to the CSV/Excel file to parse
     */
    public function __construct($fileToProcess)
    {
        /**
         * @uses subClass::readData() 
         * 
         * childClass requirment readData should exist in PluginBsdImporterCsv and PluginBsdImporterExcel
         */
        $this->setValidation("pre");
        $this->readData($fileToProcess); 
    }

    public function setAllowOptionalHeaders($boolValue)
    {
        $this->allowOptionalHeaders = (bool) $boolValue;
    }

    public function getInvalidsArray()
    {
                return array( self::INVALID_COLUMN_COUNT, self::INVALID_COLUMN_HEADERS, self::INVALID_REQUIRED_FIELDS);
    }

    /*
     * BEGIN Generic Import specific  ( get it? :D )  functions
     */

    /**
     * alias for setRequiredHeaders
     * @param array $headers 
     */
    public function setHeaders(array $headers)
    {
		$this->requiredHeaders = $headers;
    }


    /**
     *
     * alias for getRequiredHeaders
     * @return array $headers 
     */
    public function getHeaders()
    {
		return $this->requiredHeaders; 
    } 


    /**
     *
     * @param array $data multideminsional array with the first sub array being headers
     * @return array 
     */
    function genHeaderBasedArray()
    {
        $headerCount = count($this->fileHeaders);
        $rowdata = array();
        foreach($this->DataRows as $rowNum => $row)
        {
            if ($headerCount == count($row)) {
                 $rowdata[] = array_combine($this->fileHeaders, $row);
            } else {
                 $this->badRow = $rowNum + 2; // 0 based index + 1 for header row
                 // trigger_error("ERROR (0015): Array Combination Epic failed -- Column count does not match!");
               return false;
            }
        }
        $this->DataRows = $rowdata;
        return true;
    }


    /**
     * defines when to execute validation 
     * accepts a string  of pre, row, or no
     * 
     * @param string $validationTime valid choices are pre, row and no
     *
     */
    public function setValidation($validationTime)
    {
         switch(strtolower($validationTime))
         {
            case("pre"):
                    $this->validation = self::PRE_PROCESS_VALIDATE;
                    break;
            case("row"):
                    $this->validation = self::BY_ROW_VALIDATE;
                    break;
            case("no"):
                    $this->validation = self::NO_VALIDATION;
                    break;
            default:
                    $this->validation = self::PRE_PROCESS_VALIDATE;
                    break;
         }
    }

    /*
     * BEGIN Magic and Misc functions
     */

    /**
     * Never mind that curtain. Pay it no attention... look over here. Look. See? A bunny!
     * hooray for magic!!
     *
     * @param string $method
     * @param mixed $arguments
     * @return mixed 
     */
    public function __call($method, $arguments) 
    {
        try {
                $verb = substr($method, 0, 3);
                if (in_array($verb, array('set', 'get'))) {
                        $name = substr($method, 3);
                }

                if (method_exists($this, $verb)) {
                        if (property_exists($this, $name)) {
                                return call_user_func_array(array($this, $verb), array_merge(array($name), $arguments));
                        } elseif (property_exists($this, lcfirst($name))) {
                                return call_user_func_array(array($this, $verb), array_merge(array(lcfirst($name)), $arguments));
                        } else {
                                throw new Exception("Variable  ($name)  Not Found");
                        }
                } else {
                        throw new Exception("Function ($verb) Not Defined");
                }
        } catch (Exception $e) {
                trigger_error("[DEV] Error (0013): Exception Thrown No User to catch it. Please insert quarter and try again.   \n\n " . $e, E_USER_ERROR);
        }
    }

    /** 
     * 
     * standard getter 
     * 
     * @param string $fieldName 
     * @return mixed 
     */ 
    public function get($fieldName) 
    { 
        if (!property_exists($this, $fieldName)) 
        { 
            trigger_error("[DEV] Error (0012): Variable ($fieldName) Not Found", E_USER_ERROR);
        }

        return $this->$fieldName;
    }

    /**
     * standard setter
     *
     * @param string $fieldName
     * @param mixed $value
     * @return boolean 
     */
    public function set($fieldName, $value) 
    {
            if (!property_exists($this, $fieldName)) {
                    trigger_error("[DEV] Error (0012): Variable ($fieldName) Not Found", E_USER_ERROR);
            }

            $this->$fieldName = $value;
            return true;
    }

    /**
     *
     * For some reason there is no lcfirst function but there 
     * is a ucfirst... oh well that's fixed. :)
     * 
     * @param string $string
     * @return string 
     */
    public function lcfirst($string) 
    {
        $string{0} = strtolower($string{0});
        return $string;
    }

    /*
     * END Magic and Misc functions
     */

    /*
     * BEGIN validation
     */

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
          $headers = $this->getFileHeaders();
          if($headers[$key] != $header)
          {
                if($this->allowOptionalHeaders){
                        continue;
                } else{
                        $this->badHeader = $header;
                        return false;
                }
          }
        }
        return true;
    }

    public function validateAllColumnCount()
    {
        foreach($this->getDataRows() as $row => $rowData)
        {
          $rowTest = $this->validateColumnCount($rowData);
          if(!$rowTest)
          {
             $this->badRow = $row + 2; // 0 based index + 1 for header row
             return false;
                // return self::INVALID_COLUMN_COUNT;
          }
            return true;
        }
    }

    protected function validateColumnCount($row)
    {
        if (count($this->fileHeaders) == count($row))
        {
                return true;
        }
                return false;
                // return self::INVALID_COLUMN_COUNT;
    }

   /**
     * validateRequiredFieldsInRow() - examines row provided
     * and determines if the predefined required fields exist
     * and returns based of that examination.
     * 
     * @param array $row
     * @return boolean 
     */
    protected function validateRequiredFieldsInRow($row)
    {
        $requiredFields = $this->getRequiredFields();
        foreach($requiredFields as $key => $reqdField)
        {
          if(!(isset($row[$reqdField])) || ($row[$reqdField] == "" || is_null($row[$reqdField])))
          {
            $this->badColumn = $reqdField;
            return false;
          }
        }
        return true;
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
                  $this->badRow = $row + 2;
            return false;
          }
        }
        return true;
    }


    /**
     * preProcessValidation() checks validation if validation IS set to occur before processing
     * execute validation and return bool (obviously true if valid or false if not)
     * 
     * if it is not set for preProcess validation then return the code for when to execute
     * 
     * return mixed char|null
     */
    protected function preProcessValidation()
    {
        $reqdFields = $this->validateRequiredFields();
        $validColumnCount = $this->validateAllColumnCount();
        if(!$validColumnCount)
        {
                return self::INVALID_COLUMN_COUNT;
        }
        if(!$reqdFields)
        {
                return self::INVALID_REQUIRED_FIELDS;
        }
        
        return null;
    }

    /*
     * BEGIN import processing
     */

    /**
     * processImport() - Runs validation and beings import functonality 
     * dryRun gives you the optional parameter that can be passed
     * on to your execute function so you can treat dryRun import 
     * differently than a real import.
     * 
     * @param boolean $dryRun Process import in a dry-run
     * @param boolean $allAsOne Process data as one big chunk instead of row-by-row 
     * @param array $options Optional parameters which can be handed off to execute or executeAll
     * @return array
     */
    public function processImport($dryRun = false, $options = array())
    {
        // Check Headers
        if(!$this->validateHeaders())
        {
                return $this->validationFailed(self::INVALID_COLUMN_HEADERS);
        }
                
        $redoHeaders = $this->genHeaderBasedArray();
        if (!$redoHeaders) {
                return $this->validationFailed(self::INVALID_COLUMN_COUNT);
        }

        if ( $this->validation ==  self::PRE_PROCESS_VALIDATE )
        {
                $preValidation = $this->preProcessValidation();
                if ( in_array($preValidation, $this->getInvalidsArray()) )
                {
                    return $this->validationFailed($preValidation); // user defined function (generic function provided)
                }
        }
        elseif ($preValidation === self::BY_ROW_VALIDATE)
        {
                $options['rowValidation'] = true;
        }
        
        return $this->doImport($dryRun, $options);
        
    }

    /**
     * doImport - Meat & Potatos
     *
     *
     * @param boolean $dryRun Process import in a dry-run
     * @param array $options Optional parameters which can be handed off to execute or executeAll
     * @return array
     *  
     */
    protected function doImport($dryRun = false, $options = array())
    {
        $allAsOne = (isset($options['allAsOne'])) ? $options['allAsOne'] : false;
        if(!$allAsOne)
        {
                foreach ($this->DataRows as $row => $rowData)
                {
                        if ($options['rowValidation'])
                        {
                                if ( !($this->validateRequiredFieldsInRow($rowData)) && !($this->validateColumnCount($row)) )
                                {
                                        return array("success"=> false, "message" => "Validation Error in row  $row.  Please check the file and try again.");
                                }
                        }

                        $data = $this->execute($rowData, $dryRun, $options);
                        
                }
        }
        else
        {
                $data = $this->executeAll($dryRun, $options);
        }
        return $data;
    }


    /**
     * execute() - Programmer defined logic for what should be done 
     * with each row as it's processed. 
     * 
     * @see processImport()
     * @param array $row
     * @param boolean $dryRun 
     * @param array $options
     */
    public function execute($row, $dryRun = false, $options = array()) 
    {
        /* 
         *
         * does nothing here 
         * all the magic is handled in the user's class's execute()
         * I don't know what you want to do with your imports... 
         * I'm not psychic :D
         *
         */
    } 

    /**
     * executeAll() - Programmer defined logic for what should be done 
     * with all rows. 
     * 
     * @see processImport()
     * @param boolean $dryRun 
     * @param array $options
     */ 
    public function executeAll($dryRun = false, $options = array())
    {
        /* 
         *
         * does nothing here 
         * all the magic is handled in the user's class's executeAll()
         * I don't know what you want to do with your imports... 
         * I'm not psychic :D
         *
         */
 
    }
}
