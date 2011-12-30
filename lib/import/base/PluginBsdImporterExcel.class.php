
<?PHP

abstract class PluginBsdImporterExcel implements PluginBsdImporter
{
 	protected $requiredHeaders = array();
 	protected $headerRow;


	/**
	 *
	 * @param int|String $rowNum
	 */
	public function setHeaderRow($rowNum)
	{
		$this->headerRow = $rowNum;
	}

	/**
	 *
	 * @return int|String
	 */
	public function getHeaderRow()
	{
		return $this->headerRow;
	}


	/**
	 *
	 * @param array $headers
	 */
	public function setHeaders(array $headers)
	{
		$this->requiredHeaders = $headers;
	}

	/**
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->requiredHeaders;
	}



	public function openFileForReading($uploadFile)
	{
		$baseFileReader = new ExcelExplorer();
		$status = $baseFileReader->ExploreFile($uploadFile);
		$baseFile = $baseFileReader->Worksheet(0);

		if ($status != EE_OK)
		{
			switch ($status)
			{
				case EE_INVFILE :
					$seption = "Given file ($uploadFile) is corrupted or not in Excel 5.0 or above format.\n";
					break;
				case EE_INVVER :
					$seption= "Given file ($uploadFile) is saved in an unknown Excel version.\n";
					break;
				case EE_FILENOTFOUND :
					$seption = "Given file ($uploadFile) could not be opened.\n";
					break;
				default:
					break;
			}
			throw new sfFileException($seption);
		}
		
		$validator = new bsdImportValidatorExcel($uploadFile, $this->getHeaderRow(), $this->getHeaders());
		/**
		 * @todo continue here
		 */

	}

}