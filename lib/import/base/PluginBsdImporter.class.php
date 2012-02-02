<?PHP

abstract PluginBsdImporter implements PluginBsdImporterInterface 
{

 	protected $requiredHeaders = array();
 	protected $requiredFields = array();
 	protected $DataRows = array();

	/**
	 *
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
                        printf("You done yucked up!");
                        var_dump($e);
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
                if (!property_exists($this, $fieldName)) {
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

        public function execute() 
        {
            /** does nothing here 
             * all the magic is handled in the user's class's execute()
             * I don't know what you want to do with your imports... 
             * I'm not psychic :D
             */
        } 

}
