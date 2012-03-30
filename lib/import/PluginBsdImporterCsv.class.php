<?PHP

/**
 * @package PluginBsdImporter (com.blueshamrock.importer)
 * @author Micah Breedlove <micah@BlueShamrock.com>
 * 
 * PluginBsdImporterCsv should be extended by YOU the user
 * it *SHOULD* have everything you need but feel free to do
 * with it as you see fit 
 * 
 * REQUIRED FUNCTION:
 *    +  execute($row, $dryRun = false) - The execute function is 
 *        executed on each row of your import. It would be there 
 *        you would save or do what you will with your data.
 * 
 */
class PluginBsdImporterCsv extends PluginBsdImporter
{

    /**
     *
     * @param string $filename  the full path to the CSV file to parse
     */
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
        /**
         * This is the point when your extended class should kick in.
         * The execute function is executed on each row and do with the 
         * data what you will. 
         * The power is in your hands now go and be free.
         *
         * You have all the weapons you need.
         * Now fight.
         * 
         * @uses subclass::execute($row, $dryRun = false)
         */
        /* 
         *
         * does nothing here 
         * all the magic is handled in the user's class's execute()
         * I don't know what you want to do with your imports... 
         * I'm not psychic :D
         *
         */
     }


}
