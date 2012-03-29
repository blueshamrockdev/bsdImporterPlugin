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
    public function processImport($dryRun = false);

    /**
     * action to execute for each row during import process
     */
    public function execute($row);
	
}
