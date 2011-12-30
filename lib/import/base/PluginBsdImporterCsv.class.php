<?PHP

abstract class PluginBsdImporterCsv implements PluginBsdImporter
{
 	protected $requiredHeaders = array();

	/**
	 *
	 * @param array $headers
	 */
	public function setHeaders(array $headers)
	{
		$this->requiredHeaders = $headers;
	}

	public function getHeaders()
	{
		return $this->requiredHeaders;
	}


}