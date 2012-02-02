<?PHP

interface PluginBsdImporterInterface 
{

 	protected $requiredHeaders = array();

	public function setHeaders();

	public function getHeaders();

  public function execute();
	
}
