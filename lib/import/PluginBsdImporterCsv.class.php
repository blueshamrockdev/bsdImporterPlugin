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
