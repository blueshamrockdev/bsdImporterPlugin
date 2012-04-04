<?PHP

/**
 * NOTE: This file (Excel Import) is still in development and is not recommended for use
 *       PLEASE look at the CSV file it is ready for use.
 * 
 * @package bsdImporterPlugin (com.blueshamrock.importer)
 * @author Micah Breedlove <micah@BlueShamrock.com>
 * 
 * PluginBsdImporterExcel should be extended by YOU the user
 * it *SHOULD* have everything you need but feel free to do
 * with it as you see fit 
 * 
 * REQUIRED FUNCTION:
 *    +  execute($row, $dryRun = false, $options = array()) - The execute function is 
 *        executed on each row of your import. It would be there 
 *        you would save or do what you will with your data.
 * 
 */
class PluginBsdImporterExcel extends PluginBsdImporter
{
 	protected $headerRow;
 	protected $activeSheet;
 	protected $excelType;


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

	public function openFileForReading($uploadFile)
	{
		$baseFileReader = new ExcelExplorer();
		$status = $baseFileReader->ExploreFile($uploadFile);
		$baseFile = $baseFileReader->Worksheet($this->getActiveSheet());

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
