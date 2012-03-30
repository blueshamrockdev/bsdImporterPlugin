<?PHP

abstract class PluginBsdImporter implements PluginBsdImporterInterface 
{

    const PRE_PROCCESS_VALIDATE = 1;
    const BY_ROW_VALIDATE       = 2;
    const NO_VALIDATION         = 13;
    
    protected $requiredHeaders = array();
    protected $fileHeaders     = array();
    protected $requiredFields  = array();
    protected $DataRows        = array();
    protected $validation      = self::PRE_PROCESS_VALIDATE;

    public function __construct($fileToProcess)
    {
        $this->readData($fileToProcess);
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
    function genHeaderBasedArray($data)
    {
        $this->fileHeaders = array_shift($data);
        $headerCount = count($this->fileHeaders);
        $rowdata = array();
        foreach($data as $row)
        {
            if ($headerCount == count($row)) {
                $rowdata[] = array_combine($this->fileHeaders, $row);
            } else {
                trigger_error("ERROR (0014): Array Combination Epic failed -- Column count does not match!");
            }
        }
        return $rowdata;
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
     * 
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
                trigger_error("Error (0013): Exception Thrown No User to catch it. Please insert quarter and try again.   \n\n " . $e, E_USER_ERROR);
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
            trigger_error("Variable ($fieldName) Not Found", E_USER_ERROR);
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
                    trigger_error("Variable ($fieldName) Not Found", E_USER_ERROR);
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
          $headers = $this->getHeaders();
          if($headers[$key] != $header)
          {
             return false;
          }
        }
        return true;
    }

    public function validateAllRowCount()
    {
        foreach($this->getDataRows() as $row => $rowData)
        {
          $rowTest = $this->validateRowCount($rowData);
          if(!$rowTest)
          {
            return false;
          }
            return true;
        }
    }

    public function validateRowCount($row)
    {
        if (count($this->fileHeaders) == count($row))
        {
                return true;
        }
                return false;
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


    /*
     * BEGIN import processing
     */

    /**
     * processImport() - Runs validation and beings import functonality 
     * dryRun gives you the optional parameter that can be passed
     * on to your execute function so you can treat dryRun import 
     * differently than a real import.
     * 
     * @param boolean $dryRun 
     * @return array
     */
    public function processImport($dryRun = false)
    {
        if ( $this->validation ==  self::PRE_PROCCESS_VALIDATE )
        {
            $this->validateRequiredFields();
            $this->validateAllRowCount();
        }
        elseif ( $this->validation ==  self::BY_ROW_VALIDATE )
        {
                $rowValidation = true;
        }


        foreach ($this->DataRows as $row => $rowData)
        {
                if ($rowValidation)
                {
                        if ( !($this->validateRequiredFieldsInRow($rowData)) && !($this->validateRowCount($row)) )
                        {
                                return array("success"=> false, "message" => "Validation Error in row  $row.  Please check the file and try again.");
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
        /** 
         *
         * does nothing here 
         * all the magic is handled in the user's class's execute()
         * I don't know what you want to do with your imports... 
         * I'm not psychic :D
         *
         */
    } 
}
