bsdImporterPlugin
=================
*symfony 1.4* plugin for importing CSV and Excel files  
  
author Micah Breedlove <Micah@BlueShamrock.com>  
package bsdImporterPlugin (com.blueshamrock.importer)  

Symfony 1.4 Plugin which simplifies CSV and Excel imports.  
Create your own classes which extend the appropriate class and with in a few minutes you 
will be ready to being the import process.  
  
Your extended classes will require the following functions:  
 * execute($row, $dryRun = false, $options = array()) - The execute function accepts the (array) row to be processed and (boolean) if the execute should be treated as dry run and lastly (array) options.  
 * executeAll($dryRun = false, $options = array()) - The executeAll function accepts (boolean) if the execute should be treated as dry run and lastly (array) options.  
 * validationFailed($failureCode) - The validationFailed function accepts the (CONST string) validation Code of the given error. It allows you to define what happens when an error occurs.  
  
Dependency
---------
[PHP Excel](http://phpexcel.codeplex.com)  

Execution
---------
Execution in your application occurs by using the processImport function. processImport accepts 2 arguments:  

   * **dryRun** (*default: false*)  
   * **options** (*default: empty array*)   

     * allAsOne is currently the only plugin specific option "allAsOne" forces the processImport function to call the executeAll()  
  
Both of these arguments are passed to the appropriate execute(All) function.  
The following examples assumes you have extended the appropriate importer class (in this example bsdPaymentCSVImport extends PluginBsdImporterCsv):  
    
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->processImport(true); // process this file row by row in a dryRun  
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->setValidation("row"); // execute validation row-by-row as being processed  
    $payments->processImport(false); // process this file row-by-row  (default: could also be executed as $payments->processImport())  
      
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->processImport(false, array('allAsOne' => true)); // process this file as one chunk  
      
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->setValidation("no"); // execute NO validation  (you obviously live on the edge)  
    $payments->processImport(false); // process this file row-by-row   
      
       
    $payments = new bsdPaymentCSVImport($csvFile);  
    $payments->setValidation("pre"); // execute validation before processing any data (default)  
    $payments->processImport(false,  array('allAsOne' => true)); // process this file as one chunk  


