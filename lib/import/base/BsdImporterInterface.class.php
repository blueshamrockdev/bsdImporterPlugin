<?PHP

interface PluginBsdImporterInterface 
{

    protected $requiredHeaders = array();

    /**
     * alias for setRequiredHeaders
     */
    public function setHeaders(array $headers);

    /**
     * alias for getRequiredHeaders
     */
    public function getHeaders();

    /**
     * the actual import process
     */
    public function processImport($dryRun = false, $allAsOne = false, $options = array());

    /**
     * action to execute for each row during import process
     */
    public function execute($row, $dryRun = false, $options = array());
	
    /**
     * action to execute for all rows during import process
     */
    public function executeAll($dryRun = false, $options = array());
}
